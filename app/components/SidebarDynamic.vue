<template>
  <aside class="sidebar space-y-4">
    <template v-for="block in sidebarBlocks" :key="block.id">
      <div class="bg-white rounded-lg shadow p-4">
        <h4
          v-if="block.title"
          class="text-sm font-semibold text-gray-700 mb-3 border-b pb-2"
        >
          {{ block.title }}
        </h4>
        <!-- Composant dynamique du plugin -->
        <component
          :is="block.componentDef ?? block.component"
          v-if="block.componentDef"
        />
        <p v-else class="text-xs text-gray-400 italic">
          Composant "{{ block.component }}" introuvable.
        </p>
      </div>
    </template>
  </aside>
</template>

<script setup lang="ts">
  import { computed, onMounted } from "vue"
  import { usePlugins } from "~/composables/usePlugins"

  const { executeHook } = useHooks()
  const { getSidebarBlocks } = usePlugins()

  const sidebarBlocks = computed(() => getSidebarBlocks())

  onMounted(async () => {
    await executeHook("sidebar:before", {
      blocks: sidebarBlocks.value,
    })
    await executeHook("sidebar:after", {
      blocks: sidebarBlocks.value,
    })
  })
</script>
