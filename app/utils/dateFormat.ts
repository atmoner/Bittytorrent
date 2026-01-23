// utils/dateFormat.ts
// Utilitaire pour formater une date en string lisible

export function formatDate(dateInput: string | number | Date): string {
  const date = new Date(dateInput)
  if (isNaN(date.getTime())) return ''
  return date.toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}
