<template>
  <aside class="lg:col-span-1">
    <div class="bg-white rounded-lg shadow-sm p-6 space-y-6">
      <div class="flex items-center gap-3 pb-6">
        <div
          class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-xl"
        >
          {{ userData?.username?.charAt(0).toUpperCase() }}
        </div>
        <div class="flex-1">
          <h3 class="font-semibold text-gray-900">{{ userData?.username }}</h3>
          <p class="text-sm text-gray-500">{{ userData?.email }}</p>
        </div>
      </div>

      <div class="grid grid-cols-3 gap-3 pb-6">
        <NuxtLink
          to="/account/profile"
          class="flex flex-col items-center gap-2 p-3 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition"
        >
          <svg
            class="w-5 h-5"
            fill="currentColor"
            viewBox="0 0 20 20"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path
              fill-rule="evenodd"
              d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
              clip-rule="evenodd"
            ></path>
          </svg>
          <span class="text-xs font-medium">Profil</span>
        </NuxtLink>
        <NuxtLink
          to="/account/favorites"
          class="flex flex-col items-center gap-2 p-3 rounded-lg bg-purple-50 text-purple-600 hover:bg-purple-100 transition"
        >
          <svg
            class="w-5 h-5"
            fill="currentColor"
            viewBox="0 0 20 20"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z"></path>
          </svg>
          <span class="text-xs font-medium">Favoris</span>
        </NuxtLink>
        <NuxtLink
          to="/account/wallet"
          class="flex flex-col items-center gap-2 p-3 rounded-lg bg-teal-50 text-teal-600 hover:bg-teal-100 transition"
        >
          <svg
            class="w-5 h-5"
            fill="currentColor"
            viewBox="0 0 20 20"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"></path>
            <path
              fill-rule="evenodd"
              d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"
              clip-rule="evenodd"
            ></path>
          </svg>
          <span class="text-xs font-medium">Wallet</span>
        </NuxtLink>
      </div>

      <nav class="space-y-1">
        <NuxtLink
          to="/torrents"
          class="flex items-center gap-3 px-3 py-2 text-gray-700 hover:bg-gray-50 rounded-lg transition"
        >
          <svg
            class="w-5 h-5 text-gray-400"
            fill="currentColor"
            viewBox="0 0 20 20"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path
              d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"
            ></path>
          </svg>
          <span class="text-sm">Mes torrents</span>
        </NuxtLink>
        <NuxtLink
          to="/account/reviews"
          class="flex items-center gap-3 px-3 py-2 text-gray-700 hover:bg-gray-50 rounded-lg transition w-full"
        >
          <svg
            class="w-5 h-5 text-gray-400"
            fill="currentColor"
            viewBox="0 0 20 20"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path
              d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
            ></path>
          </svg>
          <span class="text-sm">Avis</span>
        </NuxtLink>
        <NuxtLink
          to="/account/favorites"
          class="flex items-center gap-3 px-3 py-2 text-gray-700 hover:bg-gray-50 rounded-lg transition w-full"
        >
          <svg
            class="w-5 h-5 text-gray-400"
            fill="currentColor"
            viewBox="0 0 20 20"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z"></path>
          </svg>
          <span class="text-sm">Favoris</span>
        </NuxtLink>
      </nav>

      <div class="pt-6 space-y-1">
        <NuxtLink
          to="/account/settings"
          class="flex items-center gap-3 px-3 py-2 text-gray-700 hover:bg-gray-50 rounded-lg transition w-full"
        >
          <svg
            class="w-5 h-5 text-gray-400"
            fill="currentColor"
            viewBox="0 0 20 20"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path
              fill-rule="evenodd"
              d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z"
              clip-rule="evenodd"
            ></path>
          </svg>
          <span class="text-sm">Paramètres</span>
        </NuxtLink>
        <NuxtLink
          to="/logout"
          class="flex items-center gap-3 px-3 py-2 text-red-600 hover:bg-red-50 rounded-lg transition"
        >
          <svg
            class="w-5 h-5"
            fill="currentColor"
            viewBox="0 0 20 20"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path
              fill-rule="evenodd"
              d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z"
              clip-rule="evenodd"
            ></path>
          </svg>
          <span class="text-sm">Déconnexion</span>
        </NuxtLink>
      </div>
    </div>
  </aside>
</template>

<script setup lang="ts">
  import type { User } from "~/models"

  defineProps<{
    userData: User | null
  }>()
</script>
