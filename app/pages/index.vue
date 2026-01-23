<template>
  <section class="text-gray-600 body-font">
    <!-- Loading state -->
    <div
      v-if="configLoading"
      class="container px-5 py-24 mx-auto flex flex-wrap items-center"
    >
      <p>Chargement...</p>
    </div>
    <div
      v-else
      class="container px-5 py-24 mx-auto flex flex-wrap items-center"
    >
      <div class="lg:w-3/5 md:w-1/2 md:pr-16 lg:pr-0 pr-0">
        <h1 class="title-font font-medium text-3xl text-gray-900">
          Bienvenue sur {{ siteName }}
        </h1>
        <p class="leading-relaxed mt-4">
          Partagez, téléchargez et suivez vos torrents facilement grâce à notre
          plateforme communautaire.<br />
          Connectez-vous pour accéder à la liste des torrents, uploader les
          vôtres et suivre vos statistiques !
        </p>
      </div>
      <div
        class="lg:w-2/6 md:w-1/2 bg-gray-100 rounded-lg p-8 flex flex-col md:ml-auto w-full mt-10 md:mt-0"
      >
        <h2
          class="text-gray-900 text-lg font-medium title-font mb-5 text-center"
        >
          Connexion
        </h2>
        <LoginForm />

        <p class="text-xs text-gray-500 mt-3 text-center">
          Pas encore de compte ?
          <NuxtLink to="/register" class="text-indigo-500 hover:underline"
            >Inscrivez-vous</NuxtLink
          >
        </p>
      </div>
    </div>

    <!-- Section des derniers torrents -->
    <div class="container mx-auto px-5 py-12">
      <TorrentsList />
    </div>
  </section>
</template>
<script setup lang="ts">
  import LoginForm from "~/components/LoginForm.vue"
  import TorrentsList from "~/components/TorrentsList.vue"

  const { config, configLoading } = useConfig()

  const siteName = computed(() => config.value?.siteName)
</script>
