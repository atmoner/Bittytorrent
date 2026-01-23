<template>
  <span
    :class="[
      'inline-flex items-center px-3 py-1 rounded-full text-xs font-medium',
      color === 'primary' ? 'bg-blue-100 text-blue-800' : '',
      color === 'secondary' ? 'bg-gray-200 text-gray-800' : '',
      color === 'success' ? 'bg-green-100 text-green-800' : '',
      color === 'danger' ? 'bg-red-100 text-red-800' : '',
    ]"
  >
    <slot />
    <button
      v-if="closable"
      @click="$emit('close')"
      class="ml-2 focus:outline-none"
    >
      <svg
        xmlns="http://www.w3.org/2000/svg"
        class="h-3 w-3"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M6 18L18 6M6 6l12 12"
        />
      </svg>
    </button>
  </span>
</template>

<script setup lang="ts">
  const props = defineProps({
    color: { type: String, default: "primary" },
    closable: { type: Boolean, default: false },
  })
</script>
