import { connectToDatabase } from "../../utils/mongodb"
import { H3Event, sendError, createError, eventHandler } from "h3"
import { promises as fs } from "fs"
import { join } from "path"

export default eventHandler(async (event: H3Event) => {
  // Vérifier l'authentification
  const session = await getUserSession(event)
  if (!session || !session.user) {
    return sendError(
      event,
      createError({ statusCode: 401, statusMessage: "Non autorisé." })
    )
  }

  try {
    const infoHash = getRouterParam(event, "infoHash")

    if (!infoHash) {
      return sendError(
        event,
        createError({ statusCode: 400, statusMessage: "InfoHash manquant." })
      )
    }

    const db = await connectToDatabase()

    // Récupérer le torrent à supprimer
    const torrent = await db.collection("torrents").findOne({
      infoHash: infoHash,
    })

    if (!torrent) {
      return sendError(
        event,
        createError({ statusCode: 404, statusMessage: "Torrent non trouvé." })
      )
    }

    // Récupérer les informations de l'utilisateur connecté
    const user = await db.collection("users").findOne({
      app_id: (session.user as any).id,
    })

    if (!user) {
      return sendError(
        event,
        createError({
          statusCode: 404,
          statusMessage: "Utilisateur non trouvé.",
        })
      )
    }

    // Vérifier les permissions : soit admin, soit propriétaire du torrent
    const isAdmin = user.role === "admin"
    const isOwner = torrent.uploadedBy === (session.user as any).id

    if (!isAdmin && !isOwner) {
      return sendError(
        event,
        createError({
          statusCode: 403,
          statusMessage:
            "Accès interdit - Vous ne pouvez supprimer que vos propres torrents.",
        })
      )
    }

    // Supprimer le torrent de la base de données
    const deleteResult = await db.collection("torrents").deleteOne({
      infoHash: infoHash,
    })

    if (deleteResult.deletedCount === 0) {
      return sendError(
        event,
        createError({
          statusCode: 500,
          statusMessage: "Erreur lors de la suppression du torrent.",
        })
      )
    }

    // Supprimer le fichier .torrent physique
    try {
      const filePath = join(process.cwd(), "torrents", `${infoHash}.torrent`)
      await fs.unlink(filePath)
    } catch (fileError) {
      // Log l'erreur mais ne pas faire échouer la requête
      console.error(
        "Erreur lors de la suppression du fichier .torrent:",
        fileError
      )
    }

    return {
      success: true,
      message: "Torrent supprimé avec succès.",
      deletedTorrent: {
        infoHash: torrent.infoHash,
        name: torrent.name,
      },
    }
  } catch (e: any) {
    return sendError(
      event,
      createError({
        statusCode: 500,
        statusMessage: e.message || "Erreur serveur lors de la suppression",
      })
    )
  }
})
