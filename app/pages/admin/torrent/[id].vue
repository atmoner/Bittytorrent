<template>
  <div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-8">
      <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold">Édition du torrent</h1>
        <NuxtLink
          to="/admin/torrents"
          class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700"
        >
          Retour aux torrents
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

      <div v-else-if="torrent" class="bg-white rounded-lg shadow p-6">
        <form @submit.prevent="saveTorrent" class="space-y-6">
          <!-- Informations générales -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Nom -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Nom du torrent
              </label>
              <input
                v-model="editForm.name"
                type="text"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>

            <!-- Catégorie -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Catégorie
              </label>
              <select
                v-model="editForm.categoryId"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                <option value="">Aucune catégorie</option>
                <option
                  v-for="category in categories"
                  :key="category._id"
                  :value="category._id"
                >
                  {{ category.name }}
                </option>
              </select>
            </div>

            <!-- Seeds -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Seeds
              </label>
              <input
                v-model.number="editForm.seeds"
                type="number"
                min="0"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>

            <!-- Leechs -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Leechers
              </label>
              <input
                v-model.number="editForm.leechs"
                type="number"
                min="0"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>

            <!-- Privé -->
            <div class="md:col-span-2">
              <label class="flex items-center">
                <input
                  v-model="editForm.private"
                  type="checkbox"
                  class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                />
                <span class="ml-2 text-sm font-medium text-gray-700">
                  Torrent privé
                </span>
              </label>
            </div>
          </div>

          <!-- Trackers -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Trackers (un par ligne)
            </label>
            <textarea
              v-model="announceText"
              rows="4"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="http://tracker1.example.com:8080/announce&#10;udp://tracker2.example.com:1337/announce"
            ></textarea>
            <p class="text-xs text-gray-500 mt-1">
              Entrez chaque URL de tracker sur une ligne séparée
            </p>
          </div>

          <!-- URLs Web -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              URLs Web (un par ligne)
            </label>
            <textarea
              v-model="urlListText"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="http://example.com/path/&#10;http://mirror.example.com/path/"
            ></textarea>
            <p class="text-xs text-gray-500 mt-1">
              URLs pour téléchargement direct (optionnel)
            </p>
          </div>

          <!-- Informations en lecture seule -->
          <div class="border-t pt-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
              Informations du torrent
            </h3>
            <div
              class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 text-sm"
            >
              <div>
                <span class="font-medium text-gray-700">InfoHash:</span>
                <p class="font-mono text-xs text-gray-600 break-all">
                  {{ torrent.infoHash }}
                </p>
              </div>
              <div>
                <span class="font-medium text-gray-700">Taille:</span>
                <p class="text-gray-600">
                  {{ formatSize(torrent.length || 0) }}
                </p>
              </div>
              <div>
                <span class="font-medium text-gray-700">Fichiers:</span>
                <p class="text-gray-600">{{ torrent.files?.length || 0 }}</p>
              </div>
              <div>
                <span class="font-medium text-gray-700">Créé le:</span>
                <p class="text-gray-600">{{ formatDate(torrent.created) }}</p>
              </div>
              <div>
                <span class="font-medium text-gray-700"
                  >Taille des pièces:</span
                >
                <p class="text-gray-600">
                  {{ formatSize(torrent.pieceLength || 0) }}
                </p>
              </div>
              <div>
                <span class="font-medium text-gray-700">Nombre de pièces:</span>
                <p class="text-gray-600">{{ torrent.pieces?.length || 0 }}</p>
              </div>
            </div>
          </div>

          <!-- Liste des fichiers -->
          <div
            v-if="torrent.files && torrent.files.length > 0"
            class="border-t pt-6"
          >
            <h3 class="text-lg font-medium text-gray-900 mb-4">
              Fichiers ({{ torrent.files.length }})
            </h3>
            <div class="max-h-64 overflow-y-auto border rounded-md">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th
                      class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase"
                    >
                      Fichier
                    </th>
                    <th
                      class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase"
                    >
                      Taille
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="file in torrent.files" :key="file.path">
                    <td class="px-4 py-2 text-sm text-gray-900">
                      {{ file.path }}
                    </td>
                    <td class="px-4 py-2 text-sm text-gray-600">
                      {{ formatSize(file.length) }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex justify-between pt-6 border-t">
            <button
              type="button"
              @click="deleteTorrent"
              class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500"
            >
              Supprimer le torrent
            </button>
            <div class="flex gap-3">
              <NuxtLink
                to="/admin/torrents"
                class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500"
              >
                Annuler
              </NuxtLink>
              <button
                type="submit"
                :disabled="saving"
                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50"
              >
                {{ saving ? "Sauvegarde..." : "Sauvegarder" }}
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
  import type { Torrent, Category } from "~/models"
  import { formatDate } from "~/utils/dateFormat"

  const route = useRoute()
  const session = useUserSession()
  const { executeHook } = useHooks()
  const { showAlert, showConfirm } = useModal()

  const torrent = ref<Torrent | null>(null)
  const categories = ref<Category[]>([])
  const loading = ref(true)
  const saving = ref(false)
  const error = ref("")

  const editForm = ref({
    name: "",
    categoryId: "",
    seeds: 0,
    leechs: 0,
    private: false,
  })

  const announceText = ref("")
  const urlListText = ref("")

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
      page: "admin-torrent-detail",
      torrentId: route.params.id,
    })
    await Promise.all([loadTorrent(), loadCategories()])
  })

  async function loadTorrent() {
    try {
      loading.value = true
      const torrentId = route.params.id as string

      // Récupérer le torrent depuis l'API torrents
      const allTorrents = await $fetch<Torrent[]>("/api/torrents")
      const foundTorrent = allTorrents.find((t) => t._id === torrentId)

      if (!foundTorrent) {
        error.value = "Torrent non trouvé"
        return
      }

      torrent.value = foundTorrent

      // Remplir le formulaire
      editForm.value = {
        name: foundTorrent.name,
        categoryId: foundTorrent.categoryId || "",
        seeds: foundTorrent.seeds || 0,
        leechs: foundTorrent.leechs || 0,
        private: foundTorrent.private || false,
      }

      // Convertir les arrays en texte
      announceText.value = (foundTorrent.announce || []).join("\n")
      urlListText.value = (foundTorrent.urlList || []).join("\n")
    } catch (e: any) {
      error.value =
        e.data?.statusMessage || "Erreur lors du chargement du torrent"
    } finally {
      loading.value = false
    }
  }

  async function loadCategories() {
    try {
      const response = await $fetch<Category[]>("/api/admin/categories")
      categories.value = response || []
    } catch (e: any) {
      console.error("Erreur lors du chargement des catégories:", e)
    }
  }

  async function saveTorrent() {
    if (!torrent.value?._id) return

    try {
      saving.value = true

      // Convertir le texte en arrays
      const announce = announceText.value
        .split("\n")
        .map((line) => line.trim())
        .filter((line) => line.length > 0)

      const urlList = urlListText.value
        .split("\n")
        .map((line) => line.trim())
        .filter((line) => line.length > 0)

      await $fetch("/api/admin/torrent-update", {
        method: "POST",
        body: {
          torrentId: torrent.value._id,
          ...editForm.value,
          announce,
          urlList,
        },
      })

      // Recharger le torrent pour afficher les nouvelles données
      await loadTorrent()

      // Message de succès
      await showAlert("Torrent mis à jour avec succès", "success")
    } catch (e: any) {
      error.value = e.data?.statusMessage || "Erreur lors de la sauvegarde"
    } finally {
      saving.value = false
    }
  }

  async function deleteTorrent() {
    if (!torrent.value?._id) return

    if (
      !(await showConfirm(
        "Êtes-vous sûr de vouloir supprimer ce torrent ? Cette action est irréversible.",
      ))
    ) {
      return
    }

    try {
      await $fetch("/api/admin/torrent-delete", {
        method: "POST",
        body: { torrentId: torrent.value._id },
      })

      // Rediriger vers la liste des torrents
      navigateTo("/admin/torrents")
    } catch (e: any) {
      error.value = e.data?.statusMessage || "Erreur lors de la suppression"
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
