# PLUGINS.md

Documentation de référence du système de plugins et de hooks pour BittyTorrent.

Ce document décrit le fonctionnement réel du code actuel : chargement des plugins, exécution des hooks, points d’extension UI, API disponibles et limites connues.

---

## 1. Vue d’ensemble

Le système de plugins de BittyTorrent est un système **côté client** basé sur :

- des plugins déclaratifs de type `PluginMeta`
- un registre global de plugins actifs/inactifs
- un registre global de hooks
- des points d’extension UI : menu, footer, sidebar, topbar, pages plugins
- une persistance d’état en base de données via `plugin_states`

### Cycle de démarrage

Au chargement de l’application :

1. tous les fichiers `app/plugins/builtin/**/*.plugin.ts` sont chargés automatiquement ;
2. chaque export valide de type `PluginMeta` est enregistré ;
3. les hooks déclarés dans `meta.hooks` sont branchés automatiquement si le plugin est actif ;
4. l’état d’activation est ensuite synchronisé depuis `GET /api/plugins` ;
5. le hook global `app:init` est exécuté.

Conséquence importante :

- un plugin détecté est **actif par défaut** ;
- si la base contient un état, celui-ci reprend la main après l’enregistrement initial.

---

## 2. Architecture des fichiers

Structure actuelle recommandée pour un plugin built-in :

```text
app/plugins/builtin/<plugin>/
├── index.ts
├── <plugin>.plugin.ts
├── components/
│   ├── index.ts
│   └── ...
└── composables/
    └── index.ts
```

Exemples présents dans le dépôt :

- `app/plugins/builtin/hello/hello.plugin.ts`
- `app/plugins/builtin/donate/donate.plugin.ts`

Le chargement automatique ne dépend pas du nom du dossier, mais du pattern :

- `app/plugins/builtin/**/*.plugin.ts`

---

## 3. Types clés

## 3.1 `HookName`

La liste des hooks est **fermée** et typée dans `types/plugin.d.ts`.

Hooks disponibles :

- `app:init`
- `page:plugin`
- `page:home`
- `page:login`
- `page:register`
- `page:account`
- `page:stats`
- `page:install`
- `page:logout`
- `page:torrent-list`
- `page:torrent-detail`
- `page:upload`
- `sidebar:before`
- `sidebar:after`
- `menu:main`
- `menu:user`
- `torrent:before-upload`
- `torrent:after-upload`
- `torrent:before-download`
- `admin:page`
- `admin:menu`
- `admin:settings`
- `user:login`
- `user:register`
- `footer:content`

Pour ajouter de nouveaux hooks, il faut étendre le type `HookName` puis créer les `executeHook()` correspondants.

## 3.2 `HookPriority`

Priorités autorisées : `1` à `10`.

Convention actuelle :

- `1` = exécuté le plus tôt
- `10` = exécuté le plus tard

## 3.3 `HookContext`

Chaque callback reçoit au minimum :

```ts
{
  hook: HookName
  timestamp: string
  path?: string
}
```

Puis le contexte métier est fusionné.

Règle de normalisation :

- si le contexte passé à `executeHook()` est un objet, ses propriétés sont fusionnées ;
- sinon, la valeur est placée dans `payload`.

Exemple :

```ts
hooks: {
  "page:torrent-detail": (ctx) => {
    console.log(ctx.hook)
    console.log(ctx.timestamp)
    console.log(ctx.path)
    console.log(ctx.infoHash)
    console.log(ctx.torrent)
  },
}
```

## 3.4 `PluginMeta`

Un plugin déclare ses métadonnées et ses points d’extension dans un objet `PluginMeta`.

Champs principaux :

- `id`: identifiant unique du plugin
- `name`: nom affiché
- `version`: version
- `description`: description courte
- `author`: auteur
- `authorUrl?`
- `pluginUrl?`
- `hooks?`: callbacks de hooks à enregistrer automatiquement
- `adminPages?`: pages admin déclarées par le plugin
- `menuItems?`: éléments de menu
- `sidebarBlocks?`: blocs de sidebar
- `footerBlocks?`: blocs de footer
- `topbarIcons?`: icônes de topbar
- `pluginPages?`: pages servies sous `/plugins/*`

