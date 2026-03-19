<template>
  <aside class="sidebar space-y-4">
    <template v-for="block in sidebarBlocks" :key="block.id">
      <div class="bg-white rounded-lg shadow p-4">
        <h4 v-if="block.title" class="text-sm font-semibold text-gray-700 mb-3 border-b pb-2">
          {{ block.title }}
        </h4>
        <!-- Composant dynamique du plugin -->
        <component
          :is="resolvePluginComponent(block.component)"
          v-if="resolvePluginComponent(block.component)"
        />
        <p v-else class="text-xs text-gray-400 italic">
          Composant "{{ block.component }}" introuvable.
        </p>
      </div>
    </template>
  </aside>
</template>

<script setup lang="ts">
import { usePlugins } from '~/composables/usePlugins'

const { getSidebarBlocks } = usePlugins()

const sidebarBlocks = computed(() => getSidebarBlocks())

/**
 * Résout un composant Vue par son nom (doit être globalement déclaré
 * via app/components/Plugin*.vue ou via un plugin Nuxt).
 */
const resolvePluginComponent = (name: string) => {
  try {
    return resolveComponent(name)
  } catch {
    return null
  }
}
</script>
