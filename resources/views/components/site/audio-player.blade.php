<div id="audio-player" class="fixed bottom-0 left-0 right-0 bg-yellow-400 border-t border-gray-200 shadow-lg z-50 hidden">
    <div class="max-w-7xl mx-auto px-4">
        <!-- Player para Desktop -->
        <div class="hidden md:block">
            <div class="flex items-center justify-between py-4">
                <!-- Informações da Música -->
                <div class="flex items-center space-x-4 flex-1">
                    <div class="relative">
                        <img
                            id="album-cover"
                            src="/path/to/default/cover.jpg"
                            alt="Album cover"
                            class="w-16 h-16 object-cover rounded-full shadow-lg transition-transform duration-500"
                            data-playing="false"
                        >
                        <div class="absolute inset-0 rounded-full border-2 border-black border-t-transparent animate-spin opacity-0 transition-opacity duration-300" data-spinner></div>
                    </div>
                    <div class="min-w-0">
                        <h3 id="track-title" class="text-lg font-semibold truncate"></h3>
                        <p id="track-artist" class="text-sm text-gray-900 truncate"></p>
                    </div>
                </div>

                <!-- Controles de Reprodução -->
                <div class="flex flex-col items-center space-y-2 flex-1">
                    <div class="flex items-center space-x-4">
                        <button id="prev-btn" class="p-2 hover:bg-black hover:text-white rounded-full transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>
                        <button id="play-pause-btn" class="p-3 bg-black hover:bg-gray-900 text-white rounded-full transition-colors">
                            <svg id="play-icon" class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z"></path>
                            </svg>
                            <svg id="pause-icon" class="w-6 h-6 hidden" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"></path>
                            </svg>
                        </button>
                        <button id="next-btn" class="p-2 hover:bg-black hover:text-white rounded-full transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Barra de Progresso -->
                    <div class="w-full flex items-center space-x-2">
                        <span id="current-time" class="text-sm text-gray-600 w-12 text-right">0:00</span>
                        <div class="flex-1 relative h-2" id="progress-container">
                            <div class="absolute inset-0 bg-gray-200 rounded-full"></div>
                            <div id="progress-bar" class="absolute inset-y-0 left-0 bg-black rounded-full" style="width: 0%">
                                <div id="progress-handle" class="absolute right-0 top-1/2 -translate-y-1/2 w-4 h-4 bg-black rounded-full shadow-lg cursor-pointer hover:scale-110 transition-transform"></div>
                            </div>
                        </div>
                        <span id="duration" class="text-sm text-gray-600 w-12">0:00</span>
                    </div>
                </div>

                <!-- Controle de Volume -->
                <div class="flex items-center space-x-4 flex-1 justify-end">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15.536a5 5 0 001.414 1.414m0 0l-2.828 2.828-1.414-1.414 2.828-2.828a9 9 0 0112.728 0l-1.414 1.414a7 7 0 00-9.9 0z"></path>
                        </svg>
                        <input
                            type="range"
                            id="volume-control"
                            min="0"
                            max="100"
                            value="100"
                            class="w-24 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer"
                        >
                    </div>
                </div>
            </div>
        </div>

        <!-- Player para Mobile -->
        <div class="md:hidden">
            <div class="py-2">
                <!-- Barra de Progresso Mobile -->
                <div class="w-full flex items-center space-x-2 mb-2">
                    <span id="current-time-mobile" class="text-xs text-gray-600">0:00</span>
                    <div class="flex-1 relative h-1" id="progress-container-mobile">
                        <div class="absolute inset-0 bg-gray-200 rounded-full"></div>
                        <div id="progress-bar-mobile" class="absolute inset-y-0 left-0 bg-black rounded-full" style="width: 0%">
                            <div id="progress-handle-mobile" class="absolute right-0 top-1/2 -translate-y-1/2 w-3 h-3 bg-black rounded-full shadow-lg cursor-pointer"></div>
                        </div>
                    </div>
                    <span id="duration-mobile" class="text-xs text-gray-600">0:00</span>
                </div>

                <div class="flex items-center justify-between">
                    <!-- Informações da Música -->
                    <div class="flex items-center space-x-3 flex-1 min-w-0">
                        <div class="relative w-12 h-12">
                            <img
                                id="album-cover-mobile"
                                src="/path/to/default/cover.jpg"
                                alt="Album cover"
                                class="w-12 h-12 object-cover rounded-full shadow"
                                data-playing="false"
                            >
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 id="track-title-mobile" class="text-sm font-semibold truncate">Carregando...</h3>
                            <p id="track-artist-mobile" class="text-xs text-gray-600 truncate">Carregando...</p>
                        </div>
                    </div>

                    <!-- Controles Mobile -->
                    <div class="flex items-center space-x-2">
                        <button id="prev-btn-mobile" class="p-1 hover:bg-gray-100 rounded-full">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>
                        <button id="play-pause-btn-mobile" class="p-2 bg-black hover:bg-black text-white rounded-full">
                            <svg id="play-icon-mobile" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z"></path>
                            </svg>
                            <svg id="pause-icon-mobile" class="w-4 h-4 hidden" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"></path>
                            </svg>
                        </button>
                        <button id="next-btn-mobile" class="p-1 hover:bg-gray-100 rounded-full">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="youtube-player"></div>
</div>

<style>
@keyframes rotate {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

.album-cover-rotating {
    animation: rotate 1.82s linear infinite; /* Ajustado para 33 RPM */
}

.album-cover-paused {
    animation-play-state: paused;
}
/* Estilização do progress bar para mobile */
#progress-container-mobile {
    cursor: pointer;
    touch-action: none;
}

