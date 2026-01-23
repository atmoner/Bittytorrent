<template>
  <div class="container">
    <!-- Étape 1: Upload du fichier -->
    <form v-if="step === 1" @submit.prevent="uploadTorrent">
      <h2 class="text-2xl font-bold mb-6">Ajouter un torrent</h2>

      <div class="border-b border-white/10 pb-12">
        <div class="col-span-full">
          <label
            for="cover-photo"
            class="block text-sm/6 font-medium text-white"
            >Fichier .torrent</label
          >
          <div
            class="mt-2 flex justify-center rounded-lg border border-dashed px-6 py-10"
          >
            <div class="text-center">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
                class="mx-auto size-16 text-gray-600"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"
                />
              </svg>

              <div class="mt-4 flex text-sm/6 text-gray-400 justify-center">
                <label class="cursor-pointer">
                  <span class="text-blue-500 hover:text-blue-400"
                    >Choisir un fichier</span
                  >
                  <input
                    type="file"
                    @change="onFileChange"
                    accept=".torrent"
                    required
                    class="hidden"
                  />
                </label>
              </div>
              <p v-if="torrentFile" class="mt-2 text-sm text-gray-400">
                {{ torrentFile.name }}
              </p>
            </div>
          </div>
        </div>

        <!-- Sélecteur de catégorie -->
        <div v-if="torrentFile" class="mt-6">
          <label class="block text-sm/6 font-medium text-white mb-2">
            Catégorie (optionnel)
          </label>
          <select
            v-model="selectedCategoryId"
            class="w-full px-3 py-2 bg-white/10 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          >
            <option value="">Aucune catégorie</option>
            <option
              v-for="category in categories"
              :key="category._id"
              :value="category._id"
              class="text-gray-900"
            >
              {{ category.name }}
            </option>
          </select>
        </div>
      </div>
      <BittyUiButton
        v-if="torrentFile"
        type="submit"
        class="mt-4 w-full"
        :disabled="isUploading || !selectedCategoryId"
      >
        {{ isUploading ? "Upload en cours..." : "Uploader le torrent" }}
      </BittyUiButton>
      <span v-if="formError" class="error text-red-500 mt-2 block">{{
        formError
      }}</span>
    </form>

    <!-- Étape 2: Détails et édition -->
    <div v-if="step === 2" class="space-y-6">
      <h2 class="text-2xl font-bold">Détails du torrent</h2>

      <div class="bg-white/5 rounded-lg p-6 space-y-4">
        <div>
          <label class="block text-sm mb-2">Nom du torrent</label>
          <input
            v-model="name"
            type="text"
            class="w-full border px-4 py-2 bg-white/10 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>

        <div>
          <label class="block text-sm mb-2">Description (optionnel)</label>
          <textarea
            v-model="description"
            rows="4"
            class="w-full border px-4 py-2 bg-white/10 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            placeholder="Ajoutez une description pour ce torrent..."
          ></textarea>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm mb-1">InfoHash</label>
            <p class="text-sm text-gray-400 font-mono break-all">
              {{ infoHash }}
            </p>
          </div>
          <div>
            <label class="block text-sm mb-1">Taille</label>
            <p class="text-sm text-gray-400">
              {{ formatBytes(size || 0) }}
            </p>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm mb-1">Privé</label>
            <p class="text-sm text-gray-400">
              {{ isPrivate ? "Oui" : "Non" }}
            </p>
          </div>
          <div>
            <label class="block text-sm mb-1">Nombre de fichiers</label>
            <p class="text-sm text-gray-400">
              {{ torrentDetails?.files?.length || 1 }}
            </p>
          </div>
        </div>

        <div v-if="torrentDetails?.files && torrentDetails.files.length > 0">
          <label class="block text-sm mb-2">Fichiers inclus</label>
          <div class="max-h-48 overflow-y-auto space-y-2">
            <div
              v-for="(file, idx) in torrentDetails.files"
              :key="idx"
              class="text-sm text-gray-400 flex justify-between bg-white/5 rounded"
            >
              <span class="truncate">{{ file.name }}</span>
              <span class="text-gray-500">{{ formatBytes(file.length) }}</span>
            </div>
          </div>
        </div>

        <div v-if="torrentDetails?.announce">
          <label class="block text-sm mb-2"
            >Trackers ({{ torrentDetails.announce.length }})</label
          >
          <div
            class="max-h-64 overflow-y-auto border border-white/10 rounded-lg"
          >
            <table class="w-full text-sm">
              <thead class="bg-white/5 sticky top-0">
                <tr>
                  <th class="text-left px-3 py-2 text-gray-300 font-medium">
                    #
                  </th>
                  <th class="text-left px-3 py-2 text-gray-300 font-medium">
                    URL du Tracker
                  </th>
                  <th class="text-left px-3 py-2 text-gray-300 font-medium">
                    Type
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="(tracker, idx) in torrentDetails.announce"
                  :key="idx"
                  class="border-t border-white/5 hover:bg-white/5"
                >
                  <td class="px-3 py-2 text-gray-500">{{ Number(idx) + 1 }}</td>
                  <td
                    class="px-3 py-2 text-gray-400 break-all font-mono text-xs"
                  >
                    {{ tracker }}
                  </td>
                  <td class="px-3 py-2">
                    <span
                      class="px-2 py-1 rounded text-xs"
                      :class="{
                        'bg-blue-500/20 text-blue-400':
                          tracker.startsWith('udp://'),
                        'bg-green-500/20 text-green-400':
                          tracker.startsWith('http://'),
                        'bg-purple-500/20 text-purple-400':
                          tracker.startsWith('https://'),
                        'bg-orange-500/20 text-orange-400':
                          tracker.startsWith('wss://') ||
                          tracker.startsWith('ws://'),
                      }"
                    >
                      {{ tracker.split("://")[0].toUpperCase() }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="flex gap-4">
        <BittyUiButton @click="step = 1" class="flex-1" type="button">
          Retour
        </BittyUiButton>
        <BittyUiButton
          @click="confirmUpload"
          class="flex-1"
          type="button"
          :disabled="isConfirming"
        >
          {{ isConfirming ? "Confirmation..." : "Confirmer l'ajout" }}
        </BittyUiButton>
      </div>
      <span v-if="formError" class="error text-red-500 block text-center">{{
        formError
      }}</span>
    </div>

    <!-- Étape 3: Succès -->
    <div v-if="step === 3" class="text-center space-y-4">
      <svg
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
        stroke-width="1.5"
        stroke="currentColor"
        class="mx-auto size-16 text-green-500"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
        />
      </svg>
      <h2 class="text-2xl font-bold text-green-500">
        Torrent ajouté avec succès !
      </h2>
      <p class="text-gray-400">
        Votre torrent est maintenant disponible sur le tracker.
      </p>
      <div class="flex gap-4 justify-center mt-6">
        <NuxtLink to="/torrents">
          <BittyUiButton> Voir tous les torrents </BittyUiButton>
        </NuxtLink>
        <NuxtLink :to="`/torrent/${infoHash}`">
          <BittyUiButton> Voir ce torrent </BittyUiButton>
        </NuxtLink>
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
  import { ref } from "vue"
  import { formatBytes } from "~/utils/byteFormat"

  const step = ref(1)
  const name = ref("")
  const description = ref("")
  const infoHash = ref("")
  const isPrivate = ref(false)
  const size = ref<number | null>(null)
  const formError = ref("")
  const torrentFile = ref<File | null>(null)
  const torrentDetails = ref<any>(null)
  const isUploading = ref(false)
  const isConfirming = ref(false)
  const selectedCategoryId = ref("")
  const categories = ref()

  // Charger les catégories au montage du composant
  const loadCategories = async () => {
    try {
      const data = await $fetch("/api/categories")
      categories.value = data
    } catch (error) {
      console.error("Erreur lors du chargement des catégories:", error)
    }
  }

  onMounted(() => {
    loadCategories()
  })

  const onFileChange = (e: Event) => {
    const files = (e.target as HTMLInputElement).files
    torrentFile.value = files && files[0] ? files[0] : null
  }

  const uploadTorrent = async () => {
    formError.value = ""
    if (!torrentFile.value) {
      formError.value = "Veuillez sélectionner un fichier .torrent."
      return
    }

    isUploading.value = true
    try {
      const formData = new FormData()
      formData.append("torrent", torrentFile.value)

      // Ajouter la catégorie si sélectionnée
      if (selectedCategoryId.value) {
        formData.append("categoryId", selectedCategoryId.value)
      }

      const uploadRes = await fetch("/api/upload-torrent", {
        method: "POST",
        body: formData,
      })
      const uploadData = await uploadRes.json()
      if (!uploadRes.ok) {
        formError.value =
          uploadData.statusMessage || "Erreur lors de l'upload du fichier"
        return
      }
      // Stocker les détails du torrent
      torrentDetails.value = uploadData.detailTorrent

      // Pré-remplir les champs éditables
      name.value = uploadData.detailTorrent.name || ""
      infoHash.value = uploadData.detailTorrent.infoHash || ""
      isPrivate.value = uploadData.detailTorrent.private || false
      size.value = uploadData.detailTorrent.length || null

      // Passer à l'étape de détails/édition
      step.value = 2
    } catch (e: any) {
      formError.value = e.message || "Erreur lors de l'upload du fichier"
    } finally {
      isUploading.value = false
    }
  }

  const confirmUpload = async () => {
    formError.value = ""
    if (!name.value || !infoHash.value) {
      formError.value = "Le nom et l'infoHash sont requis."
      return
    }

    isConfirming.value = true
    try {
      // Lancer le scrape pour obtenir les stats initiales
      if (torrentDetails.value?.announce?.[0]) {
        await fetch(
          `/api/scrape?infoHash=${infoHash.value}&announce=${torrentDetails.value.announce[0]}`,
          { method: "GET" },
        )
      }

      // Passer à l'étape de succès
      step.value = 3
    } catch (e: any) {
      formError.value = e.message || "Erreur lors de la confirmation"
    } finally {
      isConfirming.value = false
    }
  }
</script>
<style scoped>
  .container {
    max-width: 1000px;
    margin: auto;
    padding: 2rem;
  }
</style>
