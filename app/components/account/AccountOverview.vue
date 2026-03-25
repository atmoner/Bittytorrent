<template>
  <main class="lg:col-span-3 space-y-6">
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
              {{ userData?.uploaded ? formatBytes(userData.uploaded) : "-" }}
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
                userData?.downloaded ? formatBytes(userData.downloaded) : "-"
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

    <div class="bg-white rounded-lg shadow-sm">
      <div class="p-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <div class="lg:col-span-2 space-y-6">
            <div class="flex items-start gap-4">
              <div
                class="w-16 h-16 rounded-lg bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-2xl flex-shrink-0"
              >
                {{ sessionUser?.username?.charAt(0).toUpperCase() }}
              </div>
              <div>
                <span
                  class="inline-block px-3 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full mb-2"
                >
                  {{ sessionUser?.role === "admin" ? "Admin" : "Utilisateur" }}
                </span>
                <h3 class="text-xl font-bold text-gray-900">
                  {{ sessionUser?.username }}
                </h3>
              </div>
            </div>

            <div class="space-y-4">
              <div>
                <h4 class="text-sm font-semibold text-gray-900 mb-1">
                  Adresse email
                </h4>
                <p class="text-gray-600">{{ sessionUser?.email }}</p>
              </div>
              <div>
                <h4 class="text-sm font-semibold text-gray-900 mb-1">Pid</h4>
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
                    sessionUser?.createdAt
                      ? formatDate(sessionUser.createdAt)
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

          <div class="lg:col-span-1">
            <div class="rounded-lg border border-blue-200 p-6">
              <div class="flex items-center gap-3 mb-4">
                <p class="text-2xl font-bold text-gray-900">Basic</p>
                <span
                  class="inline-block px-2 py-0.5 bg-blue-600 text-white text-xs font-medium rounded"
                  >Free</span
                >
              </div>
              <hr class="border-gray-800 mb-4" />

              <div class="space-y-2">
                <p class="text-sm font-semibold text-gray-900">Avantages</p>
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
                <BittyUiButton variant="primary" class="w-full rounded-lg mt-4"
                  >Passer à Premium</BittyUiButton
                >
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

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
          <p class="text-gray-500">Aucun téléchargement actif pour le moment</p>
        </div>

        <div v-else class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="border-b border-gray-200">
                <th class="text-left py-3 px-4 font-semibold text-gray-700">
                  Nom du torrent
                </th>
                <th class="text-left py-3 px-4 font-semibold text-gray-700">
                  Statut
                </th>
                <th class="text-left py-3 px-4 font-semibold text-gray-700">
                  Seeds
                </th>
                <th class="text-left py-3 px-4 font-semibold text-gray-700">
                  Leechs
                </th>
                <th class="text-left py-3 px-4 font-semibold text-gray-700">
                  Actions
                </th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="torrentId in userData?.totalUploadTorrents"
                :key="torrentId._id"
                class="border-b border-gray-100"
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
                    >Actif</span
                  >
                </td>
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
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>
</template>

<script setup lang="ts">
  import type { User } from "~/models"

  type SessionUser = {
    username?: string
    email?: string
    createdAt?: Date
    role?: "admin" | "user"
  }

  defineProps<{
    userData: User | null
    sessionUser: SessionUser | null
    formatDate: (date: Date) => string
    formatBytes: (bytes: number) => string
  }>()
</script>
