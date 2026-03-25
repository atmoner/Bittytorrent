<template>
  <Teleport to="body">
    <Transition name="modal">
      <div
        v-if="state.visible"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        @mousedown.self="onBackdropClick"
      >
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/50" />

        <!-- Panel -->
        <div
          class="relative bg-white rounded-xl shadow-2xl w-full max-w-md mx-auto flex flex-col overflow-hidden"
          role="dialog"
          aria-modal="true"
          :aria-labelledby="'modal-title'"
        >
          <!-- Header -->
          <div
            class="flex items-center gap-3 px-6 pt-5 pb-4 border-b border-gray-100"
            :class="headerColors[state.type]"
          >
            <span
              class="flex-shrink-0 flex items-center justify-center w-9 h-9 rounded-full"
              :class="iconBg[state.type]"
            >
              <svg
                v-if="state.type === 'success'"
                class="w-5 h-5 text-green-600"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M5 13l4 4L19 7"
                />
              </svg>
              <svg
                v-else-if="state.type === 'error'"
                class="w-5 h-5 text-red-600"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M6 18L18 6M6 6l12 12"
                />
              </svg>
              <svg
                v-else-if="state.type === 'warning'"
                class="w-5 h-5 text-yellow-600"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"
                />
              </svg>
              <svg
                v-else
                class="w-5 h-5 text-blue-600"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 110 20A10 10 0 0112 2z"
                />
              </svg>
            </span>
            <h2 id="modal-title" class="text-base font-semibold text-gray-800">
              {{ state.title }}
            </h2>
          </div>

          <!-- Body -->
          <div
            class="px-6 py-5 text-sm text-gray-700 whitespace-pre-wrap leading-relaxed"
          >
            {{ state.message }}
          </div>

          <!-- Footer -->
          <div class="flex justify-end gap-3 px-6 pb-5">
            <button
              v-if="state.mode === 'confirm'"
              class="px-4 py-2 rounded-lg text-sm font-medium bg-gray-100 text-gray-700 hover:bg-gray-200 transition"
              @click="cancel"
            >
              Annuler
            </button>
            <button
              class="px-4 py-2 rounded-lg text-sm font-medium text-white transition"
              :class="confirmColors[state.type]"
              @click="confirm"
            >
              {{ state.mode === "confirm" ? "Confirmer" : "OK" }}
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup lang="ts">
  import { useModal } from "~/composables/useModal"

  const { state, confirm, cancel } = useModal()

  const headerColors: Record<string, string> = {
    info: "bg-blue-50",
    success: "bg-green-50",
    error: "bg-red-50",
    warning: "bg-yellow-50",
  }

  const iconBg: Record<string, string> = {
    info: "bg-blue-100",
    success: "bg-green-100",
    error: "bg-red-100",
    warning: "bg-yellow-100",
  }

  const confirmColors: Record<string, string> = {
    info: "bg-blue-600 hover:bg-blue-700",
    success: "bg-green-600 hover:bg-green-700",
    error: "bg-red-600 hover:bg-red-700",
    warning: "bg-yellow-500 hover:bg-yellow-600",
  }

  // Le clic sur le backdrop ferme la modale comme un "OK/annuler" selon le mode
  const onBackdropClick = () => {
    if (state.mode === "confirm") cancel()
    else confirm()
  }
</script>

<style scoped>
  .modal-enter-active,
  .modal-leave-active {
    transition: opacity 0.2s ease;
  }
  .modal-enter-from,
  .modal-leave-to {
    opacity: 0;
  }
  .modal-enter-active .relative,
  .modal-leave-active .relative {
    transition: transform 0.2s ease;
  }
  .modal-enter-from .relative,
  .modal-leave-to .relative {
    transform: scale(0.95);
  }
</style>
