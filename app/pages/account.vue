<template>
  <div class="min-h-screen bg-gray-50">
    <NuxtPage v-if="isNestedAccountRoute" />

    <div v-else class="container mx-auto px-4 py-8">
      <div v-if="blocksAbove.length > 0" class="space-y-4 mb-6">
        <component
          v-for="block in blocksAbove"
          :key="block.id"
          :is="block.componentDef ?? block.component"
        />
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <AccountSidebar :user-data="userData" />

        <AccountOverview
          :user-data="userData"
          :session-user="sessionUser"
          :format-date="formatDate"
          :format-bytes="formatBytes"
        />
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
  import AccountOverview from "~/components/account/AccountOverview.vue"
  import AccountSidebar from "~/components/account/AccountSidebar.vue"
  import { formatDate } from "~/utils/dateFormat"
  import { formatBytes } from "~/utils/byteFormat"
  import type { User } from "~/models"

  type SessionUser = {
    username?: string
    email?: string
    createdAt?: Date
    role?: "admin" | "user"
  }

  const { user, loggedIn } = useUserSession()
  const { executeHook } = useHooks()
  const route = useRoute()
  const userData = ref<User | null>(null)
  const sessionUser = computed<SessionUser | null>(
    () => (user.value as SessionUser | null) ?? null,
  )
  const isNestedAccountRoute = computed(
    () => route.path !== "/account" && route.path.startsWith("/account/"),
  )

  type AccountPageBlock = {
    id: string
    component?: string
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    componentDef?: any
    priority?: number
  }

  const pageBlocks = ref<AccountPageBlock[]>([])

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

  const registerBlock = (block: AccountPageBlock) => {
    if (pageBlocks.value.find((item) => item.id === block.id)) return
    pageBlocks.value.push({
      ...block,
      priority: block.priority ?? 10,
    })
  }

  onMounted(async () => {
    if (isNestedAccountRoute.value) return

    pageBlocks.value = []
    await executeHook("page:account", {
      page: "account",
      loggedIn: loggedIn.value,
      registerBlock,
    })

    if (!loggedIn.value) {
      navigateTo("/login")
    }
    await fetchUser()
  })

  const fetchUser = async () => {
    try {
      const res = await fetch("/api/user-stats")
      const data = await res.json()
      userData.value = data.data
    } catch (e) {}
  }
</script>
