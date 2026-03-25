<template>
  <component
    :is="iconComponent"
    v-bind="iconProps"
    class="inline-block align-middle"
  />
</template>

<script setup lang="ts">
  import * as HeroIcons from "@heroicons/vue/24/outline"

  const props = defineProps({
    name: { type: String, required: true },
    size: { type: [String, Number], default: 24 },
    class: { type: String, default: "" },
  })

  // Format props.name (e.g., "arrow-small-leftIcon") to "ArrowSmallLeftIcon"
  function toPascalCase(str: string) {
    return (
      str
        .replace(/-([a-z])/g, (_, c) => c.toUpperCase()) // kebab-case to camelCase
        .replace(/^([a-z])/, (_, c) => c.toUpperCase()) // capitalize first letter
        .replace(/Icon$/, "") + // remove trailing 'Icon' if present
      "Icon"
    ) // ensure 'Icon' suffix
  }

  const iconComponent =
    HeroIcons[toPascalCase(props.name) as keyof typeof HeroIcons]
  const iconProps = {
    class: props.class,
    width: props.size,
    height: props.size,
  }
</script>
