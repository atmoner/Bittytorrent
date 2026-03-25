import type {
  HookName,
  HookCallback,
  HookEntry,
  HookPriority,
  HookContext,
} from "../../types/plugin"

type HookStore = Map<HookName, HookEntry[]>

// Singleton partagé côté client
const _hooks: HookStore = new Map()

export const useHooks = () => {
  const buildHookContext = <T = unknown>(
    name: HookName,
    context?: T,
  ): HookContext => {
    const base: HookContext = {
      hook: name,
      timestamp: new Date().toISOString(),
      path: process.client ? window.location.pathname : undefined,
    }

    if (typeof context === "undefined") {
      return base
    }

    if (
      context !== null &&
      typeof context === "object" &&
      !Array.isArray(context)
    ) {
      return {
        ...base,
        ...(context as Record<string, unknown>),
      }
    }

    return {
      ...base,
      payload: context as unknown,
    }
  }

  /**
   * Enregistre un callback sur un hook donné.
   * Les callbacks sont triés par priorité croissante (1 = exécuté en premier).
   */
  const addHook = <T = unknown>(
    name: HookName,
    callback: HookCallback<T>,
    priority: HookPriority = 5,
    pluginId?: string,
  ): void => {
    if (!_hooks.has(name)) {
      _hooks.set(name, [])
    }
    const entries = _hooks.get(name)!
    entries.push({
      callback: callback as HookCallback,
      priority,
      pluginId,
    })
    entries.sort((a, b) => a.priority - b.priority)
  }

  /**
   * Supprime un callback précis d'un hook.
   */
  const removeHook = (name: HookName, callback: HookCallback): void => {
    const entries = _hooks.get(name)
    if (!entries) return
    const index = entries.findIndex((e) => e.callback === callback)
    if (index !== -1) entries.splice(index, 1)
  }

  /**
   * Supprime tous les hooks enregistrés par un plugin donné.
   */
  const removePluginHooks = (pluginId: string): void => {
    for (const [name, entries] of _hooks.entries()) {
      const filtered = entries.filter((e) => e.pluginId !== pluginId)
      _hooks.set(name, filtered)
    }
  }

  /**
   * Exécute tous les callbacks enregistrés sur un hook,
   * dans l'ordre de priorité, en attendant les Promises.
   */
  const executeHook = async <T = unknown>(
    name: HookName,
    context?: T,
  ): Promise<void> => {
    const entries = _hooks.get(name)
    if (!entries || entries.length === 0) return
    const normalizedContext = buildHookContext(name, context)
    for (const entry of entries) {
      await entry.callback(normalizedContext)
    }
  }

  /**
   * Vérifie si un hook a au moins un callback enregistré.
   */
  const hookExists = (name: HookName): boolean => {
    const entries = _hooks.get(name)
    return !!entries && entries.length > 0
  }

  /**
   * Retourne tous les callbacks d'un hook (triés par priorité).
   */
  const getHookEntries = (name: HookName): HookEntry[] => {
    return _hooks.get(name) ?? []
  }

  /**
   * Retourne la liste de tous les hooks enregistrés avec leur nombre de callbacks.
   */
  const getHooksSummary = (): { name: HookName; count: number }[] => {
    const result: { name: HookName; count: number }[] = []
    for (const [name, entries] of _hooks.entries()) {
      result.push({ name, count: entries.length })
    }
    return result
  }

  return {
    addHook,
    removeHook,
    removePluginHooks,
    buildHookContext,
    executeHook,
    hookExists,
    getHookEntries,
    getHooksSummary,
  }
}
