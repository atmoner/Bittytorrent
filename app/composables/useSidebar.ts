/**
 * État global partagé pour la visibilité de la sidebar.
 * Utilisé par AppHeader (boutons plugins) et app.vue (affichage).
 */
const _sidebarOpen = ref(false)

export const useSidebar = () => {
  const toggleSidebar = () => {
    _sidebarOpen.value = !_sidebarOpen.value
  }

  const openSidebar = () => {
    _sidebarOpen.value = true
  }

  const closeSidebar = () => {
    _sidebarOpen.value = false
  }

  return {
    sidebarOpen: readonly(_sidebarOpen),
    toggleSidebar,
    openSidebar,
    closeSidebar,
  }
}
