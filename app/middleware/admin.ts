export default defineNuxtRouteMiddleware((to, from) => {
  const { user } = useUserSession()

  // Rediriger vers login si pas connecté
  if (!user.value) {
    return navigateTo("/login")
  }

  // Vérifier si l'utilisateur est admin
  if (user.value.role !== "admin") {
    throw createError({
      statusCode: 403,
      statusMessage: "Accès interdit - Admin requis",
    })
  }
})
