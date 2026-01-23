<template>
  <div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-8">
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
        <svg
          class="w-12 h-12 text-red-500 mx-auto mb-4"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
          />
        </svg>
        <h3 class="text-lg font-semibold text-red-800 mb-2">Erreur</h3>
        <p class="text-red-600">{{ error }}</p>
        <NuxtLink
          to="/torrents"
          class="inline-block mt-4 px-4 py-2 bg-red-100 text-red-800 rounded-lg hover:bg-red-200 transition"
        >
          Retour à la liste
        </NuxtLink>
      </div>

      <!-- Torrent Details -->
      <div v-else-if="torrent" class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Main Content -->
        <main class="lg:col-span-3 space-y-6">
          <!-- Info Cards -->
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg shadow-sm p-6">
              <div class="flex items-center gap-4">
                <div class="p-3 bg-blue-100 rounded-lg">
                  <svg
                    class="w-6 h-6 text-blue-600"
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
                  <p class="text-sm text-gray-500">Fichiers</p>
                  <p class="text-2xl font-bold text-gray-900">
                    {{ torrent.files?.length || 1 }}
                  </p>
                </div>
              </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6">
              <div class="flex items-center gap-4">
                <div class="p-3 bg-purple-100 rounded-lg">
                  <svg
                    class="w-6 h-6 text-purple-600"
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
                <div>
                  <p class="text-sm text-gray-500">Taille</p>
                  <p class="text-2xl font-bold text-gray-900">
                    {{ formatSize(torrent.length) }}
                  </p>
                </div>
              </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6">
              <div class="flex items-center gap-4">
                <div class="p-3 bg-green-100 rounded-lg">
                  <svg
                    class="w-6 h-6 text-green-600"
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
                <div>
                  <p class="text-sm text-gray-500">Seeds</p>
                  <p class="text-2xl font-bold text-green-600">
                    {{ torrent.seeds || 0 }}
                  </p>
                </div>
              </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6">
              <div class="flex items-center gap-4">
                <div class="p-3 bg-red-100 rounded-lg">
                  <svg
                    class="w-6 h-6 text-red-600"
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
                <div>
                  <p class="text-sm text-gray-500">Leechs</p>
                  <p class="text-2xl font-bold text-red-600">
                    {{ torrent.leechs || 0 }}
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- Torrent Details Section -->
          <div class="bg-white rounded-lg shadow-sm">
            <div class="p-6">
              <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left: Torrent Info -->
                <div class="lg:col-span-2 space-y-6">
                  <div class="flex items-start gap-4">
                    <div
                      class="w-16 h-16 rounded-lg bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white flex-shrink-0"
                    >
                      <svg
                        class="w-8 h-8"
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
                    <div>
                      <span
                        class="inline-block px-3 py-1 text-xs font-medium rounded-full mb-2"
                        :class="
                          torrent.private
                            ? 'bg-yellow-100 text-yellow-800'
                            : 'bg-green-100 text-green-800'
                        "
                      >
                        {{ torrent.private ? "Privé" : "Public" }}
                      </span>
                      <h3 class="text-xl font-bold text-gray-900">
                        {{ torrent.name }}
                      </h3>
                    </div>
                  </div>

                  <div class="space-y-4">
                    <div>
                      <h4 class="text-sm font-semibold text-gray-900 mb-1">
                        InfoHash
                      </h4>
                      <p
                        class="text-gray-600 font-mono text-sm break-all bg-gray-50 p-2 rounded"
                      >
                        {{ torrent.infoHash }}
                      </p>
                    </div>

                    <div>
                      <h4 class="text-sm font-semibold text-gray-900 mb-1">
                        Date de création
                      </h4>
                      <p class="text-gray-600">
                        {{
                          torrent.created
                            ? formatDate(torrent.created)
                            : "Inconnue"
                        }}
                      </p>
                    </div>

                    <div v-if="torrent.createdBy">
                      <h4 class="text-sm font-semibold text-gray-900 mb-1">
                        Créé par
                      </h4>
                      <p class="text-gray-600">{{ torrent.createdBy }}</p>
                    </div>

                    <div>
                      <h4 class="text-sm font-semibold text-gray-900 mb-1">
                        Taille des pièces
                      </h4>
                      <p class="text-gray-600">
                        {{ formatSize(torrent.pieceLength) }}
                      </p>
                    </div>
                  </div>
                </div>

                <!-- Right: Additional Stats -->
                <div class="space-y-4">
                  <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-sm font-semibold text-gray-900 mb-3">
                      Statistiques
                    </h4>
                    <div class="space-y-2">
                      <div class="flex justify-between">
                        <span class="text-gray-500">Taille totale</span>
                        <span class="font-medium">{{
                          formatSize(torrent.length)
                        }}</span>
                      </div>
                      <div class="flex justify-between">
                        <span class="text-gray-500">Nombre de pièces</span>
                        <span class="font-medium">{{
                          torrent.pieces?.length || 0
                        }}</span>
                      </div>
                      <div class="flex justify-between">
                        <span class="text-gray-500">Dernière pièce</span>
                        <span class="font-medium">{{
                          formatSize(torrent.lastPieceLength)
                        }}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Trackers Section -->
          <div class="bg-white rounded-lg shadow-sm">
            <div class="p-6">
              <h3
                class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2"
              >
                <svg
                  class="w-5 h-5 text-gray-400"
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
                Trackers ({{ torrent.announce?.length || 0 }})
              </h3>
              <div class="space-y-2">
                <div
                  v-for="(tracker, idx) in torrent.announce"
                  :key="idx"
                  class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg"
                >
                  <div
                    class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 text-sm font-medium"
                  >
                    {{ idx + 1 }}
                  </div>
                  <span class="text-gray-700 text-sm font-mono break-all">{{
                    tracker.split("?").shift()
                  }}</span>
                </div>
                <div
                  v-if="!torrent.announce?.length"
                  class="text-gray-500 text-center py-4"
                >
                  Aucun tracker disponible
                </div>
              </div>
            </div>
          </div>

          <!-- Files Section -->
          <div class="bg-white rounded-lg shadow-sm">
            <div class="p-6">
              <h3
                class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2"
              >
                <svg
                  class="w-5 h-5 text-gray-400"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"
                  />
                </svg>
                Fichiers ({{ torrent.files?.length || 1 }})
              </h3>
              <div class="space-y-2">
                <div
                  v-for="(file, idx) in torrent.files"
                  :key="idx"
                  class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
                >
                  <div class="flex items-center gap-3 min-w-0">
                    <svg
                      class="w-5 h-5 text-gray-400 flex-shrink-0"
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
                    <span class="text-gray-700 text-sm truncate">{{
                      file.path || file.name
                    }}</span>
                  </div>
                  <span class="text-gray-500 text-sm flex-shrink-0 ml-4">{{
                    formatSize(file.length)
                  }}</span>
                </div>
                <div
                  v-if="!torrent.files?.length"
                  class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
                >
                  <div class="flex items-center gap-3">
                    <svg
                      class="w-5 h-5 text-gray-400"
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
                    <span class="text-gray-700 text-sm">{{
                      torrent.name
                    }}</span>
                  </div>
                  <span class="text-gray-500 text-sm">{{
                    formatSize(torrent.length)
                  }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- URL List Section (if available) -->
          <!--           <div
            v-if="torrent.urlList?.length"
            class="bg-white rounded-lg shadow-sm"
          >
            <div class="p-6">
              <h3
                class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2"
              >
                <svg
                  class="w-5 h-5 text-gray-400"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"
                  />
                </svg>
                Web Seeds ({{ torrent.urlList.length }})
              </h3>
              <div class="space-y-2">
                <div
                  v-for="(url, idx) in torrent.urlList"
                  :key="idx"
                  class="p-3 bg-gray-50 rounded-lg"
                >
                  <a
                    :href="url"
                    target="_blank"
                    class="text-blue-600 hover:underline text-sm font-mono break-all"
                    >{{ url }}</a
                  >
                </div>
              </div>
            </div>
          </div> -->
        </main>
        <!-- Sidebar -->
        <aside class="lg:col-span-1">
          <div class="bg-white rounded-lg shadow-sm p-6 space-y-6">
            <!-- Torrent Icon -->
            <div class="flex items-center gap-3 pb-6 border-b border-gray-100">
              <div
                class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white"
              >
                <svg
                  class="w-6 h-6"
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
              <div class="flex-1 min-w-0">
                <h3 class="font-semibold text-gray-900 truncate">
                  {{ torrent.name }}
                </h3>
                <p class="text-sm text-gray-500">Torrent</p>
              </div>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-2 gap-3 pb-6 border-b border-gray-100">
              <div
                class="flex flex-col items-center gap-2 p-3 rounded-lg bg-green-50 text-green-600"
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
                    d="M5 10l7-7m0 0l7 7m-7-7v18"
                  />
                </svg>
                <span class="text-xs font-medium">Seeds</span>
                <span class="text-lg font-bold">{{ torrent.seeds || 0 }}</span>
              </div>
              <div
                class="flex flex-col items-center gap-2 p-3 rounded-lg bg-red-50 text-red-600"
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
                    d="M19 14l-7 7m0 0l-7-7m7 7V3"
                  />
                </svg>
                <span class="text-xs font-medium">Leechs</span>
                <span class="text-lg font-bold">{{ torrent.leechs || 0 }}</span>
              </div>
            </div>

            <!-- Actions -->
            <div class="space-y-2">
              <button
                @click="downloadTorrent"
                :disabled="!loggedIn"
                class="group w-full relative inline-flex h-12 items-center justify-center overflow-hidden rounded-md bg-blue-500 px-6 font-medium text-white duration-500"
                :class="
                  !loggedIn
                    ? 'cursor-not-allowed opacity-50'
                    : 'cursor-pointer hover:bg-blue-600'
                "
              >
                <div
                  class="translate-y-0 opacity-100 transition group-hover:-translate-y-[150%] group-hover:opacity-0"
                >
                  Télécharger
                </div>
                <div
                  v-if="loggedIn"
                  class="flex gap-2 absolute translate-y-[150%] opacity-0 transition group-hover:translate-y-0 group-hover:opacity-100"
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
                      d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"
                    />
                  </svg>
                  .torrent
                </div>
                <div
                  v-else
                  class="absolute translate-y-[150%] opacity-0 transition group-hover:translate-y-0 group-hover:opacity-100 text-sm"
                >
                  Connectez-vous pour télécharger
                </div>
              </button>
              <NuxtLink
                v-if="torrent.uploadedBy === user.user.value?.id"
                @click="deleteTorrent()"
                class="flex cursor-pointer items-center justify-center gap-2 w-full px-4 py-2 bg-red-100 text-red-500 rounded-lg hover:bg-red-200 transition"
              >
                <span>Effacer</span>
              </NuxtLink>
              <button
                v-if="loggedIn"
                @click="scrapeTorrent"
                :disabled="scraping"
                class="flex cursor-pointer items-center justify-center gap-2 w-full px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition disabled:opacity-50"
              >
                <svg
                  class="w-5 h-5"
                  :class="{ 'animate-spin': scraping }"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                  />
                </svg>
                <span>{{
                  scraping ? "Actualisation..." : "Actualiser les stats"
                }}</span>
              </button>

              <NuxtLink
                to="/torrents"
                class="flex items-center justify-center gap-2 w-full px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition"
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
                <span>Retour à la liste</span>
              </NuxtLink>
            </div>
          </div>
        </aside>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
  import { ref, onMounted } from "vue"
  import type { Torrent } from "~/models"
  import { formatDate } from "~/utils/dateFormat"

  const route = useRoute()
  const { loggedIn } = useUserSession()

  const torrent = ref<Torrent | null>(null)
  const user = useUserSession()
  const loading = ref(true)
  const error = ref<string | null>(null)
  const scraping = ref(false)

  const infoHash = computed(() => route.params.infoHash as string)

  const formatSize = (bytes: number): string => {
    if (!bytes || bytes === 0) return "0 B"
    const k = 1024
    const sizes = ["B", "KB", "MB", "GB", "TB"]
    const i = Math.floor(Math.log(bytes) / Math.log(k))
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + " " + sizes[i]
  }

  const fetchTorrent = async () => {
    loading.value = true
    error.value = null
    try {
      const res = await fetch(`/api/torrent/${infoHash.value}`)
      if (!res.ok) {
        const data = await res.json()
        throw new Error(data.statusMessage || "Torrent non trouvé")
      }
      torrent.value = await res.json()
    } catch (e: any) {
      error.value = e.message || "Erreur lors du chargement du torrent"
    } finally {
      loading.value = false
    }
  }

  const deleteTorrent = async () => {
    if (!torrent.value) return

    const confirmDelete = confirm(
      `Êtes-vous sûr de vouloir supprimer le torrent "${torrent.value.name}" ?\n\nCette action est irréversible.`,
    )
    if (!confirmDelete) return

    try {
      const res = await fetch(`/api/torrent/${infoHash.value}`, {
        method: "DELETE",
      })

      const data = await res.json()

      if (!res.ok) {
        throw new Error(data.statusMessage || "Erreur lors de la suppression")
      }

      // Succès - rediriger vers la liste des torrents
      alert(
        `Torrent "${
          data.deletedTorrent?.name || torrent.value.name
        }" supprimé avec succès.`,
      )
      await navigateTo("/torrents")
    } catch (e: any) {
      const errorMessage =
        e.message || "Erreur lors de la suppression du torrent"
      alert(`Erreur: ${errorMessage}`)
      console.error("Delete error:", e)
    }
  }

  const scrapeTorrent = async () => {
    if (!torrent.value || scraping.value) return
    scraping.value = true
    try {
      const tracker = torrent.value.announce?.[0] || ""
      const res = await fetch(
        `/api/scrape?infoHash=${encodeURIComponent(
          infoHash.value,
        )}&announce=${encodeURIComponent(tracker)}`,
      )
      if (res.ok) {
        await fetchTorrent()
      }
    } catch (e) {
      console.error("Error scraping torrent:", e)
    } finally {
      scraping.value = false
    }
  }

  const downloadTorrent = () => {
    if (!torrent.value) return
    window.location.href = `/api/download-torrent/${infoHash.value}`
  }

  onMounted(fetchTorrent)
</script>
