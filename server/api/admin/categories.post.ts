import { connectToDatabase } from "../../utils/mongodb"
import type { Category } from "~/models"
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

    // Vérifier si une catégorie avec ce nom existe déjà
    const existingCategory = await db.collection("categories").findOne({ name })

    if (existingCategory) {
      return sendError(
        event,
        createError({
          statusCode: 409,
          statusMessage: "Une catégorie avec ce nom existe déjà.",
        }),
      )
    }

    const newCategory: Omit<Category, "_id"> = {
      name,
      description: description || "",
      color: color || "#3b82f6",
      createdAt: new Date(),
      updatedAt: new Date(),
    }

    const result = await db.collection("categories").insertOne(newCategory)

    return {
      success: true,
      categoryId: result.insertedId,
      category: newCategory,
    }
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
