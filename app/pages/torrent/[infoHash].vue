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
          <!-- Slots triés par priority : blocs plugins + contenu (priority: 10 par défaut) -->
          <!-- priority < 10 → au-dessus du contenu | priority > 10 → en-dessous -->
          <template v-for="slot in sortedSlots" :key="slot.id">
            <component
              v-if="slot.id !== 'torrent-detail'"
              :is="slot.componentDef ?? slot.component"
              :torrent="torrent"
            />
            <template v-else>
              <TorrentDetailContent :torrent="torrent" />
            </template>
          </template>
        </main>
        <TorrentDetailSidebar
          :torrent="torrent"
          :logged-in="loggedIn"
          :session-user-id="sessionUserId"
          :scraping="scraping"
          @download="downloadTorrent"
          @delete="deleteTorrent"
          @scrape="scrapeTorrent"
        />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
  import { ref, onMounted, computed } from "vue"
  import TorrentDetailContent from "~/components/torrent/TorrentDetailContent.vue"
  import TorrentDetailSidebar from "~/components/torrent/TorrentDetailSidebar.vue"
  import type { Torrent } from "~/models"

  const { executeHook } = useHooks()
  const { showAlert, showConfirm } = useModal()
  const route = useRoute()
  const session = useUserSession()

  type SessionUser = {
    id?: string
  }

  const torrent = ref<Torrent | null>(null)
  const loggedIn = computed(() => session.loggedIn.value)
  const sessionUserId = computed(
    () => (session.user.value as SessionUser | null)?.id,
  )
  const loading = ref(true)
  const error = ref<string | null>(null)
  const scraping = ref(false)

  const infoHash = computed(() => route.params.infoHash as string)

  type TorrentDetailBlock = {
    id: string
    component?: string
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    componentDef?: any
    priority?: number
  }

  // Blocs enregistrés par les plugins
  const pluginBlocks = ref<TorrentDetailBlock[]>([])

  // Slots unifiés : blocs plugins + sentinelle "torrent-detail" (priority 10)
  // priority < 10 → au-dessus du contenu
  // priority > 10 → en-dessous du contenu
  const sortedSlots = computed<TorrentDetailBlock[]>(() =>
    [{ id: "torrent-detail", priority: 10 }, ...pluginBlocks.value].sort(
      (a, b) => (a.priority ?? 10) - (b.priority ?? 10),
    ),
  )

  const registerBlock = (block: TorrentDetailBlock) => {
    if (pluginBlocks.value.find((b) => b.id === block.id)) return
    pluginBlocks.value.push(block)
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

    const confirmDelete = await showConfirm(
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
      await showAlert(
        `Torrent "${
          data.deletedTorrent?.name || torrent.value.name
        }" supprimé avec succès.`,
        "success",
      )
      await navigateTo("/torrents")
    } catch (e: any) {
      const errorMessage =
        e.message || "Erreur lors de la suppression du torrent"
      await showAlert(`Erreur: ${errorMessage}`, "error")
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

  const downloadTorrent = async () => {
    if (!torrent.value) return
    await executeHook("torrent:before-download", {
      infoHash: infoHash.value,
      torrent: torrent.value,
    })
    window.location.href = `/api/download-torrent/${infoHash.value}`
  }

  onMounted(async () => {
    await fetchTorrent()
    pluginBlocks.value = []
    await executeHook("page:torrent-detail", {
      page: "torrent-detail",
      infoHash: infoHash.value,
      torrent: torrent.value,
      registerBlock,
    })
  })
</script>
