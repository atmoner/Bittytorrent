<template>
  <div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-8">
      <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold">Gestion des utilisateurs</h1>
        <NuxtLink
          to="/admin"
          class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700"
        >
          Retour à l'admin
        </NuxtLink>
      </div>

      <div v-if="loading" class="text-center py-8">
        <p>Chargement...</p>
      </div>

      <div
        v-else-if="error"
        class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded"
      >
        {{ error }}
      </div>

      <div v-else class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Nom d'utilisateur
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Email
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Rôle
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Date de création
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Actions
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="user in users" :key="user._id">
              <td class="px-6 py-4 whitespace-nowrap">
                <input
                  v-if="editingUserId === user._id"
                  v-model="editForm.username"
                  type="text"
                  class="border rounded px-2 py-1"
                />
                <span v-else>{{ user.username }}</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <input
                  v-if="editingUserId === user._id"
                  v-model="editForm.email"
                  type="email"
                  class="border rounded px-2 py-1"
                />
                <span v-else>{{ user.email }}</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <select
                  v-if="editingUserId === user._id"
                  v-model="editForm.role"
                  class="border rounded px-2 py-1"
                >
                  <option value="user">User</option>
                  <option value="admin">Admin</option>
                </select>
                <span
                  v-else
                  :class="
                    user.role === 'admin'
                      ? 'text-purple-600 font-semibold'
                      : 'text-gray-600'
                  "
                >
                  {{ user.role || "user" }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ user.createdAt ? formatDate(user.createdAt) : "N/A" }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <div v-if="editingUserId === user._id" class="flex gap-2">
                  <button
                    @click="saveUser(user._id!)"
                    class="text-green-600 hover:text-green-900"
                  >
                    Sauvegarder
                  </button>
                  <button
                    @click="cancelEdit"
                    class="text-gray-600 hover:text-gray-900"
                  >
                    Annuler
                  </button>
                </div>
                <div v-else class="flex gap-2">
                  <button
                    @click="startEdit(user)"
                    class="text-blue-600 hover:text-blue-900"
                  >
                    Éditer
                  </button>
                  <button
                    @click="deleteUser(user._id!)"
                    class="text-red-600 hover:text-red-900"
                  >
                    Supprimer
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
  import type { User } from "~/models"
  import { formatDate } from "~/utils/dateFormat"

  const session = useUserSession()

  const users = ref<User[]>([])
  const loading = ref(true)
  const error = ref("")
  const editingUserId = ref<string | null>(null)
  const editForm = ref({
    username: "",
    email: "",
    role: "user" as "admin" | "user",
  })

  // Redirection si non admin
  onMounted(async () => {
    if (!session.user.value) {
      navigateTo("/login")
      return
    }

    await loadUsers()
  })

  async function loadUsers() {
    try {
      loading.value = true
      const response = await $fetch<{ users: User[] }>("/api/admin/users")
      users.value = response.users
    } catch (e: any) {
      error.value =
        e.data?.statusMessage || "Erreur lors du chargement des utilisateurs"
    } finally {
      loading.value = false
    }
  }

  function startEdit(user: User) {
    editingUserId.value = user._id!
    editForm.value = {
      username: user.username,
      email: user.email,
      role: user.role || "user",
    }
  }

  function cancelEdit() {
    editingUserId.value = null
    editForm.value = {
      username: "",
      email: "",
      role: "user",
    }
  }

  async function saveUser(userId: string) {
    try {
      await $fetch("/api/admin/user-update", {
        method: "POST",
        body: {
          userId,
          ...editForm.value,
        },
      })
      await loadUsers()
      cancelEdit()
    } catch (e: any) {
      alert(e.data?.statusMessage || "Erreur lors de la mise à jour")
    }
  }

  async function deleteUser(userId: string) {
    if (!confirm("Êtes-vous sûr de vouloir supprimer cet utilisateur ?")) {
      return
    }

    try {
      await $fetch("/api/admin/user-delete", {
        method: "POST",
        body: { userId },
      })
      await loadUsers()
    } catch (e: any) {
      alert(e.data?.statusMessage || "Erreur lors de la suppression")
    }
  }
</script>
