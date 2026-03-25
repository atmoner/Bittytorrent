import { connectToDatabase } from "../../utils/mongodb"
import { H3Event, sendError, createError, eventHandler } from "h3"
import { promises as fs } from "fs"
import { join } from "path"
import bencode from "bencode"

export default eventHandler(async (event: H3Event) => {
  const infoHash = getRouterParam(event, "infoHash")

  const session = await getUserSession(event)
  if (!session || !session.user) {
    return sendError(
      event,
      createError({ statusCode: 401, statusMessage: "Non autorisé." })
    )
  }

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
    const config = await db.collection("config").findOne({})
    const user = await db
      .collection("users")
      .findOne({ app_id: (session.user as any).id })

    if (!user) {
      return sendError(
        event,
        createError({
          statusCode: 401,
          statusMessage: "Utilisateur non trouvé.",
        })
      )
    }

    if (!torrent) {
      return sendError(
        event,
        createError({
          statusCode: 404,
          statusMessage: "Torrent non trouvé.",
        })
      )
    }

    const filePath = join(process.cwd(), "torrents", `${infoHash}.torrent`)
    const baseUrl =
      process.env.BASE_URL ||
      config?.privateTrackerUrl ||
      "http://localhost:4200"
    const announceUrl = `${baseUrl}/announce?pid=${user.pid}`

    if (torrent.private) {
      try {
        const fileBuffer = await fs.readFile(filePath)
        const torrentData = bencode.decode(fileBuffer)

        torrentData.announce = announceUrl
        if (torrentData["announce-list"]) {
          torrentData["announce-list"] = [[announceUrl]]
        }

        const modifiedBuffer = bencode.encode(torrentData)

        // Set headers for file download
        setHeader(event, "Content-Type", "application/x-bittorrent")
        setHeader(
          event,
          "Content-Disposition",
          `attachment; filename="${torrent.name}.torrent"`
        )
        setHeader(event, "Content-Length", modifiedBuffer.length)

        return modifiedBuffer
      } catch (fileError) {
        return sendError(
          event,
          createError({
            statusCode: 404,
            statusMessage: "Fichier torrent non trouvé  ",
          })
        )
      }
    } else {
      try {
        const fileBuffer = await fs.readFile(filePath)
        const torrentData = bencode.decode(fileBuffer)
        const announceUrl = `${baseUrl}/announce`

        if (torrentData["announce-list"]) {
          torrentData["announce-list"].push([announceUrl])
        }

        const modifiedBuffer = bencode.encode(torrentData)

        // Set headers for file download
        setHeader(event, "Content-Type", "application/x-bittorrent")
        setHeader(
          event,
          "Content-Disposition",
          `attachment; filename="${torrent.name}.torrent"`
        )
        setHeader(event, "Content-Length", modifiedBuffer.length)

        return modifiedBuffer
      } catch (fileError) {
        return sendError(
          event,
          createError({
            statusCode: 404,
            statusMessage: "Fichier torrent non trouvé sur le disque.",
          })
        )
      }
    }
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