---

## 4. Enregistrement et exécution des hooks

## 4.1 Enregistrement automatique

Quand `registerPlugin(meta, true)` est appelé :

- le plugin est ajouté au registre global ;
- `installedAt` est défini ;
- chaque entrée de `meta.hooks` est enregistrée via `addHook()`.

### Point important sur la priorité

Les hooks déclarés dans `PluginMeta.hooks` sont actuellement enregistrés avec une priorité fixe de `5`.

Cela signifie :

- on peut définir une priorité sur les éléments UI (`menuItems`, `footerBlocks`, etc.) ;
- mais **pas directement** sur les hooks déclaratifs de `meta.hooks`.

Si un plugin a besoin d’une priorité différente, il doit utiliser `useHooks().addHook()` manuellement dans du code exécuté côté client.

## 4.2 Ordre d’exécution

Les hooks sont exécutés :

1. par ordre de priorité croissante ;
2. de façon séquentielle ;
3. avec `await` sur chaque callback.

Conséquence :

- un hook lent bloque les suivants ;
- un callback asynchrone est attendu avant le suivant.

## 4.3 Gestion des erreurs

Le moteur actuel n’isole pas les erreurs de callback.

Si un callback lève une exception pendant `executeHook()`, l’exécution du hook s’interrompt et les callbacks suivants ne seront pas exécutés, sauf si l’appelant capture lui-même l’erreur.

## 4.4 Désactivation d’un plugin

Lorsqu’un plugin est désactivé :

- son état `active` passe à `false` ;
- tous ses hooks sont retirés via `removePluginHooks(pluginId)`.

Les points d’extension UI ne sont plus exposés non plus, car ils sont toujours recalculés à partir de `getActivePlugins()`.

---

## 5. Catalogue complet des hooks

Le tableau suivant documente le comportement actuel observé dans le code.

| Hook                      | Déclenchement                                                              | Contexte spécifique actuel                      | Usage typique                             |
| ------------------------- | -------------------------------------------------------------------------- | ----------------------------------------------- | ----------------------------------------- |
| `app:init`                | Après chargement des plugins built-in et synchronisation des états serveur | aucun champ additionnel                         | initialisation globale d’un plugin        |
| `page:plugin`             | À l’ouverture d’une page `/plugins/*` et lors du changement de page plugin | `page`, `pluginPagePath`                        | analytics, chargement de données plugin   |
| `page:home`               | Au montage de la page d’accueil                                            | `page: "home"`                                  | contenu home, tracking                    |
| `page:login`              | Au montage de la page login                                                | `page: "login"`                                 | métriques, préparation UI                 |
| `page:register`           | Au montage de la page register                                             | `page: "register"`, `registrationEnabled`       | personnalisation de l’inscription         |
| `page:account`            | Au montage de la page account                                              | `page: "account"`, `loggedIn`                   | enrichissement du compte                  |
| `page:stats`              | Au montage de la page stats                                                | `page: "stats"`                                 | statistiques étendues                     |
| `page:install`            | Au montage de la page d’installation                                       | `page: "install"`                               | installation assistée                     |
| `page:logout`             | À l’ouverture de la page logout                                            | `page: "logout"`                                | nettoyage session côté plugin             |
| `page:torrent-list`       | Au montage de la liste des torrents                                        | `page: "torrents"`, `categoryId`                | filtres, tracking, enrichissement liste   |
| `page:torrent-detail`     | Au montage d’une fiche torrent                                             | `page: "torrent-detail"`, `infoHash`, `torrent` | enrichissement détail torrent             |
| `page:upload`             | Au montage de la page d’upload                                             | `page: "upload"`                                | préparation d’upload                      |
| `sidebar:before`          | Au montage de la sidebar dynamique                                         | `blocks`                                        | instrumentation avant rendu sidebar       |
| `sidebar:after`           | Immédiatement après `sidebar:before`, au même montage                      | `blocks`                                        | instrumentation après préparation sidebar |
| `menu:main`               | Au montage du header                                                       | `loggedIn`, `isAdmin`                           | personnalisation du menu principal        |
| `menu:user`               | Au montage du header                                                       | `loggedIn`, `isAdmin`                           | personnalisation du menu utilisateur      |
| `torrent:before-upload`   | Juste avant l’envoi d’un fichier torrent                                   | `fileName`, `categoryId`                        | validation, logs, préparation             |
| `torrent:after-upload`    | Juste après un upload réussi                                               | `torrent`                                       | indexation, logs, notifications           |
| `torrent:before-download` | Juste avant téléchargement d’un torrent                                    | `infoHash`, `torrent`                           | audit, tracking, contrôle                 |
| `admin:page`              | Au montage des pages admin instrumentées                                   | `page`, parfois `torrentId`                     | instrumentation des écrans admin          |
| `admin:menu`              | Au montage du header si l’utilisateur est admin                            | `page: "header-menu"`                           | logique de menu admin                     |
| `admin:settings`          | Au montage de la page admin config                                         | `page: "admin-config"`, `form`                  | extension des réglages                    |
| `user:login`              | Après un login réussi                                                      | `email`                                         | audit, welcome flow, tracking             |
| `user:register`           | Après une inscription réussie                                              | `username`, `email`                             | onboarding, logs, intégrations            |
| `footer:content`          | Au montage du footer                                                       | `page: "footer"`, `blocks`                      | instrumentation et contenu additionnel    |

