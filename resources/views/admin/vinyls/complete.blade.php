@extends('layouts.admin')

@section('title', 'Complete Vinyl Record')

@section('content')
<div x-data="vinylComplete" class="p-4">
    <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm 2xl:col-span-2 dark:border-gray-700 dark:bg-gray-800">
        <h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">
            Completar cadastro de: {{ $vinylMaster->artists->pluck('name')->join(', ') }} - {{ $vinylMaster->title }}
        </h2>

        <form action="{{ route('admin.vinyl.storeComplete', $vinylMaster->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if ($errors->any())
                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400">
                    <ul class="list-disc pl-4">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Required Fields Alert -->
            <div class="flex p-4 mb-4 text-red-800 border-t-4 border-red-300 bg-red-50 dark:text-red-400 dark:bg-gray-800 dark:border-red-800" role="alert">
                <svg class="flex-shrink-0 w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                <div class="ml-3 text-sm font-medium">
                    <h3 class="font-bold">Campos obrigatórios</h3>
                    <p>Complete todos esses campos, é importante:</p>
                    <a href="{{ $vinylMaster->discogs_url }}" target='_blank' class="inline-flex items-center px-4 py-2 mt-2 text-sm font-medium text-white bg-red-700 rounded-lg hover:bg-red-800 focus:ring-4 focus:ring-red-300 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">
                        Link do disco no Discogs
                    </a>
                </div>
            </div>

            <!-- Basic Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <!-- Weight -->
                <div>
                    <label for="weight_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Peso:</label>
                    <select id="weight_id" name="weight_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" required>
                        <option value="">Selecionar peso:</option>
                        @foreach($weights as $weight)
                            <option value="{{ $weight->id }}" {{ $weight->id == 1 ? 'selected' : '' }}>
                                {{ $weight->name }} ({{ $weight->value }} {{ $weight->unit }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Dimensions -->
                <div>
                    <label for="dimension_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Dimensões:</label>
                    <select id="dimension_id" name="dimension_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" required>
                        <option value="">Selecionar dimensão</option>
                        @foreach($dimensions as $dimension)
                            <option value="{{ $dimension->id }}" {{ $dimension->id == 3 ? 'selected' : '' }}>
                                {{ $dimension->name }} ({{ $dimension->height }}x{{ $dimension->width }}x{{ $dimension->depth }} {{ $dimension->unit }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Quantity -->
                <div>
                    <label for="quantity" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Estoque:</label>
                    <input type="number" id="quantity" name="quantity" min="0" value="1" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" required>
                </div>

                <!-- Price -->
                <div>
                    <label for="price" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Preço de Venda:</label>
                    <input type="number" id="price" name="price" step="0.01" min="0" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" required>
                </div>
            </div>

            <!-- Categories and Status -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <!-- Categories -->
                <div class="md:col-span-2">
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Categorias de Estilo da Loja</label>
                    <div class="grid grid-cols-2 gap-4">
                        @foreach($categories as $category)
                            <div class="flex items-center">
                                <input type="checkbox" name="category_ids[]" value="{{ $category->id }}"
                                    class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500"
                                    {{ in_array($category->id, old('category_ids', $selectedCategories ?? [])) ? 'checked' : '' }}>
                                <label class="ml-2 text-sm font-medium text-gray-900">{{ $category->nome }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Status Options -->
                <div class="space-y-4 md:col-span-2">
                    <!-- New/Used -->
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Produto novo?</label>
                        <div class="flex gap-4">
                            <div class="flex items-center">
                                <input type="radio" name="is_new" value="1" class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                <label class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">Novo</label>
                            </div>
                            <div class="flex items-center">
                                <input type="radio" name="is_new" value="0" class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" checked>
                                <label class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">Usado</label>
                            </div>
                        </div>
                    </div>

                    <!-- Promotional -->
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Em promoção?</label>
                        <div class="flex gap-4">
                            <div class="flex items-center">
                                <input type="radio" name="is_promotional" value="1" class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                <label class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">SIM</label>
                            </div>
                            <div class="flex items-center">
                                <input type="radio" name="is_promotional" value="0" class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" checked>
                                <label class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">NÃO</label>
                            </div>
                        </div>
                    </div>

                    <!-- Stock Status -->
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Em Estoque</label>
                        <div class="flex gap-4">
                            <div class="flex items-center">
                                <input type="radio" name="in_stock" value="1" class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" checked>
                                <label class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">Com estoque</label>
                            </div>
                            <div class="flex items-center">
                                <input type="radio" name="in_stock" value="0" class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                <label class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">Sem estoque</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="h-px my-8 bg-gray-200 border-0 dark:bg-gray-700">

            <!-- Optional Fields Alert -->
            <div class="flex p-4 mb-4 text-green-800 border-t-4 border-green-300 bg-green-50 dark:text-green-400 dark:bg-gray-800 dark:border-green-800" role="alert">
                <svg class="flex-shrink-0 w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                <div class="ml-3 text-sm font-medium">
                    <h3 class="font-bold">Campos opcionais</h3>
                    <p>Campos auxiliares no cadastro, mas não são obrigatórios:</p>
                </div>
            </div>

            <!-- Optional Fields -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <!-- Cover Status -->
                <div>
                    <label for="cover_status" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Estado da capa:</label>
                    <select id="cover_status" name="cover_status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                        <option value="">Selecione estado da capa</option>
                        @foreach(['mint', 'near_mint', 'very_good', 'good', 'fair', 'poor'] as $status)
                            <option value="{{ $status }}">{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                        @en$status}}">{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Media Status -->
                <div>
                    <label for="midia_status" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Estado da mídia:</label>
                    <select id="midia_status" name="midia_status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                        <option value="">Selecionar estado da midia</option>
                        @foreach(['mint', 'near_mint', 'very_good', 'good', 'fair', 'poor'] as $status)
                            <option value="{{ $status }}">{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Catalog Number -->
                <div>
                    <label for="catalog_number" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Numero de catálogo:</label>
                    <input type="text" id="catalog_number" name="catalog_number" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                </div>

                <!-- Barcode -->
                <div>
                    <label for="barcode" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Barcode:</label>
                    <input type="text" id="barcode" name="barcode" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                </div>
            </div>

            <!-- Additional Optional Fields -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <!-- Format -->
                <div>
                    <label for="format" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Formato:</label>
                    <input type="text" id="format" name="format" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                </div>

                <!-- Number of Discs -->
                <div>
                    <label for="num_discs" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Numero de discos:</label>
                    <input type="number" id="num_discs" name="num_discs" min="1" value="1" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" required>
                </div>

                <!-- Speed -->
                <div>
                    <label for="speed" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Velocidade:</label>
                    <input type="text" id="speed" name="speed" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                </div>

                <!-- Edition -->
                <div>
                    <label for="edition" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Edição (se aplicável):</label>
                    <input type="text" id="edition" name="edition" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                </div>
            </div>

            <!-- Price Fields -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <!-- Buy Price -->
                <div>
                    <label for="buy_price" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Preço de compra:</label>
                    <input type="number" id="buy_price" name="buy_price" step="0.01" min="0" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                </div>

                <!-- Promotional Price -->
                <div>
                    <label for="promotional_price" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Preço promocional:</label>
                    <input type="number" id="promotional_price" name="promotional_price" step="0.01" min="0" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                </div>
            </div>

            <!-- Notes -->
            <div class="mb-6">
                <label for="notes" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Notas e descrição:</label>
                <textarea id="notes" name="notes" rows="3" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"></textarea>
            </div>

            <!-- Tracks Section -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Faixas: importante</h3>
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">Faixa</th>
                                <th scope="col" class="px-6 py-3">Duração</th>
                                <th scope="col" class="px-6 py-3">YouTube URL</th>
                                <th scope="col" class="px-6 py-3">Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tracks as $track)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <td class="px-6 py-4">{{ $track->name }}</td>
                                    <td class="px-6 py-4">{{ $track->duration }}</td>
                                    <td class="px-6 py-4">
                                        <input type="text" name="track_youtube_urls[{{ $track->id }}]" placeholder="YouTube URL (optional)"
                                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500 youtube-url-input">
                                    </td>
                                    <td class="px-6 py-4">
                                        <button type="button" class="text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800"
                                                @click="searchYouTube('{{ $track->name }}', $event.target.closest('tr').querySelector('.youtube-url-input'))">
                                            <svg class="w-4 h-4 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                            </svg>
                                            Buscar
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
                <button type="submit" class="text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800">
                    Completar cadastro
                </button>
            </div>
        </form>
    </div>

    <!-- YouTube Search Modal -->
    <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModal"></div>

            <div x-show="showModal" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full dark:bg-gray-800">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 dark:bg-gray-800">
                    <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white mb-4">Select YouTube Video</h3>
                    <div class="space-y-4">
                        <template x-for="result in youtubeResults" :key="result.id.videoId">
                            <div @click="selectVideo(result)" class="p-4 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600">
                                <h4 x-text="result.snippet.title" class="font-medium text-gray-900 dark:text-white"></h4>
                                <p x-text="result.snippet.description" class="text-sm text-gray-500 dark:text-gray-400"></p>
                            </div>
                        </template>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse dark:bg-gray-700">
                    <button type="button" @click="closeModal" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:bg-gray-600 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('vinylComplete', () => ({
        showModal: false,
        youtubeResults: [],
        activeInputField: null,

        async searchYouTube(trackName, inputField) {
            this.activeInputField = inputField;
            const artistName = '{{ $vinylMaster->artists->pluck('name')->join(' ') }}';
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

                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }

                const data = await response.json();
                if (data.error) {
                    throw new Error(data.error);
                }

                this.youtubeResults = data;
                this.showModal = true;
            } catch (error) {
                console.error('Erro ao pesquisar no YouTube:', error);
                alert('Ocorreu um erro ao pesquisar no YouTube. Por favor, tente novamente.');
            }
        },

        selectVideo(video) {
            if (this.activeInputField) {
                this.activeInputField.value = `https://www.youtube.com/watch?v=${video.id.videoId}`;
            }
            this.closeModal();
        },

        closeModal() {
            this.showModal = false;
            this.youtubeResults = [];
            this.activeInputField = null;
        }
    }));
});
</script>
@endpush

