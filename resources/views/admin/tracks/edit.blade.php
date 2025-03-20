@extends('layouts.admin')

@section('title', 'Editar Faixas')

@section('content')
<div x-data="trackManager" class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Editar Faixas: {{ $vinyl->title }}</h2>
        <a href="{{ route('admin.vinyls.index') }}" class="btn btn-outline">Voltar</a>
    </div>

    <div class="card shadow-xl mb-6">
        <div class="card-body p-4">
            <div class="text-sm">
                <span class="font-bold">Artista:</span> {{ $vinyl->artists->pluck('name')->join(', ') }} |
                <span class="font-bold">Ano:</span> {{ $vinyl->release_year }}
            </div>
        </div>
    </div>

    <form action="{{ route('admin.vinyls.update-tracks', $vinyl->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card shadow-xl">
            <div class="card-body p-4">
                <div id="tracks-container" class="space-y-4">
                    <template x-for="(track, index) in tracks" :key="index">
                        <div class="grid grid-cols-12 gap-2 items-center bg-gray-100 p-2 rounded">
                            <input type="hidden" :name="'tracks['+index+'][id]'" :value="track.id">
                            <input type="text" x-model="track.name" :name="'tracks['+index+'][name]'" class="input input-sm input-bordered col-span-4" placeholder="Nome da Faixa" required>
                            <input type="text" x-model="track.duration" :name="'tracks['+index+'][duration]'" class="input input-sm input-bordered col-span-2" placeholder="Duração">
                            <div class="col-span-6 flex">
                                <input type="url" x-model="track.youtube_url" :name="'tracks['+index+'][youtube_url]'" class="input input-sm input-bordered flex-grow" placeholder="URL do YouTube">
                                <button type="button" class="btn btn-sm btn-square btn-secondary ml-1"
                                    @click="searchYouTube(index)" :disabled="isLoading">
                                    <template x-if="!isLoading">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                        </svg>
                                    </template>
                                    <template x-if="isLoading">
                                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                        </svg>
                                    </template>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
                <button type="button" class="btn btn-secondary btn-sm mt-4" @click="addTrack">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Adicionar Faixa
                </button>
            </div>
            <div class="card-footer flex justify-end p-4">
                <button type="submit" class="btn btn-primary">Salvar Alterações</button>
            </div>
        </div>
    </form>

    <!-- Modal para resultados do YouTube -->
    <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModal"></div>
            <div class="modal-box bg-white rounded-lg shadow-xl max-w-lg w-full p-6 relative">
                <h3 class="font-bold text-lg mb-4">Selecionar Vídeo do YouTube</h3>
                <div class="space-y-2">
                    <template x-for="result in youtubeResults" :key="result.id.videoId">
                        <div class="p-2 hover:bg-gray-100 rounded cursor-pointer" @click="selectVideo(result)">
                            <h4 class="font-semibold" x-text="result.snippet.title"></h4>
                            <p class="text-sm text-gray-600" x-text="result.snippet.description"></p>
                        </div>
                    </template>
                </div>
                <div class="mt-4 text-right">
                    <button class="btn btn-sm" @click="closeModal">Fechar</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('trackManager', () => ({
        tracks: @json($vinyl->tracks),
        showModal: false,
        youtubeResults: [],
        activeTrackIndex: null,
        isLoading: false, // Propriedade para controlar o loading

        addTrack() {
            this.tracks.push({ name: '', duration: '', youtube_url: '' });
        },

        async searchYouTube(index) {
            this.activeTrackIndex = index;
            this.isLoading = true;
            const trackName = this.tracks[index].name;
            const artistName = '{{ $vinyl->artists->pluck('name')->join(" ") }}';
            const query = `${artistName} ${trackName}`;

            try {
                const response = await fetch('{{ route('youtube.search') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ query })
                });

                if (!response.ok) throw new Error('Network response was not ok');
                const data = await response.json();
                if (data.error) throw new Error(data.error);

                this.youtubeResults = data;
                this.showModal = true;
            } catch (error) {
                console.error('Erro ao pesquisar no YouTube:', error);
                Toastify({
                    text: "Erro ao pesquisar no YouTube. Tente novamente.",
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "linear-gradient(to right, #FF5F6D, #FFC371)"
                }).showToast();
            } finally {
                this.isLoading = false;
            }
        },

        selectVideo(video) {
            if (this.activeTrackIndex !== null) {
                this.tracks[this.activeTrackIndex].youtube_url = `https://www.youtube.com/watch?v=${video.id.videoId}`;
            }
            this.closeModal();
        },

        closeModal() {
            this.showModal = false;
            this.youtubeResults = [];
            this.activeTrackIndex = null;
        }
    }));
});
</script>

<!-- Toastify Flash Messages -->
@if(session('success'))
<script>
    Toastify({
        text: "{{ session('success') }}",
        duration: 3000,
        gravity: "top",
        position: "right",
        backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)"
    }).showToast();
</script>
@endif

@if(session('error'))
<script>
    Toastify({
        text: "{{ session('error') }}",
        duration: 3000,
        gravity: "top",
        position: "right",
        backgroundColor: "linear-gradient(to right, #FF5F6D, #FFC371)"
    }).showToast();
</script>
@endif
@endpush
