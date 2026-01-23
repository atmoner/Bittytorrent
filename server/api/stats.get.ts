import { connectToDatabase } from "../utils/mongodb"
import { H3Event, sendError, createError, eventHandler } from "h3"

export default eventHandler(async (event: H3Event) => {
  try {
    const db = await connectToDatabase()

    // Récupérer tous les torrents
    const torrents = await db.collection("torrents").find({}).toArray()

    // Récupérer tous les utilisateurs
    const users = await db.collection("users").find({}).toArray()

    // Calculer les statistiques
    const totalTorrents = torrents.length
    const privateTorrents = torrents.filter((t) => t.private === true).length
    const publicTorrents = totalTorrents - privateTorrents

    const totalSeeds = torrents.reduce((sum, t) => sum + (t.seeds || 0), 0)
    const totalLeeches = torrents.reduce((sum, t) => sum + (t.leechs || 0), 0)

    const totalSize = torrents.reduce((sum, t) => sum + (t.length || 0), 0)

    // Trackers uniques
    const allTrackers = torrents.flatMap((t) => t.announce || [])
    const uniqueTrackers = [...new Set(allTrackers)].length

    // Nombre de fichiers
    const totalFiles = torrents.reduce(
      (sum, t) => sum + (t.files?.length || 1),
      0
    )

    // Top 5 torrents par seeds
    const topSeeds = torrents
      .sort((a, b) => (b.seeds || 0) - (a.seeds || 0))
      .slice(0, 5)
      .map((t) => ({
        name: t.name,
        infoHash: t.infoHash,
        seeds: t.seeds || 0,
      }))

    // Top 5 torrents les plus récents
    const recentTorrents = torrents
      .sort(
        (a, b) => new Date(b.created).getTime() - new Date(a.created).getTime()
      )
      .slice(0, 5)
      .map((t) => ({
        name: t.name,
        infoHash: t.infoHash,
        created: t.created,
      }))

    // Nombre d'utilisateurs
    const totalUsers = users.length

    // Calcul du ratio moyen (uploaded/downloaded)
    const usersWithRatio = users.filter(
      (u) => u.uploaded > 0 && u.downloaded > 0
    )
    const averageRatio =
      usersWithRatio.length > 0
        ? usersWithRatio.reduce(
            (sum, u) => sum + u.uploaded / u.downloaded,
            0
          ) / usersWithRatio.length
        : 0

    return {
      torrents: {
        total: totalTorrents,
        private: privateTorrents,
        public: publicTorrents,
        totalSeeds,
        totalLeeches,
        totalSize,
        totalFiles,
        uniqueTrackers,
        topSeeds,
        recent: recentTorrents,
      },
      users: {
        total: totalUsers,
        averageRatio: parseFloat(averageRatio.toFixed(2)),
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
