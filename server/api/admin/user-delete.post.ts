import { connectToDatabase } from "../../utils/mongodb"
import { H3Event, sendError, createError, eventHandler } from "h3"
import { ObjectId } from "mongodb"

export default eventHandler(async (event: H3Event) => {
  const session = await getUserSession(event)
  if (!session || !session.user) {
    return sendError(
      event,
      createError({ statusCode: 401, statusMessage: "Non autorisé." })
    )
  }

  // Vérifier si l'utilisateur est admin
  const db = await connectToDatabase()
  const user = await db.collection("users").findOne({ app_id: session.user.id })

  if (!user || user.role !== "admin") {
    return sendError(
      event,
      createError({
        statusCode: 403,
        statusMessage: "Accès interdit - Admin requis.",
      })
    )
  }

  try {
    const body = await readBody(event)
    const { userId } = body

    if (!userId) {
      return sendError(
        event,
        createError({
          statusCode: 400,
          statusMessage: "ID utilisateur manquant.",
        })
      )
    }

    const result = await db
      .collection("users")
      .deleteOne({ _id: new ObjectId(userId) })

    if (result.deletedCount === 0) {
      return sendError(
        event,
        createError({
          statusCode: 404,
          statusMessage: "Utilisateur non trouvé.",
        })
      )
    }

    return { success: true, message: "Utilisateur supprimé avec succès." }
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
