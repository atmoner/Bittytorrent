<template>
  <div
    class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-100 py-8 px-4"
  >
    <div class="max-w-7xl mx-auto">
      <!-- Header -->
      <div class="text-center mb-10">
        <h1 class="text-4xl font-extrabold text-gray-900 mb-2 tracking-tight">
          <span
            class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent"
          >
            Statistiques
          </span>
        </h1>
        <p class="text-gray-600 text-lg">Vue d'ensemble du site</p>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="flex justify-center items-center py-20">
        <div
          class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500"
        ></div>
      </div>

      <!-- Error State -->
      <div
        v-else-if="error"
        class="bg-red-50 border border-red-200 rounded-lg p-6 text-center"
      >
        <p class="text-red-600">{{ error }}</p>
      </div>

      <!-- Stats Content -->
      <div v-else-if="stats" class="space-y-6">
        <!-- Overview Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <!-- Total Torrents -->
          <div
            class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow"
          >
            <div class="flex items-center justify-between mb-4">
              <div
                class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center"
              >
                <svg
                  class="w-6 h-6 text-white"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"
                  />
                </svg>
              </div>
            </div>
            <p class="text-3xl font-bold text-gray-900">
              {{ stats.torrents.total }}
            </p>
            <p class="text-sm text-gray-500 mt-1">Torrents totaux</p>
            <div
              class="mt-4 pt-4 border-t border-gray-100 text-xs text-gray-600"
            >
              <span class="text-amber-600 font-medium"
                >{{ stats.torrents.private }} privés</span
              >
              •
              <span class="text-blue-600 font-medium"
                >{{ stats.torrents.public }} publics</span
              >
            </div>
          </div>

          <!-- Total Seeds -->
          <div
            class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow"
          >
            <div class="flex items-center justify-between mb-4">
              <div
                class="w-12 h-12 rounded-xl bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center"
              >
                <svg
                  class="w-6 h-6 text-white"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M5 10l7-7m0 0l7 7m-7-7v18"
                  />
                </svg>
              </div>
            </div>
            <p class="text-3xl font-bold text-gray-900">
              {{ stats.torrents.totalSeeds }}
            </p>
            <p class="text-sm text-gray-500 mt-1">Seeds totaux</p>
            <div
              class="mt-4 pt-4 border-t border-gray-100 text-xs text-gray-600"
            >
              Sources disponibles
            </div>
          </div>

          <!-- Total Leeches -->
          <div
            class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow"
          >
            <div class="flex items-center justify-between mb-4">
              <div
                class="w-12 h-12 rounded-xl bg-gradient-to-br from-red-500 to-red-600 flex items-center justify-center"
              >
                <svg
                  class="w-6 h-6 text-white"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M19 14l-7 7m0 0l-7-7m7 7V3"
                  />
                </svg>
              </div>
            </div>
            <p class="text-3xl font-bold text-gray-900">
              {{ stats.torrents.totalLeeches }}
            </p>
            <p class="text-sm text-gray-500 mt-1">Leeches totaux</p>
            <div
              class="mt-4 pt-4 border-t border-gray-100 text-xs text-gray-600"
            >
              Téléchargements actifs
            </div>
          </div>

          <!-- Total Size -->
          <div
            class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow"
          >
            <div class="flex items-center justify-between mb-4">
              <div
                class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center"
              >
                <svg
                  class="w-6 h-6 text-white"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"
                  />
                </svg>
              </div>
            </div>
            <p class="text-3xl font-bold text-gray-900">
              {{ formatSize(stats.torrents.totalSize) }}
            </p>
            <p class="text-sm text-gray-500 mt-1">Taille totale</p>
            <div
              class="mt-4 pt-4 border-t border-gray-100 text-xs text-gray-600"
            >
              Contenu hébergé
            </div>
          </div>
        </div>

        <!-- Secondary Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <!-- Users -->
          <div
            class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6"
          >
            <div class="flex items-center gap-3 mb-4">
              <div
                class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center"
              >
                <svg
                  class="w-5 h-5 text-indigo-600"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                  />
                </svg>
              </div>
              <div>
                <p class="text-2xl font-bold text-gray-900">
                  {{ stats.users.total }}
                </p>
                <p class="text-sm text-gray-500">Utilisateurs</p>
              </div>
            </div>
            <div class="mt-3 pt-3 border-t border-gray-100">
              <p class="text-xs text-gray-600">
                Ratio moyen:
                <span class="font-semibold text-gray-900">{{
                  stats.users.averageRatio
                }}</span>
              </p>
            </div>
          </div>

          <!-- Files -->
          <div
            class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6"
          >
            <div class="flex items-center gap-3 mb-4">
              <div
                class="w-10 h-10 rounded-lg bg-orange-100 flex items-center justify-center"
              >
                <svg
                  class="w-5 h-5 text-orange-600"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                  />
                </svg>
              </div>
              <div>
                <p class="text-2xl font-bold text-gray-900">
                  {{ stats.torrents.totalFiles }}
                </p>
                <p class="text-sm text-gray-500">Fichiers</p>
              </div>
            </div>
            <div class="mt-3 pt-3 border-t border-gray-100">
              <p class="text-xs text-gray-600">Dans tous les torrents</p>
            </div>
          </div>

          <!-- Trackers -->
          <div
            class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6"
          >
            <div class="flex items-center gap-3 mb-4">
              <div
                class="w-10 h-10 rounded-lg bg-teal-100 flex items-center justify-center"
              >
                <svg
                  class="w-5 h-5 text-teal-600"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"
                  />
                </svg>
              </div>
              <div>
                <p class="text-2xl font-bold text-gray-900">
                  {{ stats.torrents.uniqueTrackers }}
                </p>
                <p class="text-sm text-gray-500">Trackers</p>
              </div>
            </div>
            <div class="mt-3 pt-3 border-t border-gray-100">
              <p class="text-xs text-gray-600">Trackers uniques</p>
            </div>
          </div>
        </div>

        <!-- Top Torrents -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <!-- Top Seeds -->
          <div
            class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6"
          >
            <h3
              class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2"
            >
              <svg
                class="w-5 h-5 text-green-600"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"
                />
              </svg>
              Top 5 - Plus seedés
            </h3>
            <div class="space-y-3">
              <NuxtLink
                v-for="(torrent, idx) in stats.torrents.topSeeds"
                :key="torrent.infoHash"
                :to="`/torrent/${torrent.infoHash}`"
                class="flex items-center justify-between p-3 rounded-lg bg-gray-50 hover:bg-green-50 transition-colors group"
              >
                <div class="flex items-center gap-3 min-w-0 flex-1">
                  <div
                    class="w-8 h-8 rounded-full bg-green-100 text-green-600 flex items-center justify-center text-sm font-bold flex-shrink-0"
                  >
                    {{ idx + 1 }}
                  </div>
                  <p
                    class="text-sm font-medium text-gray-900 truncate group-hover:text-green-600"
                    :title="torrent.name"
                  >
                    {{ torrent.name }}
                  </p>
                </div>
                <span
                  class="flex items-center gap-1 text-green-600 font-bold text-sm flex-shrink-0 ml-3"
                >
                  <svg
                    class="w-4 h-4"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M5 10l7-7m0 0l7 7m-7-7v18"
                    />
                  </svg>
                  {{ torrent.seeds }}
                </span>
              </NuxtLink>
            </div>
          </div>

          <!-- Recent Torrents -->
          <div
            class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6"
          >
            <h3
              class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2"
            >
              <svg
                class="w-5 h-5 text-blue-600"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
              Les plus récents
            </h3>
            <div class="space-y-3">
              <NuxtLink
                v-for="(torrent, idx) in stats.torrents.recent"
                :key="torrent.infoHash"
                :to="`/torrent/${torrent.infoHash}`"
                class="flex items-center justify-between p-3 rounded-lg bg-gray-50 hover:bg-blue-50 transition-colors group"
              >
                <div class="flex items-center gap-3 min-w-0 flex-1">
                  <div
                    class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-sm font-bold flex-shrink-0"
                  >
                    {{ idx + 1 }}
                  </div>
                  <p
                    class="text-sm font-medium text-gray-900 truncate group-hover:text-blue-600"
                    :title="torrent.name"
                  >
                    {{ torrent.name }}
                  </p>
                </div>
                <span
                  class="text-xs text-gray-500 flex-shrink-0 ml-3"
                  :title="formatDate(torrent.created)"
                >
                  {{ formatRelativeTime(torrent.created) }}
                </span>
              </NuxtLink>
            </div>
          </div>
        </div>

        <!-- Back Button -->
        <div class="flex justify-center">
          <NuxtLink
            to="/torrents"
            class="inline-flex items-center gap-2 px-6 py-3 bg-white hover:bg-gray-50 text-gray-700 rounded-xl border border-gray-300 font-medium transition-all shadow-sm"
          >
            <svg
              class="w-5 h-5"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M10 19l-7-7m0 0l7-7m-7 7h18"
              />
            </svg>
            Retour aux torrents
          </NuxtLink>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
  import { ref, onMounted } from "vue"
  import { formatDate } from "~/utils/dateFormat"
  import { formatBytes } from "~/utils/byteFormat"

  interface Stats {
    torrents: {
      total: number
      private: number
      public: number
      totalSeeds: number
      totalLeeches: number
      totalSize: number
      totalFiles: number
      uniqueTrackers: number
      topSeeds: Array<{ name: string; infoHash: string; seeds: number }>
      recent: Array<{ name: string; infoHash: string; created: Date }>
    }
    users: {
      total: number
      averageRatio: number
    }
  }

  const stats = ref<Stats | null>(null)
  const loading = ref(true)
  const error = ref<string | null>(null)

  const formatSize = (bytes: number): string => {
    return formatBytes(bytes)
  }

  const formatRelativeTime = (date: Date): string => {
    const now = new Date()
    const diff = now.getTime() - new Date(date).getTime()
    const minutes = Math.floor(diff / 60000)
    const hours = Math.floor(diff / 3600000)
    const days = Math.floor(diff / 86400000)

    if (minutes < 60) return `${minutes}m`
    if (hours < 24) return `${hours}h`
    return `${days}j`
  }

  const fetchStats = async () => {
    loading.value = true
    error.value = null
    try {
      const res = await fetch("/api/stats")
      if (!res.ok) {
        throw new Error("Erreur lors du chargement des statistiques")
      }
      stats.value = await res.json()
    } catch (e: any) {
      error.value = e.message || "Erreur lors du chargement"
    } finally {
      loading.value = false
    }
  }

  onMounted(fetchStats)
</script>
