export interface User {
  _id?: string
  id: string
  username: string
  email: string
  password: string
  createdAt?: Date
  app_id?: string
  pid?: string
  role?: "admin" | "user" | "banned" | "premium"
  uploaded: number
  downloaded: number
  lastActivity?: Date
  ratio?: number
  totalUploadTorrents?: Torrent[]
}

export interface Config {
  _id?: string
  siteName: string
  siteDescription: string
  contactEmail: string
  maxUploadSize: number
  allowRegistration: boolean
  registrationClosedMessage?: string
  themeCssFile?: string
  privateTracker: boolean
  privateTrackerUrl: string
  createdAt?: Date
  updatedAt?: Date
  privateTrackerRatio: number
}

export interface Category {
  _id?: string
  name: string
  description?: string
  color?: string
  createdAt?: Date
  updatedAt?: Date
}

export interface Torrent {
  _id?: string
  name: string
  announce: string[]
  infoHash: string
  private: boolean
  created: Date
  createdBy: string
  urlList: string[]
  peers: number
  size: number
  files: Array<{
    path: string
    name: string
    length: number
    offset: number
  }>
  length: number
  pieceLength: number
  lastPieceLength: number
  pieces: string[]
  uploadedBy: string
  seeds: number
  leechs: number
  categoryId?: string
}

export interface PeerSession {
  _id?: string
  peer_id: string
  infoHash: string
  pid: string
  uploaded: number
  downloaded: number
  left: number
  event: string
  lastSeen: Date
  ip?: string
  port?: number
}
