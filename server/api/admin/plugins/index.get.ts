import { connectToDatabase } from "../../../utils/mongodb"
import { defineEventHandler, createError, sendError, H3Event } from "h3"
import type { PluginState } from "../../../../types/plugin"

/**
 * GET /api/admin/plugins
 * Retourne la liste complète des états de plugins pour l'interface d'administration.
 * Route protégée : admin uniquement.
 */
export default defineEventHandler(
  async (event: H3Event): Promise<PluginState[]> => {
    const session = await getUserSession(event)
    if (!session?.user) {
      return sendError(
        event,
        createError({ statusCode: 401, statusMessage: "Non autorisé." }),
      ) as any
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
      ) as any
    }

    const states = await db
      .collection<PluginState>("plugin_states")
      .find({})
      .toArray()
    return states.map((s) => ({ id: s.id, active: s.active }))
  },
)
