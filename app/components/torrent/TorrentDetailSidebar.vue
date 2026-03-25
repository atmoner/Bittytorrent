<template>
  <aside class="lg:col-span-1">
    <div class="bg-white rounded-lg shadow-sm p-6 space-y-6">
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

      <div class="space-y-2">
        <button
          @click="$emit('download')"
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

        <button
          v-if="torrent.uploadedBy === sessionUserId"
          @click="$emit('delete')"
          class="flex cursor-pointer items-center justify-center gap-2 w-full px-4 py-2 bg-red-100 text-red-500 rounded-lg hover:bg-red-200 transition"
        >
          <span>Effacer</span>
        </button>

        <button
          v-if="loggedIn"
          @click="$emit('scrape')"
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
</template>

<script setup lang="ts">
  import type { Torrent } from "~/models"

  defineEmits<{
    download: []
    delete: []
    scrape: []
  }>()

  defineProps<{
    torrent: Torrent
    loggedIn: boolean
    sessionUserId?: string
    scraping: boolean
  }>()
</script>
