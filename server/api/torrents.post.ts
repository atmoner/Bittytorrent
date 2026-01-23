import { connectToDatabase } from "../utils/mongodb"
import type { Torrent } from "~/models"
import { H3Event, sendError, createError, send, eventHandler } from "h3"

export default eventHandler(async (event: H3Event) => {
  try {
    const body = await readBody(event)
    const { infoHash, name, size, uploadedBy } = body
    if (!infoHash || !name || !size || !uploadedBy) {
      return sendError(
        event,
        createError({
          statusCode: 400,
          statusMessage: "Champs requis manquants.",
        })
      )
    }
    const db = await connectToDatabase()
    const newTorrent = { infoHash, name, size, uploadedBy }
    const { _id, ...torrentData } = newTorrent as any // Omit _id if present
    await db.collection("torrents").insertOne(torrentData)
    return send(event, { success: true })
  } catch (e: any) {
    return sendError(
      event,
      createError({
        statusCode: 500,
        statusMessage: e.message || "Erreur serveur",
      })
    )
  }
})
