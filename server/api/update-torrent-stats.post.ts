import { connectToDatabase } from "../utils/mongodb"
import { H3Event, readBody } from "h3"

export default defineEventHandler(async (event: H3Event) => {
  if (event.method !== "POST") {
    return { error: "Method not allowed" }
  }

  const body = await readBody(event)
  const { infoHash, uploaded, downloaded, peer_id } = body

  if (!infoHash) {
    return { error: "Missing infoHash" }
  }

  try {
    const db = await connectToDatabase()
    const result = await db.collection("torrents").updateOne(
      { infoHash },
      {
        $set: {
          uploaded,
          downloaded,
          peer_id,
        },
      },
      { upsert: false }
    )
    return { success: true, result }
  } catch (e) {
    return { error: "Erreur update MongoDB", details: e }
  }
})
