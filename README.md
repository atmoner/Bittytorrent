# BittyTorrent

## Description

Un tracker BitTorrent moderne construit avec Nuxt 4. L'application permet de gérer des torrents, effectuer du scraping en temps réel des trackers externes, et inclut un système d'authentification utilisateur.

**Fonctionnalités principales :**

- Upload et gestion de torrents
- Scraping automatique des stats depuis les trackers externes
- Interface d'administration complète
- Authentification et gestion d'utilisateurs
- Base de données MongoDB

## Installation

### Pré-requis

- Node.js 18+
- MongoDB (local ou distant)

### Étapes d'installation

1. **Installer les dépendances**

   ```bash
   npm install
   ```

2. **Lancer le serveur de développement**

   ```bash
   npm run dev
   ```

3. **Configuration initiale**

   Ouvrez `http://localhost:3000` dans votre navigateur. L'application vous redirigera vers `/install` pour configurer :
   - La connexion MongoDB
   - Les informations du site
   - Le compte administrateur initial

4. **Utilisation**

   Une fois l'installation terminée :
   - Connectez-vous avec le compte admin créé
   - Accédez au panel admin via `/admin`
   - Commencez à uploader et gérer vos torrents
