@extends('layouts.admin')

@section('title', 'Create New Vinyl')

@section('content')
<div x-data="vinylManager" class="p-4">
    <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
        <h2 class="text-xl font-semibold mb-4 text-gray-900">Pesquisar novo disco:</h2>

        <form action="{{ route('admin.vinyls.create') }}" method="GET" @submit="startSearch()">
            <div class="flex items-center gap-2">
                <div class="flex-grow">
                    <input type="text"
                           name="query"
                           value="{{ $query }}"
                           class="form-input"
                           placeholder="Encontre o disco pelo artista, título ou código do disco"
                           required>
                </div>
                <button type="submit"
                        class="btn-primary"
                        :disabled="loading">
                    <template x-if="loading">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </template>
                    <svg x-show="!loading" class="w-4 h-4 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <span x-text="loading ? 'Pesquisando...' : 'Pesquisar'"></span>
                </button>
            </div>
        </form>

        <div id="searchResults" class="mt-6">
            @if($selectedRelease)
                <!-- Selected Release Content -->
                <div class="max-w-6xl mx-auto">
                    <h3 class="text-xl font-semibold mb-4 text-gray-900">Você selecionou o disco: {{ $selectedRelease['title'] }}</h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Image Column -->
                        <div class="md:col-span-1">
                            @if(isset($selectedRelease['images']) && count($selectedRelease['images']) > 0)
                                <img src="{{ $selectedRelease['images'][0]['uri'] }}"
                                     alt="{{ $selectedRelease['title'] }}"
                                     class="w-full rounded-lg shadow-lg">
                            @else
                                <div class="w-full h-64 bg-gray-200 flex items-center justify-center rounded-lg">
                                    <span class="text-gray-500">Sem imagem</span>
                                </div>
                            @endif
                        </div>

                        <!-- Details Column -->
                        <div class="md:col-span-2 space-y-6">
                            <!-- Basic Info -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">Artista</h4>
                                    <p class="text-base text-gray-900">{{ implode(', ', array_column($selectedRelease['artists'], 'name')) }}</p>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">Título</h4>
                                    <p class="text-base text-gray-900">{{ $selectedRelease['title'] }}</p>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">Ano</h4>
                                    <p class="text-base text-gray-900">{{ $selectedRelease['year'] ?? 'Desconhecido' }}</p>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">Gênero</h4>
                                    <p class="text-base text-gray-900">{{ implode(', ', $selectedRelease['genres'] ?? ['Não especificado']) }}</p>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">Estilos</h4>
                                    <p class="text-base text-gray-900">{{ implode(', ', $selectedRelease['styles'] ?? ['Não especificado']) }}</p>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">País</h4>
                                    <p class="text-base text-gray-900">{{ $selectedRelease['country'] ?? 'Desconhecido' }}</p>
                                </div>
                                @if(isset($selectedRelease['labels']))
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500">Gravadora</h4>
                                        <p class="text-base text-gray-900">{{ implode(', ', array_column($selectedRelease['labels'], 'name')) }}</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Market Info -->
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h4 class="text-lg font-semibold mb-3 text-gray-900">Informações de Mercado (Discogs)</h4>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                    @if(isset($selectedRelease['community']['have']))
                                        <div>
                                            <h5 class="text-sm font-medium text-gray-500">Quantidade em coleções</h5>
                                            <p class="text-base text-gray-900">{{ $selectedRelease['community']['have'] }}</p>
                                        </div>
                                    @endif
                                    @if(isset($selectedRelease['num_for_sale']))
                                        <div>
                                            <h5 class="text-sm font-medium text-gray-500">Quantidade à venda</h5>
                                            <p class="text-base text-gray-900">{{ $selectedRelease['num_for_sale'] }}</p>
                                        </div>
                                    @endif
                                    @php
                                        $exchangeRate = 5.8;
                                        $lowestPriceUSD = $selectedRelease['lowest_price'] ?? 0;
                                        $medianPriceUSD = $selectedRelease['median_price'] ?? 0;
                                        $highestPriceUSD = $selectedRelease['highest_price'] ?? 0;
                                        $lowestPriceBRL = $lowestPriceUSD * $exchangeRate;
                                        $medianPriceBRL = $medianPriceUSD * $exchangeRate;
                                        $highestPriceBRL = $highestPriceUSD * $exchangeRate;
                                    @endphp
                                    @if($lowestPriceUSD > 0)
                                        <div>
                                            <h5 class="text-sm font-medium text-gray-500">Preço mais baixo</h5>
                                            <p class="text-base text-gray-900">
                                                US$ {{ number_format($lowestPriceUSD, 2, ',', '.') }}
                                                <span class="text-xs text-gray-500">(R$ {{ number_format($lowestPriceBRL, 2, ',', '.') }})</span>
                                            </p>
                                        </div>
                                        <div>
                                            <h5 class="text-sm font-medium text-gray-500">Preço médio</h5>
                                            <p class="text-base text-gray-900">
                                                US$ {{ number_format($medianPriceUSD, 2, ',', '.') }}
                                                <span class="text-xs text-gray-500">(R$ {{ number_format($medianPriceBRL, 2, ',', '.') }})</span>
                                            </p>
                                        </div>
                                        <div>
                                            <h5 class="text-sm font-medium text-gray-500">Preço mais alto</h5>
                                            <p class="text-base text-gray-900">
                                                US$ {{ number_format($highestPriceUSD, 2, ',', '.') }}
                                                <span class="text-xs text-gray-500">(R$ {{ number_format($highestPriceBRL, 2, ',', '.') }})</span>
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Tracklist -->
                            @if(isset($selectedRelease['tracklist']))
                                <div>
                                    <h4 class="text-lg font-semibold mb-3 text-gray-900">Tracklist</h4>
                                    <ul class="space-y-2">
                                        @foreach($selectedRelease['tracklist'] as $track)
                                            <li class="flex items-center text-gray-900">
                                                <span class="w-6 h-6 flex items-center justify-center bg-gray-100 rounded-full mr-2 text-sm">
                                                    {{ $loop->iteration }}
                                                </span>
                                                <span class="font-medium">{{ $track['title'] }}</span>
                                                @if(isset($track['duration']))
                                                    <span class="ml-2 text-sm text-gray-500">({{ $track['duration'] }})</span>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <!-- Notes -->
                            @if(isset($selectedRelease['notes']))
                                <div>
                                    <h4 class="text-lg font-semibold mb-2 text-gray-900">Notas</h4>
                                    <p class="text-sm text-gray-700">{{ $selectedRelease['notes'] }}</p>
                                </div>
                            @endif

                            <!-- Actions -->
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ $selectedRelease['uri'] }}"
                                   target="_blank"
                                   class="btn-primary bg-blue-700 hover:bg-blue-800">
                                    Link do disco no Discogs
                                </a>
                                <button @click="saveVinyl({{ $selectedRelease['id'] }})"
                                        class="btn-primary"
                                        :disabled="saveLoading">
                                    <template x-if="saveLoading">
                                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </template>
                                    <span x-text="saveLoading ? 'Salvando...' : 'Salvar disco'"></span>
                                </button>
                                <a href="{{ route('admin.vinyls.create') }}"
                                   class="btn-secondary">
                                    Voltar para busca
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif(count($searchResults) > 0)
                <!-- Search Results -->
                <div x-data="{ formatFilter: '', countryFilter: '', yearFilter: '' }">
                    <h3 class="text-xl font-semibold mb-4 text-gray-900">Resultados da Busca</h3>

                    <!-- Filters -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                        <select x-model="formatFilter" class="form-input">
                            <option value="">Todos os Formatos</option>
                            @foreach(collect($searchResults)->pluck('format')->flatten()->unique() as $format)
                                <option value="{{ $format }}">{{ $format }}</option>
                            @endforeach
                        </select>

                        <select x-model="countryFilter" class="form-input">
                            <option value="">Todos os Países</option>
                            @foreach(collect($searchResults)->pluck('country')->unique() as $country)
                                <option value="{{ $country }}">{{ $country }}</option>
                            @endforeach
                        </select>

                        <select x-model="yearFilter" class="form-input">
                            <option value="">Todos os Anos</option>
                            @foreach(collect($searchResults)->pluck('year')->filter()->unique() as $year)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endforeach
                            <option value="Desconhecido">Ano desconhecido</option>
                        </select>
                    </div>

                    <!-- Results -->
                    <div class="space-y-4">
                        @foreach($searchResults as $result)
                            <div class="card"
                                 x-show="
                                    (!formatFilter || '{{ $result['format'][0] ?? '' }}'.includes(formatFilter)) &&
                                    (!countryFilter || '{{ $result['country'] }}' === countryFilter) &&
                                    (!yearFilter || '{{ $result['year'] ?? 'Desconhecido' }}' === yearFilter)
                                 ">
                                <div class="p-4 flex items-center gap-4">
                                    <img src="{{ $result['thumb'] ?? '/placeholder-image.jpg' }}"
                                         alt="{{ $result['title'] }}"
                                         class="w-16 h-16 object-cover rounded-lg">
                                    <div class="flex-grow">
                                        <h4 class="text-lg font-semibold text-gray-900">{{ $result['title'] }}</h4>
                                        <div class="text-sm text-gray-500 space-x-2">
                                            <span>{{ $result['year'] ?? 'Ano desconhecido' }}</span>
                                            @if(isset($result['format']))
                                                <span>•</span>
                                                <span>{{ $result['format'][0] ?? 'Format unknown' }}</span>
                                            @endif
                                            @if(isset($result['country']))
                                                <span>•</span>
                                                <span>{{ $result['country'] }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <a href="{{ route('admin.vinyls.create', ['release_id' => $result['id'], 'query' => $query]) }}"
                                       class="btn-primary"
                                       x-data="{ loading: false }"
                                       @click.prevent="loading = true; window.location.href = $el.href">
                                        <template x-if="loading">
                                            <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </template>
                                        <span x-text="loading ? 'Carregando...' : 'Selecionar'"></span>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @elseif($query)
                <div class="p-4 mb-4 text-sm text-blue-800 rounded-lg bg-blue-50" role="alert">
                    Nenhum resultado encontrado para "{{ $query }}".
                </div>
            @endif
        </div>
    </div>

    <!-- Modal -->
    <x-modal id="result-modal">
        <div class="p-6">
            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4" x-text="modalTitle"></h3>
            <p class="text-sm text-gray-500 mb-4" x-text="modalMessage"></p>

            <div class="flex justify-end gap-2">
                <template x-if="modalStatus === 'success'">
                    <div class="flex gap-2">
                        <a :href="completeVinylUrl"
                           class="btn-primary">
                            Completar cadastro
                        </a>
                        <button @click="closeModalAndRedirect"
                                class="btn-secondary">
                            Voltar para lista de discos
                        </button>
                    </div>
                </template>
                <template x-if="modalStatus === 'exists'">
                    <button @click="closeModalAndRedirect"
                            class="btn-primary">
                        Voltar para lista de discos
                    </button>
                </template>
                <template x-if="modalStatus === 'error'">
                    <button @click="closeModal"
                            class="btn-primary bg-red-600 hover:bg-red-700">
                        Fechar
                    </button>
                </template>
            </div>
        </div>
    </x-modal>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('vinylManager', () => ({
        loading: false,
        saveLoading: false,
        modalTitle: '',
        modalMessage: '',
        modalStatus: '',
        savedVinylId: null,

        init() {
            // Initialize any required setup
        },

        startSearch() {
            this.loading = true;
            // The form will handle the actual submission
            return true;
        },

        get completeVinylUrl() {
            if (!this.savedVinylId) return '#';
            const baseUrl = document.querySelector('meta[name="complete-vinyl-url"]')?.content;
            return baseUrl?.replace(':id', this.savedVinylId) || '#';
        },

        async saveVinyl(releaseId) {
            this.saveLoading = true;
            try {
                const storeUrl = document.querySelector('meta[name="store-vinyl-url"]')?.content;
                if (!storeUrl) throw new Error('Store URL not found');

                const response = await fetch(storeUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ release_id: releaseId })
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Network response was not ok');
                }

                const data = await response.json();
                this.savedVinylId = data.vinyl_id;
                this.modalMessage = data.message;
                this.modalStatus = data.status;
                this.modalTitle = this.modalStatus === 'exists' ? 'Disco já cadastrado' :
                                 this.modalStatus === 'success' ? 'Disco salvo com sucesso!' :
                                 'Erro';
                window.dispatchEvent(new CustomEvent('open-modal', { detail: 'result-modal' }));
            } catch (error) {
                console.error('Erro:', error);
                this.modalMessage = error.message || 'Ocorreu um erro ao processar o disco. Por favor, tente novamente.';
                this.modalStatus = 'error';
                this.modalTitle = 'Erro';
                window.dispatchEvent(new CustomEvent('open-modal', { detail: 'result-modal' }));
            } finally {
                this.saveLoading = false;
            }
        },

        closeModal() {
            window.dispatchEvent(new CustomEvent('close-modal'));
        },

        closeModalAndRedirect() {
            const indexUrl = document.querySelector('meta[name="vinyl-index-url"]')?.content;
            if (indexUrl) {
                window.dispatchEvent(new CustomEvent('close-modal'));
                setTimeout(() => {
                    window.location.href = indexUrl;
                }, 150);
            }
        }
    }));
});
</script>
@endpush
@endsection
