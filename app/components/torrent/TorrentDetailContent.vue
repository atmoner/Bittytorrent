<template>
  <div class="space-y-6">
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

    <div class="bg-white rounded-lg shadow-sm">
      <div class="p-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
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
                    torrent.created ? formatDate(torrent.created) : "Inconnue"
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
              <span class="text-gray-700 text-sm">{{ torrent.name }}</span>
            </div>
            <span class="text-gray-500 text-sm">{{
              formatSize(torrent.length)
            }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
  import type { Torrent } from "~/models"
  import { formatDate } from "~/utils/dateFormat"

  defineProps<{
    torrent: Torrent
  }>()

  const formatSize = (bytes: number): string => {
    if (!bytes || bytes === 0) return "0 B"
    const k = 1024
    const sizes = ["B", "KB", "MB", "GB", "TB"]
    const i = Math.floor(Math.log(bytes) / Math.log(k))
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + " " + sizes[i]
  }
</script>
