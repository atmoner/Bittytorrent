<template>
  <header class="text-gray-600 body-font relative">
    <div
      class="container mx-auto flex flex-wrap p-5 flex-col md:flex-row items-center"
    >
      <div class="flex items-center justify-between w-full md:w-auto">
        <NuxtLink
          class="flex title-font font-medium items-center text-gray-900 mb-4 md:mb-0"
          to="/"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            stroke="currentColor"
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            class="w-10 h-10 text-white p-2 bg-indigo-500 rounded-full"
            viewBox="0 0 24 24"
          >
            <path
              d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"
            ></path>
          </svg>
          <span class="ml-3 text-xl">BittyTorrent</span>
        </NuxtLink>

        <!-- Mobile Navigation Component -->
        <MobileNav :logged-in-app="loggedInApp" :is-admin="isAdmin" />
      </div>
      <!-- Desktop Navigation -->
      <nav
        class="hidden md:flex md:mr-auto md:ml-4 md:py-1 md:pl-4 md:border-l md:border-gray-400 flex-wrap items-center text-base justify-center"
      >
        <NuxtLink v-if="!loggedInApp" to="/" class="mr-5 hover:text-gray-900"
          >Home</NuxtLink
        >

        <!-- Menu déroulant des catégories -->
        <div class="relative mr-5 group">
          <button class="hover:text-gray-900 flex items-center">
            Torrents par catégorie
            <svg
              class="w-4 h-4 ml-1 transition-transform group-hover:rotate-180"
              fill="currentColor"
              viewBox="0 0 20 20"
            >
              <path
                fill-rule="evenodd"
                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                clip-rule="evenodd"
              />
            </svg>
          </button>

          <!-- Mega menu dropdown -->
          <div
            class="absolute left-0 top-full mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50"
          >
            <div class="p-4">
              <div class="mb-3">
                <NuxtLink
                  to="/torrents"
                  class="block px-3 py-2 text-sm font-semibold text-gray-900 hover:bg-gray-100 rounded-md"
                >
                  Tous les Torrents
                </NuxtLink>
              </div>
              <hr class="my-3 border-gray-200" />
              <div v-if="categoriesLoading" class="text-center py-4">
                <div class="text-sm text-gray-500">Chargement...</div>
              </div>
              <div v-else-if="categoriesError" class="text-center py-4">
                <div class="text-sm text-red-500">
                  Erreur lors du chargement
                </div>
              </div>
              <div v-else class="grid grid-cols-2 gap-1">
                <NuxtLink
                  v-for="category in categories"
                  :key="category._id"
                  :to="`/torrents?category=${category._id}`"
                  class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 rounded-md"
                >
                  <span
                    v-if="category.color"
                    class="inline-block w-3 h-3 rounded-full mr-2"
                    :style="{ backgroundColor: category.color }"
                  ></span>
                  {{ category.name }}
                </NuxtLink>
              </div>
              <div
                v-if="!categoriesLoading && categories?.length === 0"
                class="text-center py-4"
              >
                <div class="text-sm text-gray-500">
                  Aucune catégorie disponible
                </div>
              </div>
            </div>
          </div>
        </div>

        <NuxtLink to="/stats" class="mr-5 hover:text-gray-900">
          Statistiques
        </NuxtLink>
        <NuxtLink
          v-if="loggedInApp"
          to="/upload"
          class="mr-5 hover:text-gray-900"
        >
          Ajouter un torrent
        </NuxtLink>
      </nav>

      <!-- Icônes plugins topbar -->
      <div class="hidden md:flex items-center space-x-2 mr-4">
        <button
          v-for="icon in topbarIcons"
          :key="icon.id"
          :title="icon.title"
          class="p-2 rounded-full hover:bg-gray-100 text-gray-600 hover:text-indigo-600 transition-colors"
          @click="handleTopbarIconClick(icon)"
        >
          <!-- <BittyUiIcon :name="icon.icon" :size="22" /> -->{{ icon.title }}
        </button>
      </div>

      <!-- Liens menu plugins -->
      <div
        v-if="visibleMenuItems.length"
        class="hidden md:flex items-center space-x-4"
      >
        <template v-for="item in visibleMenuItems" :key="item.id">
          <a
            v-if="item.external"
            :href="item.url"
            target="_blank"
            rel="noopener noreferrer"
            class="mr-5 hover:text-gray-900"
          >
            {{ item.label }}
          </a>
          <NuxtLink v-else :to="item.url" class="mr-5 hover:text-gray-900">
            {{ item.label }}
          </NuxtLink>
        </template>
      </div>

      <!-- Desktop User Menu -->
      <div v-if="loggedInApp" class="hidden md:flex items-center space-x-4">
        <NuxtLink to="/account" class="mr-5 hover:text-gray-900">
          Mon compte
        </NuxtLink>
        <NuxtLink
          v-if="isAdmin"
          to="/admin"
          class="mr-5 hover:text-indigo-600 font-semibold"
        >
          Admin
        </NuxtLink>
        <NuxtLink to="/logout" class="mr-5 hover:text-gray-900">
          <BittyUiButton icon="arrow-right" iconPosition="right">
            Logout
          </BittyUiButton>
        </NuxtLink>
      </div>
      <div v-else class="hidden md:flex items-center">
        <NuxtLink to="/login" class="mr-5 rounded">
          <BittyUiButton
            icon="arrow-right"
            iconPosition="right"
            class="bg-indigo-500 hover:bg-indigo-700"
          >
            Login
          </BittyUiButton>
        </NuxtLink>
      </div>
    </div>
  </header>
