<template>
  <div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-8">
      <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold">Configuration du site</h1>
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

      <div v-if="!loading" class="bg-white rounded-lg shadow p-6">
        <form @submit.prevent="saveConfig" class="space-y-6">
          <div>
            <label
              for="siteName"
              class="block text-sm font-medium text-gray-700 mb-2"
            >
              Nom du site
            </label>
            <input
              id="siteName"
              v-model="form.siteName"
              type="text"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Nom de votre tracker"
            />
          </div>

          <div>
            <label
              for="siteDescription"
              class="block text-sm font-medium text-gray-700 mb-2"
            >
              Description du site
            </label>
            <textarea
              id="siteDescription"
              v-model="form.siteDescription"
              rows="4"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Description de votre tracker"
            ></textarea>
          </div>

          <div>
            <label
              for="contactEmail"
              class="block text-sm font-medium text-gray-700 mb-2"
            >
              Email de contact
            </label>
            <input
              id="contactEmail"
              v-model="form.contactEmail"
              type="email"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="contact@example.com"
            />
          </div>

          <div>
            <label
              for="maxUploadSize"
              class="block text-sm font-medium text-gray-700 mb-2"
            >
              Taille maximale d'upload (en octets)
            </label>
            <input
              id="maxUploadSize"
              v-model.number="form.maxUploadSize"
              type="number"
              required
              min="1"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
            <p class="mt-1 text-sm text-gray-500">
              Actuellement: {{ formatSize(form.maxUploadSize) }}
            </p>
          </div>

          <div class="flex items-center">
            <input
              id="allowRegistration"
              v-model="form.allowRegistration"
              type="checkbox"
              class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
            />
            <label
              for="allowRegistration"
              class="ml-2 block text-sm text-gray-900"
            >
              Autoriser les nouvelles inscriptions
            </label>
          </div>

          <div v-if="!form.allowRegistration">
            <label
              for="registrationClosedMessage"
              class="block text-sm font-medium text-gray-700 mb-2"
            >
              Message pour les inscriptions fermées
            </label>
            <textarea
              id="registrationClosedMessage"
              v-model="form.registrationClosedMessage"
              rows="3"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Message personnalisé affiché quand les inscriptions sont fermées"
            ></textarea>
            <p class="mt-1 text-sm text-gray-500">
              Laissez vide pour utiliser le message par défaut
            </p>
          </div>

          <div class="flex items-center">
            <input
              id="privateTracker"
              v-model="form.privateTracker"
              type="checkbox"
              class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
            />
            <label
              for="privateTracker"
              class="ml-2 block text-sm text-gray-900"
            >
              Tracker privé
            </label>
          </div>

          <div v-if="form.privateTracker" class="grid grid-cols-2 gap-4">
            <div
              class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow"
            >
              <label
                for="privateTrackerUrl"
                class="block text-sm font-medium text-gray-700 mb-2"
              >
                URL du tracker privé
              </label>
              <input
                id="privateTrackerUrl"
                v-model="form.privateTrackerUrl"
                type="url"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="https://tracker.example.com/announce"
              />
              <p class="mt-1 text-sm text-gray-500">
                URL d'annonce complète de votre tracker privé
              </p>
            </div>
            <div
              class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow"
            >
              <label
                for="privateTrackerRatio"
                class="block text-sm font-medium text-gray-700 mb-2"
              >
                Ratio min pour le téléchargement
              </label>
              <input
                id="privateTrackerRatio"
                v-model="form.privateTrackerRatio"
                type="number"
                step="0.1"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="1.0"
              />
              <p class="mt-1 text-sm text-gray-500">
                Ratio minimum requis pour le téléchargement sur le tracker privé
              </p>
            </div>
          </div>

          <div class="flex justify-end">
            <button
              type="submit"
              :disabled="saving"
              class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50"
            >
              {{
                saving ? "Enregistrement..." : "Enregistrer les modifications"
              }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
  import type { Config } from "~/models"

  const session = useUserSession()
  const { executeHook } = useHooks()

  const form = ref<Omit<Config, "_id" | "createdAt" | "updatedAt">>({
    siteName: "",
    siteDescription: "",
    contactEmail: "",
    maxUploadSize: 10485760,
    allowRegistration: true,
    registrationClosedMessage: "",
    privateTracker: false,
    privateTrackerUrl: "",
    privateTrackerRatio: 1.0,
  })

  const loading = ref(true)
  const saving = ref(false)
  const error = ref("")
  const successMessage = ref("")

  const isAdmin = computed(() => {
    return session.user.value?.role === "admin"
  })

  // Redirection si non admin
  onMounted(async () => {
    if (!isAdmin.value) {
      navigateTo("/")
      return
    }
    await executeHook("admin:page", {
      page: "admin-config",
    })
    await executeHook("admin:settings", {
      page: "admin-config",
      form: form.value,
    })
    await loadConfig()
  })

  async function loadConfig() {
    try {
      loading.value = true
      const response = await $fetch<{ config: Config }>("/api/admin/config")
      if (response.config) {
        form.value = {
          siteName: response.config.siteName,
          siteDescription: response.config.siteDescription,
          contactEmail: response.config.contactEmail,
          maxUploadSize: response.config.maxUploadSize,
          allowRegistration: response.config.allowRegistration,
          registrationClosedMessage:
            response.config.registrationClosedMessage || "",
          privateTracker: response.config.privateTracker,
          privateTrackerRatio: response.config.privateTrackerRatio,
          privateTrackerUrl: response.config.privateTrackerUrl || "",
        }
      }
    } catch (e: any) {
      error.value =
        e.data?.statusMessage || "Erreur lors du chargement de la configuration"
    } finally {
      loading.value = false
    }
  }

  async function saveConfig() {
    try {
      saving.value = true
      error.value = ""
      successMessage.value = ""

      await $fetch("/api/admin/config", {
        method: "POST",
        body: form.value,
      })

      successMessage.value = "Configuration enregistrée avec succès !"
      setTimeout(() => {
        successMessage.value = ""
      }, 3000)
    } catch (e: any) {
      error.value = e.data?.statusMessage || "Erreur lors de l'enregistrement"
    } finally {
      saving.value = false
    }
  }

  function formatSize(bytes: number): string {
    if (bytes === 0) return "0 B"
    const k = 1024
    const sizes = ["B", "KB", "MB", "GB", "TB"]
    const i = Math.floor(Math.log(bytes) / Math.log(k))
    return Math.round((bytes / Math.pow(k, i)) * 100) / 100 + " " + sizes[i]
  }
</script>
