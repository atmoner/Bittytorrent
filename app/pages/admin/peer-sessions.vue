<template>
  <div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-8">
      <h1 class="text-3xl font-bold text-gray-900 mb-8">
        Système Anti-Triche & Peer Sessions
      </h1>

      <!-- Actions -->
      <div class="mb-8 flex flex-wrap gap-4">
        <Button
          @click="initPeerSessions"
          :disabled="loading"
          class="bg-blue-600 text-white hover:bg-blue-700"
        >
          {{ loading ? "Initialisation..." : "Initialiser Collection" }}
        </Button>

        <Button
          @click="clearPeerSessions"
          :disabled="loading"
          class="bg-red-600 text-white hover:bg-red-700"
        >
          Vider Sessions
        </Button>

        <Button
          @click="refreshData"
          :disabled="loading"
          class="bg-green-600 text-white hover:bg-green-700"
        >
          Actualiser
        </Button>
      </div>

      <!-- Message d'état -->
      <div
        v-if="message"
        :class="`mb-6 p-4 rounded-lg ${messageType === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`"
      >
        {{ message }}
      </div>

      <!-- Statistiques générales -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-2">
            Sessions Actives
          </h3>
          <p class="text-3xl font-bold text-blue-600">
            {{ stats?.totalSessions || 0 }}
          </p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-2">Total Upload</h3>
          <p class="text-3xl font-bold text-green-600">
            {{ formatBytes(stats?.totalUploaded || 0) }}
          </p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-2">
            Total Download
          </h3>
          <p class="text-3xl font-bold text-purple-600">
            {{ formatBytes(stats?.totalDownloaded || 0) }}
          </p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-2">Ratio Moyen</h3>
          <p class="text-3xl font-bold text-orange-600">
            {{
              (
                (stats?.totalUploaded || 0) /
                Math.max(stats?.totalDownloaded || 1, 1)
              ).toFixed(2)
            }}
          </p>
        </div>
      </div>

      <!-- Sessions actives -->
      <div class="bg-white rounded-lg shadow mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
          <h2 class="text-xl font-semibold text-gray-900">
            Sessions Actives Récentes
          </h2>
        </div>

        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  Peer ID
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  InfoHash
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  Status
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  Upload
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  Download
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  Dernière Vue
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  IP
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="session in activeSessions" :key="session._id">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ session.peer_id.substring(0, 12) }}...
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ session.infoHash.substring(0, 12) }}...
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ session.event }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">
                  {{ formatBytes(session.uploaded) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-purple-600">
                  {{ formatBytes(session.downloaded) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ formatDate(session.lastSeen) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ session.ip || "N/A" }}
                </td>
              </tr>

              <tr v-if="!activeSessions?.length">
                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                  Aucune session active
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Stats par torrent et par utilisateur -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Sessions par Torrent -->
        <div class="bg-white rounded-lg shadow">
          <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">
              Top Torrents (Sessions)
            </h2>
          </div>

          <div class="p-6">
            <div
              v-for="item in sessionsByTorrent"
              :key="item._id"
              class="mb-4 last:mb-0"
            >
              <div class="flex justify-between items-center mb-2">
                <div class="text-sm font-medium text-gray-900 truncate">
                  {{ item.torrentName }}
                </div>
                <Chip :label="`${item.sessionCount} sessions`" />
              </div>
              <div class="text-xs text-gray-500">
                ↑ {{ formatBytes(item.totalUploaded) }} | ↓
                {{ formatBytes(item.totalDownloaded) }}
              </div>
            </div>

            <div
              v-if="!sessionsByTorrent?.length"
              class="text-center text-gray-500"
            >
              Aucune donnée disponible
            </div>
          </div>
        </div>

        <!-- Sessions par Utilisateur -->
        <div class="bg-white rounded-lg shadow">
          <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">
              Top Utilisateurs (Sessions)
            </h2>
          </div>

          <div class="p-6">
            <div
              v-for="item in sessionsByUser"
              :key="item._id"
              class="mb-4 last:mb-0"
            >
              <div class="flex justify-between items-center mb-2">
                <div class="text-sm font-medium text-gray-900">
                  {{ item.username }}
                </div>
                <Chip :label="`${item.sessionCount} sessions`" />
              </div>
              <div class="text-xs text-gray-500">
                ↑ {{ formatBytes(item.totalUploaded) }} | ↓
                {{ formatBytes(item.totalDownloaded) }}
              </div>
            </div>

            <div
              v-if="!sessionsByUser?.length"
              class="text-center text-gray-500"
            >
              Aucune donnée disponible
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
  import Button from "~/components/bittyUi/Button.vue"
  import Chip from "~/components/bittyUi/Chip.vue"
  import { formatDate } from "~/utils/dateFormat"

  const { executeHook } = useHooks()
  const { showConfirm } = useModal()

  definePageMeta({
    middleware: "admin",
  })

  const loading = ref(false)
  const message = ref("")
  const messageType = ref<"success" | "error">("success")

  // Data
  const stats = ref(null)
  const activeSessions = ref([])
  const sessionsByTorrent = ref([])
  const sessionsByUser = ref([])

  // Fonction pour formater les bytes
  function formatBytes(bytes: number): string {
    if (bytes === 0) return "0 B"

    const k = 1024
    const sizes = ["B", "KB", "MB", "GB", "TB"]
    const i = Math.floor(Math.log(bytes) / Math.log(k))

    return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + " " + sizes[i]
  }

  // Initialiser la collection peer_sessions
  async function initPeerSessions() {
    loading.value = true
    message.value = ""

    try {
      const response = await $fetch("/api/admin/init-peer-sessions", {
        method: "POST",
      })

      message.value = response.message
      messageType.value = "success"

      // Rafraîchir les données après initialisation
      await refreshData()
    } catch (error: any) {
      message.value = error.data?.message || "Erreur lors de l'initialisation"
      messageType.value = "error"
    } finally {
      loading.value = false
    }
  }

  // Vider les sessions
  async function clearPeerSessions() {
    if (
      !(await showConfirm(
        "Êtes-vous sûr de vouloir supprimer toutes les sessions actives ?",
      ))
    ) {
      return
    }

    loading.value = true
    message.value = ""

    try {
      const response = await $fetch("/api/admin/clear-peer-sessions", {
        method: "POST",
      })

      message.value = response.message
      messageType.value = "success"

      // Rafraîchir les données
      await refreshData()
    } catch (error: any) {
      message.value = error.data?.message || "Erreur lors de la suppression"
      messageType.value = "error"
    } finally {
      loading.value = false
    }
  }

  // Rafraîchir les données
  async function refreshData() {
    loading.value = true

    try {
      const data = await $fetch("/api/admin/peer-sessions")

      stats.value = data.stats
      activeSessions.value = data.activeSessions
      sessionsByTorrent.value = data.sessionsByTorrent
      sessionsByUser.value = data.sessionsByUser
    } catch (error: any) {
      message.value = "Erreur lors du chargement des données"
      messageType.value = "error"
    } finally {
      loading.value = false
    }
  }

  // Charger les données au montage
  onMounted(async () => {
    await executeHook("admin:page", {
      page: "admin-peer-sessions",
    })
    refreshData()
  })
</script>
