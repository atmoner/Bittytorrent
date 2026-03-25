import Client from "bittorrent-tracker"

export async function scrapeTorrents(infoHash: string, announce?: string[]) {
  return new Promise((resolve, reject) => {
    let torrents = {
      infoHash: infoHash,
      seeds: 0,
      leechs: 0,
    }
    const client = new Client({
      infoHash: infoHash,
      peerId: "-NT0001-000000000000",
      port: 6881,
      announce: announce || ["http://127.0.0.1:4200/announce"],
    })

    client.start()
    console.log("got an announce response from hash: " + client.infoHash)
    client.scrape()

    client.on("error", function (err: { message: any }) {
      console.log("error:", err.message)
      //reject(err)
      //client.destroy()
    })

    client.on("warning", function (err: { message: any }) {
      console.log("warning:", err.message)
      // Optionnel : tu peux aussi reject ici si tu veux traiter les warnings comme des erreurs
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

        torrents.seeds = Number(data.complete)
        torrents.leechs = Number(data.incomplete)
        resolve(torrents)
      }
    )
  })
}
