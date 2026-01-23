<template>
  <div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-8">
      <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Sidebar -->
        <aside class="lg:col-span-1">
          <div class="bg-white rounded-lg shadow-sm p-6 space-y-6">
            <!-- User Profile Card -->
            <div class="flex items-center gap-3 pb-6">
              <div
                class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-xl"
              >
                {{ userData?.username?.charAt(0).toUpperCase() }}
              </div>
              <div class="flex-1">
                <h3 class="font-semibold text-gray-900">
                  {{ userData?.username }}
                </h3>
                <p class="text-sm text-gray-500">{{ userData?.email }}</p>
              </div>
            </div>
            <!-- Quick Actions -->
            <div class="grid grid-cols-3 gap-3 pb-6">
              <button
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
              </button>
              <button
                class="flex flex-col items-center gap-2 p-3 rounded-lg bg-purple-50 text-purple-600 hover:bg-purple-100 transition"
              >
                <svg
                  class="w-5 h-5"
                  fill="currentColor"
                  viewBox="0 0 20 20"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <path
                    d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z"
                  ></path>
                </svg>
                <span class="text-xs font-medium">Favoris</span>
              </button>
              <button
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
              </button>
            </div>

            <!-- Navigation Menu -->
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
              <button
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
              </button>

              <button
                class="flex items-center gap-3 px-3 py-2 text-gray-700 hover:bg-gray-50 rounded-lg transition w-full"
              >
                <svg
                  class="w-5 h-5 text-gray-400"
                  fill="currentColor"
                  viewBox="0 0 20 20"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <path
                    d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z"
                  ></path>
                </svg>
                <span class="text-sm">Favoris</span>
              </button>
            </nav>

            <!-- Settings & Logout -->
            <div class="pt-6 space-y-1">
              <button
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
              </button>
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

        <!-- Main Content -->
        <main class="lg:col-span-3 space-y-6">
          <!-- Stats Cards -->
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg shadow-sm p-6">
              <div class="flex items-center gap-4">
                <div class="p-3 bg-gray-100 rounded-lg">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    class="size-6"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M12 9.75v6.75m0 0-3-3m3 3 3-3m-8.25 6a4.5 4.5 0 0 1-1.41-8.775 5.25 5.25 0 0 1 10.233-2.33 3 3 0 0 1 3.758 3.848A3.752 3.752 0 0 1 18 19.5H6.75Z"
                    />
                  </svg>
                </div>
                <div>
                  <p class="text-sm text-gray-500">Torrent envoyés</p>
                  <p class="text-2xl font-bold text-gray-900">
                    {{ userData?.totalUploadTorrents?.length ?? 0 }}
                  </p>
                </div>
              </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6">
              <div class="flex items-center gap-4">
                <div class="p-3 bg-gray-100 text-green-500 rounded-lg">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    class="size-6"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M4.5 10.5 12 3m0 0 7.5 7.5M12 3v18"
                    />
                  </svg>
                </div>
                <div>
                  <p class="text-sm text-gray-500">Données uploadées</p>
                  <p class="text-2xl font-bold text-gray-900">
                    {{
                      userData?.uploaded ? formatBytes(userData.uploaded) : "-"
                    }}
                  </p>
                </div>
              </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6">
              <div class="flex items-center gap-4">
                <div class="p-3 bg-gray-100 text-red-500 rounded-lg">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    class="size-6"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M19.5 13.5 12 21m0 0-7.5-7.5M12 21V3"
                    />
                  </svg>
                </div>
                <div>
                  <p class="text-sm text-gray-500">Données téléchargées</p>
                  <p class="text-2xl font-bold text-gray-900">
                    {{
                      userData?.downloaded
                        ? formatBytes(userData.downloaded)
                        : "-"
                    }}
                  </p>
                </div>
              </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6">
              <div class="flex items-center gap-4">
                <div class="p-3 bg-gray-100 rounded-lg">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    class="size-6"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M3 7.5 7.5 3m0 0L12 7.5M7.5 3v13.5m13.5 0L16.5 21m0 0L12 16.5m4.5 4.5V7.5"
                    />
                  </svg>
                </div>
                <div>
                  <p class="text-sm text-gray-500">Ratio</p>
                  <p class="text-2xl font-bold text-gray-900">
                    {{ userData?.ratio }}
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- Account Data Section -->
          <div class="bg-white rounded-lg shadow-sm">
            <div class="p-6">
              <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left: User Info -->
                <div class="lg:col-span-2 space-y-6">
                  <div class="flex items-start gap-4">
                    <div
                      class="w-16 h-16 rounded-lg bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-2xl flex-shrink-0"
                    >
                      {{ user?.username?.charAt(0).toUpperCase() }}
                    </div>
                    <div>
                      <span
                        class="inline-block px-3 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full mb-2"
                      >
                        {{ user?.role === "admin" ? "Admin" : "Utilisateur" }}
                      </span>
                      <h3 class="text-xl font-bold text-gray-900">
                        {{ user?.username }}
                      </h3>
                    </div>
                  </div>

                  <div class="space-y-4">
                    <div>
                      <h4 class="text-sm font-semibold text-gray-900 mb-1">
                        Adresse email
                      </h4>
                      <p class="text-gray-600">{{ user?.email }}</p>
                    </div>
                    <div>
                      <h4 class="text-sm font-semibold text-gray-900 mb-1">
                        Pid
                      </h4>
                      <p
                        class="text-gray-600 font-mono text-sm break-all bg-gray-50 p-2 rounded"
                      >
                        {{ userData?.pid }}
                      </p>
                    </div>
                    <div>
                      <h4 class="text-sm font-semibold text-gray-900 mb-1">
                        Membre depuis
                      </h4>
                      <p class="text-gray-600">
                        {{
                          user?.createdAt
                            ? formatDate(user.createdAt)
                            : "Date inconnue"
                        }}
                      </p>
                    </div>
                    <div>
                      <h4 class="text-sm font-semibold text-gray-900 mb-1">
                        Dernière activité
                      </h4>
                      <p class="text-gray-600">
                        {{
                          userData?.lastActivity
                            ? formatDate(userData.lastActivity)
                            : "Date inconnue"
                        }}
                      </p>
                    </div>
                  </div>
                </div>

                <!-- Right: Subscription Card (placeholder) -->
                <div class="lg:col-span-1">
                  <div
                    class="bg-gradient-to-br from-blue-50 to-purple-50 rounded-lg border border-blue-200 p-6"
                  >
                    <div class="flex items-center gap-3 mb-4">
                      <p class="text-2xl font-bold text-gray-900">Basic</p>
                      <span
                        class="inline-block px-2 py-0.5 bg-blue-600 text-white text-xs font-medium rounded"
                      >
                        Free
                      </span>
                    </div>
                    <hr class="border-gray-800 mb-4" />

                    <div class="space-y-2">
                      <p class="text-sm font-semibold text-gray-900">
                        Avantages
                      </p>
                      <div class="flex items-start gap-2 text-sm text-gray-700">
                        <svg
                          class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5"
                          fill="currentColor"
                          viewBox="0 0 20 20"
                          xmlns="http://www.w3.org/2000/svg"
                        >
                          <path
                            fill-rule="evenodd"
                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                            clip-rule="evenodd"
                          ></path>
                        </svg>
                        <span>Téléchargements opensource</span>
                      </div>
                      <div class="flex items-start gap-2 text-sm text-gray-700">
                        <svg
                          class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5"
                          fill="currentColor"
                          viewBox="0 0 20 20"
                          xmlns="http://www.w3.org/2000/svg"
                        >
                          <path
                            fill-rule="evenodd"
                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                            clip-rule="evenodd"
                          ></path>
                        </svg>
                        <span>Support communautaire</span>
                      </div>
                      <div class="flex items-start gap-2 text-sm text-red-700">
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          fill="none"
                          viewBox="0 0 24 24"
                          stroke="currentColor"
                          class="size-6"
                        >
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M6 18 18 6M6 6l12 12"
                          />
                        </svg>

                        <span>Ratio illimités</span>
                      </div>
                      <div class="flex items-start gap-2 text-sm text-red-700">
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          fill="none"
                          viewBox="0 0 24 24"
                          stroke="currentColor"
                          class="size-6"
                        >
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M6 18 18 6M6 6l12 12"
                          />
                        </svg>

                        <span>Accès à tous les torrents</span>
                      </div>
                      <BittyUiButton
                        variant="primary"
                        class="w-full rounded-lg mt-4"
                        >Passer à Premium</BittyUiButton
                      >
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Active Downloads (placeholder) -->
          <div class="bg-white rounded-lg shadow-sm">
            <div class="p-6">
              <h2 class="text-xl font-bold text-gray-900 mb-6">
                Mes torrents envoyés
              </h2>

              <div
                v-if="
                  !userData?.totalUploadTorrents ||
                  userData.totalUploadTorrents.length === 0
                "
                class="text-center py-12"
              >
                <div class="text-gray-400 mb-2">
                  <svg
                    class="w-12 h-12 mx-auto"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                    />
                  </svg>
                </div>
                <p class="text-gray-500">
                  Aucun téléchargement actif pour le moment
                </p>
              </div>

              <div v-else class="overflow-x-auto">
                <table class="w-full">
                  <thead>
                    <tr class="border-b border-gray-200">
                      <th
                        class="text-left py-3 px-4 font-semibold text-gray-700"
                      >
                        Nom du torrent
                      </th>
                      <th
                        class="text-left py-3 px-4 font-semibold text-gray-700"
                      >
                        Statut
                      </th>

                      <th
                        class="text-left py-3 px-4 font-semibold text-gray-700"
                      >
                        Seeds
                      </th>
                      <th
                        class="text-left py-3 px-4 font-semibold text-gray-700"
                      >
                        Leechs
                      </th>
                      <th
                        class="text-left py-3 px-4 font-semibold text-gray-700"
                      >
                        Actions
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr
                      v-for="torrentId in userData?.totalUploadTorrents"
                      :key="torrentId._id"
                      class="border-b border-gray-100 hover:bg-gray-50"
                    >
                      <td class="py-3 px-4">
                        <div class="font-medium text-gray-900">
                          Torrent {{ torrentId.name }}
                        </div>
                        <div class="text-sm text-gray-500">
                          ID: {{ torrentId.infoHash }}
                        </div>
                      </td>
                      <td class="py-3 px-4">
                        <span
                          class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800"
                        >
                          Actif
                        </span>
                      </td>
                      <!--                      <td class="py-3 px-4">
                        <div class="w-full bg-gray-200 rounded-full h-2">
                          <div
                            class="bg-blue-600 h-2 rounded-full"
                            style="width: 45%"
                          ></div>
                        </div>
                        <span class="text-sm text-gray-500 mt-1">45%</span>
                      </td> -->
                      <td class="py-3 px-4 text-sm text-gray-600">
                        <p class="text-green-600">↑ {{ torrentId.seeds }}</p>
                      </td>
                      <td class="py-3 px-4 text-sm text-gray-600">
                        <p class="text-red-600">↓ {{ torrentId.leechs }}</p>
                      </td>
                      <td class="py-3 px-4">
                        <div class="flex items-center gap-2">
                          <NuxtLink :to="'/torrent/' + torrentId.infoHash"
                            >Voir</NuxtLink
                          >
                          <!--                           <button
                            class="text-red-600 hover:text-red-800 text-sm"
                          >
                            Supprimer
                          </button> -->
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </main>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
  import { formatDate } from "~/utils/dateFormat"
  import { formatBytes } from "~/utils/byteFormat"
  import type { User } from "~/models"

  const { user, loggedIn } = useUserSession()
  const userData = ref<User | null>(null)

  onMounted(async () => {
    if (!loggedIn.value) {
      navigateTo("/login")
    }
    await fetchUser()
  })

  const fetchUser = async () => {
    try {
      const res = await fetch("/api/user-stats")
      const data = await res.json()
      userData.value = data.data
    } catch (e) {}
  }
</script>