</template>
<script setup lang="ts">
  import { computed, ref, onMounted } from "vue"
  import type { Category } from "~/models"
  import type { PluginTopbarIcon, PluginMenuItem } from "../../types/plugin"
  import { usePlugins } from "~/composables/usePlugins"
  import { useSidebar } from "~/composables/useSidebar"

  type SessionUser = {
    role?: "admin" | "user"
  }

  const { executeHook } = useHooks()
  const { getTopbarIcons, getMenuItems } = usePlugins()
  const { toggleSidebar } = useSidebar()

  const handleTopbarIconClick = (icon: PluginTopbarIcon) => {
    if (icon.action === "toggle-sidebar") {
      toggleSidebar()
    } else if (icon.action === "open-url" && icon.url) {
      window.open(icon.url, "_blank")
    }
  }

  const { loggedIn: _loggedIn, user } = useUserSession()
  const loggedInApp = computed(() => _loggedIn.value)
  const sessionUser = computed<SessionUser | null>(
    () => (user.value as SessionUser | null) ?? null,
  )
  const isAdmin = computed(() => sessionUser.value?.role === "admin")
  const topbarIcons = computed(() => getTopbarIcons())
  const menuItems = computed<PluginMenuItem[]>(() => getMenuItems())
  const visibleMenuItems = computed<PluginMenuItem[]>(() =>
    menuItems.value.filter((item) => !item.requiresAuth || loggedInApp.value),
  )

  // Gestion des catégories
  const categories = ref<Category[]>([])
  const categoriesLoading = ref(false)
  const categoriesError = ref(false)

  const loadCategories = async () => {
    categoriesLoading.value = true
    categoriesError.value = false
    try {
      const response = await $fetch<Category[]>("/api/categories")
      categories.value = response
    } catch (error) {
      categoriesError.value = true
      console.error("Erreur lors du chargement des catégories:", error)
    } finally {
      categoriesLoading.value = false
    }
  }

  onMounted(async () => {
    loadCategories()
    await executeHook("menu:main", {
      loggedIn: loggedInApp.value,
      isAdmin: isAdmin.value,
    })
    await executeHook("menu:user", {
      loggedIn: loggedInApp.value,
      isAdmin: isAdmin.value,
    })
    if (isAdmin.value) {
      await executeHook("admin:menu", {
        page: "header-menu",
      })
    }
  })
</script>
