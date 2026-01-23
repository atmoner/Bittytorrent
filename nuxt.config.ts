// https://nuxt.com/docs/api/configuration/nuxt-config
import tailwindcss from "@tailwindcss/vite"

export default defineNuxtConfig({
  compatibilityDate: "2025-07-15",
  devtools: { enabled: true },
  plugins: ["~/plugins/tracker.server.ts"],
  modules: ["nuxt-auth-utils"],
  css: ["./app/assets/css/main.css"],
  vite: {
    plugins: [tailwindcss()],
  },
  nitro: {
    experimental: {
      wasm: true,
    },
    // Exclure les modules natifs problématiques
    rollupConfig: {
      external: ["node-datachannel"],
    },
    // Configuration des modules qui ne doivent pas être bundlés
    unenv: {
      external: ["node-datachannel"],
    },
    // Ignorer les modules optionnels
    ignore: ["node-datachannel"],
  },
  runtimeConfig: {
    mongoDbUrl: process.env.NUXT_MONGODB_URI || "",
    mongoDbName: process.env.NUXT_MONGODB_DB || "",
    session: {
      password:
        process.env.NUXT_SESSION_PASSWORD ||
        "default-session-password-change-me",
    },
  },
})
