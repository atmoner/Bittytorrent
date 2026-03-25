# BittyTorrent

<img src="https://i.imgur.com/7tkb4xQ.png" width="45%"></img> <img src="https://i.imgur.com/nzBERFc.png" width="45%"></img> <img src="https://i.imgur.com/7ky0ySu.png" width="45%"></img> <img src="https://i.imgur.com/W1MIwuI.png" width="45%"></img>

## Description

A modern BitTorrent tracker built with Nuxt 4. The application lets you manage torrents, scrape real-time statistics from external trackers, and includes a user authentication system.

**Main features:**

- Torrent upload and management
- Automatic scraping of stats from external trackers
- Full administration interface
- User authentication and management
- MongoDB database

## Installation

### Prerequisites

- Node.js 18+
- MongoDB (local or remote)

### Installation steps

1. **Install dependencies**

   ```bash
   npm install
   ```

2. **Start the development server**

   ```bash
   npm run dev
   ```

3. **Initial setup**

   Open `http://localhost:3000` in your browser. The application will redirect you to `/install` to configure:
   - MongoDB connection
   - Site information
   - Initial administrator account

4. **Usage**

   Once installation is complete:
   - Sign in with the created admin account
   - Access the admin panel at `/admin`
   - Start uploading and managing your torrents

## Plugin system

BittyTorrent provides a client-side plugin system based on hooks.

### Full documentation

- French: [PLUGINS.md](PLUGINS.md)
- English: [PLUGINS.en.md](PLUGINS.en.md)

To avoid duplication across documents, plugin documentation is centralized in:

- [PLUGINS.md](PLUGINS.md) (full reference in French)
- [PLUGINS.en.md](PLUGINS.en.md) (complete English reference)

You will find:

- the full catalog of hooks and contexts
- the system architecture and execution lifecycle
- the `useHooks()` / `usePlugins()` APIs
- a plugin creation guide (including third-party plugin use cases)
