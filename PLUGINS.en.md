# PLUGINS.en.md

Reference documentation for the BittyTorrent plugin and hook system.

This document describes the current implementation as it exists in the codebase: plugin loading, hook execution, UI extension points, available APIs, and known limitations.

---

## 1. Overview

The BittyTorrent plugin system is a **client-side** system based on:

- declarative plugins typed as `PluginMeta`
- a global active/inactive plugin registry
- a global hook registry
- UI extension points: menu, footer, sidebar, topbar, plugin pages
- persistent plugin state stored in `plugin_states`

### Startup flow

When the application starts:

1. all `app/plugins/builtin/**/*.plugin.ts` files are loaded automatically;
2. each valid `PluginMeta` export is registered;
3. hooks declared in `meta.hooks` are automatically attached if the plugin is active;
4. plugin activation state is synchronized from `GET /api/plugins`;
5. the global `app:init` hook is executed.

Important consequence:

- a newly discovered plugin is **active by default**;
- if the database contains a saved state, that state overrides the initial registration state.

---

## 2. File architecture

Recommended current structure for a built-in plugin:

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

Examples already present in the repository:

- `app/plugins/builtin/hello/hello.plugin.ts`
- `app/plugins/builtin/donate/donate.plugin.ts`

Auto-loading does not depend on the folder name, only on this pattern:

- `app/plugins/builtin/**/*.plugin.ts`

---

## 3. Core types

## 3.1 `HookName`

The hook list is **closed** and typed in `types/plugin.d.ts`.

Available hooks:

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

To add new hooks, you must extend `HookName` and create the matching `executeHook()` calls.

## 3.2 `HookPriority`

Allowed priorities: `1` to `10`.

Current convention:

- `1` = executed first
- `10` = executed last

## 3.3 `HookContext`

Each callback receives at least:

```ts
{
  hook: HookName
  timestamp: string
  path?: string
}
```

Then the business-specific context is merged in.

Normalization rules:

- if the value passed to `executeHook()` is an object, its properties are merged into the base context;
- otherwise, the value is stored in `payload`.

Example:

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

A plugin declares its metadata and extension points through a `PluginMeta` object.

Main fields:

- `id`: unique plugin identifier
- `name`: display name
- `version`: version
- `description`: short description
- `author`: author name
- `authorUrl?`
- `pluginUrl?`
- `hooks?`: hook callbacks automatically registered on plugin registration
- `adminPages?`: admin pages declared by the plugin
- `menuItems?`: menu entries
- `sidebarBlocks?`: sidebar blocks
- `footerBlocks?`: footer blocks
- `topbarIcons?`: topbar icons
- `pluginPages?`: pages served under `/plugins/*`

---

## 4. Hook registration and execution

## 4.1 Automatic registration

When `registerPlugin(meta, true)` is called:

- the plugin is added to the global registry;
- `installedAt` is set;
- every `meta.hooks` entry is registered through `addHook()`.

### Important note about priority

Hooks declared in `PluginMeta.hooks` are currently registered with a fixed priority of `5`.

This means:

- UI elements can define their own priority (`menuItems`, `footerBlocks`, and so on);
- but declarative `meta.hooks` entries cannot directly do so.

If a plugin needs a different priority, it must call `useHooks().addHook()` manually from client-side code.

## 4.2 Execution order

Hooks are executed:

1. in ascending priority order;
2. sequentially;
3. with `await` on every callback.

Consequences:

- a slow hook blocks the following ones;
- an async callback is fully awaited before the next one runs.

## 4.3 Error handling

The current engine does not isolate callback errors.

If a callback throws during `executeHook()`, hook execution stops and the following callbacks are not executed unless the caller catches the error.

## 4.4 Plugin deactivation

When a plugin is deactivated:

- its `active` state becomes `false`;
- all its hooks are removed through `removePluginHooks(pluginId)`.

Its UI extension points also disappear because they are always recomputed from `getActivePlugins()`.

---

## 5. Full hook catalog

The following table documents the current behavior observed in the code.

