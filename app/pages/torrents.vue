<template>
  <div class="min-h-screen py-8 px-4">
    <div class="max-w-370 mx-auto">
      <!-- Header -->
      <div class="text-center mb-10">
        <h1 class="text-4xl font-extrabold text-gray-900 mb-2 tracking-tight">
          <span
            class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent"
          >
            {{ filteredTorrents.length }} torrents disponibles
          </span>
        </h1>
      </div>

      <!-- Search & Filters Bar -->
      <div
        class="flex flex-col sm:flex-row gap-4 mb-8 items-center justify-between"
      >
        <div class="flex items-center gap-3 w-full sm:w-auto">
          <div class="relative flex-1 sm:w-96">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Rechercher un torrent..."
              class="w-full pl-12 pr-4 py-3 bg-white border border-gray-300 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all shadow-sm"
            />
            <svg
              class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
              />
            </svg>
          </div>
          <!-- View Mode Toggle -->
          <div
            class="flex gap-1 bg-white border border-gray-300 rounded-lg p-1"
          >
            <button
              @click="viewMode = 'grid'"
              :class="[
                'p-2 rounded-md transition-all',
                viewMode === 'grid'
                  ? 'bg-gray-900 text-white'
                  : 'text-gray-600 hover:text-gray-900',
              ]"
              title="Affichage en cartes"
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
                  d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"
                />
              </svg>
            </button>
            <button
              @click="viewMode = 'list'"
              :class="[
                'p-2 rounded-md transition-all',
                viewMode === 'list'
                  ? 'bg-gray-900 text-white'
                  : 'text-gray-600 hover:text-gray-900',
              ]"
              title="Affichage en liste"
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
                  d="M4 6h16M4 12h16M4 18h16"
                />
              </svg>
            </button>
          </div>
        </div>
        <div class="flex flex-wrap gap-2">
          <!-- Filtre Catégorie -->
          <div class="relative">
            <select
              v-model="selectedCategoryId"
              @change="loadTorrents"
              class="px-3 py-1.5 h-full bg-white border border-gray-300 rounded-lg text-gray-900 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent appearance-none pr-8"
            >
              <option value="all">Toutes les catégories</option>
              <option
                v-for="category in categories"
                :key="category._id"
                :value="category._id"
              >
                {{ category.name }}
              </option>
            </select>
            <svg
              class="absolute right-2 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M19 9l-7 7-7-7"
              ></path>
            </svg>
          </div>
          <!-- Filtre Type -->
          <div
            class="flex gap-1 bg-white border border-gray-300 rounded-lg p-1"
          >
            <button
              @click="filterType = 'all'"
              :class="[
                'px-3 py-1.5 rounded-md text-sm font-medium transition-all',
                filterType === 'all'
                  ? 'bg-gray-900 text-white'
                  : 'text-gray-600 hover:text-gray-900',
              ]"
            >
              Tous
            </button>
            <button
              @click="filterType = 'private'"
              :class="[
                'px-3 py-1.5 rounded-md text-sm font-medium transition-all',
                filterType === 'private'
                  ? 'bg-amber-500 text-white'
                  : 'text-gray-600 hover:text-gray-900',
              ]"
            >
              Privés
            </button>
            <button
              @click="filterType = 'public'"
              :class="[
                'px-3 py-1.5 rounded-md text-sm font-medium transition-all',
                filterType === 'public'
                  ? 'bg-blue-500 text-white'
                  : 'text-gray-600 hover:text-gray-900',
              ]"
            >
              Publics
            </button>
          </div>
          <!-- Tri -->
          <div
            class="flex gap-1 bg-white border border-gray-300 rounded-lg p-1"
          >
            <button
              @click="sortBy = 'date'"
              :class="[
                'px-3 py-1.5 rounded-md text-sm font-medium transition-all',
                sortBy === 'date'
                  ? 'bg-blue-600 text-white'
                  : 'text-gray-600 hover:text-gray-900',
              ]"
            >
              Plus récents
            </button>
            <button
              @click="sortBy = 'seeds'"
              :class="[
                'px-3 py-1.5 rounded-md text-sm font-medium transition-all',
                sortBy === 'seeds'
                  ? 'bg-green-600 text-white'
                  : 'text-gray-600 hover:text-gray-900',
              ]"
            >
              Plus seedés
            </button>
          </div>
        </div>
      </div>

      <!-- Slots triés par priority : blocs plugins + liste (priority: 10 par défaut) -->
      <!-- priority < 10 → au-dessus de la liste | priority > 10 → en-dessous -->
      <template v-for="slot in sortedSlots" :key="slot.id">
        <div v-if="slot.id !== 'torrent-list'" class="mb-8">
          <component :is="slot.componentDef ?? slot.component" />
        </div>
        <template v-else>
          <TorrentGridCards
            v-if="viewMode === 'grid'"
            :torrents="filteredTorrents"
            :logged-in="loggedIn"
            :scraping-hash="scrapingHash"
            :search-query="searchQuery"
            :get-category-color="getCategoryColor"
            :get-category-name="getCategoryName"
            :format-date="formatDate"
            :format-size="formatSize"
            @update="updateTorrent"
          />

          <!-- Torrents List View -->
          <div
            v-if="filteredTorrents.length > 0 && viewMode === 'list'"
            class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden"
          >
            <table class="w-full">
              <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                  <th
                    class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"
                  >
                    Nom
                  </th>
                  <th
                    class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider hidden sm:table-cell"
                  >
                    Catégorie
                  </th>
                  <th
                    class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider hidden md:table-cell"
                  >
                    Type
                  </th>
                  <th
                    class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider hidden lg:table-cell"
                  >
                    Taille
                  </th>
                  <th
                    class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider hidden xl:table-cell"
                  >
                    Date
                  </th>
                  <th
                    class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider"
                  >
                    Seeds
                  </th>
                  <th
                    class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider"
                  >
                    Leechs
                  </th>

                  <th
                    class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider"
                  >
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <tr
                  v-for="torrent in filteredTorrents"
                  :key="torrent._id"
                  class="cursor-pointer md:cursor-auto"
                  @click="navigateToTorrent(torrent.infoHash, $event)"
                >
                  <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                      <div
                        class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white flex-shrink-0"
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
                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"
                          />
                        </svg>
                      </div>
                      <div class="min-w-0">
                        <p
                          class="text-sm font-medium text-gray-900 truncate"
                          :title="torrent.name"
                        >
                          {{ torrent.name }}
                        </p>
                        <p
                          class="text-xs text-gray-500 font-mono truncate"
                          :title="torrent.infoHash"
                        >
                          {{ torrent.infoHash.substring(0, 16) }}...
                        </p>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 hidden sm:table-cell">
                    <span
                      v-if="torrent.categoryId"
                      class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium text-white"
                      :style="{
                        backgroundColor: getCategoryColor(torrent.categoryId),
                      }"
                    >
                      {{ getCategoryName(torrent.categoryId) }}
                    </span>
                    <span v-else class="text-xs text-gray-400 italic">
                      Aucune catégorie
                    </span>
                  </td>
                  <td class="px-6 py-4 hidden md:table-cell">
                    <span
                      class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium"
                      :class="
                        torrent.private
                          ? 'bg-amber-500/20 text-amber-600 border border-amber-500/30'
                          : 'bg-blue-500/20 text-blue-600 border border-blue-500/30'
                      "
                    >
                      {{ torrent.private ? "Privé" : "Public" }}
                    </span>
                  </td>

                  <td
                    class="px-6 py-4 text-sm text-gray-600 hidden lg:table-cell"
                  >
                    {{ torrent.length ? formatSize(torrent.length) : "-" }}
                  </td>
                  <td
                    class="px-6 py-4 text-sm text-gray-600 hidden xl:table-cell"
                  >
                    {{ formatDate(torrent.created) }}
                  </td>

                  <td class="px-6 py-4 text-center">
                    <span
                      class="inline-flex items-center gap-1 text-green-600 font-semibold"
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
                  </td>
                  <td class="px-6 py-4 text-center">
                    <span
                      class="inline-flex items-center gap-1 text-red-600 font-semibold"
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
                          d="M19 14l-7 7m0 0l-7-7m7 7V3"
                        />
                      </svg>
                      {{ torrent.leechs }}
                    </span>
                  </td>
                  <td class="px-6 py-4">
                    <div class="flex items-center justify-center gap-2">
                      <NuxtLink
                        :to="`/torrent/${torrent.infoHash}`"
                        class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-600 hover:bg-blue-500 text-white text-sm rounded-lg font-medium transition-all"
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
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                          />
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                          />
                        </svg>
                        <span class="hidden sm:inline">Détails</span>
                      </NuxtLink>
                      <button
                        v-if="loggedIn"
                        @click="
                          updateTorrent(
                            torrent.infoHash,
                            torrent.announce[0] || '',
                          )
                        "
                        :disabled="scrapingHash === torrent.infoHash"
                        class="inline-flex items-center justify-center w-9 h-9 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-all disabled:opacity-50 disabled:cursor-not-allowed border border-gray-300"
                        title="Actualiser les stats"
                      >
                        <svg
                          :class="[
                            'w-4 h-4',
                            scrapingHash === torrent.infoHash
                              ? 'animate-spin'
                              : '',
                          ]"
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
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div
            v-else-if="viewMode === 'list' && filteredTorrents.length === 0"
            class="flex flex-col items-center justify-center py-20"
          >
            <div
              class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6 border-2 border-gray-200"
            >
              <svg
                class="w-12 h-12 text-gray-400"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">
              Aucun torrent trouvé
            </h3>
            <p class="text-gray-500">
              {{
                searchQuery
                  ? "Essayez une autre recherche"
                  : "Aucun torrent disponible pour le moment"
              }}
            </p>
          </div>
        </template>
      </template>
    </div>
  </div>
