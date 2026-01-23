<template>
  <div class="md:hidden">
    <!-- Burger Button -->
    <button
      @click="toggleMenu"
      class="inline-flex items-center p-2 ml-3 text-sm text-gray-500 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200"
      aria-controls="mobile-menu"
      :aria-expanded="isOpen"
    >
      <span class="sr-only">Ouvrir le menu principal</span>
      <svg
        class="w-6 h-6"
        :class="{ hidden: isOpen, block: !isOpen }"
        fill="currentColor"
        viewBox="0 0 20 20"
      >
        <path
          fill-rule="evenodd"
          d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
          clip-rule="evenodd"
        />
      </svg>
      <svg
        class="w-6 h-6"
        :class="{ block: isOpen, hidden: !isOpen }"
        fill="currentColor"
        viewBox="0 0 20 20"
      >
        <path
          fill-rule="evenodd"
          d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
          clip-rule="evenodd"
        />
      </svg>
    </button>

    <!-- Mobile Menu Overlay -->
    <div
      v-if="isOpen"
      class="fixed inset-0 z-40 bg-black bg-opacity-50 md:hidden"
      @click="closeMenu"
    ></div>

    <!-- Mobile Menu -->
    <div
      id="mobile-menu"
      :class="{
        'translate-x-0': isOpen,
        '-translate-x-full': !isOpen,
      }"
      class="fixed top-0 left-0 bottom-0 w-full z-50 bg-white shadow-xl transform transition-transform duration-300 ease-in-out md:hidden"
    >
      <!-- Header du menu mobile -->
      <div
        class="flex items-center justify-between p-4 border-b border-gray-200"
      >
        <h2 class="text-lg font-semibold text-gray-900">Menu</h2>
        <button
          @click="closeMenu"
          class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg"
        >
          <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
            <path
              fill-rule="evenodd"
              d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
              clip-rule="evenodd"
            />
          </svg>
        </button>
      </div>

      <!-- Contenu du menu mobile avec scroll -->
      <div class="flex-1 overflow-y-auto p-4 space-y-1">
        <!-- Home (si pas connecté) -->
        <NuxtLink
          v-if="!loggedInApp"
          to="/"
          class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50"
          @click="closeMenu"
        >
          Home
        </NuxtLink>

        <!-- Tous les torrents -->
        <NuxtLink
          to="/torrents"
          class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50"
          @click="closeMenu"
        >
          Tous les Torrents
        </NuxtLink>

        <!-- Catégories -->
        <div class="px-3 py-2">
          <button
            @click="toggleCategories"
            class="flex items-center justify-between w-full text-left text-base font-medium text-gray-700 hover:text-gray-900"
          >
            Torrents par catégorie
            <svg
              class="w-4 h-4 transition-transform"
              :class="{ 'rotate-180': showCategories }"
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

          <!-- Submenu des catégories -->
          <div
            v-show="showCategories"
            class="mt-2 ml-4 space-y-1 border-l-2 border-gray-200 pl-3"
          >
            <div v-if="categoriesLoading" class="py-2">
              <div class="text-sm text-gray-500">Chargement...</div>
            </div>
            <div v-else-if="categoriesError" class="py-2">
              <div class="text-sm text-red-500">Erreur lors du chargement</div>
            </div>
            <template v-else>
              <NuxtLink
                v-for="category in categories"
                :key="category._id"
                :to="`/torrents?category=${category._id}`"
                class="block px-3 py-2 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-md"
                @click="closeMenu"
              >
                <span
                  v-if="category.color"
                  class="inline-block w-2 h-2 rounded-full mr-2"
                  :style="{ backgroundColor: category.color }"
                ></span>
                {{ category.name }}
              </NuxtLink>
              <div
                v-if="categories?.length === 0"
                class="px-3 py-2 text-sm text-gray-500"
              >
                Aucune catégorie disponible
              </div>
            </template>
          </div>
        </div>

        <!-- Statistiques -->
        <NuxtLink
          to="/stats"
          class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50"
          @click="closeMenu"
        >
          Statistiques
        </NuxtLink>

        <!-- Ajouter un torrent (si connecté) -->
        <NuxtLink
          v-if="loggedInApp"
          to="/upload"
          class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50"
          @click="closeMenu"
        >
          Ajouter un torrent
        </NuxtLink>

        <hr class="my-3 border-gray-200" />

        <!-- Menu utilisateur connecté -->
        <div v-if="loggedInApp" class="space-y-1">
          <NuxtLink
            to="/account"
            class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50"
            @click="closeMenu"
          >
            Mon compte
          </NuxtLink>
          <NuxtLink
            v-if="isAdmin"
            to="/admin"
            class="block px-3 py-2 rounded-md text-base font-medium text-indigo-600 hover:text-indigo-900 hover:bg-indigo-50"
            @click="closeMenu"
          >
            Administration
          </NuxtLink>
          <NuxtLink
            to="/logout"
            class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50"
            @click="closeMenu"
          >
            Déconnexion
          </NuxtLink>
        </div>

        <!-- Menu utilisateur non connecté -->
        <div v-else class="pt-2">
          <NuxtLink
            to="/login"
            class="block px-3 py-2 rounded-md text-base font-medium text-white bg-indigo-500 hover:bg-indigo-600"
            @click="closeMenu"
          >
            Se connecter
          </NuxtLink>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
  import { ref, computed, onMounted, watch } from "vue"
  import type { Category } from "~/models"

  // Props
  interface Props {
    loggedInApp: boolean
    isAdmin: boolean
  }

  const props = defineProps<Props>()

  // State
  const isOpen = ref(false)
  const showCategories = ref(false)
  const categories = ref<Category[]>([])
  const categoriesLoading = ref(false)
  const categoriesError = ref(false)

  // Methods
  const toggleMenu = () => {
    isOpen.value = !isOpen.value
    if (!isOpen.value) {
      showCategories.value = false
    }
  }

  const closeMenu = () => {
    isOpen.value = false
    showCategories.value = false
  }

  const toggleCategories = () => {
    showCategories.value = !showCategories.value
    if (showCategories.value && categories.value.length === 0) {
      loadCategories()
    }
  }

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

  // Fermer le menu quand on change de route
  const route = useRoute()
  watch(
    () => route.path,
    () => {
      closeMenu()
    },
  )

  onMounted(() => {
    // Fermer le menu si on clique à l'extérieur
    const handleClickOutside = (event: Event) => {
      const target = event.target as Element
      const mobileMenu = document.getElementById("mobile-menu")
      const burgerButton = target.closest('[aria-controls="mobile-menu"]')

      if (isOpen.value && !mobileMenu?.contains(target) && !burgerButton) {
        closeMenu()
      }
    }

    document.addEventListener("click", handleClickOutside)

    // Cleanup
    onUnmounted(() => {
      document.removeEventListener("click", handleClickOutside)
    })
  })
</script>
