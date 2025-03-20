@extends('layouts.admin')

@section('content')
    <div class="p-4">
        <div class="mb-4">
            <h1 class="text-2xl font-semibold text-gray-900 sm:text-3xl">{{ __('Editar Faixas da Playlist') }}: {{ $playlist->name }}</h1>
        </div>

        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
            <form action="{{ route('admin.playlists.update_tracks', $playlist) }}" method="POST"
                  x-data="playlistTracksForm({{
                    json_encode([
                        'vinyls' => $playlist->tracks->map(function($track) {
                            return [
                                'vinyl_master_id' => $track->vinyl_master_id,
                                'vinyl_sec_id' => $track->trackable_id,
                                'title' => $track->vinylMaster->title . ' - ' . $track->vinylMaster->artists->pluck('name')->implode(', ')
                            ];
                        }),
                        'playlistId' => $playlist->id
                    ])
                }})"
                  class="space-y-6">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    <!-- Vinyls -->
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900">{{ __('Discos') }} <span class="text-xs text-gray-500">({{ __('máximo 10') }})</span></label>

                        <div class="mb-2 flex justify-between items-center">
                            <span class="text-sm text-gray-500" x-text="selectedVinyls.length + ' de 10 discos selecionados'"></span>
                            <button
                                type="button"
                                @click="selectedVinyls = []"
                                x-show="selectedVinyls.length > 0"
                                class="text-sm text-red-600 hover:underline">
                                {{ __('Limpar todos') }}
                            </button>
                        </div>

                        <div class="space-y-2 mb-4">
                            <template x-for="(vinyl, index) in selectedVinyls" :key="index">
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <span x-text="vinyl.title" class="text-sm text-gray-900"></span>
                                    <button type="button" @click="removeVinyl(index)" class="text-red-600 hover:text-red-900">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                    </button>
                                    <input type="hidden" :name="'vinyls['+index+'][vinyl_master_id]'" :value="vinyl.vinyl_master_id">
                                    <input type="hidden" :name="'vinyls['+index+'][vinyl_sec_id]'" :value="vinyl.vinyl_sec_id">
                                </div>
                            </template>
                        </div>

                        <div class="mt-4" x-show="selectedVinyls.length < 10">
                            <div class="relative" x-data="searchVinyls">
                                <div class="flex">
                                    <div class="relative w-full">
                                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                            <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                                            </svg>
                                        </div>
                                        <input
                                            type="text"
                                            x-model="search"
                                            @input="searchVinyls"
                                            @focus="isOpen = true"
                                            placeholder="{{ __('Buscar discos...') }}"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 p-2.5"
                                        >
                                    </div>
                                </div>

                                <p class="mt-1 text-xs text-gray-500">Digite pelo menos 3 caracteres para buscar. Você pode arrastar os discos para reordenar.</p>

                                <div
                                    x-show="isOpen && results.length > 0"
                                    @click.away="isOpen = false"
                                    class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg"
                                >
                                    <ul class="max-h-60 overflow-auto">
                                        <template x-for="result in results" :key="result.id">
                                            <li>
                                                <div
                                                    class="w-full px-4 py-2 text-left hover:bg-gray-100"
                                                >
                                                    <span x-text="result.title" class="block font-medium"></span>
                                                    <span x-text="result.artist" class="block text-sm text-gray-500"></span>
                                                    <div class="mt-1">
                                                        <template x-if="result.isAlreadyAdded">
                                                            <span class="inline-block px-2 py-1 text-xs font-medium text-gray-700 bg-gray-200 rounded">Já adicionado</span>
                                                        </template>
                                                        <template x-if="!result.isAlreadyAdded">
                                                            <button
                                                                type="button"
                                                                @click="addVinyl(result); isOpen = false; search = ''"
                                                                class="inline-block px-2 py-1 text-xs font-medium text-white bg-primary-700 rounded hover:bg-primary-800"
                                                            >
                                                                Adicionar
                                                            </button>
                                                        </template>
                                                    </div>
                                                </div>
                                            </li>
                                        </template>
                                    </ul>
                                </div>

                                <div x-show="search.length >= 3 && isOpen && results.length === 0" class="absolute z-10 w-full mt-1 p-4 bg-white border border-gray-300 rounded-lg shadow-lg">
                                    <p class="text-sm text-gray-500">Nenhum disco encontrado</p>
                                </div>

                                <div x-show="search.length >= 3 && isSearching" class="absolute z-10 w-full mt-1 p-4 bg-white border border-gray-300 rounded-lg shadow-lg">
                                    <p class="text-sm text-gray-500">Buscando discos...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-4">
                    <a href="{{ route('admin.playlists.edit', $playlist) }}" class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5">
                        {{ __('Voltar') }}
                    </a>
                    <button type="submit" class="text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5">
                        {{ __('Atualizar Faixas') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function playlistTracksForm(data) {
        return {
            selectedVinyls: data.vinyls || [],
            playlistId: data.playlistId,

            addVinyl(vinyl) {
                if (this.selectedVinyls.length >= 10) {
                    alert('Você já selecionou o número máximo de discos (10).');
                    return;
                }

                // Verifica se o disco já está na lista
                const exists = this.selectedVinyls.some(item =>
                    item.vinyl_sec_id === vinyl.vinylSecId
                );

                if (!exists) {
                    this.selectedVinyls.push({
                        vinyl_master_id: vinyl.vinylMasterId,
                        vinyl_sec_id: vinyl.vinylSecId,
                        title: vinyl.title + ' - ' + vinyl.artist
                    });
                }
            },

            removeVinyl(index) {
                this.selectedVinyls.splice(index, 1);
            }
        }
    }

    document.addEventListener('alpine:init', () => {
        Alpine.data('playlistTracksForm', playlistTracksForm);
    });

    // Adiciona a função de busca de discos
    document.addEventListener('alpine:init', () => {
        Alpine.data('searchVinyls', () => ({
            search: '',
            isOpen: false,
            results: [],
            isSearching: false,
            timeout: null,

            searchVinyls() {
                // Limpa o timeout anterior para evitar múltiplas requisições
                clearTimeout(this.timeout);

                // Se a busca for muito curta, limpa os resultados
                if (this.search.length < 3) {
                    this.results = [];
                    return;
                }

                // Define um timeout para evitar requisições a cada tecla digitada
                this.timeout = setTimeout(() => {
                    this.isSearching = true;

                    // Obtém o ID da playlist do formulário pai
                    const playlistForm = document.querySelector('form[x-data^="playlistTracksForm"]');
                    const playlistData = JSON.parse(playlistForm.getAttribute('x-data').replace('playlistTracksForm(', '').replace(')', ''));
                    const playlistId = playlistData.playlistId;

                    // URL para a busca de discos
                    const searchUrl = '{{ route('admin.playlists.search-tracks') }}';

                    // Faz a requisição para a API
                    fetch(`${searchUrl}?query=${encodeURIComponent(this.search)}&playlist_id=${playlistId}`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Erro ao buscar discos');
                            }
                            return response.json();
                        })
                        .then(data => {
                            this.results = data;
                            this.isSearching = false;
                        })
                        .catch(error => {
                            console.error('Erro na busca:', error);
                            this.results = [];
                            this.isSearching = false;
                            alert('Ocorreu um erro ao buscar discos. Por favor, tente novamente.');
                        });
                }, 300);
            }
        }));
    });
</script>
@endpush
