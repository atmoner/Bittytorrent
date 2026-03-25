<template>
  <div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-8">
      <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold">Thème du site</h1>
        <NuxtLink
          to="/admin"
          class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700"
        >
          Retour à l'admin
        </NuxtLink>
      </div>

      <div v-if="loading" class="text-center py-8">
        <p>Chargement...</p>
      </div>

      <div
        v-else-if="error"
        class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"
      >
        {{ error }}
      </div>

      <div
        v-if="successMessage"
        class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4"
      >
        {{ successMessage }}
      </div>

      <div v-if="!loading && !error" class="bg-white rounded-lg shadow p-6">
        <p class="text-sm text-gray-600 mb-6">
          Les thèmes sont chargés depuis le dossier
          <span class="font-semibold">public/themes</span>. Ajoutez un fichier
          <span class="font-semibold">.css</span> pour le rendre disponible.
        </p>

        <form @submit.prevent="saveTheme" class="space-y-4">
          <label
            class="flex items-start gap-3 p-3 border rounded-lg cursor-pointer"
          >
            <input
              v-model="selectedTheme"
              type="radio"
              value=""
              name="theme"
              class="mt-1"
            />
            <div>
              <p class="font-medium text-gray-900">Thème par défaut</p>
              <p class="text-sm text-gray-500">
                N'applique aucun CSS additionnel.
              </p>
            </div>
          </label>

          <label
            v-for="theme in themes"
            :key="theme"
            class="flex items-start gap-3 p-3 border rounded-lg cursor-pointer"
          >
            <input
              v-model="selectedTheme"
              type="radio"
              :value="theme"
              name="theme"
              class="mt-1"
            />
            <div>
              <p class="font-medium text-gray-900">{{ themeLabel(theme) }}</p>
              <p class="text-sm text-gray-500">Fichier: {{ theme }}</p>
            </div>
          </label>

          <p v-if="themes.length === 0" class="text-sm text-gray-500">
            Aucun thème trouvé dans public/themes.
          </p>

          <div class="flex justify-end pt-2">
            <button
              type="submit"
              :disabled="saving"
              class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50"
            >
              {{ saving ? "Enregistrement..." : "Enregistrer le thème" }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
  import type { Config } from "~/models"

  type SessionUser = {
    role?: "admin" | "user"
  }

  const session = useUserSession()
  const { executeHook } = useHooks()
  const { config } = useConfig()

  const loading = ref(true)
  const saving = ref(false)
  const error = ref("")
  const successMessage = ref("")

  const themes = ref<string[]>([])
  const selectedTheme = ref("")

  const sessionUser = computed<SessionUser | null>(
    () => (session.user.value as SessionUser | null) ?? null,
  )

  const isAdmin = computed(() => {
    return sessionUser.value?.role === "admin"
  })

  onMounted(async () => {
    if (!isAdmin.value) {
      navigateTo("/")
      return
    }

    await executeHook("admin:page", {
      page: "admin-themes",
    })

    await loadData()
  })

  async function loadData() {
    try {
      loading.value = true
      error.value = ""

      const [themesResponse, configResponse] = await Promise.all([
        $fetch<{ success: boolean; themes: string[] }>("/api/admin/themes"),
        $fetch<{ success: boolean; config: Config }>("/api/admin/config"),
      ])

      themes.value = themesResponse.themes || []
      selectedTheme.value = configResponse.config?.themeCssFile || ""
    } catch (e: any) {
      error.value =
        e.data?.statusMessage || "Erreur lors du chargement des thèmes"
    } finally {
      loading.value = false
    }
  }

  async function saveTheme() {
    try {
      saving.value = true
      error.value = ""
      successMessage.value = ""

      await $fetch("/api/admin/config", {
        method: "POST",
        body: {
          themeCssFile: selectedTheme.value,
        },
      })

      if (config.value) {
        config.value.themeCssFile = selectedTheme.value
      }

      successMessage.value = "Thème enregistré avec succès !"
      setTimeout(() => {
        successMessage.value = ""
      }, 3000)
    } catch (e: any) {
      error.value = e.data?.statusMessage || "Erreur lors de l'enregistrement"
    } finally {
      saving.value = false
    }
  }

  function themeLabel(fileName: string): string {
    return fileName
      .replace(/\.css$/i, "")
      .replace(/[-_]+/g, " ")
      .replace(/\b\w/g, (char) => char.toUpperCase())
  }
</script>
