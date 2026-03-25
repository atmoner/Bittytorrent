export type ModalType = "info" | "success" | "error" | "warning"

interface ModalState {
  visible: boolean
  mode: "alert" | "confirm"
  type: ModalType
  title: string
  message: string
  resolve: ((value: boolean) => void) | null
}

const state = reactive<ModalState>({
  visible: false,
  mode: "alert",
  type: "info",
  title: "",
  message: "",
  resolve: null,
})

export const useModal = () => {
  /**
   * Affiche une modale de type "alerte" (OK uniquement).
   * Remplace window.alert()
   */
  const showAlert = (
    message: string,
    type: ModalType = "info",
    title?: string,
  ): Promise<void> => {
    return new Promise((resolve) => {
      state.visible = true
      state.mode = "alert"
      state.type = type
      state.title = title ?? defaultTitle(type)
      state.message = message
      state.resolve = () => {
        state.visible = false
        resolve()
      }
    })
  }

  /**
   * Affiche une modale de confirmation (Confirmer / Annuler).
   * Remplace window.confirm() — retourne une Promise<boolean>
   */
  const showConfirm = (
    message: string,
    title = "Confirmation",
  ): Promise<boolean> => {
    return new Promise((resolve) => {
      state.visible = true
      state.mode = "confirm"
      state.type = "warning"
      state.title = title
      state.message = message
      state.resolve = (value: boolean) => {
        state.visible = false
        resolve(value)
      }
    })
  }

  const confirm = () => state.resolve?.(true)
  const cancel = () => state.resolve?.(false)

  return { state, showAlert, showConfirm, confirm, cancel }
}

function defaultTitle(type: ModalType): string {
  const titles: Record<ModalType, string> = {
    info: "Information",
    success: "Succès",
    error: "Erreur",
    warning: "Attention",
  }
  return titles[type]
}
