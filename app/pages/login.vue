<template>
  <div class="flex flex-col items-center justify-center">
    <div class="w-full max-w-md bg-white rounded-lg shadow-md p-8">
      <h2 class="text-2xl font-bold mb-6 text-center">Connexion</h2>
      <LoginForm />
      <p class="mt-4 text-center text-sm text-gray-600">
        Pas de compte ?
        <NuxtLink to="/register" class="text-indigo-500 hover:underline"
          >Inscription</NuxtLink
        >
      </p>
    </div>
  </div>
</template>

<script setup lang="ts">
  import LoginForm from "~/components/LoginForm.vue"
</script>

<style scoped>
  /* Tout le style est géré par Tailwind */
</style>
