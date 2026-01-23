<template>
  <div class="min-h-screen bg-gray-50 p-6">
    <div class="max-w-6xl mx-auto">
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Gestion des catégories</h1>
        <p class="text-gray-600 mt-2">Gérez les catégories de torrents</p>
      </div>

      <!-- Bouton d'ajout -->
      <div class="mb-6">
        <button
          @click="showAddForm = true"
          class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors inline-flex items-center"
        >
          <svg
            class="w-5 h-5 mr-2"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M12 4v16m8-8H4"
            ></path>
          </svg>
          Ajouter une catégorie
        </button>
      </div>

      <!-- Formulaire d'ajout/modification -->
      <div
        v-if="showAddForm || editingCategory"
        class="bg-white rounded-lg shadow-md p-6 mb-6"
      >
        <h2 class="text-xl font-bold mb-4">
          {{
            editingCategory ? "Modifier la catégorie" : "Ajouter une catégorie"
          }}
        </h2>

        <form @submit.prevent="submitCategory" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Nom de la catégorie *
            </label>
            <input
              v-model="categoryForm.name"
              type="text"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="Nom de la catégorie"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Description
            </label>
            <textarea
              v-model="categoryForm.description"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="Description de la catégorie"
            ></textarea>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Couleur
            </label>
            <div class="flex items-center space-x-4">
              <input
                v-model="categoryForm.color"
                type="color"
                class="h-10 w-20 border border-gray-300 rounded-md cursor-pointer"
              />
              <input
                v-model="categoryForm.color"
                type="text"
                class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="#3b82f6"
              />
            </div>
          </div>

          <div class="flex space-x-3">
            <button
              type="submit"
              :disabled="loading"
              class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 disabled:opacity-50 transition-colors"
            >
              {{
                loading
                  ? "Enregistrement..."
                  : editingCategory
                    ? "Modifier"
                    : "Ajouter"
              }}
            </button>
            <button
              type="button"
              @click="cancelEdit"
              class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 transition-colors"
            >
              Annuler
            </button>
          </div>
        </form>
      </div>

      <!-- Liste des catégories -->
      <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
          <h2 class="text-lg font-semibold">
            Catégories existantes ({{ categories.length }})
          </h2>
        </div>

        <div
          v-if="categories.length === 0"
          class="p-6 text-center text-gray-500"
        >
          Aucune catégorie créée pour le moment.
        </div>

        <div v-else class="divide-y divide-gray-200">
          <div
            v-for="category in categories"
            :key="category._id"
            class="p-6 hover:bg-gray-50"
          >
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-4">
                <div
                  class="w-6 h-6 rounded-full border-2 border-gray-300"
                  :style="{ backgroundColor: category.color }"
                ></div>
                <div>
                  <h3 class="font-medium text-gray-900">{{ category.name }}</h3>
                  <p v-if="category.description" class="text-sm text-gray-500">
                    {{ category.description }}
                  </p>
                  <p class="text-xs text-gray-400 mt-1">
                    Créée le {{ formatDate(category.createdAt) }}
                  </p>
                </div>
              </div>

              <div class="flex space-x-2">
                <button
                  @click="editCategory(category)"
                  class="text-blue-600 hover:text-blue-700 p-2 rounded-md hover:bg-blue-50 transition-colors"
                  title="Modifier"
                >
                  <svg
                    class="w-5 h-5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                    ></path>
                  </svg>
                </button>
                <button
                  @click="deleteCategory(category._id, category.name)"
                  class="text-red-600 hover:text-red-700 p-2 rounded-md hover:bg-red-50 transition-colors"
                  title="Supprimer"
                >
                  <svg
                    class="w-5 h-5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                    ></path>
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
  import { formatDate } from "~/utils/dateFormat"

  definePageMeta({
    middleware: "admin",
  })

  const categories = ref([])
  const loading = ref(false)
  const showAddForm = ref(false)
  const editingCategory = ref(null)

  const categoryForm = ref({
    name: "",
    description: "",
    color: "#3b82f6",
  })

  const loadCategories = async () => {
    try {
      const data = await $fetch("/api/admin/categories")
      categories.value = data
    } catch (error) {
      console.error("Erreur lors du chargement des catégories:", error)
    }
  }

  const submitCategory = async () => {
    loading.value = true
    try {
      if (editingCategory.value) {
        // Modification
        await $fetch(`/api/admin/category/${editingCategory.value._id}`, {
          method: "PUT",
          body: categoryForm.value,
        })
      } else {
        // Ajout
        await $fetch("/api/admin/categories", {
          method: "POST",
          body: categoryForm.value,
        })
      }

      await loadCategories()
      cancelEdit()
    } catch (error) {
      console.error("Erreur:", error)
      alert(error.data?.message || "Une erreur est survenue")
    } finally {
      loading.value = false
    }
  }

  const editCategory = (category) => {
    editingCategory.value = category
    categoryForm.value = {
      name: category.name,
      description: category.description || "",
      color: category.color || "#3b82f6",
    }
    showAddForm.value = false
  }

  const cancelEdit = () => {
    showAddForm.value = false
    editingCategory.value = null
    categoryForm.value = {
      name: "",
      description: "",
      color: "#3b82f6",
    }
  }

  const deleteCategory = async (categoryId, categoryName) => {
    if (
      !confirm(
        `Êtes-vous sûr de vouloir supprimer la catégorie "${categoryName}" ?`,
      )
    ) {
      return
    }

    try {
      await $fetch(`/api/admin/category/${categoryId}`, {
        method: "DELETE",
      })
      await loadCategories()
    } catch (error) {
      console.error("Erreur:", error)
      alert(error.data?.message || "Une erreur est survenue")
    }
  }

  onMounted(() => {
    loadCategories()
  })
</script>
