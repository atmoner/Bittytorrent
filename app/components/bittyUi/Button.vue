<template>
  <button
    :class="[
      'inline-flex items-center justify-center font-medium rounded transition focus:outline-none',
      color === 'primary' ? 'bg-blue-600 text-white hover:bg-blue-700' : '',
      color === 'secondary'
        ? 'bg-gray-200 text-gray-800 hover:bg-gray-300'
        : '',
      color === 'danger' ? 'bg-red-600 text-white hover:bg-red-700' : '',
      size === 'sm'
        ? 'px-3 py-1 text-sm'
        : size === 'lg'
        ? 'px-6 py-3 text-lg'
        : 'px-4 py-2',
      disabled ? 'opacity-50 cursor-not-allowed' : '',
    ]"
    :disabled="disabled"
    @click="$emit('click', $event)"
  >
    <template v-if="icon && iconPosition === 'left'">
      <BittyIcon :name="icon" class="w-5 h-5 mr-2 -ml-1" />
    </template>
    <slot />
    <template v-if="icon && iconPosition === 'right'">
      <BittyIcon :name="icon" class="w-5 h-5 ml-2 -mr-1" />
    </template>
  </button>
</template>

<script setup lang="ts">
  import BittyIcon from "~/components/bittyUi/Icon.vue"

  const props = defineProps({
    color: { type: String, default: "primary" },
    size: { type: String, default: "md" },
    disabled: { type: Boolean, default: false },
    icon: { type: String, default: "" },
    iconPosition: {
      type: String,
      default: "left",
      validator: (v: string) => ["left", "right"].includes(v),
    },
  })
</script>
