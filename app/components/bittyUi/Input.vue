<template>
  <div>
    <label
      v-if="label"
      :for="id"
      class="block text-sm font-medium text-gray-700 mb-1"
      >{{ label }}</label
    >
    <input
      :id="id"
      :type="type"
      :placeholder="placeholder"
      v-model="modelValue"
      :class="[
        'block w-full rounded border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 text-base outline-none py-2 px-3 transition-colors duration-200',
        error ? 'border-red-500' : '',
      ]"
      @input="$emit('update:modelValue', $event.target.value)"
      :disabled="disabled"
    />
    <p v-if="error" class="text-xs text-red-600 mt-1">{{ error }}</p>
  </div>
</template>

<script setup lang="ts">
  const props = defineProps({
    modelValue: { type: [String, Number], default: "" },
    label: { type: String, default: "" },
    id: { type: String, default: "" },
    type: { type: String, default: "text" },
    placeholder: { type: String, default: "" },
    error: { type: String, default: "" },
    disabled: { type: Boolean, default: false },
  })
</script>
