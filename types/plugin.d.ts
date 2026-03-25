export type HookName =
  | "app:init"
  | "page:plugin"
  | "page:home"
  | "page:login"
  | "page:register"
  | "page:account"
  | "page:account-profile"
  | "page:account-favorites"
  | "page:account-wallet"
  | "page:account-reviews"
  | "page:account-settings"
  | "page:stats"
  | "page:install"
  | "page:logout"
  | "page:torrent-list"
  | "page:torrent-detail"
  | "page:upload"
  | "sidebar:before"
  | "sidebar:after"
  | "menu:main"
  | "menu:user"
  | "torrent:before-upload"
  | "torrent:after-upload"
  | "torrent:before-download"
  | "admin:page"
  | "admin:menu"
  | "admin:settings"
  | "user:login"
  | "user:register"
  | "footer:content"

export type HookPriority = 1 | 2 | 3 | 4 | 5 | 6 | 7 | 8 | 9 | 10

export interface HookBaseContext {
  hook: HookName
  timestamp: string
  path?: string
}

export type HookContext<
  T extends Record<string, unknown> = Record<string, unknown>,
> = HookBaseContext & T

export type HookCallback<T = HookContext> = (context: T) => void | Promise<void>

export interface HookEntry<T = HookContext> {
  callback: HookCallback<T>
  priority: HookPriority
  pluginId?: string
}

export interface PluginAdminPage {
  id: string
  title: string
  icon?: string
  /** Nom du composant Vue à charger dynamiquement */
  component: string
}

export interface PluginMenuItem {
  id: string
  label: string
  url: string
  priority?: HookPriority
  external?: boolean
  /** Si true, l'item n'est visible que pour un utilisateur connecté */
  requiresAuth?: boolean
}

export type PluginTopbarAction = "toggle-sidebar" | "open-url"

export interface PluginTopbarIcon {
  id: string
  /** Nom de l'icône HeroIcons (ex: "heart", "bars-3") */
  icon: string
  title?: string
  priority?: HookPriority
  action: PluginTopbarAction
  /** URL cible si action === "open-url" */
  url?: string
}

export interface PluginSidebarBlock {
  id: string
  title: string
  /** Nom du composant Vue (pour affichage/debug) */
  component: string
  /** Définition directe du composant Vue (prioritaire sur component string) */
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  componentDef?: any
  priority?: HookPriority
}

export interface PluginFooterBlock {
  id: string
  title?: string
  /** Nom du composant Vue (pour affichage/debug) */
  component: string
  /** Définition directe du composant Vue (prioritaire sur component string) */
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  componentDef?: any
  priority?: HookPriority
}

export interface PluginPage {
  id: string
  title: string
  /** Chemin relatif sous /plugins (ex: "hello" => /plugins/hello) */
  path: string
  /** Nom du composant Vue (pour affichage/debug) */
  component: string
  /** Définition directe du composant Vue (prioritaire sur component string) */
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  componentDef?: any
  priority?: HookPriority
  /** Si true, la page nécessite une session connectée */
  requiresAuth?: boolean
  /** Si true, accès réservé aux administrateurs */
  adminOnly?: boolean
}

export interface PluginMeta {
  id: string
  name: string
  version: string
  description: string
  author: string
  authorUrl?: string
  pluginUrl?: string
  /** Hooks à enregistrer automatiquement lors du register */
  hooks?: Partial<Record<HookName, HookCallback>>
  /** Pages ajoutées dans l'administration */
  adminPages?: PluginAdminPage[]
  /** Items ajoutés dans les menus */
  menuItems?: PluginMenuItem[]
  /** Blocs ajoutés dans la sidebar */
  sidebarBlocks?: PluginSidebarBlock[]
  /** Blocs ajoutés dans le footer */
  footerBlocks?: PluginFooterBlock[]
  /** Icônes ajoutées dans le header (top bar) */
  topbarIcons?: PluginTopbarIcon[]
  /** Pages ajoutées par les plugins sous /plugins/* */
  pluginPages?: PluginPage[]
}

export interface RegisteredPlugin extends PluginMeta {
  active: boolean
  installedAt?: string
}

export interface PluginState {
  id: string
  active: boolean
}
