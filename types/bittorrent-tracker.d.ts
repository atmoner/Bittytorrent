declare module "bittorrent-tracker" {
  interface ClientOptions {
    infoHash: string
    peerId: string
    port: number
    announce: string | string[]
  }

  interface UpdateData {
    announce: string
    complete: string
    incomplete: string
  }

  class Client {
    infoHash: string

    constructor(options: ClientOptions)
    start(): void
    scrape(): void
    destroy(): void
    on(event: "error", callback: (err: { message: any }) => void): void
    on(event: "warning", callback: (err: { message: any }) => void): void
    on(event: "update", callback: (data: UpdateData) => void): void
  }

  export = Client
}
