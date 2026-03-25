<template>
  <div class="min-h-screen py-8 px-4">
    <div class="max-w-4xl mx-auto space-y-6">
      <div v-if="blocksAbove.length > 0" class="space-y-4">
        <component
          v-for="block in blocksAbove"
          :key="block.id"
          :is="block.componentDef ?? block.component"
        />
      </div>

      <div class="bg-white rounded-lg shadow-sm p-6">
        <p class="text-xs font-mono text-gray-500 mb-2">account/</p>
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Paramètres</h1>
        <p class="text-gray-600">
          Configurez vos préférences de compte et de sécurité.
        </p>
        <div class="mt-6">
          <NuxtLink to="/account" class="text-indigo-600 hover:underline"
            >← Retour au compte</NuxtLink
          >
        </div>
      </div>

      <div v-if="blocksBelow.length > 0" class="space-y-4">
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
  const { loggedIn } = useUserSession()

  type AccountSettingsPageBlock = {
    id: string
    component?: string
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    componentDef?: any
    priority?: number
  }

  const pageBlocks = ref<AccountSettingsPageBlock[]>([])

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

  const registerBlock = (block: AccountSettingsPageBlock) => {
    if (pageBlocks.value.find((item) => item.id === block.id)) return
    pageBlocks.value.push({
      ...block,
      priority: block.priority ?? 10,
    })
  }

  onMounted(async () => {
    if (!loggedIn.value) {
      await navigateTo("/login")
      return
    }

    pageBlocks.value = []
    await executeHook("page:account-settings", {
      page: "account-settings",
      loggedIn: loggedIn.value,
      registerBlock,
    })
  })
</script>
