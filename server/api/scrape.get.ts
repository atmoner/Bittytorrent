import { connectToDatabase } from "../utils/mongodb"
import { H3Event, sendError, createError, send, eventHandler } from "h3"

import Client from "bittorrent-tracker"

export default eventHandler(async (event) => {
  const query = getQuery(event)

  if (!query.infoHash) {
    return sendError(
      event,
      createError({ statusCode: 400, statusMessage: "infoHash manquant." }),
    )
  }

  if (!query.announce) {
    return sendError(
      event,
      createError({ statusCode: 400, statusMessage: "announce manquant." }),
    )
  }

  try {
    const db = await connectToDatabase()
    const torrents = await db
      .collection("torrents")
      .find({
        infoHash: query.infoHash,
      })
      .toArray()

    //console.log("Torrents found in DB:", torrents[0])

    //return torrents

    return new Promise((resolve, reject) => {
      console.log("Torrents announce:", torrents[0].announce)

      let torrentInfo = {
        announce: torrents[0].announce[0],
        infoHash: query.infoHash,
        seeds: 0,
        leechs: 0,
      }

      const announceList = Array.isArray(torrents[0].announce)
        ? torrents[0].announce
        : [torrents[0].announce]
      let responsesReceived = 0
      let hasUpdate = false

      // Timeout réduit à 10 secondes
      const timeout = setTimeout(() => {
        console.log(
          `Scrape timeout - ${responsesReceived}/${announceList.length} trackers responded`,
        )
        client.destroy()
        resolve(torrentInfo)
      }, 10000)

      const checkComplete = () => {
        responsesReceived++
        if (responsesReceived >= announceList.length || hasUpdate) {
          clearTimeout(timeout)
          client.destroy()
          resolve(torrentInfo)
        }
      }

      const client = new Client({
        infoHash: query.infoHash,
        peerId: "2d5452343036302d6f61636b37356e3871753639",
        port: 6881,
        announce: announceList,
        wrtc: false, // Désactiver WebRTC pour éviter node-datachannel
      })

      client.start()
      console.log("Scraping hash: " + client.infoHash)
      client.scrape()

      client.on("error", function (err: { message: any }) {
        console.log("error:", err.message)
        clearTimeout(timeout)
        client.destroy()
        resolve(torrentInfo) // Résoudre avec 0/0 plutôt que rejeter
      })

      client.on("warning", function (err: { message: any }) {
        console.log("warning:", err.message)
        checkComplete() // Compter les warnings comme des réponses
      })

      client.on(
        "update",
        async function (data: {
          announce: string
          complete: string
          incomplete: string
        }) {
          hasUpdate = true
          console.log("got an announce response from tracker: " + data.announce)
          console.log("number of seeders in the swarm: " + data.complete)
          console.log("number of leechers in the swarm: " + data.incomplete)

          torrentInfo.seeds = Number(data.complete)
          torrentInfo.leechs = Number(data.incomplete)

          // Mettre à jour la BDD
          try {
            await db.collection("torrents").updateOne(
              { infoHash: query.infoHash },
              {
                $set: {
                  seeds: torrentInfo.seeds,
                  leechs: torrentInfo.leechs,
                },
              },
            )
            console.log("DB updated for infoHash:", query.infoHash)
          } catch (err) {
            console.error("Failed to update DB:", err)
          }

          checkComplete()
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