---

## 6. Où chaque hook est déclenché

### Hooks de pages publiques

- `page:home` : page d’accueil
- `page:login` : page de connexion
- `page:register` : page d’inscription
- `page:account` : page compte
- `page:stats` : page statistiques
- `page:install` : page installation
- `page:logout` : page de déconnexion
- `page:torrent-list` : liste des torrents
- `page:torrent-detail` : détail d’un torrent
- `page:upload` : page d’upload
- `page:plugin` : page dynamique `/plugins/*`

### Hooks de navigation et layout

- `menu:main` : header desktop
- `menu:user` : header desktop
- `sidebar:before` / `sidebar:after` : sidebar dynamique
- `footer:content` : footer
- `admin:menu` : header si admin

### Hooks métier

- `user:login` : après login réussi
- `user:register` : après inscription réussie
- `torrent:before-upload` : avant upload
- `torrent:after-upload` : après upload réussi
- `torrent:before-download` : avant téléchargement

### Hooks d’administration

`admin:page` est déclenché sur les pages admin instrumentées suivantes :

- dashboard admin
- catégories
- configuration
- plugins
- peer sessions
- liste torrents
- détail torrent admin
- utilisateurs

`admin:settings` est déclenché uniquement sur la page de configuration admin.

---

## 7. Points d’extension UI

## 7.1 `menuItems`

Permet d’ajouter des liens dans la navigation plugin.

Structure :

```ts
{
  id: string
  label: string
  url: string
  priority?: 1..10
  external?: boolean
  requiresAuth?: boolean
}
```

Comportement actuel :

- tri par priorité croissante ;
- affichage desktop dans le header ;
- affichage mobile dans la navigation mobile ;
- si `external: true`, le lien est ouvert comme lien externe ;
- si `requiresAuth: true`, l’item n’est visible que pour un utilisateur connecté.

## 7.2 `topbarIcons`

Permet d’ajouter des actions dans la top bar desktop.

Structure :

```ts
{
  id: string
  icon: string
  title?: string
  priority?: 1..10
  action: "toggle-sidebar" | "open-url"
  url?: string
}
```

Comportement actuel :

- tri par priorité croissante ;
- `toggle-sidebar` appelle `toggleSidebar()` ;
- `open-url` ouvre `url` dans un nouvel onglet.

Note : le champ `icon` est prévu, mais l’affichage actuel utilise surtout `title` côté header.

## 7.3 `sidebarBlocks`

Permet d’ajouter des blocs dans la sidebar.

Structure :

```ts
{
  id: string
  title: string
  component: string
  componentDef?: Component
  priority?: 1..10
}
```

Comportement actuel :

- tri par priorité croissante ;
- rendu dans `SidebarDynamic.vue` ;
- dans l’implémentation actuelle, il faut **fournir `componentDef`** pour que le bloc soit réellement rendu ;
- sinon, un message “composant introuvable” est affiché.