#progress-handle-mobile {
    display: none;
}

#progress-container-mobile:active #progress-handle-mobile {
    display: block;
}
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const albumCover = document.getElementById('album-cover');
        const albumCoverMobile = document.getElementById('album-cover-mobile');
        const trackTitle = document.getElementById('track-title');
        const trackTitleMobile = document.getElementById('track-title-mobile');
        const trackArtist = document.getElementById('track-artist');
        const trackArtistMobile = document.getElementById('track-artist-mobile');
        const progressBar = document.getElementById('progress-bar');
        const progressBarMobile = document.getElementById('progress-bar-mobile');
        const currentTime = document.getElementById('current-time');
        const currentTimeMobile = document.getElementById('current-time-mobile');
        const duration = document.getElementById('duration');
        const durationMobile = document.getElementById('duration-mobile');

        // Função para sincronizar elementos do player
        function syncPlayerElements() {
            // Sincroniza informações da música
            if (trackTitle && trackTitleMobile) {
                trackTitleMobile.textContent = trackTitle.textContent;
            }
            if (trackArtist && trackArtistMobile) {
                trackArtistMobile.textContent = trackArtist.textContent;
            }

            // Sincroniza capa do álbum
            if (albumCover && albumCoverMobile) {
                albumCoverMobile.src = albumCover.src;
            }

            // Sincroniza progresso
            if (progressBar && progressBarMobile) {
                progressBarMobile.style.width = progressBar.style.width;
            }

            // Sincroniza tempos
            if (currentTime && currentTimeMobile) {
                currentTimeMobile.textContent = currentTime.textContent;
            }
            if (duration && durationMobile) {
                durationMobile.textContent = duration.textContent;
            }
        }

        // Observer para mudanças no player
        const playerObserver = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.type === 'attributes' || mutation.type === 'childList') {
                    syncPlayerElements();
                }
            });
        });

        // Observa mudanças em elementos relevantes
        const elementsToObserve = [
            trackTitle,
            trackArtist,
            albumCover,
            progressBar,
            currentTime,
            duration
        ].filter(Boolean);

        elementsToObserve.forEach(element => {
            playerObserver.observe(element, {
                attributes: true,
                childList: true,
                characterData: true,
                subtree: true
            });
        });

        // Função para atualizar o estado de rotação
        function updateRotation(isPlaying) {
            [albumCover, albumCoverMobile].forEach(cover => {
                if (cover) {
                    cover.classList.toggle('album-cover-rotating', isPlaying);
                    cover.dataset.playing = isPlaying;
                }
            });
        }

        // Observer para estado de reprodução
        const playerStateObserver = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.target.id === 'play-icon' || mutation.target.id === 'pause-icon') {
                    const isPlaying = mutation.target.id === 'pause-icon' && !mutation.target.classList.contains('hidden');
                    updateRotation(isPlaying);

                    // Sincroniza estado de play/pause entre desktop e mobile
                    const playIconMobile = document.getElementById('play-icon-mobile');
                    const pauseIconMobile = document.getElementById('pause-icon-mobile');
                    if (playIconMobile && pauseIconMobile) {
                        playIconMobile.classList.toggle('hidden', isPlaying);
                        pauseIconMobile.classList.toggle('hidden', !isPlaying);
                    }
                }
            });
        });

        // Observa mudanças nos ícones de play/pause
        ['play-icon', 'pause-icon'].forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                playerStateObserver.observe(element, { attributes: true, attributeFilter: ['class'] });
            }
        });

        // Adiciona eventos de toque para a barra de progresso mobile
        const progressContainerMobile = document.getElementById('progress-container-mobile');
        if (progressContainerMobile) {
            progressContainerMobile.addEventListener('touchstart', function(e) {
                const rect = this.getBoundingClientRect();
                const x = e.touches[0].clientX - rect.left;
                const width = rect.width;
                const percentage = (x / width) * 100;
                progressBarMobile.style.width = `${percentage}%`;

                // Dispara evento para atualizar o tempo do player
                const event = new CustomEvent('progressUpdate', {
                    detail: { percentage: percentage }
                });
                document.dispatchEvent(event);
            });

            progressContainerMobile.addEventListener('touchmove', function(e) {
                const rect = this.getBoundingClientRect();
                const x = e.touches[0].clientX - rect.left;
                const width = rect.width;
                const percentage = Math.max(0, Math.min(100, (x / width) * 100));
                progressBarMobile.style.width = `${percentage}%`;

                // Dispara evento para atualizar o tempo do player
                const event = new CustomEvent('progressUpdate', {
                    detail: { percentage: percentage }
                });
                document.dispatchEvent(event);
            });
        }

        // Sincroniza botões de controle mobile com desktop
        const prevBtnMobile = document.getElementById('prev-btn-mobile');
        const nextBtnMobile = document.getElementById('next-btn-mobile');
        const playPauseBtnMobile = document.getElementById('play-pause-btn-mobile');

        if (prevBtnMobile) {
            prevBtnMobile.addEventListener('click', () => {
                document.getElementById('prev-btn')?.click();
            });
        }

        if (nextBtnMobile) {
            nextBtnMobile.addEventListener('click', () => {
                document.getElementById('next-btn')?.click();
            });
        }

        if (playPauseBtnMobile) {
            playPauseBtnMobile.addEventListener('click', () => {
                document.getElementById('play-pause-btn')?.click();
            });
        }
    });
    </script>

