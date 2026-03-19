import { usePlugins } from '~/composables/usePlugins'
import { useHooks } from '~/composables/useHooks'
import { DonatePlugin } from '~/plugins/builtin/donate.plugin'

/**
 * Plugin Nuxt côté client : initialise le système de plugins.
 * Charge les états depuis le serveur et enregistre les plugins built-in.
 */
export default defineNuxtPlugin(async () => {
  const { registerPlugin, activatePlugin, deactivatePlugin } = usePlugins()
  const { executeHook } = useHooks()

  // --- Plugins built-in ---
  registerPlugin(DonatePlugin, true)

  // --- Synchronisation des états depuis le serveur (DB) ---
  try {
    const states = await $fetch<Array<{ id: string; active: boolean }>>('/api/plugins')
    for (const state of states) {
      if (state.active) {
        activatePlugin(state.id)
      } else {
        deactivatePlugin(state.id)
      }
    }
  } catch (e) {
    console.warn('[Plugins] Impossible de charger les états depuis le serveur.', e)
  }

  // --- Hook global d'initialisation ---
  await executeHook('app:init')

  return {
    provide: {
      hooks: useHooks(),
      plugins: usePlugins(),
    },
  }
})
