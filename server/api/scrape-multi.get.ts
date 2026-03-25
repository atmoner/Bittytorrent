import { connectToDatabase } from "../utils/mongodb"
import { H3Event, sendError, createError, send, eventHandler } from "h3"

import Client from "bittorrent-tracker"

export default eventHandler(async (event) => {
  const query = getQuery(event)
  try {
    let torrents = {
      infoHash: query.infoHash,
      seeds: 0,
      leechs: 0,
    }

    return new Promise((resolve, reject) => {
      // Timeout de 20 secondes pour plusieurs trackers
      const timeout = setTimeout(() => {
        console.log("Multi-scrape timeout - resolving with current stats")
        client.destroy()
        resolve(torrents)
      }, 20000)

      const client = new Client({
        infoHash: query.infoHash,
        peerId: "2d5452343036302d6f61636b37356e3871753639",
        port: 6881,
        announce: query.announce || [
          "https://wolf.parrotsec.io:443/announce",
          "https://wolf.parrot.run:443/announce",
          "https://colibri.parrotsec.io:443/announce",
          "https://colibri.parrot.run:443/announce",
          "https://local.tr.rfc2549.network:443/announce",
          "https://sanfrancisco.tr.rfc2549.network:443/announce",
          "https://london.tr.rfc2549.network:443/announce",
          "https://singapore.tr.rfc2549.network:443/announce",
          "https://tracker.bt4g.com:443/announce",
          "udp://tracker.opentrackr.org:1337/announce",
        ],
        wrtc: false, // Désactiver WebRTC pour éviter node-datachannel
      })

      let updateCount = 0
      const maxUpdates = 10 // Nombre max de trackers attendus

      client.start()
      console.log("got an announce response from hash: " + client.infoHash)
      client.scrape()

      client.on("error", function (err: { message: any }) {
        console.log("error:", err.message)
        clearTimeout(timeout)
        client.destroy()
        resolve(torrents) // Résoudre avec les stats actuelles même en cas d'erreur
      })

      client.on("warning", function (err: { message: any }) {
        console.log("warning:", err.message)
      })

      client.on(
        "update",
        function (data: {
          announce: string
          complete: string
          incomplete: string
        }) {
          console.log("got an announce response from tracker: " + data.announce)
          console.log("number of seeders in the swarm: " + data.complete)
          console.log("number of leechers in the swarm: " + data.incomplete)

          torrents.seeds = torrents.seeds + Number(data.complete)
          torrents.leechs = torrents.leechs + Number(data.incomplete)

          updateCount++

          // Si on a reçu des réponses de tous les trackers, résoudre
          if (updateCount >= maxUpdates) {
            clearTimeout(timeout)
            client.destroy()
            resolve(torrents)
          }
        },
      )
    })
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
