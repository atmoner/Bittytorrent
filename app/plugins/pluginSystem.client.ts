import { usePlugins } from "~/composables/usePlugins"
import { useHooks } from "~/composables/useHooks"
import type { PluginMeta } from "../../types/plugin"

const isPluginMeta = (value: unknown): value is PluginMeta => {
  if (!value || typeof value !== "object") return false
  const plugin = value as Record<string, unknown>
  return (
    typeof plugin.id === "string" &&
    typeof plugin.name === "string" &&
    typeof plugin.version === "string" &&
    typeof plugin.description === "string" &&
    typeof plugin.author === "string"
  )
}

const loadBuiltinPlugins = (): PluginMeta[] => {
  const modules = import.meta.glob("./builtin/**/*.plugin.ts", { eager: true })

  return Object.entries(modules)
    .sort(([a], [b]) => a.localeCompare(b))
    .flatMap(([, module]) => {
      const candidates = Object.values(module as Record<string, unknown>)
      return candidates.filter(isPluginMeta)
    })
}

/**
 * Plugin Nuxt côté client : initialise le système de plugins.
 * Charge les états depuis le serveur et enregistre les plugins built-in.
 */
export default defineNuxtPlugin(async () => {
  const { registerPlugin, activatePlugin, deactivatePlugin } = usePlugins()
  const { executeHook } = useHooks()

  // --- Plugins built-in ---
  for (const plugin of loadBuiltinPlugins()) {
    registerPlugin(plugin, false)
  }

  // --- Synchronisation des états depuis le serveur (DB) ---
  try {
    const states =
      await $fetch<Array<{ id: string; active: boolean }>>("/api/plugins")
    for (const state of states) {
      if (state.active) {
        activatePlugin(state.id)
      } else {
        deactivatePlugin(state.id)
      }
    }
  } catch (e) {
    console.warn(
      "[Plugins] Impossible de charger les états depuis le serveur.",
      e,
    )
  }

  // --- Hook global d'initialisation ---
  await executeHook("app:init")

  return {
    provide: {
      hooks: useHooks(),
      plugins: usePlugins(),
    },
  }
})
