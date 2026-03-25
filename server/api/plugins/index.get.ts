import { connectToDatabase } from "../../utils/mongodb"
import { defineEventHandler } from "h3"
import type { PluginState } from "../../../types/plugin"

/**
 * GET /api/plugins
 * Retourne la liste des états de plugins (actif/inactif) depuis la base de données.
 * Si la collection est vide, tous les plugins sont considérés actifs par défaut.
 */
export default defineEventHandler(async (): Promise<PluginState[]> => {
  try {
    const db = await connectToDatabase()
    const states = await db
      .collection<PluginState>("plugin_states")
      .find({})
      .toArray()
    return states.map((s) => ({ id: s.id, active: s.active }))
  } catch (e) {
    console.error("[API /api/plugins] Erreur MongoDB :", e)
    return []
  }
})
