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
    // Récupérer les sessions actives
    const activeSessions = await db
      .collection("peer_sessions")
      .find({})
      .sort({ lastSeen: -1 })
      .limit(100)
      .toArray()

    // Statistiques générales
    const stats = await db
      .collection("peer_sessions")
      .aggregate([
        {
          $group: {
            _id: null,
            totalSessions: { $sum: 1 },
            totalUploaded: { $sum: "$uploaded" },
            totalDownloaded: { $sum: "$downloaded" },
            avgUploadRate: { $avg: "$uploaded" },
            avgDownloadRate: { $avg: "$downloaded" },
          },
        },
      ])
      .toArray()

    // Sessions par torrent
    const sessionsByTorrent = await db
      .collection("peer_sessions")
      .aggregate([
        {
          $group: {
            _id: "$infoHash",
            sessionCount: { $sum: 1 },
            totalUploaded: { $sum: "$uploaded" },
            totalDownloaded: { $sum: "$downloaded" },
          },
        },
        {
          $sort: { sessionCount: -1 },
        },
        {
          $limit: 20,
        },
      ])
      .toArray()

    // Sessions par utilisateur (via PID)
    const sessionsByUser = await db
      .collection("peer_sessions")
      .aggregate([
        {
          $group: {
            _id: "$pid",
            sessionCount: { $sum: 1 },
            totalUploaded: { $sum: "$uploaded" },
            totalDownloaded: { $sum: "$downloaded" },
          },
        },
        {
          $sort: { sessionCount: -1 },
        },
        {
          $limit: 20,
        },
      ])
      .toArray()

    // Récupérer les noms des torrents pour les sessions par torrent
    const torrentHashes = sessionsByTorrent.map((s) => s._id)
    const torrents = await db
      .collection("torrents")
      .find({ infoHash: { $in: torrentHashes } })
      .project({ infoHash: 1, name: 1 })
      .toArray()

    const torrentsMap = torrents.reduce((acc, t) => {
      acc[t.infoHash] = t.name
      return acc
    }, {})

    // Récupérer les usernames pour les sessions par utilisateur
    const pids = sessionsByUser.map((s) => s._id)
    const users = await db
      .collection("users")
      .find({ pid: { $in: pids } })
      .project({ pid: 1, username: 1 })
      .toArray()

    const usersMap = users.reduce((acc, u) => {
      acc[u.pid] = u.username
      return acc
    }, {})

    return {
      activeSessions: activeSessions.map((session) => ({
        ...session,
        _id: session._id.toString(),
      })),
      stats: stats[0] || {
        totalSessions: 0,
        totalUploaded: 0,
        totalDownloaded: 0,
        avgUploadRate: 0,
        avgDownloadRate: 0,
      },
      sessionsByTorrent: sessionsByTorrent.map((s) => ({
        ...s,
        torrentName: torrentsMap[s._id] || `Hash: ${s._id.substring(0, 8)}...`,
      })),
      sessionsByUser: sessionsByUser.map((s) => ({
        ...s,
        username: usersMap[s._id] || `PID: ${s._id.substring(0, 8)}...`,
      })),
    }
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
