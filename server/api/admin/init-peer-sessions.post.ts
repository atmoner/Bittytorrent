import { connectToDatabase } from "../../utils/mongodb"
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
    // Créer la collection avec des index pour les performances
    await db.createCollection("peer_sessions")

    // Index composé pour peer_id + infoHash (clé unique)
    await db
      .collection("peer_sessions")
      .createIndex({ peer_id: 1, infoHash: 1 }, { unique: true })

    // Index pour la suppression automatique des anciennes sessions (TTL)
    await db.collection("peer_sessions").createIndex(
      { lastSeen: 1 },
      { expireAfterSeconds: 86400 }, // 24 heures
    )

    console.log("Collection peer_sessions créée avec succès")

    return { success: true, message: "Collection peer_sessions initialisée" }
  } catch (e: any) {
    if (e.codeName === "NamespaceExists") {
      return { success: true, message: "Collection peer_sessions existe déjà" }
    }

    return sendError(
      event,
      createError({
        statusCode: 500,
        statusMessage:
          e.message || "Erreur lors de la création de la collection",
      }),
    )
  }
})
