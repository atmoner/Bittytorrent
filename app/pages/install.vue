<template>
  <div class="min-h-screen bg-gray-50 flex items-center justify-center px-4">
    <div class="max-w-2xl w-full">
      <div v-if="blocksAbove.length > 0" class="space-y-4 mb-6">
        <component
          v-for="block in blocksAbove"
          :key="block.id"
          :is="block.componentDef ?? block.component"
        />
      </div>

      <div class="bg-white rounded-lg shadow-lg p-8">
        <h1 class="text-3xl font-bold text-center text-gray-900 mb-2">
          Installation - Nuxt Tracker
        </h1>
        <p class="text-gray-600 text-center mb-8">
          Configurez votre base de données MongoDB et initialisez l'application
        </p>

        <div
          v-if="error"
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

        <div v-if="!isInstalled">
          <form @submit.prevent="testConnection" class="space-y-6">
            <div>
              <label
                for="mongoUri"
                class="block text-sm font-medium text-gray-700 mb-2"
              >
                URI MongoDB
              </label>
              <input
                id="mongoUri"
                v-model="form.mongoUri"
                type="text"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="mongodb://localhost:27017"
              />
              <p class="text-sm text-gray-500 mt-1">
                URL de connexion à votre serveur MongoDB
              </p>
            </div>

            <div>
              <label
                for="mongoDb"
                class="block text-sm font-medium text-gray-700 mb-2"
              >
                Nom de la base de données
              </label>
              <input
                id="mongoDb"
                v-model="form.mongoDb"
                type="text"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="nuxt-tracker"
              />
              <p class="text-sm text-gray-500 mt-1">
                Nom de la base de données à utiliser
              </p>
            </div>

            <div class="flex gap-4">
              <button
                type="submit"
                :disabled="testingConnection"
                class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                {{
                  testingConnection ? "Test en cours..." : "Tester la connexion"
                }}
              </button>
            </div>
          </form>

          <div v-if="connectionTested" class="mt-6 space-y-6">
            <hr class="border-gray-200" />
            <h2 class="text-xl font-semibold text-gray-900">
              Configuration du site
            </h2>

            <form @submit.prevent="installApp" class="space-y-6">
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
                  placeholder="Mon Tracker"
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
                  rows="3"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="Description de votre tracker BitTorrent"
                />
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
                  placeholder="admin@mondomaine.com"
                />
              </div>

              <hr class="border-gray-200" />
              <h3 class="text-lg font-semibold text-gray-900">
                Compte administrateur
              </h3>

              <div>
                <label
                  for="adminUsername"
                  class="block text-sm font-medium text-gray-700 mb-2"
                >
                  Nom d'utilisateur admin
                </label>
                <input
                  id="adminUsername"
                  v-model="form.adminUsername"
                  type="text"
                  required
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="admin"
                />
              </div>

              <div>
                <label
                  for="adminEmail"
                  class="block text-sm font-medium text-gray-700 mb-2"
                >
                  Email admin
                </label>
                <input
                  id="adminEmail"
                  v-model="form.adminEmail"
                  type="email"
                  required
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="admin@mondomaine.com"
                />
              </div>

              <div>
                <label
                  for="adminPassword"
                  class="block text-sm font-medium text-gray-700 mb-2"
                >
                  Mot de passe admin
                </label>
                <input
                  id="adminPassword"
                  v-model="form.adminPassword"
                  type="password"
                  required
                  minlength="6"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="••••••••"
                />
                <p class="text-sm text-gray-500 mt-1">Minimum 6 caractères</p>
              </div>

              <button
                type="submit"
                :disabled="installing"
                class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                {{
                  installing
                    ? "Installation en cours..."
                    : "Installer l'application"
                }}
              </button>
            </form>
          </div>
        </div>

        <div
          v-else
          class="text-center py-8 bg-green-50 rounded-lg border border-green-200"
        >
          <div class="text-green-600 mb-4">
            <svg
              class="w-16 h-16 mx-auto"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
              />
            </svg>
          </div>
          <h2 class="text-2xl font-bold text-green-800 mb-2">
            Installation terminée !
          </h2>
          <p class="text-green-700 mb-6">
            Votre application Nuxt Tracker est maintenant configurée et prête à
            être utilisée.
          </p>
          <NuxtLink
            to="/login"
            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 inline-block"
          >
            Aller à la page de connexion
          </NuxtLink>
        </div>
      </div>

      <div v-if="blocksBelow.length > 0" class="space-y-4 mt-6">
        <component
          v-for="block in blocksBelow"
          :key="block.id"
          :is="block.componentDef ?? block.component"
        />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
  definePageMeta({
    auth: false,
  })

  const { executeHook } = useHooks()

  const form = ref({
    mongoUri: "mongodb://localhost:27017",
    mongoDb: "nuxt-tracker",
    siteName: "Nuxt Tracker",
    siteDescription: "Un tracker BitTorrent moderne",
    contactEmail: "",
    adminUsername: "admin",
    adminEmail: "",
    adminPassword: "",
  })

  const error = ref("")
  const successMessage = ref("")
  const testingConnection = ref(false)
  const installing = ref(false)
  const connectionTested = ref(false)
  const isInstalled = ref(false)

  type InstallPageBlock = {
    id: string
    component?: string
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    componentDef?: any
    priority?: number
  }

  const pageBlocks = ref<InstallPageBlock[]>([])

  const blocksAbove = computed(() =>
    pageBlocks.value
      .filter((block) => (block.priority ?? 10) < 10)
      .sort((a, b) => (a.priority ?? 10) - (b.priority ?? 10)),
  )

  const blocksBelow = computed(() =>
    pageBlocks.value
      .filter((block) => (block.priority ?? 10) >= 10)
      .sort((a, b) => (a.priority ?? 10) - (b.priority ?? 10)),
  )

  const registerBlock = (block: InstallPageBlock) => {
    if (pageBlocks.value.find((item) => item.id === block.id)) return
    pageBlocks.value.push({
      ...block,
      priority: block.priority ?? 10,
    })
  }

  // Vérifier si l'application est déjà installée
  onMounted(async () => {
    pageBlocks.value = []
    await executeHook("page:install", {
      page: "install",
      registerBlock,
    })

    try {
      const response = await $fetch("/api/install/check")
      isInstalled.value = response.installed
    } catch (err: any) {
      console.log("App not installed yet")
    }
  })

  const testConnection = async () => {
    testingConnection.value = true
    error.value = ""
    successMessage.value = ""

    try {
      const response = await $fetch("/api/install/test-connection", {
        method: "POST",
        body: {
          mongoUri: form.value.mongoUri,
          mongoDb: form.value.mongoDb,
        },
      })

      if (response.success) {
        successMessage.value = "Connexion MongoDB réussie !"
        connectionTested.value = true
      }
    } catch (err: any) {
      error.value = err.data?.message || "Erreur lors du test de connexion"
    } finally {
      testingConnection.value = false
    }
  }

  const installApp = async () => {
    installing.value = true
    error.value = ""
    successMessage.value = ""

    try {
      const response = await $fetch("/api/install/setup", {
        method: "POST",
        body: {
          mongoUri: form.value.mongoUri,
          mongoDb: form.value.mongoDb,
          siteName: form.value.siteName,
          siteDescription: form.value.siteDescription,
          contactEmail: form.value.contactEmail,
          adminUsername: form.value.adminUsername,
          adminEmail: form.value.adminEmail,
          adminPassword: form.value.adminPassword,
        },
      })

      if (response.success) {
        successMessage.value = "Installation terminée avec succès !"
        isInstalled.value = true
      }
    } catch (err: any) {
      error.value = err.data?.message || "Erreur lors de l'installation"
    } finally {
      installing.value = false
    }
  }
</script>
