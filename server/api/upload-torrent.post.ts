import { H3Event, sendError, createError, send, eventHandler } from "h3"
import { promises as fs } from "fs"
import { join } from "path"
import parseTorrent from "parse-torrent"
import { Torrent } from "~/models"

export default eventHandler(async (event: H3Event) => {
  const session = await getUserSession(event)
  if (!session || !session.user) {
    return sendError(
      event,
      createError({ statusCode: 401, statusMessage: "Non autorisé." }),
    )
  }

  try {
    const form = await readMultipartFormData(event)
    if (!form) {
      return sendError(
        event,
        createError({ statusCode: 400, statusMessage: "Aucun fichier reçu." }),
      )
    }

    const fileField = form.find((f) => f.name === "torrent" && f.type && f.data)
    const categoryField = form.find((f) => f.name === "categoryId")

    if (!fileField) {
      return sendError(
        event,
        createError({
          statusCode: 400,
          statusMessage: "Fichier .torrent manquant.",
        }),
      )
    }
    // .torrent file (as a Buffer)
    const detailTorrent = await parseTorrent(fileField.data)
    //console.log(detailTorrent)

    // Récupérer la configuration du site pour obtenir l'URL du tracker privé
    const db = await connectToDatabase()
    /*     const config = await db.collection("config").findOne({})

    // Si le torrent est public et qu'une URL de tracker privé est configurée, l'ajouter aux announces
    const isPrivate = "private" in detailTorrent ? detailTorrent.private : false
    if (!isPrivate && config?.privateTrackerUrl) {
      if (!detailTorrent.announce) {
        detailTorrent.announce = []
      }
      // Ajouter l'URL du tracker privé du site si elle n'est pas déjà présente
      if (!detailTorrent.announce.includes(config.privateTrackerUrl)) {
        detailTorrent.announce.unshift(config.privateTrackerUrl) // Ajouter au début de la liste
      }
    } */

    const fileName = `${detailTorrent.infoHash}.torrent`
    const savePath = join(process.cwd(), "torrents", fileName)
    await fs.writeFile(savePath, fileField.data)

    // Récupérer les stats du tracker au moment de l'upload
    let trackerStats = null
    try {
      if (!detailTorrent.infoHash) {
        throw new Error("Le fichier torrent ne contient pas d'infoHash valide.")
      }
    } catch (err) {
      // Optionnel : log ou ignorer l'erreur
      trackerStats = { seeds: 0, leechs: 0 }
    }

    const { _id, ...torrentData } = detailTorrent as Torrent

    if (!detailTorrent.infoHash) {
      return sendError(
        event,
        createError({
          statusCode: 400,
          statusMessage:
            "Le fichier torrent ne contient pas d'infoHash valide.",
        }),
      )
    }

    torrentData.seeds = 0
    torrentData.leechs = 0
    torrentData.created = new Date()
    torrentData.uploadedBy = (session.user as any).id

    // Ajouter la catégorie si fournie
    if (categoryField && categoryField.data) {
      const categoryId = categoryField.data.toString()
      if (categoryId && categoryId !== "undefined" && categoryId !== "null") {
        torrentData.categoryId = categoryId
      }
    }

    await db.collection("torrents").insertOne(torrentData)

    return { success: true, fileName, detailTorrent, trackerStats }
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
