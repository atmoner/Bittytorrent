import type { Config } from "~/models"

export const useConfig = () => {
  const config = useState<Partial<Config> | null>("siteConfig", () => null)
  const configLoading = useState("configLoading", () => true) // ⬅️ true par défaut
  const configError = useState<string | null>("configError", () => null)

  const fetchConfig = async () => {
    if (config.value) {
      configLoading.value = false
      return // Configuration déjà chargée
    }

    configLoading.value = true
    configError.value = null

    try {
      const data = await $fetch<{ success: boolean; config: Partial<Config> }>(
        "/api/config-public"
      )

      if (data.success && data.config) {
        config.value = data.config
      }
    } catch (e: any) {
      configError.value =
        e.message || "Erreur lors du chargement de la configuration"
      console.error("Erreur lors de la récupération de la configuration:", e)
    } finally {
      configLoading.value = false
    }
  }

  return {
    config,
    configLoading,
    configError,
    fetchConfig,
  }
}
