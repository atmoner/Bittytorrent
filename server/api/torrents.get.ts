import { connectToDatabase } from "../utils/mongodb"
import { H3Event, sendError, createError, send, eventHandler } from "h3"

export default eventHandler(async (event: H3Event) => {
  try {
    const query = getQuery(event)
    const { categoryId, search } = query

    const db = await connectToDatabase()

    // Construire le filtre de recherche
    const filter: any = {}

    // Filtre par catégorie
    if (categoryId && categoryId !== "all") {
      filter.categoryId = categoryId
    }

    // Filtre par recherche textuelle
    if (search && typeof search === "string") {
      filter.name = { $regex: search, $options: "i" }
    }

    const torrents = await db.collection("torrents").find(filter).toArray()
    return torrents
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
