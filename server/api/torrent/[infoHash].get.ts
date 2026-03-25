import { connectToDatabase } from "../../utils/mongodb"
import { H3Event, sendError, createError, eventHandler } from "h3"

export default eventHandler(async (event: H3Event) => {
  const infoHash = getRouterParam(event, "infoHash")

  if (!infoHash) {
    return sendError(
      event,
      createError({
        statusCode: 400,
        statusMessage: "infoHash manquant.",
      })
    )
  }

  try {
    const db = await connectToDatabase()
    const torrent = await db.collection("torrents").findOne({ infoHash })

    if (!torrent) {
      return sendError(
        event,
        createError({
          statusCode: 404,
          statusMessage: "Torrent non trouvé.",
        })
      )
    }

    return torrent
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