## 7.4 `footerBlocks`

Identique à `sidebarBlocks`, mais injecté dans le footer.

Même remarque : dans l’état actuel du code, fournir `componentDef` est la solution fiable.

## 7.5 `pluginPages`

Permet d’ajouter des pages sous `/plugins/*`.

Structure :

```ts
{
  id: string
  title: string
  path: string
  component: string
  componentDef?: Component
  priority?: 1..10
  requiresAuth?: boolean
  adminOnly?: boolean
}
```

Exemple :

- `path: "hello"` devient `/plugins/hello`

Comportement actuel :

- toutes les pages plugins actives sont fusionnées puis triées par priorité ;
- la résolution se fait par comparaison du `path` normalisé ;
- `requiresAuth` bloque l’accès si l’utilisateur n’est pas connecté ;
- `adminOnly` bloque l’accès si l’utilisateur n’est pas admin.

## 7.6 `adminPages`

Le type existe et `usePlugins()` expose `getAdminPages()`, mais il n’y a pas aujourd’hui de rendu branché dans l’UI pour ces pages.

Autrement dit :

- le contrat TypeScript existe ;
- l’agrégation existe ;
- l’intégration visuelle n’est pas encore câblée.

---

## 8. API disponibles pour les plugins

## 8.1 `useHooks()`

API actuelle :

```ts
const {
  addHook,
  removeHook,
  removePluginHooks,
  buildHookContext,
  executeHook,
  hookExists,
  getHookEntries,
  getHooksSummary,
} = useHooks()
```

### `addHook(name, callback, priority?, pluginId?)`

Ajoute un callback à un hook.

Cas d’usage :

- priorité personnalisée ;
- enregistrement dynamique ;
- instrumentation temporaire.

### `removeHook(name, callback)`

Retire un callback précis.

### `removePluginHooks(pluginId)`

Retire tous les hooks associés à un plugin.

### `buildHookContext(name, context?)`

Construit le contexte normalisé.

### `executeHook(name, context?)`

Exécute tous les callbacks d’un hook, dans l’ordre.

### `hookExists(name)`

Indique si un hook a au moins un callback enregistré.

### `getHookEntries(name)`

Retourne les callbacks enregistrés pour un hook donné.

### `getHooksSummary()`

Retourne un résumé du registre des hooks, utile pour debug/admin.

## 8.2 `usePlugins()`

API actuelle :

```ts
const {
  registerPlugin,
  activatePlugin,
  deactivatePlugin,
  togglePlugin,
  getPlugin,
  getAllPlugins,
  getActivePlugins,
  getAdminPages,
  getSidebarBlocks,
  getFooterBlocks,
  getMenuItems,
  getTopbarIcons,
  getPluginPages,
  plugins,
} = usePlugins()
```

Usages principaux :

- enregistrer un plugin dynamiquement ;
- activer/désactiver un plugin ;
- récupérer les extensions UI actives ;
- inspecter l’état global des plugins.

---

## 9. Persistance et administration

Le statut des plugins est persisté dans la collection MongoDB `plugin_states`.

### Endpoints

#### Public

- `GET /api/plugins`
  - retourne les états actifs/inactifs
  - utilisé au démarrage côté client

#### Admin

- `GET /api/admin/plugins`
  - retourne la liste complète des états pour l’interface d’administration
- `POST /api/admin/plugins/:id`
  - body : `{ action: "activate" | "deactivate" }`
  - met à jour l’état en base

### Règle métier actuelle

Si la collection est vide :

- aucun état explicite n’est chargé ;
- les plugins détectés restent donc actifs par défaut.

---

## 10. Exemple minimal de plugin

