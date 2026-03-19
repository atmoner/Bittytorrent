export type HookName =
  | 'app:init'
  | 'page:home'
  | 'page:torrent-list'
  | 'page:torrent-detail'
  | 'page:upload'
  | 'sidebar:before'
  | 'sidebar:after'
  | 'menu:main'
  | 'menu:user'
  | 'torrent:before-upload'
  | 'torrent:after-upload'
  | 'torrent:before-download'
  | 'admin:page'
  | 'admin:menu'
  | 'admin:settings'
  | 'user:login'
  | 'user:register'
  | 'footer:content'

export type HookPriority = 1 | 2 | 3 | 4 | 5 | 6 | 7 | 8 | 9 | 10

// eslint-disable-next-line @typescript-eslint/no-explicit-any
export type HookCallback<T = any> = (context: T) => void | Promise<void>

export interface HookEntry<T = unknown> {
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
}

export interface PluginSidebarBlock {
  id: string
  title: string
  /** Nom du composant Vue à charger dynamiquement */
  component: string
  priority?: HookPriority
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
}

export interface RegisteredPlugin extends PluginMeta {
  active: boolean
  installedAt?: string
}

export interface PluginState {
  id: string
  active: boolean
}
