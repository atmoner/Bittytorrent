import { H3Event, sendError, createError, eventHandler } from "h3"
import { MongoClient } from "mongodb"
import { hash } from "bcryptjs"
import { v4 as uuidv4 } from "uuid"
import { promises as fs } from "fs"
import { join } from "path"

export default eventHandler(async (event: H3Event) => {
  try {
    const body = await readBody(event)
    const {
      mongoUri,
      mongoDb,
      siteName,
      siteDescription,
      contactEmail,
      adminUsername,
      adminEmail,
      adminPassword,
    } = body

    // Validation des champs requis
    if (
      !mongoUri ||
      !mongoDb ||
      !siteName ||
      !contactEmail ||
      !adminUsername ||
      !adminEmail ||
      !adminPassword
    ) {
      return sendError(
        event,
        createError({
          statusCode: 400,
          statusMessage: "Tous les champs requis doivent être remplis.",
        })
      )
    }

    if (adminPassword.length < 6) {
      return sendError(
        event,
        createError({
          statusCode: 400,
          statusMessage:
            "Le mot de passe admin doit contenir au moins 6 caractères.",
        })
      )
    }

    // Connexion à MongoDB
    const client = new MongoClient(mongoUri)
    await client.connect()
    const db = client.db(mongoDb)

    // Vérifier si l'app est déjà installée
    const existingConfig = await db.collection("config").findOne({})
    if (existingConfig) {
      await client.close()
      return sendError(
        event,
        createError({
          statusCode: 409,
          statusMessage: "L'application est déjà installée.",
        })
      )
    }

    try {
      // 1. Créer la configuration du site
      const configData = {
        siteName,
        siteDescription,
        contactEmail,
        maxUploadSize: 10485760, // 10 MB par défaut
        allowRegistration: true,
        privateTracker: false,
        privateTrackerUrl: "",
        createdAt: new Date(),
        updatedAt: new Date(),
      }
      await db.collection("config").insertOne(configData)

      // 2. Créer le compte administrateur
      const hashedPassword = await hash(adminPassword, 10)
      const adminUser = {
        username: adminUsername,
        email: adminEmail,
        password: hashedPassword,
        role: "admin",
        app_id: uuidv4(),
        pid: uuidv4(),
        uploaded: 0,
        downloaded: 0,
        createdAt: new Date(),
        lastActivity: new Date(),
      }
      await db.collection("users").insertOne(adminUser)

      // 3. Créer les index nécessaires
      await db.collection("users").createIndex({ email: 1 }, { unique: true })
      await db.collection("users").createIndex({ app_id: 1 }, { unique: true })
      await db
        .collection("torrents")
        .createIndex({ infoHash: 1 }, { unique: true })
      await db.collection("torrents").createIndex({ uploadedBy: 1 })

      // 4. Créer le fichier .env avec la configuration MongoDB
      const envContent = `NUXT_MONGODB_URI=${mongoUri}
NUXT_MONGODB_DB=${mongoDb}
`
      const envPath = join(process.cwd(), ".env")
      await fs.writeFile(envPath, envContent, { flag: "w" })

      // 5. Créer le dossier torrents s'il n'existe pas
      const torrentsDir = join(process.cwd(), "torrents")
      try {
        await fs.access(torrentsDir)
      } catch {
        await fs.mkdir(torrentsDir, { recursive: true })
      }

      await client.close()

      return {
        success: true,
        message: "Installation terminée avec succès",
        config: {
          siteName,
          siteDescription,
          contactEmail,
          adminUsername,
          adminEmail,
        },
      }
    } catch (setupError: any) {
      await client.close()
      throw new Error(`Erreur lors de l'installation: ${setupError.message}`)
    }
  } catch (e: any) {
    return sendError(
      event,
      createError({
        statusCode: 500,
        statusMessage: e.message || "Erreur lors de l'installation",
      })
    )
  }
})
