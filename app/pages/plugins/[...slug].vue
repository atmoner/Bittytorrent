<template>
  <div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-8">
      <div v-if="!clientReady" class="text-gray-500">Chargement...</div>

      <div
        v-else-if="!currentPage"
        class="bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded"
      >
        Page plugin introuvable.
      </div>

      <div
        v-else-if="!canAccess"
        class="bg-yellow-100 border border-yellow-300 text-yellow-800 px-4 py-3 rounded"
      >
        Vous n'avez pas accès à cette page.
      </div>

      <section v-else>
        <div v-if="blocksAbove.length > 0" class="space-y-4 mb-6">
          <component
            v-for="block in blocksAbove"
            :key="block.id"
            :is="block.componentDef ?? block.component"
          />
        </div>

        <h1 class="text-2xl font-bold text-gray-900 mb-6">
          {{ currentPage.title }}
        </h1>
        <component :is="currentPage.componentDef ?? currentPage.component" />

        <div v-if="blocksBelow.length > 0" class="space-y-4 mt-6">
          <component
            v-for="block in blocksBelow"
            :key="block.id"
            :is="block.componentDef ?? block.component"
          />
        </div>
      </section>
    </div>
  </div>
</template>

<script setup lang="ts">
  import { computed, onMounted, ref, watch } from "vue"
  import type { PluginPage } from "../../../types/plugin"
  import { usePlugins } from "~/composables/usePlugins"

  const route = useRoute()
  const { executeHook } = useHooks()
  const { getPluginPages } = usePlugins()
  const { loggedIn, user } = useUserSession()

  type PluginPageBlock = {
    id: string
    component?: string
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    componentDef?: any
    priority?: number
  }

  type SessionUser = { role?: "admin" | "user" }

  const clientReady = ref(false)
  const pageBlocks = ref<PluginPageBlock[]>([])

  const slugPath = computed<string>(() => {
    const slug = route.params.slug
    if (Array.isArray(slug)) return slug.join("/")
    if (typeof slug === "string") return slug
    return ""
  })

  const normalizePath = (value: string): string => {
    return value.replace(/^\/+/, "").replace(/\/+$/, "")
  }

  const currentPage = computed<PluginPage | undefined>(() => {
    const path = normalizePath(slugPath.value)
    return getPluginPages().find((page) => normalizePath(page.path) === path)
  })

  const isAdmin = computed(() => {
    const currentUser = (user.value as SessionUser | null) ?? null
    return currentUser?.role === "admin"
  })

  const canAccess = computed(() => {
    if (!currentPage.value) return false
    if (currentPage.value.adminOnly && !isAdmin.value) return false
    if (currentPage.value.requiresAuth && !loggedIn.value) return false
    return true
  })

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

  const registerBlock = (block: PluginPageBlock) => {
    if (pageBlocks.value.find((item) => item.id === block.id)) return
    pageBlocks.value.push({
      ...block,
      priority: block.priority ?? 10,
    })
  }

  onMounted(async () => {
    clientReady.value = true

    if (currentPage.value) {
      pageBlocks.value = []
      await executeHook("page:plugin", {
        page: currentPage.value.id,
        pluginPagePath: normalizePath(slugPath.value),
        registerBlock,
      })
    }
  })

  watch(
    () => currentPage.value?.id,
    async (pageId, previousPageId) => {
      if (!clientReady.value || !pageId || pageId === previousPageId) return
      pageBlocks.value = []
      await executeHook("page:plugin", {
        page: pageId,
        pluginPagePath: normalizePath(slugPath.value),
        registerBlock,
      })
    },
  )
</script>
