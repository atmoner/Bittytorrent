<template>
  <div class="min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-md">
      <div v-if="blocksAbove.length > 0" class="space-y-4 mb-6">
        <component
          v-for="block in blocksAbove"
          :key="block.id"
          :is="block.componentDef ?? block.component"
        />
      </div>

      <div class="bg-white rounded-lg shadow-md p-8 text-center">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Déconnexion</h1>
        <p class="text-gray-600">Déconnexion en cours...</p>
      </div>

      <div v-if="blocksBelow.length > 0" class="space-y-4 mt-6">
        <component
          v-for="block in blocksBelow"
          :key="block.id"
          :is="block.componentDef ?? block.component"
        />
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
  const { executeHook } = useHooks()
  const { clear } = useUserSession()

  type LogoutPageBlock = {
    id: string
    component?: string
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    componentDef?: any
    priority?: number
  }

  const pageBlocks = ref<LogoutPageBlock[]>([])

  const blocksAbove = computed(() =>
    pageBlocks.value
      .filter((block) => (block.priority ?? 10) < 10)
      .sort((a, b) => (a.priority ?? 10) - (b.priority ?? 10)),
  )

  const blocksBelow = computed(() =>
    pageBlocks.value
      .filter((block) => (block.priority ?? 10) >= 10)
      .sort((a, b) => (a.priority ?? 10) - (b.priority ?? 10)),
  )

  const registerBlock = (block: LogoutPageBlock) => {
    if (pageBlocks.value.find((item) => item.id === block.id)) return
    pageBlocks.value.push({
      ...block,
      priority: block.priority ?? 10,
    })
  }

  pageBlocks.value = []
  await executeHook("page:logout", {
    page: "logout",
    registerBlock,
  })
  await clear()
  await navigateTo("/login")
</script>
