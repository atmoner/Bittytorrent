import { connectToDatabase } from "../../../utils/mongodb"
import { ObjectId } from "mongodb"
import { H3Event, sendError, createError, eventHandler } from "h3"

export default eventHandler(async (event: H3Event) => {
  const session = await getUserSession(event)
  // Vérifier si l'utilisateur est admin
  const db = await connectToDatabase()
  const user = await db
    .collection("users")
    .findOne({ app_id: (session.user as any).id })

  if (!user || user.role !== "admin") {
    return sendError(
      event,
      createError({
        statusCode: 403,
        statusMessage: "Accès interdit - Admin requis.",
      }),
    )
  }
  try {
    const categoryId = getRouterParam(event, "id")
    if (!categoryId) {
      return sendError(
        event,
        createError({
          statusCode: 400,
          statusMessage: "ID de catégorie manquant.",
        }),
      )
    }

    // Vérifier si des torrents utilisent cette catégorie
    const torrentCount = await db
      .collection("torrents")
      .countDocuments({ categoryId })

    if (torrentCount > 0) {
      return sendError(
        event,
        createError({
          statusCode: 409,
          statusMessage: `Impossible de supprimer cette catégorie. ${torrentCount} torrent(s) l'utilisent.`,
        }),
      )
    }

    const result = await db.collection("categories").deleteOne({
      _id: new ObjectId(categoryId),
    })

    if (result.deletedCount === 0) {
      return sendError(
        event,
        createError({
          statusCode: 404,
          statusMessage: "Catégorie non trouvée.",
        }),
      )
    }

    return { success: true }
  } catch (e: any) {
    return sendError(
      event,
      createError({
        statusCode: 500,
        statusMessage: e.message || "Erreur serveur",
      }),
    )
  }
})
