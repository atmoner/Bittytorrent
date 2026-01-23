import { connectToDatabase } from "../../utils/mongodb"
import { H3Event, sendError, createError, eventHandler } from "h3"

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
    const users = await db
      .collection("users")
      .find({})
      .project({ password: 0 }) // Ne pas envoyer les mots de passe
      .toArray()

    return { success: true, users }
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
