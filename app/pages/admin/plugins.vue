<template>
  <div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-8">
      <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold">Gestion des plugins</h1>
        <NuxtLink
          to="/admin"
          class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 transition-colors"
        >
          Retour à l'admin
        </NuxtLink>
      </div>

      <!-- Chargement -->
      <div v-if="loading" class="text-center py-12">
        <svg class="animate-spin h-8 w-8 text-indigo-500 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
        </svg>
        <p class="mt-4 text-gray-500">Chargement des plugins...</p>
      </div>

      <!-- Message de succès -->
      <div
        v-if="successMessage"
        class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded"
      >
        {{ successMessage }}
      </div>

      <!-- Message d'erreur -->
      <div
        v-if="errorMessage"
        class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded"
      >
        {{ errorMessage }}
      </div>

      <!-- Grille des plugins -->
      <div v-if="!loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="plugin in allPlugins"
          :key="plugin.id"
          class="bg-white rounded-lg shadow-md overflow-hidden"
          :class="plugin.active ? 'border-l-4 border-green-500' : 'border-l-4 border-gray-300'"
        >
          <div class="p-6">
            <!-- En-tête du plugin -->
            <div class="flex items-start justify-between mb-3">
              <div>
                <h2 class="text-lg font-semibold text-gray-900">{{ plugin.name }}</h2>
                <span class="text-xs text-gray-400">v{{ plugin.version }}</span>
              </div>
              <!-- Badge actif/inactif -->
              <span
                class="px-2 py-1 text-xs font-semibold rounded-full"
                :class="plugin.active
                  ? 'bg-green-100 text-green-800'
                  : 'bg-gray-100 text-gray-600'"
              >
                {{ plugin.active ? 'Actif' : 'Inactif' }}
              </span>
            </div>

            <!-- Description -->
            <p class="text-sm text-gray-600 mb-4">{{ plugin.description }}</p>

            <!-- Auteur -->
            <p class="text-xs text-gray-400 mb-4">
              Par
              <a
                v-if="plugin.authorUrl"
                :href="plugin.authorUrl"
                target="_blank"
                rel="noopener noreferrer"
                class="text-indigo-500 hover:underline"
              >
                {{ plugin.author }}
              </a>
              <span v-else>{{ plugin.author }}</span>
            </p>

            <!-- Hooks enregistrés -->
            <div v-if="plugin.hooks" class="mb-4">
              <p class="text-xs font-medium text-gray-500 mb-1">Hooks :</p>
              <div class="flex flex-wrap gap-1">
                <span
                  v-for="hook in Object.keys(plugin.hooks)"
                  :key="hook"
                  class="px-2 py-0.5 bg-indigo-50 text-indigo-700 text-xs rounded font-mono"
                >
                  {{ hook }}
                </span>
              </div>
            </div>

            <!-- Toggle actif/inactif -->
            <button
              :disabled="toggling === plugin.id"
              class="w-full px-4 py-2 text-sm font-semibold rounded-lg transition-colors disabled:opacity-50"
              :class="plugin.active
                ? 'bg-red-50 text-red-700 hover:bg-red-100'
                : 'bg-green-50 text-green-700 hover:bg-green-100'"
              @click="toggle(plugin.id, plugin.active)"
            >
              <span v-if="toggling === plugin.id">...</span>
              <span v-else>{{ plugin.active ? 'Désactiver' : 'Activer' }}</span>
            </button>
          </div>
        </div>

        <!-- Placeholder si aucun plugin -->
        <div
          v-if="allPlugins.length === 0"
          class="col-span-3 text-center py-12 text-gray-400"
        >
          Aucun plugin enregistré.
        </div>
      </div>

      <!-- Résumé des hooks actifs -->
      <div class="mt-10 bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold mb-4">Hooks actifs</h2>
        <div v-if="hooksSummary.length === 0" class="text-gray-400 text-sm">
          Aucun hook enregistré.
        </div>
        <div v-else class="overflow-x-auto">
          <table class="min-w-full text-sm">
            <thead>
              <tr class="text-left text-gray-500 border-b">
                <th class="pr-6 pb-2 font-medium">Hook</th>
                <th class="pb-2 font-medium">Callbacks enregistrés</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="hook in hooksSummary"
                :key="hook.name"
                class="border-b last:border-0"
              >
                <td class="pr-6 py-2 font-mono text-indigo-700">{{ hook.name }}</td>
                <td class="py-2">
                  <span class="px-2 py-0.5 bg-gray-100 text-gray-700 rounded text-xs">
                    {{ hook.count }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { usePlugins } from '~/composables/usePlugins'
import { useHooks } from '~/composables/useHooks'

definePageMeta({ middleware: 'admin' })

const { getAllPlugins, activatePlugin, deactivatePlugin } = usePlugins()
const { getHooksSummary } = useHooks()

const loading = ref(false)
const toggling = ref<string | null>(null)
const successMessage = ref('')
const errorMessage = ref('')

const allPlugins = computed(() => getAllPlugins())
const hooksSummary = computed(() => getHooksSummary())

/**
 * Active ou désactive un plugin et persiste l'état en base de données.
 */
const toggle = async (id: string, currentlyActive: boolean) => {
  toggling.value = id
  successMessage.value = ''
  errorMessage.value = ''

  try {
    const action = currentlyActive ? 'deactivate' : 'activate'
    await $fetch(`/api/admin/plugins/${id}`, {
      method: 'POST',
      body: { action },
    })

    if (currentlyActive) {
      deactivatePlugin(id)
    } else {
      activatePlugin(id)
    }

    successMessage.value = `Plugin "${id}" ${action === 'activate' ? 'activé' : 'désactivé'} avec succès.`
  } catch (e: any) {
    errorMessage.value = e?.data?.statusMessage ?? 'Une erreur est survenue.'
  } finally {
    toggling.value = null
  }
}
</script>