```ts
import type { PluginMeta } from "~/types/plugin"
import { HelloPluginPage } from "./components"

export const HelloPlugin: PluginMeta = {
  id: "hello-plugin",
  name: "Hello Plugin",
  version: "1.0.0",
  description: "Ajoute une page plugin et quelques hooks.",
  author: "VotreNom",

  menuItems: [
    {
      id: "hello-link",
      label: "Hello",
      url: "/plugins/hello",
      priority: 5,
      external: false,
      requiresAuth: true,
    },
  ],

  pluginPages: [
    {
      id: "hello-plugin-page",
      title: "Hello Plugin",
      path: "hello",
      component: "HelloPluginPage",
      componentDef: HelloPluginPage,
      requiresAuth: true,
      priority: 5,
    },
  ],

  hooks: {
    "app:init": (ctx) => {
      console.info("[HelloPlugin] init", ctx)
    },
    "user:login": (ctx) => {
      console.info("[HelloPlugin] login", ctx.email)
    },
    "page:plugin": (ctx) => {
      console.info("[HelloPlugin] page", ctx.pluginPagePath)
    },
  },
}
```

---

## 11. Exemple avancé avec priorité personnalisée

Si vous avez besoin d’une priorité autre que `5` pour un hook, utilisez `addHook()` directement.

```ts
const { addHook } = useHooks()

addHook(
  "page:home",
  async (ctx) => {
    console.log("Je passe avant les hooks par défaut", ctx)
  },
  1,
  "my-plugin",
)
```

---

## 12. Bonnes pratiques

- utiliser des `id` uniques pour tout : plugin, menu, bloc, page, icône ;
- préfixer les identifiants avec le nom du plugin ;
- garder les hooks rapides et non bloquants ;
- encapsuler les erreurs dans les callbacks critiques ;
- utiliser `componentDef` pour les blocs visuels injectés ;
- prévoir `requiresAuth` et `adminOnly` quand nécessaire ;
- journaliser clairement en développement : `"[NomPlugin] ..."` ;
- éviter les effets de bord lourds dans `app:init`.

---

## 13. Limites connues du système actuel

### 13.1 Système essentiellement client-side

Le moteur de hooks est initialisé dans un plugin Nuxt client. Les callbacks de `PluginMeta.hooks` sont donc pensés pour le runtime client.

### 13.2 Pas d’isolation des erreurs

Une erreur dans un callback peut interrompre la chaîne d’exécution du hook.

### 13.3 Exécution séquentielle

Tous les callbacks sont exécutés un par un avec `await`.

### 13.4 Priorité fixe pour `meta.hooks`

Les hooks déclarés dans `PluginMeta.hooks` sont enregistrés avec une priorité `5`.

### 13.5 `adminPages` non rendu dans l’UI

Le contrat est prêt, mais l’intégration visuelle n’est pas encore branchée.

### 13.6 `componentDef` fortement recommandé

Pour les blocs sidebar/footer en particulier, l’absence de `componentDef` conduit actuellement à un fallback “composant introuvable”.

---

## 14. Checklist de création d’un plugin

1. créer un dossier dans `app/plugins/builtin/<nom>/`
2. créer un fichier `<nom>.plugin.ts`
3. exporter un objet `PluginMeta`
4. ajouter si besoin des composants dans `components/`
5. fournir `componentDef` pour les composants injectés
6. redémarrer l’application si nécessaire
7. vérifier l’activation du plugin dans l’admin
8. contrôler les logs des hooks dans le navigateur

---

## 15. Comment construire un plugin tiers

Dans l’état actuel du projet, un plugin tiers n’est pas chargé depuis un package npm externe ou un registre de plugins.

Pour être reconnu par BittyTorrent, le plugin doit aujourd’hui être **présent dans le code source** sous :

- `app/plugins/builtin/<nom-du-plugin>/`

Autrement dit, un “plugin tiers” signifie actuellement :

- un plugin développé en dehors du cœur applicatif ;
- puis intégré au dépôt, ou copié dans le dossier `app/plugins/builtin/`.

### Étape 1 — Créer la structure minimale

Exemple :

```text
app/plugins/builtin/my-company-plugin/
├── index.ts
├── my-company.plugin.ts
├── components/
│   ├── MyCompanyPage.vue
│   └── index.ts
└── composables/
    └── index.ts
```

### Étape 2 — Déclarer le plugin

Dans `my-company.plugin.ts`, exportez un objet `PluginMeta`.

Exemple minimal réaliste :

