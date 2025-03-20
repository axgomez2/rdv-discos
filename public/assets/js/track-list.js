console.log("Track list script loaded")

class TrackList {
  constructor(listElement) {
    this.list = listElement
    this.tracks = Array.from(this.list.querySelectorAll(".track-item"))

    this.initializeEventListeners()
  }

  initializeEventListeners() {
    this.tracks.forEach((track) => {
      const playButton = track.querySelector(".play-button")
      if (playButton) {
        playButton.addEventListener("click", () => this.playTrack(track))
      }
    })
  }

  playTrack(trackElement) {
    const trackData = {
      id: trackElement.dataset.trackId,
      title: trackElement.dataset.trackTitle,
      artist: trackElement.dataset.trackArtist,
      youtube_url: trackElement.dataset.youtubeUrl,
    }

    if (trackData.youtube_url) {
      window.audioPlayer.loadPlaylist([trackData])
    } else {
      console.error("No YouTube URL available for this track")
      // TODO: Add user notification
    }
  }
}

document.addEventListener("DOMContentLoaded", () => {
  const trackList = document.querySelector(".track-list")
  if (trackList) {
    new TrackList(trackList)
  }
})

