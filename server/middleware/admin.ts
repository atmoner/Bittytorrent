import { connectToDatabase } from "../utils/mongodb"
import { H3Event, sendError, createError } from "h3"

export default defineEventHandler(async (event: H3Event) => {
  // Vérifier uniquement les routes /api/admin/*
  if (!event.path?.startsWith("/api/admin/")) {
    return
  }

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
})
