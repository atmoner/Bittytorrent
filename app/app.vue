<template>
  <div class="flex flex-col min-h-screen">
    <AppHeader />
    <div class="flex-1 flex">
      <main class="flex-1 min-w-0">
        <NuxtPage />
      </main>
      <transition name="sidebar">
        <SidebarDynamic
          v-if="sidebarOpen"
          class="w-72 flex-shrink-0 p-4 border-l border-gray-200"
        />
      </transition>
    </div>
    <AppFooter />
    <BittyUiModal />
  </div>
</template>

<script setup lang="ts">
  import { useSidebar } from "~/composables/useSidebar"
  const { config, fetchConfig } = useConfig()
  const { sidebarOpen } = useSidebar()

  await fetchConfig()

  const activeThemeHref = computed(() => {
    const fileName = config.value?.themeCssFile
    if (!fileName || !/^[a-zA-Z0-9._-]+\.css$/.test(fileName)) {
      return null
    }
    return `/themes/${fileName}`
  })

  useHead(() => ({
    link: activeThemeHref.value
      ? [{ rel: "stylesheet", href: activeThemeHref.value }]
      : [],
  }))
</script>

<style scoped>
  .sidebar-enter-active,
  .sidebar-leave-active {
    transition: all 0.25s ease;
  }
  .sidebar-enter-from,
  .sidebar-leave-to {
    opacity: 0;
    transform: translateX(1.5rem);
  }
</style>
