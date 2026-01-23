<template>
  <form class="flex flex-col gap-4" @submit.prevent="onLogin">
    <div class="relative">
      <label for="email" class="leading-7 text-sm text-gray-600">Email</label>
      <input
        v-model="email"
        type="email"
        id="email"
        name="email"
        placeholder="Votre email"
        class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-2 px-3 leading-8 transition-colors duration-200 ease-in-out"
        required
      />
    </div>
    <div class="relative">
      <label for="password" class="leading-7 text-sm text-gray-600"
        >Mot de passe</label
      >
      <input
        v-model="password"
        type="password"
        id="password"
        name="password"
        placeholder="Votre mot de passe"
        class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-2 px-3 leading-8 transition-colors duration-200 ease-in-out"
        required
      />
    </div>
    <button
      type="submit"
      class="text-white bg-indigo-500 border-0 py-2 px-8 focus:outline-none hover:bg-indigo-700 rounded text-lg"
    >
      Se connecter
    </button>
    <div v-if="error" class="text-red-600 mt-2 text-center">{{ error }}</div>
  </form>
</template>

<script setup lang="ts">
  import { ref } from "vue"
  import { useRouter } from "vue-router"
  const { loggedIn, user, fetch: _fetch, session } = useUserSession()

  const emit = defineEmits(["login-success"])
  const email = ref("contact.atmoner@gmail.com")
  const password = ref("azerty")
  const error = ref("")
  const router = useRouter()

  onMounted(async () => {
    // Si déjà connecté, émettre l'événement de succès de connexion
    if (loggedIn.value) {
      await navigateTo("/account")
    }
  })

  const onLogin = async () => {
    error.value = ""
    try {
      const res = await fetch("/api/login", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email: email.value, password: password.value }),
        credentials: "include",
      })
      const data = await res.json()
      if (!res.ok) {
        error.value = data.error || "Erreur de connexion"
        return
      }

      //router.push("/torrents")
      _fetch() // Met à jour le state de la session utilisateur côté client
      await navigateTo("/account")
      emit("login-success")
    } catch (e: any) {
      error.value = e.message || "Erreur de connexion"
    }
  }
</script>
