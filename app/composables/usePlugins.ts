import type {
  PluginMeta,
  RegisteredPlugin,
  PluginAdminPage,
  PluginSidebarBlock,
  PluginFooterBlock,
  PluginMenuItem,
  PluginTopbarIcon,
  PluginPage,
} from "../../types/plugin"
import type { HookName } from "../../types/plugin"
import { useHooks } from "./useHooks"

// Singleton partagé côté client
const _plugins: RegisteredPlugin[] = reactive([])

export const usePlugins = () => {
  const { addHook, removePluginHooks } = useHooks()

  /**
   * Enregistre un plugin et attache automatiquement ses hooks.
   */
  const registerPlugin = (meta: PluginMeta, active = true): void => {
    if (_plugins.find((p) => p.id === meta.id)) {
      console.warn(`[Plugins] Plugin "${meta.id}" already registered.`)
      return
    }

    const plugin: RegisteredPlugin = {
      ...meta,
      active,
      installedAt: new Date().toISOString(),
    }

    _plugins.push(plugin)

    if (active && meta.hooks) {
      for (const [hookName, callback] of Object.entries(meta.hooks)) {
        if (callback) {
          addHook(hookName as HookName, callback, 5, meta.id)
        }
      }
    }

    console.info(
      `[Plugins] "${meta.name}" v${meta.version} ${active ? "activé" : "désactivé"}.`,
    )
  }

  /**
   * Active un plugin et re-branche ses hooks.
   */
  const activatePlugin = (id: string): void => {
    const plugin = _plugins.find((p) => p.id === id)
    if (!plugin) return
    plugin.active = true
    if (plugin.hooks) {
      for (const [hookName, callback] of Object.entries(plugin.hooks)) {
        if (callback) {
          addHook(hookName as HookName, callback, 5, id)
        }
      }
    }
  }

  /**
   * Désactive un plugin et retire ses hooks.
   */
  const deactivatePlugin = (id: string): void => {
    const plugin = _plugins.find((p) => p.id === id)
    if (!plugin) return
    plugin.active = false
    removePluginHooks(id)
  }

  /**
   * Toggle l'état actif/inactif d'un plugin.
   */
  const togglePlugin = (id: string): void => {
    const plugin = _plugins.find((p) => p.id === id)
    if (!plugin) return
    if (plugin.active) {
      deactivatePlugin(id)
    } else {
      activatePlugin(id)
    }
  }

  const getPlugin = (id: string): RegisteredPlugin | undefined => {
    return _plugins.find((p) => p.id === id)
  }

  const getAllPlugins = (): RegisteredPlugin[] => _plugins

  const getActivePlugins = (): RegisteredPlugin[] =>
    _plugins.filter((p) => p.active)

  /**
   * Collecte et fusionne tous les adminPages des plugins actifs.
   */
  const getAdminPages = (): PluginAdminPage[] => {
    return getActivePlugins().flatMap((p) => p.adminPages ?? [])
  }

  /**
   * Collecte et fusionne tous les blocs sidebar des plugins actifs,
   * triés par priorité.
   */
  const getSidebarBlocks = (): PluginSidebarBlock[] => {
    return getActivePlugins()
      .flatMap((p) => p.sidebarBlocks ?? [])
      .sort((a, b) => (a.priority ?? 5) - (b.priority ?? 5))
  }

  /**
   * Collecte et fusionne tous les blocs footer des plugins actifs,
   * triés par priorité.
   */
  const getFooterBlocks = (): PluginFooterBlock[] => {
    return getActivePlugins()
      .flatMap((p) => p.footerBlocks ?? [])
      .sort((a, b) => (a.priority ?? 5) - (b.priority ?? 5))
  }

  /**
   * Collecte et fusionne tous les menuItems des plugins actifs,
   * triés par priorité.
   */
  const getMenuItems = (): PluginMenuItem[] => {
    return getActivePlugins()
      .flatMap((p) => p.menuItems ?? [])
      .sort((a, b) => (a.priority ?? 5) - (b.priority ?? 5))
  }

  /**
   * Collecte et fusionne toutes les icônes topbar des plugins actifs,
   * triées par priorité.
   */
  const getTopbarIcons = (): PluginTopbarIcon[] => {
    return getActivePlugins()
      .flatMap((p) => p.topbarIcons ?? [])
      .sort((a, b) => (a.priority ?? 5) - (b.priority ?? 5))
  }

  /**
   * Collecte et fusionne toutes les pages plugins actives,
   * triées par priorité.
   */
  const getPluginPages = (): PluginPage[] => {
    return getActivePlugins()
      .flatMap((p) => p.pluginPages ?? [])
      .sort((a, b) => (a.priority ?? 5) - (b.priority ?? 5))
  }

  return {
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
    plugins: _plugins,
  }
}
