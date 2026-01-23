import { MongoClient, Db } from "mongodb"

let cachedDb: Db | null = null

export async function connectToDatabase(): Promise<Db> {
  if (cachedDb) {
    return cachedDb
  }

  const config = useRuntimeConfig()
  const uri = config.mongoDbUrl
  const dbName = config.mongoDbName

  const client = new MongoClient(uri)
  await client.connect()

  const db = client.db(dbName)
  cachedDb = db

  return db
}