| Hook                      | Trigger                                                                | Current specific context                        | Typical usage                             |
| ------------------------- | ---------------------------------------------------------------------- | ----------------------------------------------- | ----------------------------------------- |
| `app:init`                | After built-in plugins are loaded and server states are synchronized   | no extra field                                  | global plugin initialization              |
| `page:plugin`             | When a `/plugins/*` page opens and when the active plugin page changes | `page`, `pluginPagePath`                        | analytics, plugin page data loading       |
| `page:home`               | On home page mount                                                     | `page: "home"`                                  | home customization, tracking              |
| `page:login`              | On login page mount                                                    | `page: "login"`                                 | metrics, UI preparation                   |
| `page:register`           | On register page mount                                                 | `page: "register"`, `registrationEnabled`       | registration customization                |
| `page:account`            | On account page mount                                                  | `page: "account"`, `loggedIn`                   | account enrichment                        |
| `page:stats`              | On stats page mount                                                    | `page: "stats"`                                 | advanced statistics                       |
| `page:install`            | On install page mount                                                  | `page: "install"`                               | installation assistance                   |
| `page:logout`             | When the logout page opens                                             | `page: "logout"`                                | plugin-side session cleanup               |
| `page:torrent-list`       | On torrent list page mount                                             | `page: "torrents"`, `categoryId`                | filters, tracking, list enrichment        |
| `page:torrent-detail`     | On torrent detail page mount                                           | `page: "torrent-detail"`, `infoHash`, `torrent` | torrent detail enrichment                 |
| `page:upload`             | On upload page mount                                                   | `page: "upload"`                                | upload preparation                        |
| `sidebar:before`          | On dynamic sidebar mount                                               | `blocks`                                        | instrumentation before sidebar rendering  |
| `sidebar:after`           | Immediately after `sidebar:before` during the same mount               | `blocks`                                        | instrumentation after sidebar preparation |
| `menu:main`               | On header mount                                                        | `loggedIn`, `isAdmin`                           | main menu customization                   |
| `menu:user`               | On header mount                                                        | `loggedIn`, `isAdmin`                           | user menu customization                   |
| `torrent:before-upload`   | Right before a torrent file upload                                     | `fileName`, `categoryId`                        | validation, logs, preparation             |
| `torrent:after-upload`    | Right after a successful upload                                        | `torrent`                                       | indexing, logs, notifications             |
| `torrent:before-download` | Right before a torrent download                                        | `infoHash`, `torrent`                           | audit, tracking, control                  |
| `admin:page`              | On instrumented admin page mount                                       | `page`, sometimes `torrentId`                   | admin screen instrumentation              |
| `admin:menu`              | On header mount when the user is admin                                 | `page: "header-menu"`                           | admin menu logic                          |
| `admin:settings`          | On admin config page mount                                             | `page: "admin-config"`, `form`                  | settings extension                        |
| `user:login`              | After a successful login                                               | `email`                                         | audit, welcome flow, tracking             |
| `user:register`           | After a successful registration                                        | `username`, `email`                             | onboarding, logs, integrations            |
| `footer:content`          | On footer mount                                                        | `page: "footer"`, `blocks`                      | instrumentation and extra footer content  |

---

## 6. Where each hook is triggered

### Public page hooks

- `page:home`: home page
- `page:login`: login page
- `page:register`: registration page
- `page:account`: account page
- `page:stats`: stats page
- `page:install`: install page
- `page:logout`: logout page
- `page:torrent-list`: torrent list page
- `page:torrent-detail`: torrent detail page
- `page:upload`: upload page
- `page:plugin`: dynamic `/plugins/*` page

### Navigation and layout hooks

- `menu:main`: desktop header
- `menu:user`: desktop header
- `sidebar:before` / `sidebar:after`: dynamic sidebar
- `footer:content`: footer
- `admin:menu`: header when admin

### Business hooks

- `user:login`: after successful login
- `user:register`: after successful registration
- `torrent:before-upload`: before upload
- `torrent:after-upload`: after successful upload
- `torrent:before-download`: before download

### Admin hooks

`admin:page` is triggered on the following instrumented admin pages:

- admin dashboard
- categories
- configuration
- plugins
- peer sessions
- torrent list
- admin torrent detail
- users

`admin:settings` is only triggered on the admin configuration page.

---

## 7. UI extension points

## 7.1 `menuItems`

Lets a plugin inject links into the plugin navigation.

Structure:

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

Current behavior:

- sorted by ascending priority;
- displayed in the desktop header;
- displayed in the mobile navigation;
- if `external: true`, opened as an external link;
- if `requiresAuth: true`, only visible to logged-in users.

## 7.2 `topbarIcons`

