// utils/byteFormat.ts
// Utilitaire pour convertir les bytes en format humainement lisible

export function formatBytes(bytes: number, decimals: number = 2): string {
  if (bytes === 0) return "0 Bytes"

  const k = 1024
  const dm = decimals < 0 ? 0 : decimals
  const sizes = ["Bytes", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB"]

  const i = Math.floor(Math.log(bytes) / Math.log(k))

  return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + " " + sizes[i]
}

// Version courte pour les affichages compacts
export function formatBytesShort(bytes: number): string {
  if (bytes === 0) return "0 B"

  const k = 1024
  const sizes = ["B", "KB", "MB", "GB", "TB"]

  const i = Math.floor(Math.log(bytes) / Math.log(k))

  if (i === 0) return bytes + " B"

  const value = bytes / Math.pow(k, i)
  return (value < 10 ? value.toFixed(1) : Math.round(value)) + " " + sizes[i]
}
