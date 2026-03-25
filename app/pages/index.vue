<template>
  <section class="text-gray-600 body-font">
    <div
      v-if="blocksAbove.length > 0"
      class="container mx-auto px-5 pt-8 space-y-4"
    >
      <component
        v-for="block in blocksAbove"
        :key="block.id"
        :is="block.componentDef ?? block.component"
      />
    </div>

    <!-- Loading state -->
    <div
      v-if="configLoading"
      class="container px-5 py-24 mx-auto flex flex-wrap items-center"
    >
      <p>Chargement...</p>
    </div>
    <div
      v-else
      class="container px-5 py-24 mx-auto flex flex-wrap items-center"
    >
      <div class="lg:w-3/5 md:w-1/2 md:pr-16 lg:pr-0 pr-0">
        <h1 class="title-font font-medium text-3xl text-gray-900">
          Bienvenue sur {{ siteName }}
        </h1>
        <p class="leading-relaxed mt-4">
          Partagez, téléchargez et suivez vos torrents facilement grâce à notre
          plateforme communautaire.<br />
          Connectez-vous pour accéder à la liste des torrents, uploader les
          vôtres et suivre vos statistiques !
        </p>
      </div>
      <div
        class="lg:w-2/6 md:w-1/2 bg-gray-100 rounded-lg p-8 flex flex-col md:ml-auto w-full mt-10 md:mt-0"
      >
        <h2
          class="text-gray-900 text-lg font-medium title-font mb-5 text-center"
        >
          Connexion
        </h2>
        <LoginForm />

        <p class="text-xs text-gray-500 mt-3 text-center">
          Pas encore de compte ?
          <NuxtLink to="/register" class="text-indigo-500 hover:underline"
            >Inscrivez-vous</NuxtLink
          >
        </p>
      </div>
    </div>

    <!-- Section des derniers torrents -->
    <div class="container mx-auto px-5 py-12">
      <TorrentsList />
    </div>

    <div
      v-if="blocksBelow.length > 0"
      class="container mx-auto px-5 pb-8 space-y-4"
    >
      <component
        v-for="block in blocksBelow"
        :key="block.id"
        :is="block.componentDef ?? block.component"
      />
    </div>
  </section>
</template>
<script setup lang="ts">
  import LoginForm from "~/components/LoginForm.vue"
  import TorrentsList from "~/components/TorrentsList.vue"

  const { executeHook } = useHooks()
  const { config, configLoading } = useConfig()

  type HomePageBlock = {
    id: string
    component?: string
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    componentDef?: any
    priority?: number
  }

  const pageBlocks = ref<HomePageBlock[]>([])

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

  const registerBlock = (block: HomePageBlock) => {
    if (pageBlocks.value.find((item) => item.id === block.id)) return
    pageBlocks.value.push({
      ...block,
      priority: block.priority ?? 10,
    })
  }

  const siteName = computed(() => config.value?.siteName)

  onMounted(async () => {
    pageBlocks.value = []
    await executeHook("page:home", {
      page: "home",
      registerBlock,
    })
  })
</script>
