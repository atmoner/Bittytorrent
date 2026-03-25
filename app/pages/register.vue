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
      <h2 class="text-2xl font-bold mb-6 text-center">Inscription</h2>

      <!-- Message si les inscriptions sont fermées -->

      <div
        v-if="config && !config.allowRegistration"
        class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4"
      >
        <p class="font-bold">Inscriptions fermées</p>
        <p>
          {{
            config.registrationClosedMessage ||
            "Les inscriptions sont actuellement désactivées. Veuillez contacter l'administrateur."
          }}
        </p>
      </div>

      <form
        v-else-if="config && config.allowRegistration"
        @submit.prevent="register"
        class="flex flex-col gap-4"
      >
        <input
          v-model="username"
          type="text"
          placeholder="Nom d'utilisateur"
          required
          class="px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-indigo-400"
        />
        <input
          v-model="email"
          type="email"
          placeholder="Email"
          required
          class="px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-indigo-400"
        />
        <input
          v-model="password"
          type="password"
          placeholder="Mot de passe"
          required
          class="px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-indigo-400"
        />
        <button
          type="submit"
          class="bg-indigo-500 text-white font-semibold py-2 rounded hover:bg-indigo-700 transition"
        >
          S'inscrire
        </button>
      </form>
      <p class="mt-4 text-center text-sm text-gray-600">
        Déjà inscrit ?
        <NuxtLink to="/login" class="text-indigo-500 hover:underline"
          >Connexion</NuxtLink
        >
      </p>
      <div v-if="error" class="text-red-600 mt-2 text-center">{{ error }}</div>
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
  import { ref, onMounted } from "vue"

  const username = ref("")
  const email = ref("")
  const password = ref("")
  const error = ref("")
  const allowRegistration = ref(true)
  const registrationClosedMessage = ref("")

  type RegisterPageBlock = {
    id: string
    component?: string
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    componentDef?: any
    priority?: number
  }

  const pageBlocks = ref<RegisterPageBlock[]>([])

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

  const registerBlock = (block: RegisterPageBlock) => {
    if (pageBlocks.value.find((item) => item.id === block.id)) return
    pageBlocks.value.push({
      ...block,
      priority: block.priority ?? 10,
    })
  }

  const { config } = useConfig()
  const { executeHook } = useHooks()

  onMounted(async () => {
    pageBlocks.value = []
    await executeHook("page:register", {
      page: "register",
      registrationEnabled: !!config.value?.allowRegistration,
      registerBlock,
    })
  })

  const register = async () => {
    try {
      const res = await fetch("/api/register", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          username: username.value,
          email: email.value,
          password: password.value,
        }),
      })
      const data = await res.json()
      if (!res.ok) {
        error.value = data.error || "Erreur d'inscription"
        return
      }
      await executeHook("user:register", {
        username: username.value,
        email: email.value,
      })
      // Redirige vers la page de login après inscription réussie
      window.location.href = "/login"
    } catch (e: any) {
      error.value = e.message || "Erreur d'inscription"
    }
  }
</script>

<style scoped>
  /* Tout le style est géré par Tailwind */
</style>
