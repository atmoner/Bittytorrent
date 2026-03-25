import { connectToDatabase } from "../utils/mongodb"
import type { User } from "~/models"
import { hash } from "bcryptjs"
import { H3Event, sendError, createError, send, eventHandler } from "h3"
import { v4 as uuidv4 } from "uuid"

export default eventHandler(async (event: H3Event) => {
  try {
    const body = await readBody(event)
    const { username, email, password } = body
    if (!username || !email || !password) {
      return sendError(
        event,
        createError({
          statusCode: 400,
          statusMessage: "Champs requis manquants.",
        })
      )
    }
    const db = await connectToDatabase()
    const userExists = await db.collection("users").findOne({ email })
    if (userExists) {
      return sendError(
        event,
        createError({ statusCode: 409, statusMessage: "Email déjà utilisé." })
      )
    }
    const hashedPassword = await hash(password, 10)
    const newUser: Omit<User, "_id"> = {
      username,
      email,
      password: hashedPassword,
      createdAt: new Date(),
      app_id: uuidv4(),
      pid: uuidv4(),
      role: "user", // Par défaut, un nouvel utilisateur est "user"
    }
    await db.collection("users").insertOne(newUser)
    return send(event, { success: true })
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
