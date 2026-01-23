import { connectToDatabase } from "../utils/mongodb"
import { compare } from "bcryptjs"
import { H3Event, sendError, createError, eventHandler } from "h3"

export default eventHandler(async (event: H3Event) => {
  try {
    const body = await readBody(event)
    const { email, password } = body
    if (!email || !password) {
      return sendError(
        event,
        createError({
          statusCode: 400,
          statusMessage: "Champs requis manquants.",
        })
      )
    }
    const db = await connectToDatabase()
    const user = await db.collection("users").findOne({ email })
    if (!user) {
      return sendError(
        event,
        createError({
          statusCode: 401,
          statusMessage: "Utilisateur non trouvé.",
        })
      )
    }
    const valid = await compare(password, user.password)
    if (!valid) {
      return sendError(
        event,
        createError({
          statusCode: 401,
          statusMessage: "Mot de passe incorrect.",
        })
      )
    }
    await setUserSession(event, {
      // User data
      user: {
        id: user.app_id,
        username: user.username,
        email: email,
        createdAt: user.createdAt!,
        role: user.role || "user", // Inclure le rôle dans la session
      },
      // Any extra fields for the session data
      loggedInAt: new Date(),
    })
    return {
      success: true,
      user: { username: user.username, email: user.email },
    }
  } catch (e: any) {
    return sendError(
      event,
      createError({
        statusCode: 500,
        statusMessage: e.message || "Erreur serveur",
      })
    )
  }
})
