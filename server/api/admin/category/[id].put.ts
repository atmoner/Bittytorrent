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

    const body = await readBody(event)
    const { name, description, color } = body

    if (!name) {
      return sendError(
        event,
        createError({
          statusCode: 400,
          statusMessage: "Le nom de la catégorie est requis.",
        }),
      )
    }

    // Vérifier si une autre catégorie avec ce nom existe déjà
    const existingCategory = await db
      .collection("categories")
      .findOne({ name, _id: { $ne: new ObjectId(categoryId) } })

    if (existingCategory) {
      return sendError(
        event,
        createError({
          statusCode: 409,
          statusMessage: "Une catégorie avec ce nom existe déjà.",
        }),
      )
    }

    const updateData = {
      name,
      description: description || "",
      color: color || "#3b82f6",
      updatedAt: new Date(),
    }

    const result = await db
      .collection("categories")
      .updateOne({ _id: new ObjectId(categoryId) }, { $set: updateData })

    if (result.matchedCount === 0) {
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
