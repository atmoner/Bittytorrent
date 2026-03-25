<template>
  <div class="torrents-list">
    <h2 class="text-2xl font-semibold text-gray-900 mb-6">Derniers Torrents</h2>

    <!-- Loading state -->
    <div v-if="pending" class="text-center py-8">
      <div
        class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-500 mx-auto"
      ></div>
      <p class="text-gray-600 mt-2">Chargement des torrents...</p>
    </div>

    <!-- Error state -->
    <div v-else-if="error" class="text-center py-8">
      <p class="text-red-600">Erreur lors du chargement des torrents</p>
    </div>

    <!-- Empty state -->
    <div
      v-else-if="!torrents || torrents.length === 0"
      class="text-center py-8"
    >
      <p class="text-gray-600">Aucun torrent disponible pour le moment</p>
    </div>

    <!-- Torrents grid -->
    <div v-else class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
      <div
        v-for="torrent in torrents"
        :key="torrent.infoHash"
        class="bg-white border border-indigo-500 rounded-lg shadow-md p-4 hover:shadow-lg transition-shadow"
      >
        <h3 class="font-medium text-gray-900 mb-2 line-clamp-2">
          {{ torrent.name }}
        </h3>

        <div class="text-sm text-gray-600 space-y-1">
          <div class="flex justify-between">
            <span>Taille:</span>
            <span class="font-medium">{{ formatBytes(torrent.size) }}</span>
          </div>

          <div class="flex justify-between">
            <span>Seeds:</span>
            <span class="font-medium text-green-600">{{
              torrent.seeds || 0
            }}</span>
          </div>

          <div class="flex justify-between">
            <span>Leeches:</span>
            <span class="font-medium text-red-600">{{
              torrent.leechs || 0
            }}</span>
          </div>

          <div class="flex justify-between">
            <span>Ajouté:</span>
            <span class="font-medium">{{ formatDate(torrent.created) }}</span>
          </div>
        </div>

        <div class="mt-3 flex gap-2">
          <NuxtLink
            :to="`/torrent/${torrent.infoHash}`"
            class="flex-1 bg-indigo-600 text-white text-center py-2 px-3 rounded text-sm hover:bg-indigo-700 transition-colors"
          >
            Voir détails
          </NuxtLink>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
  import type { Torrent } from "~/models"
  import { formatDate } from "~/utils/dateFormat"
  import { formatBytes } from "~/utils/byteFormat"

  // Récupérer les torrents depuis l'API
  const {
    data: torrents,
    pending,
    error,
  } = await useFetch<Torrent[]>("/api/torrents", {
    query: { limit: 6 }, // Limiter à 6 torrents pour la page d'accueil
  })
</script>

<style scoped>
  .line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }
</style>
