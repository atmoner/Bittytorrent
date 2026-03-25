import { pid } from "process"
import { connectToDatabase } from "../utils/mongodb"
import { H3Event, sendError, createError, eventHandler } from "h3"

export default eventHandler(async (event: H3Event) => {
  try {
    const session = await getUserSession(event)
    if (!session || !session.user) {
      return sendError(
        event,
        createError({ statusCode: 401, statusMessage: "Non autorisé." })
      )
    }

    const db = await connectToDatabase()

    // Récupérer les informations utilisateur depuis la base de données
    const user = await db
      .collection("users")
      .findOne({ app_id: (session.user as any).id })

    if (!user) {
      return sendError(
        event,
        createError({
          statusCode: 404,
          statusMessage: "Utilisateur non trouvé.",
        })
      )
    }

    // Compter le nombre de torrents uploadés par l'utilisateur
    const uploadsCount = await db.collection("torrents").countDocuments({
      uploadedBy: (session.user as any).id,
    })

    // Calculer la taille totale des torrents uploadés
    const uploadedTorrents = await db
      .collection("torrents")
      .find({
        uploadedBy: (session.user as any).id,
      })
      .toArray()

    const totalUploadedSize = uploadedTorrents.reduce((total, torrent) => {
      return total + (torrent.length || torrent.size || 0)
    }, 0)

    // Calculer le ratio (uploaded / downloaded)
    const ratio =
      user.downloaded > 0 ? (user.uploaded / user.downloaded).toFixed(2) : "∞"

    return {
      success: true,
      data: {
        username: user.username,
        email: user.email,
        createdAt: user.createdAt,
        role: user.role || "user",
        uploaded: user.uploaded || 0,
        downloaded: user.downloaded || 0,
        uploadsCount: uploadsCount,
        totalUploadedSize: totalUploadedSize,
        ratio: ratio,
        lastActivity: user.lastActivity,
        appId: user.app_id,
        pid: user.pid,
        totalUploadTorrents: uploadedTorrents,
      },
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
