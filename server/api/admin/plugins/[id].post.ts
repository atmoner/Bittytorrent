import { connectToDatabase } from "../../../utils/mongodb"
import {
  defineEventHandler,
  readBody,
  createError,
  sendError,
  H3Event,
} from "h3"

/**
 * POST /api/admin/plugins/:id
 * Body: { action: 'activate' | 'deactivate' }
 * Met à jour l'état (actif/inactif) d'un plugin en base de données.
 * Route protégée : admin uniquement.
 */
export default defineEventHandler(async (event: H3Event) => {
  const session = await getUserSession(event)
  if (!session?.user) {
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

  const id = event.context.params?.id
  if (!id) {
    return sendError(
      event,
      createError({ statusCode: 400, statusMessage: "ID de plugin manquant." }),
    )
  }

  const body = await readBody<{ action: "activate" | "deactivate" }>(event)
  if (!body?.action || !["activate", "deactivate"].includes(body.action)) {
    return sendError(
      event,
      createError({
        statusCode: 400,
        statusMessage:
          "Action invalide. Valeurs acceptées : activate, deactivate.",
      }),
    )
  }

  const active = body.action === "activate"

  await db
    .collection("plugin_states")
    .updateOne(
      { id },
      { $set: { id, active, updatedAt: new Date() } },
      { upsert: true },
    )

  return { success: true, id, active }
})
