import { H3Event, sendError, createError, eventHandler } from "h3"
import { MongoClient } from "mongodb"

export default eventHandler(async (event: H3Event) => {
  try {
    const body = await readBody(event)
    const { mongoUri, mongoDb } = body

    if (!mongoUri || !mongoDb) {
      return sendError(
        event,
        createError({
          statusCode: 400,
          statusMessage: "URI MongoDB et nom de base requis.",
        })
      )
    }

    // Test de connexion MongoDB
    const client = new MongoClient(mongoUri)

    try {
      await client.connect()

      // Test d'accès à la base de données
      const db = client.db(mongoDb)
      await db.admin().ping()

      // Vérifier les permissions de lecture/écriture
      await db.collection("_test").insertOne({ test: true })
      await db.collection("_test").deleteOne({ test: true })

      await client.close()

      return {
        success: true,
        message: "Connexion MongoDB réussie",
      }
    } catch (dbError: any) {
      await client.close()
      throw new Error(`Erreur de connexion MongoDB: ${dbError.message}`)
    }
  } catch (e: any) {
    return sendError(
      event,
      createError({
        statusCode: 500,
        statusMessage: e.message || "Erreur lors du test de connexion",
      })
    )
  }
})
