import { connectToDatabase } from "../../utils/mongodb"
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
    // Supprimer toutes les sessions actives
    const result = await db.collection("peer_sessions").deleteMany({})

    console.log(`${result.deletedCount} sessions peer supprimées`)

    return {
      success: true,
      message: `${result.deletedCount} sessions peer supprimées`,
    }
  } catch (e: any) {
    return sendError(
      event,
      createError({
        statusCode: 500,
        statusMessage:
          e.message || "Erreur lors de la suppression des sessions",
      }),
    )
  }
})
