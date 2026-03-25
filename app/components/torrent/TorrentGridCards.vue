<template>
  <div>
    <div
      v-if="torrents.length > 0"
      class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6"
    >
      <div
        v-for="torrent in torrents"
        :key="torrent._id"
        class="group border border-gray-200 rounded-2xl p-6 hover:bg-gray-50 hover:border-blue-300 transition-all duration-300 hover:shadow-xl shadow-sm"
      >
        <div class="flex items-start justify-between mb-4">
          <div class="flex-1 min-w-0">
            <h3
              class="text-lg font-bold text-gray-900 truncate group-hover:text-blue-600 transition-colors mb-2"
              :title="torrent.name"
            >
              {{ torrent.name }}
            </h3>
            <div class="flex items-center gap-2 mb-2">
              <p
                class="text-xs text-gray-500 font-mono truncate flex-1"
                :title="torrent.infoHash"
              >
                {{ torrent.infoHash.substring(0, 16) }}...
              </p>
              <div v-if="torrent.categoryId" class="shrink-0">
                <span
                  class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium text-white"
                  :style="{
                    backgroundColor: getCategoryColor(torrent.categoryId),
                  }"
                  :title="getCategoryName(torrent.categoryId)"
                >
                  {{ getCategoryName(torrent.categoryId) }}
                </span>
              </div>
            </div>
          </div>
          <div v-if="torrent.private" class="ml-2 shrink-0">
            <span
              class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-amber-500/20 text-amber-400 border border-amber-500/30"
            >
              <svg
                class="w-3 h-3 mr-1"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                />
              </svg>
              Privé
            </span>
          </div>
        </div>

        <div class="flex items-center gap-4 mb-4">
          <div
            class="flex items-center gap-2 bg-green-500/10 px-3 py-2 rounded-lg border border-green-500/20"
          >
            <svg
              class="w-4 h-4 text-green-400"
              xmlns="http://www.w3.org/2000/svg"
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
            <span class="text-green-400 font-bold">{{ torrent.seeds }}</span>
            <span class="text-green-400/70 text-xs">seeds</span>
          </div>
          <div
            class="flex items-center gap-2 bg-red-500/10 px-3 py-2 rounded-lg border border-red-500/20"
          >
            <svg
              class="w-4 h-4 text-red-400"
              xmlns="http://www.w3.org/2000/svg"
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
            <span class="text-red-400 font-bold">{{ torrent.leechs }}</span>
            <span class="text-red-400/70 text-xs">leechs</span>
          </div>
        </div>

        <div class="flex items-center gap-4 text-sm text-gray-600 mb-5">
          <div class="flex items-center gap-1">
            <svg
              class="w-4 h-4"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
              />
            </svg>
            <span>{{ formatDate(torrent.created) }}</span>
          </div>
          <div v-if="torrent.length" class="flex items-center gap-1">
            <svg
              class="w-4 h-4"
              xmlns="http://www.w3.org/2000/svg"
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
            <span>{{ formatSize(torrent.length) }}</span>
          </div>
        </div>

        <div class="flex items-center gap-3">
          <NuxtLink
            :to="`/torrent/${torrent.infoHash}`"
            class="flex-1 flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-500 text-white px-4 py-2.5 rounded-xl font-medium transition-all duration-200 hover:shadow-lg hover:shadow-blue-500/25"
          >
            <svg
              class="w-4 h-4"
              xmlns="http://www.w3.org/2000/svg"
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
            Détails
          </NuxtLink>
          <button
            v-if="loggedIn"
            @click="
              $emit('update', torrent.infoHash, torrent.announce[0] || '')
            "
            :disabled="scrapingHash === torrent.infoHash"
            class="flex items-center justify-center w-11 h-11 bg-gray-100 hover:bg-gray-200 text-gray-700 hover:text-gray-900 rounded-xl transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed border border-gray-300"
            title="Actualiser les stats"
          >
            <svg
              :class="[
                'w-5 h-5',
                scrapingHash === torrent.infoHash ? 'animate-spin' : '',
              ]"
              xmlns="http://www.w3.org/2000/svg"
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
      </div>
    </div>

    <div v-else class="flex flex-col items-center justify-center py-20">
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
  </div>
</template>

<script setup lang="ts">
  import type { Torrent } from "~/models"

  defineEmits<{
    update: [infoHash: string, announce: string]
  }>()

  defineProps<{
    torrents: Torrent[]
    loggedIn: boolean
    scrapingHash: string | null
    searchQuery: string
    getCategoryColor: (categoryId: string) => string
    getCategoryName: (categoryId: string) => string
    formatDate: (date: Date) => string
    formatSize: (bytes: number) => string
  }>()
</script>