Lets a plugin inject actions into the desktop top bar.

Structure:

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

Current behavior:

- sorted by ascending priority;
- `toggle-sidebar` calls `toggleSidebar()`;
- `open-url` opens `url` in a new tab.

Note: the `icon` field exists, but the current header rendering mainly exposes `title`.

## 7.3 `sidebarBlocks`

Lets a plugin inject blocks into the sidebar.

Structure:

```ts
{
  id: string
  title: string
  component: string
  componentDef?: Component
  priority?: 1..10
}
```

Current behavior:

- sorted by ascending priority;
- rendered in `SidebarDynamic.vue`;
- in the current implementation, you should **provide `componentDef`** for the block to render correctly;
- otherwise, a “component not found” fallback message is shown.

## 7.4 `footerBlocks`

Same idea as `sidebarBlocks`, but injected into the footer.

Same recommendation: in the current code, providing `componentDef` is the reliable approach.

## 7.5 `pluginPages`

Lets a plugin add pages under `/plugins/*`.

Structure:

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

Example:

- `path: "hello"` becomes `/plugins/hello`

Current behavior:

- all active plugin pages are merged and sorted by priority;
- page resolution is based on normalized `path` matching;
- `requiresAuth` blocks access for logged-out users;
- `adminOnly` blocks access for non-admin users.

## 7.6 `adminPages`

The type exists and `usePlugins()` exposes `getAdminPages()`, but there is currently no UI rendering wired for these pages.

In other words:

- the TypeScript contract exists;
- aggregation exists;
- visual integration is not implemented yet.

---

## 8. APIs available to plugins

## 8.1 `useHooks()`

Current API:

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

Adds a callback to a hook.

Typical uses:

- custom priority;
- dynamic registration;
- temporary instrumentation.

### `removeHook(name, callback)`

Removes one specific callback.

### `removePluginHooks(pluginId)`

Removes all hooks associated with one plugin.

### `buildHookContext(name, context?)`

Builds the normalized hook context.

### `executeHook(name, context?)`

Executes all callbacks for a hook, in order.

### `hookExists(name)`

Returns whether at least one callback is registered for that hook.

### `getHookEntries(name)`

Returns the registered callbacks for one hook.

### `getHooksSummary()`

Returns a summary of the hook registry, useful for debugging or admin tooling.

## 8.2 `usePlugins()`

Current API:

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

Main uses:

- register a plugin dynamically;
- activate/deactivate a plugin;
- retrieve active UI extensions;
- inspect the global plugin state.

---

## 9. Persistence and administration

Plugin state is persisted in the MongoDB `plugin_states` collection.

### Endpoints

#### Public

- `GET /api/plugins`
  - returns active/inactive states
  - used during client startup

#### Admin

- `GET /api/admin/plugins`
  - returns the full plugin state list for the admin UI
- `POST /api/admin/plugins/:id`
  - body: `{ action: "activate" | "deactivate" }`
  - updates the plugin state in the database

### Current business rule

If the collection is empty:

- no explicit state is loaded;
- discovered plugins therefore remain active by default.

---

## 10. Minimal plugin example

```ts
import type { PluginMeta } from "~/types/plugin"
import { HelloPluginPage } from "./components"

export const HelloPlugin: PluginMeta = {
  id: "hello-plugin",
  name: "Hello Plugin",
  version: "1.0.0",
  description: "Adds a plugin page and a few hooks.",
  author: "YourName",

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

## 11. Advanced example with custom priority

If you need a hook priority other than `5`, use `addHook()` directly.

```ts
const { addHook } = useHooks()

