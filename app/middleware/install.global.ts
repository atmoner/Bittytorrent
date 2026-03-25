export default defineNuxtRouteMiddleware(async (to) => {
  // Ignorer le middleware sur la page d'installation et les API
  if (to.path === "/install" || to.path.startsWith("/api/")) {
    return
  }

  try {
    const installCheck = await $fetch("/api/install/check")

    if (!installCheck.installed) {
      return navigateTo("/install")
    }
  } catch (error) {
    // En cas d'erreur de connexion, rediriger vers l'installation
    console.log(
      "Erreur de vérification installation, redirection vers /install"
    )
    return navigateTo("/install")
  }
})
