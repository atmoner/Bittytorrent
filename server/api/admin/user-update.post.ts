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
    const { userId, username, email, role } = body

    if (!userId) {
      return sendError(
        event,
        createError({
          statusCode: 400,
          statusMessage: "ID utilisateur manquant.",
        })
      )
    }

    const updateData: any = {}
    if (username) updateData.username = username
    if (email) updateData.email = email
    if (role) updateData.role = role

    const result = await db
      .collection("users")
      .updateOne({ _id: new ObjectId(userId) }, { $set: updateData })

    if (result.matchedCount === 0) {
      return sendError(
        event,
        createError({
          statusCode: 404,
          statusMessage: "Utilisateur non trouvé.",
        })
      )
    }

    return { success: true, message: "Utilisateur mis à jour avec succès." }
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