addHook(
  "page:home",
  async (ctx) => {
    console.log("I run before default hooks", ctx)
  },
  1,
  "my-plugin",
)
```

---

## 12. Best practices

- use unique `id` values everywhere: plugin, menu, block, page, icon;
- prefix identifiers with the plugin name;
- keep hooks fast and non-blocking;
- wrap critical callback logic in safe error handling;
- use `componentDef` for injected visual blocks;
- use `requiresAuth` and `adminOnly` when needed;
- log clearly in development: `"[PluginName] ..."`;
- avoid heavy side effects in `app:init`.

---

## 13. Known limitations of the current system

### 13.1 Mostly client-side system

The hook engine is initialized inside a Nuxt client plugin. `PluginMeta.hooks` callbacks are therefore designed for client runtime.

### 13.2 No error isolation

One callback error can stop the entire hook chain.

### 13.3 Sequential execution

All callbacks run one after another with `await`.

### 13.4 Fixed priority for `meta.hooks`

Hooks declared in `PluginMeta.hooks` are registered with priority `5`.

### 13.5 `adminPages` is not rendered in the UI

The contract exists, but the visual integration is not wired yet.

### 13.6 `componentDef` is strongly recommended

For sidebar/footer blocks in particular, omitting `componentDef` currently leads to a “component not found” fallback.

---

## 14. Plugin creation checklist

1. create a folder under `app/plugins/builtin/<name>/`
2. create a `<name>.plugin.ts` file
3. export a `PluginMeta` object
4. add components in `components/` if needed
5. provide `componentDef` for injected components
6. restart the application if needed
7. verify plugin activation in the admin UI
8. check hook logs in the browser

---

## 15. How to build a third-party plugin

In the current state of the project, a third-party plugin is not loaded from an external npm package or a plugin registry.

To be recognized by BittyTorrent, the plugin must currently be **present in the source tree** under:

- `app/plugins/builtin/<plugin-name>/`

In practice, a “third-party plugin” currently means:

- a plugin developed outside the application core;
- then integrated into the repository, or copied into `app/plugins/builtin/`.

### Step 1 — Create the minimum structure

Example:

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

### Step 2 — Declare the plugin

In `my-company.plugin.ts`, export a `PluginMeta` object.

Realistic minimal example:

```ts
import type { PluginMeta } from "~/types/plugin"
import { MyCompanyPage } from "./components"

export const MyCompanyPlugin: PluginMeta = {
  id: "my-company-plugin",
  name: "My Company Plugin",
  version: "1.0.0",
  description: "Adds a plugin page and tracking hooks.",
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

### Step 3 — Export the plugin components

Example `components/index.ts`:

```ts
export { default as MyCompanyPage } from "./MyCompanyPage.vue"
```

Minimal `components/MyCompanyPage.vue` example:

```vue
<template>
  <section class="bg-white rounded-lg shadow p-6">
    <h2 class="text-xl font-semibold mb-2">My Company Plugin</h2>
    <p class="text-gray-600">The third-party plugin is loaded correctly.</p>
  </section>
</template>
```

### Step 4 — Start with useful hooks

The easiest way to validate the plugin is to begin with observable hooks:

- `app:init`
- `page:plugin`
- `user:login`
- `page:torrent-detail`
- `torrent:before-download`

This makes it easy to confirm that the plugin is connected before adding too much UI.

### Step 5 — Add UI gradually

Recommended order:

1. `menuItems`
2. `pluginPages`
3. `footerBlocks`
4. `sidebarBlocks`
5. `topbarIcons`

For visual blocks, provide `componentDef` in addition to `component`.

### Step 6 — Test the loading flow

Practical checklist:

1. run the app in development mode;
2. open the browser and inspect the console;
3. confirm that the `app:init` log appears;
4. verify that the menu entry is visible;
5. open `/plugins/my-company`;
6. confirm that the `page:plugin` hook runs;
7. activate/deactivate the plugin from the admin UI if needed.

### Step 7 — Handle real-world cases

For a shareable third-party plugin, you should at least provide:

- IDs prefixed with the plugin name;
- explicit development logs;
- safe and fast hooks;
- access guards such as `requiresAuth` and `adminOnly`;
- installation instructions explaining that the plugin must be copied into `app/plugins/builtin/`.

### Important limitation

The current system does not yet provide:

- dynamic loading of external plugins from `node_modules`;
- a remote manifest;
- execution sandboxing;
- an official third-party plugin distribution flow.

So, the correct strategy today is:

1. develop the plugin in its own repository if needed;
2. version its source code;
3. integrate it into BittyTorrent under `app/plugins/builtin/`.

---

## 16. Operational summary

To extend BittyTorrent today:

- add a `*.plugin.ts` file under `app/plugins/builtin/`
- declare a `PluginMeta`
- use `hooks` to attach to lifecycle and business events
- use `menuItems`, `pluginPages`, `sidebarBlocks`, `footerBlocks`, and `topbarIcons` to inject UI
- persist activation through the admin UI and the `plugin_states` collection

The system is simple, readable, and already usable, with two main constraints to keep in mind: fixed priority for declarative hooks and no error isolation during hook execution.