</template>

<script setup lang="ts">
  import { ref, computed, onMounted, watch } from "vue"
  import TorrentGridCards from "~/components/torrent/TorrentGridCards.vue"
  import type { Torrent } from "~/models"
  import { formatDate } from "../utils/dateFormat"
  import { formatBytes } from "../utils/byteFormat"

  const { executeHook } = useHooks()
  const { loggedIn } = useUserSession()
  const route = useRoute()

  const torrents = ref<Torrent[]>([])
  const categories = ref()
  const searchQuery = ref("")
  const sortBy = ref<"date" | "seeds">("date")
  const filterType = ref<"all" | "private" | "public">("all")
  const selectedCategoryId = ref("all")
  const viewMode = ref<"grid" | "list">("list")
  const scrapingHash = ref<string | null>(null)
  type TorrentListBlock = {
    id: string
    component?: string
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    componentDef?: any
    priority?: number
  }

  // Blocs enregistrés par les plugins
  const pluginBlocks = ref<TorrentListBlock[]>([])

  // Slots unifiés : blocs plugins + sentinelle "torrent-list" (priority 10)
  // priority < 10 → au-dessus de la liste
  // priority > 10 → en-dessous de la liste
  const sortedSlots = computed<TorrentListBlock[]>(() =>
    [{ id: "torrent-list", priority: 10 }, ...pluginBlocks.value].sort(
      (a, b) => (a.priority ?? 10) - (b.priority ?? 10),
    ),
  )

  const registerBlock = (block: TorrentListBlock) => {
    if (pluginBlocks.value.find((b) => b.id === block.id)) return
    pluginBlocks.value.push(block)
  }

  // Initialiser selectedCategoryId depuis les paramètres de l'URL
  onMounted(() => {
    if (route.query.category) {
      selectedCategoryId.value = route.query.category as string
    }
  })

  // Watcher pour les changements de route
  watch(
    () => route.query.category,
    (newCategoryId) => {
      if (newCategoryId) {
        selectedCategoryId.value = newCategoryId as string
        loadTorrents()
      } else {
        selectedCategoryId.value = "all"
        loadTorrents()
      }
    },
  )

  // Format size helper
  const formatSize = (bytes: number) => {
    if (!bytes) return ""
    return formatBytes(bytes)
  }

  // Charger les catégories
  const loadCategories = async () => {
    try {
      const data = await $fetch("/api/categories")
      categories.value = data
    } catch (error) {
      console.error("Erreur lors du chargement des catégories:", error)
    }
  }

  // Charger les torrents avec filtre
  const loadTorrents = async () => {
    try {
      const params = new URLSearchParams()

      if (selectedCategoryId.value !== "all") {
        params.append("categoryId", selectedCategoryId.value)
      }

      if (searchQuery.value) {
        params.append("search", searchQuery.value)
      }

      const url = params.toString()
        ? `/api/torrents?${params.toString()}`
        : "/api/torrents"
      const data = await $fetch<Torrent[]>(url)
      torrents.value = data
    } catch (error) {
      console.error("Erreur lors du chargement des torrents:", error)
    }
  }

  // Obtenir le nom de la catégorie
  const getCategoryName = (categoryId: string) => {
    const category = categories.value.find(
      (c: { _id: string }) => c._id === categoryId,
    )
    return category ? category.name : "Sans catégorie"
  }

  // Obtenir la couleur de la catégorie
  const getCategoryColor = (categoryId: string) => {
    const category = categories.value.find(
      (c: { _id: string }) => c._id === categoryId,
    )
    return category ? category.color : "#6b7280"
  }

  // Filtered and sorted torrents (maintenant on filtre côté client seulement le type et on trie)
  const filteredTorrents = computed(() => {
    let result = [...torrents.value]

    // Filter by type (private/public)
    if (filterType.value === "private") {
      result = result.filter((t) => t.private === true)
    } else if (filterType.value === "public") {
      result = result.filter((t) => !t.private)
    }

    // Sort
    if (sortBy.value === "date") {
      result.sort(
        (a, b) => new Date(b.created).getTime() - new Date(a.created).getTime(),
      )
    } else if (sortBy.value === "seeds") {
      result.sort((a, b) => b.seeds - a.seeds)
    }

    return result
  })

  // Watcher pour la recherche avec debounce
  let searchTimeout: NodeJS.Timeout
  watch(searchQuery, () => {
    clearTimeout(searchTimeout)
    searchTimeout = setTimeout(() => {
      loadTorrents()
    }, 500)
  })

  const fetchTorrents = async () => {
    try {
      const res = await fetch("/api/torrents")
      const data = await res.json()
      torrents.value = data
    } catch (e) {}
  }

  const updateTorrent = async (infoHash: string, tracker: string) => {
    scrapingHash.value = infoHash
    try {
      const res = await fetch(
        `/api/scrape?infoHash=${encodeURIComponent(
          infoHash,
        )}&announce=${encodeURIComponent(tracker)}`,
      )
      const data = await res.json()
      console.log("Scrape result for", infoHash, ":", data)
      await loadTorrents()
    } catch (e) {
      console.error("Error scraping torrent:", e)
    } finally {
      scrapingHash.value = null
    }
  }

  // Navigation vers les détails du torrent (pour mobile)
  const navigateToTorrent = (infoHash: string, event: Event) => {
    // Vérifier si on est sur mobile (largeur d'écran < 768px pour md)
    if (window.innerWidth < 768) {
      // Vérifier que le clic n'est pas sur un bouton ou un lien
      const target = event.target as HTMLElement
      if (!target.closest("button") && !target.closest("a")) {
        navigateTo(`/torrent/${infoHash}`)
      }
    }
  }

  onMounted(async () => {
    await Promise.all([loadCategories(), loadTorrents()])
    pluginBlocks.value = []
    await executeHook("page:torrent-list", {
      page: "torrents",
      categoryId: selectedCategoryId.value,
      registerTopBlock: registerBlock,
    })
  })
</script>

<style scoped>
  /* Animations supplémentaires */
  .group:hover {
    transform: translateY(-2px);
  }
</style>
