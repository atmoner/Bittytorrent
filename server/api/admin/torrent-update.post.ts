import { connectToDatabase } from "../../utils/mongodb"
import { H3Event, sendError, createError, eventHandler } from "h3"
import { ObjectId } from "mongodb"

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
  const user = await db.collection("users").findOne({ app_id: session.user.id })

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
      torrentId,
      name,
      seeds,
      leechs,
      categoryId,
      private: isPrivate,
      announce,
      urlList,
    } = body

    if (!torrentId) {
      return sendError(
        event,
        createError({ statusCode: 400, statusMessage: "ID torrent manquant." }),
      )
    }

    const updateData: any = {}
    if (name !== undefined) updateData.name = name
    if (seeds !== undefined) updateData.seeds = seeds
    if (leechs !== undefined) updateData.leechs = leechs
    if (categoryId !== undefined) updateData.categoryId = categoryId || null
    if (isPrivate !== undefined) updateData.private = isPrivate
    if (announce !== undefined && Array.isArray(announce))
      updateData.announce = announce
    if (urlList !== undefined && Array.isArray(urlList))
      updateData.urlList = urlList

    const result = await db
      .collection("torrents")
      .updateOne({ _id: new ObjectId(torrentId) }, { $set: updateData })

    if (result.matchedCount === 0) {
      return sendError(
        event,
        createError({ statusCode: 404, statusMessage: "Torrent non trouvé." }),
      )
    }

    return { success: true, message: "Torrent mis à jour avec succès." }
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
