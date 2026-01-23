declare module "#auth-utils" {
  interface User {
    id: string
    username: string
    email: string
    createdAt: Date
    role: "admin" | "user"
  }

  interface UserSession {
    id: string
    user: User
    loggedInAt: Date
    role: "admin" | "user"
  }
}

export {}