```ts
import type { PluginMeta } from "~/types/plugin"
import { MyCompanyPage } from "./components"

export const MyCompanyPlugin: PluginMeta = {
  id: "my-company-plugin",
  name: "My Company Plugin",
  version: "1.0.0",
  description: "Ajoute une page plugin et des hooks de suivi.",
  author: "My Company",
  pluginUrl: "https://example.com/my-company-plugin",

  menuItems: [
    {
      id: "my-company-link",
      label: "My Company",
      url: "/plugins/my-company",
      priority: 6,
      requiresAuth: true,
    },
  ],

  pluginPages: [
    {
      id: "my-company-page",
      title: "My Company",
      path: "my-company",
      component: "MyCompanyPage",
      componentDef: MyCompanyPage,
      requiresAuth: true,
      priority: 6,
    },
  ],

  hooks: {
    "app:init": (ctx) => {
      console.info("[MyCompanyPlugin] init", ctx)
    },
    "page:plugin": (ctx) => {
      console.info("[MyCompanyPlugin] plugin page viewed", ctx.pluginPagePath)
    },
    "user:login": (ctx) => {
      console.info("[MyCompanyPlugin] user login", ctx.email)
    },
  },
}
```

### Étape 3 — Exporter les composants du plugin

Exemple pour `components/index.ts` :

```ts
export { default as MyCompanyPage } from "./MyCompanyPage.vue"
```

Exemple minimal pour `components/MyCompanyPage.vue` :

```vue
<template>
  <section class="bg-white rounded-lg shadow p-6">
    <h2 class="text-xl font-semibold mb-2">My Company Plugin</h2>
    <p class="text-gray-600">Le plugin tiers est chargé correctement.</p>
  </section>
</template>
```

### Étape 4 — Ajouter des hooks utiles

Le plus simple est de commencer par des hooks purement observables :

- `app:init`
- `page:plugin`
- `user:login`
- `page:torrent-detail`
- `torrent:before-download`

Cela permet de valider rapidement que le plugin est bien branché sans modifier trop d’éléments UI.

### Étape 5 — Ajouter de l’UI progressivement

Ordre recommandé :

1. `menuItems`
2. `pluginPages`
3. `footerBlocks`
4. `sidebarBlocks`
5. `topbarIcons`

Pour les blocs visuels, fournissez de préférence `componentDef` en plus de `component`.

### Étape 6 — Tester le chargement

Checklist pratique :

1. lancer l’application en développement ;
2. ouvrir le navigateur et vérifier la console ;
3. confirmer que le log `app:init` apparaît ;
4. vérifier que l’entrée de menu est visible ;
5. ouvrir `/plugins/my-company` ;
6. confirmer l’exécution du hook `page:plugin` ;
7. activer/désactiver le plugin depuis l’admin si nécessaire.

### Étape 7 — Gérer les cas réels

Pour un plugin tiers destiné à être partagé, prévoir au minimum :

- des IDs préfixés par le nom du plugin ;
- des logs explicites en développement ;
- des hooks rapides et sûrs ;
- des garde-fous sur l’accès (`requiresAuth`, `adminOnly`) ;
- une documentation d’installation indiquant clairement que le plugin doit être copié dans `app/plugins/builtin/`.

### Limitation importante

Le système actuel ne fournit pas encore :

- de chargement dynamique de plugins externes depuis `node_modules` ;
- de manifest distant ;
- de sandbox d’exécution ;
- de distribution officielle de plugins tiers.

Donc, aujourd’hui, la stratégie correcte pour un plugin tiers est :

1. développer le plugin dans son propre dépôt si besoin ;
2. versionner son code ;
3. l’intégrer dans BittyTorrent sous `app/plugins/builtin/`.

---

## 16. Résumé opérationnel

Pour étendre BittyTorrent aujourd’hui :

- ajoutez un fichier `*.plugin.ts` sous `app/plugins/builtin/`
- déclarez un `PluginMeta`
- utilisez `hooks` pour vous brancher sur les événements du cycle de vie
- utilisez `menuItems`, `pluginPages`, `sidebarBlocks`, `footerBlocks` et `topbarIcons` pour injecter de l’UI
- persistez l’activation via l’admin et la collection `plugin_states`

Le système est simple, lisible et déjà exploitable, avec deux limites principales à garder en tête : priorité fixe des hooks déclaratifs et absence actuelle d’isolation des erreurs.
