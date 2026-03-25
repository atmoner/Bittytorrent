import { connectToDatabase } from "../utils/mongodb"
import { H3Event, sendError, createError, eventHandler } from "h3"

export default eventHandler(async (event: H3Event) => {
  try {
    const db = await connectToDatabase()
    const categories = await db
      .collection("categories")
      .find({})
      .sort({ name: 1 })
      .toArray()

    return categories
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
