import { connectToDatabase } from "../../utils/mongodb"
import { H3Event, sendError, createError, eventHandler } from "h3"
import type { Config } from "~/models"

const THEME_FILE_REGEX = /^[a-zA-Z0-9._-]+\.css$/

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
    const body = await readBody(event)
    const {
      siteName,
      siteDescription,
      contactEmail,
      maxUploadSize,
      allowRegistration,
      registrationClosedMessage,
      themeCssFile,
      privateTracker,
      privateTrackerUrl,
      privateTrackerRatio,
    } = body

    if (themeCssFile !== undefined) {
      if (themeCssFile !== "" && !THEME_FILE_REGEX.test(themeCssFile)) {
        return sendError(
          event,
          createError({
            statusCode: 400,
            statusMessage: "Le fichier de thème est invalide.",
          }),
        )
      }
    }

    const updateData: any = {
      updatedAt: new Date(),
    }

    if (siteName !== undefined) updateData.siteName = siteName
    if (siteDescription !== undefined)
      updateData.siteDescription = siteDescription
    if (contactEmail !== undefined) updateData.contactEmail = contactEmail
    if (maxUploadSize !== undefined) updateData.maxUploadSize = maxUploadSize
    if (allowRegistration !== undefined)
      updateData.allowRegistration = allowRegistration
    if (registrationClosedMessage !== undefined)
      updateData.registrationClosedMessage = registrationClosedMessage
    if (themeCssFile !== undefined) updateData.themeCssFile = themeCssFile
    if (privateTracker !== undefined) updateData.privateTracker = privateTracker
    if (privateTrackerUrl !== undefined)
      updateData.privateTrackerUrl = privateTrackerUrl
    if (privateTrackerRatio !== undefined)
      updateData.privateTrackerRatio = privateTrackerRatio

    // Vérifier si une configuration existe déjà

    const existingConfig = await db.collection("config").findOne({})

    if (!existingConfig) {
      // Créer une nouvelle configuration
      const newConfig: Omit<Config, "_id"> = {
        siteName: siteName || "Nuxt Tracker",
        siteDescription: siteDescription || "Un tracker BitTorrent moderne",
        contactEmail: contactEmail || "admin@example.com",
        maxUploadSize: maxUploadSize || 10485760,
        allowRegistration:
          allowRegistration !== undefined ? allowRegistration : true,
        registrationClosedMessage: registrationClosedMessage || "",
        themeCssFile: themeCssFile || "",
        createdAt: new Date(),
        updatedAt: new Date(),
        privateTracker: false,
        privateTrackerUrl: "",
        privateTrackerRatio: 1.0,
      }
      await db.collection("config").insertOne(newConfig)
    } else {
      // Mettre à jour la configuration existante
      await db.collection("config").updateOne({}, { $set: updateData })
    }

    return { success: true, message: "Configuration mise à jour avec succès." }
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
