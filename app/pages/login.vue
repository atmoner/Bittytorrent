<template>
  <div class="flex flex-col items-center justify-center">
    <div v-if="blocksAbove.length > 0" class="w-full max-w-md space-y-4 mb-6">
      <component
        v-for="block in blocksAbove"
        :key="block.id"
        :is="block.componentDef ?? block.component"
      />
    </div>

    <div class="w-full max-w-md bg-white rounded-lg shadow-md p-8">
      <h2 class="text-2xl font-bold mb-6 text-center">Connexion</h2>
      <LoginForm />
      <p class="mt-4 text-center text-sm text-gray-600">
        Pas de compte ?
        <NuxtLink to="/register" class="text-indigo-500 hover:underline"
          >Inscription</NuxtLink
        >
      </p>
    </div>

    <div v-if="blocksBelow.length > 0" class="w-full max-w-md space-y-4 mt-6">
      <component
        v-for="block in blocksBelow"
        :key="block.id"
        :is="block.componentDef ?? block.component"
      />
    </div>
  </div>
</template>

<script setup lang="ts">
  import LoginForm from "~/components/LoginForm.vue"

  const { executeHook } = useHooks()

  type LoginPageBlock = {
    id: string
    component?: string
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    componentDef?: any
    priority?: number
  }

  const pageBlocks = ref<LoginPageBlock[]>([])

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

  const registerBlock = (block: LoginPageBlock) => {
    if (pageBlocks.value.find((item) => item.id === block.id)) return
    pageBlocks.value.push({
      ...block,
      priority: block.priority ?? 10,
    })
  }

  onMounted(async () => {
    pageBlocks.value = []
    await executeHook("page:login", {
      page: "login",
      registerBlock,
    })
  })
</script>

<style scoped>
  /* Tout le style est géré par Tailwind */
</style>
