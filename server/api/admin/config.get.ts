import { connectToDatabase } from "../../utils/mongodb"
import { H3Event, sendError, createError, eventHandler } from "h3"

export default eventHandler(async (event: H3Event) => {
  const session = await getUserSession(event)
  if (!session || !session.user) {
    return sendError(
      event,
      createError({ statusCode: 401, statusMessage: "Non autorisé." }),
    )
  }

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
    const config = await db.collection("config").findOne({})

    if (!config) {
      // Créer une configuration par défaut si elle n'existe pas
      const defaultConfig = {
        siteName: "Nuxt Tracker",
        siteDescription: "Un tracker BitTorrent moderne",
        contactEmail: "admin@example.com",
        maxUploadSize: 10485760, // 10 MB
        allowRegistration: true,
        themeCssFile: "",
        createdAt: new Date(),
        updatedAt: new Date(),
      }
      await db.collection("config").insertOne(defaultConfig)
      return { success: true, config: defaultConfig }
    }

    return { success: true, config }
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
