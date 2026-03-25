import { Server } from "bittorrent-tracker"
import { connectToDatabase } from "../../server/utils/mongodb"

declare global {
  // Add _trackerServer to the globalThis type
  // eslint-disable-next-line no-var
  var _trackerServer: InstanceType<typeof Server> | undefined
}

export default defineNuxtPlugin({
  name: "tracker-server",
  // enforce: "pre",
  async setup() {
    if (!globalThis._trackerServer) {
      // Vérifier si l'application est installée avant de démarrer le tracker
      const runtimeConfig = useRuntimeConfig()

      // Si MongoDB n'est pas configuré, ne pas démarrer le tracker
      if (!runtimeConfig.mongoDbUrl || !runtimeConfig.mongoDbName) {
        console.log("MongoDB non configuré, tracker non démarré")
        return
      }

      // Charger la configuration depuis MongoDB
      let config: any = {}
      let db: any = null

      try {
        db = await connectToDatabase()
        const configDoc = await db.collection("config").findOne({})

        if (!configDoc) {
          console.log("Configuration non trouvée, tracker non démarré")
          return
        }

        config = configDoc
      } catch (e) {
        console.log(
          "Erreur lors du chargement de la configuration, tracker non démarré:",
          e instanceof Error ? e.message : String(e),
        )
        return
      }

      const server = new Server({
        udp: true,
        http: true,
        ws: true,
        stats: false,
        trustProxy: false,
        filter: async function (
          infoHash: string,
          params: any,
          cb: (arg0: Error | null) => void,
        ) {
          console.log("--------------")
          console.log("infoHash:", infoHash)
          console.log("peer_id:", params.peer_id)
          console.log("uploaded:", params.uploaded)
          console.log("downloaded:", params.downloaded)

          console.log("params event:", params.event)
          console.log("params pid:", params.pid)

          //console.log("config:", config)
          //console.log("params:", params)

          const testBypass = false

          const userData = await db
            .collection("users")
            .findOne({ pid: params.pid })

          //console.log("userData:", userData)

          if (!userData) {
            console.log(
              `Rejet de la connexion du pair ${params.peer_id} : PID invalide (${params.pid})`,
            )
            return cb(new Error("PID invalide"))
          }

          if (
            userData.uploaded / (userData.downloaded + 1) <
            config.privateTrackerRatio
          ) {
            console.log(
              `Rejet de la connexion du pair ${params.peer_id} : Mauvais ratio (U:${userData.uploaded} / D:${userData.downloaded})`,
            )
            return cb(
              new Error(
                ` Mauvais ratio (U:${userData.uploaded} / D:${userData.downloaded})`,
              ),
            )
          }

          if (params.event !== "stopped") {
            try {
              // Récupérer la dernière session connue pour ce peer
              const lastSession = await db.collection("peer_sessions").findOne({
                peer_id: params.peer_id,
                infoHash: infoHash,
              })

              // Calculer les vraies différences depuis la dernière announce
              const lastUploaded = lastSession?.uploaded || 0
              const lastDownloaded = lastSession?.downloaded || 0

              const uploadedDiff = Math.max(
                0,
                Number(params.uploaded) - lastUploaded,
              )
              const downloadedDiff = Math.max(
                0,
                Number(params.downloaded) - lastDownloaded,
              )

              // Validation anti-cheat : limiter les increments suspects
              const timeSinceLastAnnounce = lastSession
                ? (Date.now() - lastSession.lastSeen.getTime()) / 1000 // secondes
                : 5 // 5 secondes par défaut pour nouvelle session
              const uploadRate =
                uploadedDiff / Math.max(timeSinceLastAnnounce, 1)
              const downloadRate =
                downloadedDiff / Math.max(timeSinceLastAnnounce, 1)
              const maxReasonableRate = 50 * 1024 * 1024 // 50MB/s max

              console.log(
                `Stats pour ${params.peer_id} sur ${infoHash}: ` +
                  `U+${(uploadedDiff / (1024 * 1024)).toFixed(2)}MB, ` +
                  `D+${(downloadedDiff / (1024 * 1024)).toFixed(2)}MB, ` +
                  `en ${timeSinceLastAnnounce.toFixed(2)}s ` +
                  `U: ${uploadRate}` +
                  `D: ${downloadRate}`,
                `maxReasonableRate: ${maxReasonableRate}`,
              )

              if (
                uploadRate > maxReasonableRate ||
                downloadRate > maxReasonableRate
              ) {
                console.log(
                  `Vitesse suspecte détectée pour ${params.peer_id}: ` +
                    `U: ${(uploadRate / (1024 * 1024)).toFixed(2)}MB/s, ` +
                    `D: ${(downloadRate / (1024 * 1024)).toFixed(2)}MB/s`,
                )
                return cb(new Error("Vitesse de transfert irréaliste"))
              }

              // Mettre à jour les stats utilisateur avec les vrais différentiels
              if (uploadedDiff > 0 || downloadedDiff > 0) {
                await db.collection("users").updateOne(
                  { pid: params.pid },
                  {
                    $inc: {
                      uploaded: uploadedDiff,
                      downloaded: downloadedDiff,
                    },
                    $set: {
                      lastActivity: new Date(),
                    },
                  },
                )

                console.log(
                  `Stats utilisateur mises à jour pour PID ${params.pid}: ` +
                    `U+${(uploadedDiff / (1024 * 1024)).toFixed(2)}MB, ` +
                    `D+${(downloadedDiff / (1024 * 1024)).toFixed(2)}MB`,
                )
              }

              // Sauvegarder/mettre à jour la session du peer
              await db.collection("peer_sessions").updateOne(
                { peer_id: params.peer_id, infoHash: infoHash },
                {
                  $set: {
                    pid: params.pid,
                    uploaded:
                      params.uploaded > 0
                        ? Number(params.uploaded)
                        : lastSession?.uploaded || 0,
                    downloaded:
                      params.downloaded > 0
                        ? Number(params.downloaded)
                        : lastSession?.downloaded || 0,
                    left: Number(params.left) || 0,
                    event: params.event || "update",
                    lastSeen: new Date(),
                    ip: params.ip,
                    port: Number(params.port) || 0,
                  },
                },
                { upsert: true },
              )
            } catch (e) {
              console.error(
                "Erreur lors de la mise à jour des stats utilisateur:",
                e,
              )
              return cb(new Error("Erreur interne du tracker"))
            }
          } else {
            // Événement "stopped" - calculer les stats finales et nettoyer la session
            try {
              const lastSession = await db.collection("peer_sessions").findOne({
                peer_id: params.peer_id,
                infoHash: infoHash,
              })

              if (lastSession && params.uploaded > 0) {
                const uploadedDiff = Math.max(
                  0,
                  Number(params.uploaded) - lastSession.uploaded,
                )

                if (uploadedDiff > 0) {
                  await db.collection("users").updateOne(
                    { pid: params.pid },
                    {
                      $inc: { uploaded: uploadedDiff },
                      $set: { lastActivity: new Date() },
                    },
                  )

                  console.log(
                    `Stats utilisateur finales mises à jour pour PID ${params.pid} (stopped): ` +
                      `U+${(uploadedDiff / (1024 * 1024)).toFixed(2)}MB`,
                  )
                }
              }
              // Sauvegarder/mettre à jour la session du peer
              await db.collection("peer_sessions").updateOne(
                { peer_id: params.peer_id, infoHash: infoHash },
                {
                  $set: {
                    pid: params.pid,
                    uploaded:
                      params.uploaded > 0
                        ? Number(params.uploaded)
                        : lastSession?.uploaded || 0,
                    downloaded:
                      params.downloaded > 0
                        ? Number(params.downloaded)
                        : lastSession?.downloaded || 0,
                    left: Number(params.left) || 0,
                    event: params.event || "update",
                    lastSeen: new Date(),
                    ip: params.ip,
                    port: Number(params.port) || 0,
                  },
                },
                { upsert: true },
              )
              // Supprimer la session du peer
              /*               await db.collection("peer_sessions").deleteOne({
                peer_id: params.peer_id,
                infoHash: infoHash,
              })

              console.log(
                `Session peer supprimée pour ${params.peer_id} sur ${infoHash}`,
              ) */
            } catch (e) {
              console.error("Erreur lors du nettoyage de la session peer:", e)
            }
          }

          // Mettre à jour les stats du torrent dans MongoDB
          try {
            const res = await fetch(
              "http://localhost:3000/api/update-torrent-stats",
              {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                  infoHash,
                  uploaded: params.uploaded,
                  downloaded: params.downloaded,
                  peer_id: params.peer_id,
                }),
              },
            )
            const data = await res.json()
            if (!data.success) {
              console.error("Erreur update API:", data.error || data)
              cb(new Error("Erreur update API:", data.error || data))
            }
            cb(null)
          } catch (e) {
            console.error("Erreur appel API:", e)
            cb(new Error("Network error"))
          }
        },
      })

      server.on("error", (err: { message: any }) => console.log(err.message))
      server.on("warning", (err: { message: any }) => console.log(err.message))
      server.on("listening", () => {
        const httpAddr = server.http.address()
        const httpHost =
          httpAddr.address !== "::" ? httpAddr.address : "localhost"
        const httpPort = httpAddr.port
        console.log(`HTTP tracker: http://${httpHost}:${httpPort}/announce`)
        const udpAddr = server.udp.address()
        console.log(`UDP tracker: udp://${udpAddr.address}:${udpAddr.port}`)
        const wsAddr = server.ws.address()
        const wsHost = wsAddr.address !== "::" ? wsAddr.address : "localhost"
        const wsPort = wsAddr.port
        console.log(`WebSocket tracker: ws://${wsHost}:${wsPort}`)
      })

      server.listen(4200, "localhost")
      globalThis._trackerServer = server

      server.on("start", function (addr: string) {
        console.log("got start message from " + addr)
        console.log(Object.keys(server.torrents))
      })
    }
  },
})
