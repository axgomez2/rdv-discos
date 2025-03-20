window.AudioPlayer = class AudioPlayer {
    constructor() {
        // Estado inicial
        this.state = {
            initialized: false,
            loading: false,
            playing: false,
            volume: 100,
            muted: false
        };

        // Player e playlist
        this.player = null;
        this.playlist = [];
        this.currentTrack = null;
        this.currentIndex = 0;

        // Cache dos elementos DOM
        this.elements = this.getPlayerElements();

        // Configuração do player do YouTube
        this.playerConfig = {
            height: '0',
            width: '0',
            playerVars: {
                autoplay: 0,
                controls: 0,
                disablekb: 1,
                enablejsapi: 1,
                iv_load_policy: 3,
                modestbranding: 1,
                playsinline: 1,
                rel: 0,
                showinfo: 0,
                origin: window.location.origin
            },
            events: {
                onReady: this.handlePlayerReady.bind(this),
                onStateChange: this.handlePlayerStateChange.bind(this),
                onError: this.handlePlayerError.bind(this)
            }
        };

        // Inicializa o player
        this.init();
    }

    // Inicialização
    async init() {
        try {
            if (this.state.initialized || this.state.loading) return;
            this.state.loading = true;

            // Carrega a API do YouTube se necessário
            await this.loadYouTubeAPI();

            // Cria o player
            await this.createPlayer();

            // Configura eventos
            this.setupEventListeners();

            this.state.initialized = true;
            console.log('Player inicializado com sucesso');
        } catch (error) {
            console.error('Erro na inicialização do player:', error);
            this.showError('Erro ao inicializar o player. Tente recarregar a página.');
        } finally {
            this.state.loading = false;
        }
    }

    // Carrega a API do YouTube
    loadYouTubeAPI() {
        return new Promise((resolve, reject) => {
            if (window.YT && window.YT.Player) {
                resolve();
                return;
            }

            // Callback global para quando a API carregar
            window.onYouTubeIframeAPIReady = () => {
                console.log('API do YouTube carregada');
                resolve();
            };

            // Carrega o script da API
            const tag = document.createElement('script');
            tag.src = 'https://www.youtube.com/iframe_api';
            tag.onerror = () => reject(new Error('Falha ao carregar API do YouTube'));
            document.head.appendChild(tag);

            // Timeout de 10 segundos
            setTimeout(() => reject(new Error('Timeout ao carregar API do YouTube')), 10000);
        });
    }

    // Cria o player do YouTube
    createPlayer() {
        return new Promise((resolve, reject) => {
            try {
                if (!this.elements.youtubePlayer) {
                    console.error('Elemento do player não encontrado');
                    reject(new Error('Elemento do player não encontrado'));
                    return;
                }

                this.player = new YT.Player('youtube-player', {
                    ...this.playerConfig,
                    events: {
                        ...this.playerConfig.events,
                        onReady: () => {
                            console.log('Player do YouTube pronto');
                            resolve();
                        },
                        onError: (error) => {
                            console.error('Erro no player do YouTube:', error);
                            reject(error);
                        }
                    }
                });
            } catch (error) {
                console.error('Erro ao criar player:', error);
                reject(error);
            }
        });
    }

    // Configura os event listeners
    setupEventListeners() {
        // Play/Pause
        this.elements.playPauseBtn?.addEventListener('click', () => this.togglePlay());

        // Próxima/Anterior
        this.elements.prevBtn?.addEventListener('click', () => this.playPrevious());
        this.elements.nextBtn?.addEventListener('click', () => this.playNext());

        // Volume
        this.elements.volumeControl?.addEventListener('input', (e) => {
            this.setVolume(parseInt(e.target.value, 10));
        });

        // Progresso
        this.elements.progressContainer?.addEventListener('click', (e) => {
            const rect = e.currentTarget.getBoundingClientRect();
            const ratio = (e.clientX - rect.left) / rect.width;
            this.seekTo(ratio);
        });
    }

    // Handlers dos eventos do player
    handlePlayerReady(event) {
        console.log('Player pronto para uso');
        this.player = event.target;
        this.setVolume(this.state.volume);
        this.startProgressUpdate();
    }

    handlePlayerStateChange(event) {
        if (!event || !YT) return;

        switch (event.data) {
            case YT.PlayerState.ENDED:
                this.playNext();
                break;
            case YT.PlayerState.PLAYING:
                this.state.playing = true;
                this.updatePlayPauseButton();
                this.startProgressUpdate();
                break;
            case YT.PlayerState.PAUSED:
                this.state.playing = false;
                this.updatePlayPauseButton();
                this.stopProgressUpdate();
                break;
        }
    }

    handlePlayerError(event) {
        const errorMessages = {
            2: 'Parâmetro inválido',
            5: 'Erro de HTML5',
            100: 'Vídeo não encontrado',
            101: 'Reprodução não permitida',
            150: 'Reprodução não permitida'
        };

        const message = errorMessages[event.data] || 'Erro desconhecido';
        console.error('Erro do YouTube:', message);
        this.showError(`Não foi possível reproduzir esta faixa (${message})`);
        this.playNext();
    }

    // Métodos de controle do player
    async loadPlaylist(tracks) {
        if (!Array.isArray(tracks) || tracks.length === 0) {
            this.showError('Playlist inválida');
            return;
        }

        try {
            // Aguarda inicialização se necessário
            if (!this.state.initialized) {
                await this.init();
            }

            this.playlist = tracks;
            this.currentIndex = 0;
            await this.loadTrack(tracks[0]);
            this.showPlayer();
        } catch (error) {
            console.error('Erro ao carregar playlist:', error);
            this.showError('Erro ao carregar playlist');
        }
    }

    async loadTrack(track) {
        if (!track?.youtube_url) {
            throw new Error('Track inválida');
        }

        try {
            const videoId = this.extractVideoId(track.youtube_url);
            if (!videoId) throw new Error('ID do vídeo inválido');

            this.currentTrack = track;

            if (this.player?.loadVideoById) {
                this.player.loadVideoById(videoId);
                this.updatePlayerInfo();
            } else {
                throw new Error('Player não está pronto');
            }
        } catch (error) {
            console.error('Erro ao carregar track:', error);
            throw error;
        }
    }

    // Utilitários
    getPlayerElements() {
        return {
            container: document.getElementById('audio-player'),
            youtubePlayer: document.getElementById('youtube-player'),
            playPauseBtn: document.getElementById('play-pause-btn'),
            prevBtn: document.getElementById('prev-btn'),
            nextBtn: document.getElementById('next-btn'),
            volumeControl: document.getElementById('volume-control'),
            progressContainer: document.getElementById('progress-container'),
            progressBar: document.getElementById('progress-bar'),
            currentTime: document.getElementById('current-time'),
            duration: document.getElementById('duration'),
            title: document.getElementById('track-title'),
            artist: document.getElementById('track-artist'),
            cover: document.getElementById('album-cover'),
            playIcon: document.getElementById('play-icon'),
            pauseIcon: document.getElementById('pause-icon')
        };
    }

    extractVideoId(url) {
        try {
            const urlObj = new URL(url);
            const searchParams = new URLSearchParams(urlObj.search);

            if (urlObj.hostname.includes('youtu.be')) {
                return urlObj.pathname.slice(1);
            }

            return searchParams.get('v');
        } catch {
            // Tenta extrair o ID diretamente se a URL estiver malformada
            const match = url.match(/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i);
            return match ? match[1] : null;
        }
    }

    showError(message) {
        const toast = document.createElement('div');
        toast.className = 'fixed bottom-5 right-5 bg-red-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 5000);
    }

    showPlayer() {
        this.elements.container?.classList.remove('hidden');
    }

    // Controles de reprodução
    togglePlay() {
        if (!this.player) return;

        if (this.state.playing) {
            this.player.pauseVideo();
        } else {
            this.player.playVideo();
        }
    }

    updatePlayPauseButton() {
        if (!this.elements.playIcon || !this.elements.pauseIcon) return;

        if (this.state.playing) {
            this.elements.playIcon.classList.add('hidden');
            this.elements.pauseIcon.classList.remove('hidden');
        } else {
            this.elements.playIcon.classList.remove('hidden');
            this.elements.pauseIcon.classList.add('hidden');
        }
    }

    playNext() {
        if (this.currentIndex < this.playlist.length - 1) {
            this.currentIndex++;
            this.loadTrack(this.playlist[this.currentIndex]);
        }
    }

    playPrevious() {
        if (this.currentIndex > 0) {
            this.currentIndex--;
            this.loadTrack(this.playlist[this.currentIndex]);
        }
    }

    setVolume(volume) {
        this.state.volume = volume;
        this.player?.setVolume(volume);

        if (this.elements.volumeControl) {
            this.elements.volumeControl.value = volume;
        }
    }

    seekTo(ratio) {
        if (!this.player) return;

        const duration = this.player.getDuration();
        const newTime = duration * ratio;
        this.player.seekTo(newTime, true);
    }

    startProgressUpdate() {
        this.stopProgressUpdate();
        this.progressInterval = setInterval(() => this.updateProgress(), 1000);
    }

    stopProgressUpdate() {
        if (this.progressInterval) {
            clearInterval(this.progressInterval);
            this.progressInterval = null;
        }
    }

    updateProgress() {
        if (!this.player || !this.elements.progressBar) return;

        try {
            const duration = this.player.getDuration() || 0;
            const current = this.player.getCurrentTime() || 0;
            const progress = (current / duration) * 100;

            this.elements.progressBar.style.width = `${progress}%`;

            if (this.elements.currentTime) {
                this.elements.currentTime.textContent = this.formatTime(current);
            }
            if (this.elements.duration) {
                this.elements.duration.textContent = this.formatTime(duration);
            }
        } catch (error) {
            console.error('Erro ao atualizar progresso:', error);
        }
    }

    formatTime(seconds) {
        const minutes = Math.floor(seconds / 60);
        const remainingSeconds = Math.floor(seconds % 60);
        return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
    }

    updatePlayerInfo() {
        if (!this.currentTrack) return;

        try {
            // Atualiza título e artista
            if (this.elements.title) {
                this.elements.title.textContent = this.currentTrack.name || 'Desconhecido';
            }
            if (this.elements.artist) {
                this.elements.artist.textContent = this.currentTrack.artist || 'Artista Desconhecido';
            }

            // Atualiza imagem com verificação e fallback
            if (this.elements.cover) {
                const defaultCover = '/images/default-cover.jpg'; // Ajuste para seu caminho padrão

                // Função para verificar se a imagem existe
                const checkImage = (url) => {
                    return new Promise((resolve) => {
                        const img = new Image();
                        img.onload = () => resolve(true);
                        img.onerror = () => resolve(false);
                        img.src = url;
                    });
                };

                // Tenta carregar a imagem com fallback
                const loadCoverImage = async () => {
                    if (!this.currentTrack.cover_url) {
                        this.elements.cover.src = defaultCover;
                        return;
                    }

                    const imageExists = await checkImage(this.currentTrack.cover_url);
                    this.elements.cover.src = imageExists ? this.currentTrack.cover_url : defaultCover;
                };

                // Carrega a imagem apenas uma vez
                if (this.elements.cover.dataset.currentTrack !== this.currentTrack.id) {
                    loadCoverImage();
                    this.elements.cover.dataset.currentTrack = this.currentTrack.id;
                }
            }
        } catch (error) {
            console.error('Erro ao atualizar informações do player:', error);
        }
    }
}

// Inicialização única do player
if (!window.audioPlayer) {
    window.audioPlayer = new AudioPlayer();
}
