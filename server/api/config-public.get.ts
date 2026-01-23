import { connectToDatabase } from "../utils/mongodb"
import { H3Event, sendError, createError, eventHandler } from "h3"

export default eventHandler(async (event: H3Event) => {
  try {
    const db = await connectToDatabase()
    const config = await db.collection("config").findOne({})

    if (!config) {
      // Retourner la configuration par défaut si elle n'existe pas
      return {
        success: true,
        config: {
          siteName: "Nuxt Tracker",
          siteDescription: "Un tracker BitTorrent moderne",
          allowRegistration: true,
        },
      }
    }

    // Ne retourner que les champs publics
    return {
      success: true,
      config: {
        siteName: config.siteName,
        siteDescription: config.siteDescription,
        allowRegistration: config.allowRegistration,
        registrationClosedMessage: config.registrationClosedMessage || "",
      },
    }
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
