import { H3Event, createError, eventHandler, sendError } from "h3"
import { promises as fs } from "fs"
import { join } from "path"
import { connectToDatabase } from "../../utils/mongodb"

const THEME_FILE_REGEX = /^[a-zA-Z0-9._-]+\.css$/

export default eventHandler(async (event: H3Event) => {
  const session = await getUserSession(event)
  if (!session || !session.user) {
    return sendError(
      event,
      createError({ statusCode: 401, statusMessage: "Non autorisé." }),
    )
  }

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
    const themesDirectory = join(process.cwd(), "public", "themes")
    let entries: string[] = []

    try {
      entries = await fs.readdir(themesDirectory)
    } catch {
      entries = []
    }

    const themes = entries
      .filter((fileName) => THEME_FILE_REGEX.test(fileName))
      .sort((a, b) => a.localeCompare(b))

    return {
      success: true,
      themes,
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
