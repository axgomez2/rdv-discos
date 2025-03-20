document.addEventListener("DOMContentLoaded", () => {
    const playButtons = document.querySelectorAll(".play-button")

    playButtons.forEach((button) => {
        button.addEventListener("click", () => {
            const vinylId = button.dataset.vinylId
            const tracks = JSON.parse(button.dataset.tracks)
            const artist = button.dataset.artist
            const coverUrl = button.dataset.coverUrl
            const vinylTitle = button.dataset.vinylTitle

            if (tracks && tracks.length > 0) {
                const playlistTracks = tracks.map((track) => ({
                    id: track.id,
                    name: track.name,
                    artist: artist,
                    youtube_url: track.youtube_url,
                    cover_url: coverUrl,
                    vinyl_title: vinylTitle,
                }))

                if (window.audioPlayer && typeof window.audioPlayer.loadPlaylist === "function") {
                    window.audioPlayer.loadPlaylist(playlistTracks)

                    // Atualizar a imagem do player
                    const playerImage = document.querySelector('#player-image') // Ajuste o seletor conforme necessário
                    if (playerImage) {
                        playerImage.src = coverUrl
                    }
                } else {
                    console.error("Audio player not initialized or loadPlaylist method not available")
                }
            } else {
                console.error("No tracks available for this vinyl")
            }
        })
    })
})
