import { H3Event, eventHandler } from "h3"
import { MongoClient } from "mongodb"

export default eventHandler(async (event: H3Event) => {
  try {
    // Vérifier si l'environnement est configuré
    const config = useRuntimeConfig()

    if (!config.mongoDbUrl || !config.mongoDbName) {
      return { installed: false, reason: "MongoDB non configuré" }
    }

    // Tester la connexion et vérifier si la config existe
    try {
      const client = new MongoClient(config.mongoDbUrl)
      await client.connect()

      const db = client.db(config.mongoDbName)
      const configExists = await db.collection("config").findOne({})

      await client.close()

      return {
        installed: !!configExists,
        reason: configExists
          ? "Application installée"
          : "Configuration manquante",
      }
    } catch (dbError: any) {
      return {
        installed: false,
        reason: "Erreur de connexion à la base de données",
      }
    }
  } catch (e: any) {
    return {
      installed: false,
      reason: "Erreur lors de la vérification de l'installation",
    }
  }
})
