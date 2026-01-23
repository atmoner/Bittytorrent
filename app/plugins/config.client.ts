export default defineNuxtPlugin(async () => {
  const { fetchConfig } = useConfig()
  await fetchConfig()
})
