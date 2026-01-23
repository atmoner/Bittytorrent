<template></template>
<script setup lang="ts">
  const { clear } = useUserSession()

  await clear()
  await navigateTo("/login")
</script>
