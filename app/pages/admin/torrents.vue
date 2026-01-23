<template>
  <div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-8">
      <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold">Gestion des torrents</h1>
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
        class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded"
      >
        {{ error }}
      </div>

      <div v-else class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Nom
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                InfoHash
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Seeds
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Leechs
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Taille
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Actions
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="torrent in torrents" :key="torrent._id">
              <td class="px-6 py-4">
                <div class="text-sm font-medium text-gray-900">
                  {{ torrent.name }}
                </div>
                <div class="text-xs text-gray-500 mt-1">
                  Uploadé le {{ formatDate(torrent.created) }}
                </div>
              </td>
              <td
                class="px-6 py-4 whitespace-nowrap text-xs font-mono text-gray-600"
              >
                {{ torrent.infoHash?.substring(0, 12) }}...
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="text-green-600 font-semibold">{{
                  torrent.seeds || 0
                }}</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="text-orange-600 font-semibold">{{
                  torrent.leechs || 0
                }}</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ formatSize(torrent.length || 0) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <div class="flex gap-2">
                  <NuxtLink
                    :to="`/admin/torrent/${torrent._id}`"
                    class="text-blue-600 hover:text-blue-900"
                  >
                    Éditer
                  </NuxtLink>
                  <button
                    @click="deleteTorrent(torrent._id!)"
                    class="text-red-600 hover:text-red-900"
                  >
                    Supprimer
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
  import type { Torrent } from "~/models"
  import { formatDate } from "~/utils/dateFormat"

  const session = useUserSession()

  const torrents = ref<Torrent[]>([])
  const loading = ref(true)
  const error = ref("")

  const isAdmin = computed(() => {
    return session.user.value?.role === "admin"
  })

  // Redirection si non admin
  onMounted(async () => {
    if (!isAdmin.value) {
      navigateTo("/")
      return
    }
    await loadTorrents()
  })

  async function loadTorrents() {
    try {
      loading.value = true
      const response = await $fetch<Torrent[]>("/api/torrents")
      torrents.value = response || []
    } catch (e: any) {
      error.value =
        e.data?.statusMessage || "Erreur lors du chargement des torrents"
    } finally {
      loading.value = false
    }
  }

  async function deleteTorrent(torrentId: string) {
    if (!confirm("Êtes-vous sûr de vouloir supprimer ce torrent ?")) {
      return
    }

    try {
      await $fetch("/api/admin/torrent-delete", {
        method: "POST",
        body: { torrentId },
      })
      await loadTorrents()
    } catch (e: any) {
      alert(e.data?.statusMessage || "Erreur lors de la suppression")
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
