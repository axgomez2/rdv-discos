console.log("Vinyl card script loaded")

class VinylCard {
  constructor(cardElement) {
    this.card = cardElement
    this.playButton = this.card.querySelector(".play-button")
    this.vinylId = this.playButton.dataset.vinylId
    this.tracks = JSON.parse(this.playButton.dataset.tracks || "[]")

    this.initializeEventListeners()
  }

  initializeEventListeners() {
    this.playButton.addEventListener("click", () => this.playVinylTracks())
  }

  playVinylTracks() {
    const validTracks = this.tracks.filter((track) => track.youtube_url)
    if (validTracks.length > 0) {
      window.audioPlayer.loadPlaylist(validTracks)
    } else {
      console.error("No playable tracks found for this vinyl")
      // TODO: Add user notification
    }
  }
}

document.addEventListener("DOMContentLoaded", () => {
  console.log("DOM carregado, inicializando vinyl cards")
  const vinylCards = document.querySelectorAll(".vinyl-card")
  vinylCards.forEach((card) => new VinylCard(card))

  const playButtons = document.querySelectorAll(".play-button")
  playButtons.forEach((button) => {
    button.addEventListener("click", () => {
      console.log("Botão de play clicado para a faixa:", button.dataset.trackTitle)
      const trackData = {
        id: button.dataset.trackId,
        title: button.dataset.trackTitle,
        artist: button.dataset.trackArtist,
        youtube_url: button.dataset.youtubeUrl,
      }
      console.log("Dados da faixa:", trackData)
      if (window.audioPlayer) {
        window.audioPlayer.loadTrack(trackData)
      } else {
        console.error("Audio player não encontrado")
      }
    })
  })
})

