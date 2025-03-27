@extends('layouts.admin')

@section('title', 'Complete Vinyl Record')

@section('content')
<div class="container mx-auto px-4 py-8" x-data="vinylCompleteForm">
    <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm 2xl:col-span-2">
        <h2 class="text-2xl font-bold mb-4 text-gray-900">
            Completar cadastro de: {{ $vinylMaster->artists->pluck('name')->join(', ') }} - {{ $vinylMaster->title }}
        </h2>

        <form action="{{ route('admin.vinyl.storeComplete', $vinylMaster->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if ($errors->any())
                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50">
                    <ul class="list-disc pl-4">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Required Fields Alert -->
            <div class="flex p-4 mb-4 text-red-800 border-t-4 border-red-300 bg-red-50" role="alert">
                <svg class="flex-shrink-0 w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                <div class="ml-3 text-sm font-medium">
                    <h3 class="font-bold">Campos obrigatórios</h3>
                    <p>Complete todos esses campos, é importante:</p>
                    <a href="{{ $vinylMaster->discogs_url }}" target='_blank' class="inline-flex items-center px-4 py-2 mt-2 text-sm font-medium text-white bg-red-700 rounded-lg hover:bg-red-800 focus:ring-4 focus:ring-red-300 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">
                        Link do disco no Discogs
                    </a>
                </div>
            </div>

            <!-- Principal information fields -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <!-- Weight -->
                <div>
                    <label for="weight_id" class="block mb-2 text-sm font-medium text-gray-900">Peso:</label>
                    <select id="weight_id" name="weight_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5" required>
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
                    <label for="dimension_id" class="block mb-2 text-sm font-medium text-gray-900">Dimensões:</label>
                    <select id="dimension_id" name="dimension_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5" required>
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
                    <label for="quantity" class="block mb-2 text-sm font-medium text-gray-900">Estoque:</label>
                    <input type="number" id="quantity" name="quantity" min="0" value="1" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5" required>
                </div>

                <!-- Price -->
                <div>
                    <label for="price" class="block mb-2 text-sm font-medium text-gray-900">Preço de Venda:</label>
                    <input type="number" id="price" name="price" step="0.01" min="0" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5" required>
                </div>
            </div>

            <!-- Product Status Card with slate-300 background -->
            <div class="bg-slate-300 p-4 rounded-lg mb-6">
                <h3 class="text-lg font-semibold mb-3 text-gray-900">Status do Produto</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- New/Used -->
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900">Produto novo?</label>
                        <div class="flex gap-4">
                            <div class="flex items-center">
                                <input type="radio" name="is_new" value="1" class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 focus:ring-primary-500 focus:ring-2" checked>
                                <label class="ml-2 text-sm font-medium text-gray-900">Sim</label>
                            </div>
                            <div class="flex items-center">
                                <input type="radio" name="is_new" value="0" class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 focus:ring-primary-500 focus:ring-2">
                                <label class="ml-2 text-sm font-medium text-gray-900">Não</label>
                            </div>
                        </div>
                    </div>

                    <!-- Promotional -->
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900">Em promoção?</label>
                        <div class="flex gap-4">
                            <div class="flex items-center">
                                <input type="radio" name="is_promotional" value="1" class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 focus:ring-primary-500 focus:ring-2">
                                <label class="ml-2 text-sm font-medium text-gray-900">SIM</label>
                            </div>
                            <div class="flex items-center">
                                <input type="radio" name="is_promotional" value="0" class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 focus:ring-primary-500 focus:ring-2" checked>
                                <label class="ml-2 text-sm font-medium text-gray-900">NÃO</label>
                            </div>
                        </div>
                    </div>

                    <!-- Stock Status -->
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900">Em Estoque</label>
                        <div class="flex gap-4">
                            <div class="flex items-center">
                                <input type="radio" name="in_stock" value="1" class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 focus:ring-primary-500 focus:ring-2" checked>
                                <label class="ml-2 text-sm font-medium text-gray-900">Com estoque</label>
                            </div>
                            <div class="flex items-center">
                                <input type="radio" name="in_stock" value="0" class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 focus:ring-primary-500 focus:ring-2">
                                <label class="ml-2 text-sm font-medium text-gray-900">Sem estoque</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Categories as a single row -->
            <div class="mb-6">
                <div class="flex justify-between items-center mb-2">
                    <label class="text-sm font-medium text-gray-900">Categorias de Estilo da Loja</label>
                    <button type="button" @click="showCategoryModal = true" class="text-sm font-medium text-blue-600 hover:text-blue-800 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Adicionar nova categoria
                    </button>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-4 p-4 border border-gray-200 rounded-lg">
                    @foreach($categories as $category)
                        <div class="flex items-center">
                            <input type="checkbox" name="category_ids[]" value="{{ $category->id }}"
                                {{ in_array($category->id, old('category_ids', $selectedCategories ?? [])) ? 'checked' : '' }}
                                class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500">
                            <label class="ml-2 text-sm font-medium text-gray-900">{{ $category->nome }}</label>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Optional Fields Alert -->
            <div class="flex p-4 mb-4 text-green-800 border-t-4 border-green-300 bg-green-50" role="alert">
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
                    <label for="cover_status" class="block mb-2 text-sm font-medium text-gray-900">Estado da capa:</label>
                    <select id="cover_status" name="cover_status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
                        <option value="">Selecione estado da capa</option>
                        @foreach(['mint', 'near_mint', 'very_good', 'good', 'fair', 'poor'] as $status)
                            <option value="{{ $status }}">{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Media Status -->
                <div>
                    <label for="midia_status" class="block mb-2 text-sm font-medium text-gray-900">Estado da mídia:</label>
                    <select id="midia_status" name="midia_status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
                        <option value="">Selecionar estado da midia</option>
                        @foreach(['mint', 'near_mint', 'very_good', 'good', 'fair', 'poor'] as $status)
                            <option value="{{ $status }}">{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Catalog Number -->
                <div>
                    <label for="catalog_number" class="block mb-2 text-sm font-medium text-gray-900">Numero de catálogo:</label>
                    <input type="text" id="catalog_number" name="catalog_number" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
                </div>

                <!-- Barcode -->
                <div>
                    <label for="barcode" class="block mb-2 text-sm font-medium text-gray-900">Barcode:</label>
                    <input type="text" id="barcode" name="barcode" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
                </div>
            </div>

            <!-- Additional Optional Fields -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <!-- Format -->
                <div>
                    <label for="format" class="block mb-2 text-sm font-medium text-gray-900">Formato:</label>
                    <input type="text" id="format" name="format" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
                </div>

                <!-- Number of Discs -->
                <div>
                    <label for="num_discs" class="block mb-2 text-sm font-medium text-gray-900">Numero de discos:</label>
                    <input type="number" id="num_discs" name="num_discs" min="1" value="1" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5" required>
                </div>

                <!-- Speed -->
                <div>
                    <label for="speed" class="block mb-2 text-sm font-medium text-gray-900">Velocidade:</label>
                    <input type="text" id="speed" name="speed" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
                </div>

                <!-- Edition -->
                <div>
                    <label for="edition" class="block mb-2 text-sm font-medium text-gray-900">Edição (se aplicável):</label>
                    <input type="text" id="edition" name="edition" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
                </div>
            </div>

            <!-- Price Fields -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <!-- Buy Price -->
                <div>
                    <label for="buy_price" class="block mb-2 text-sm font-medium text-gray-900">Preço de compra:</label>
                    <input type="number" id="buy_price" name="buy_price" step="0.01" min="0" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
                </div>

                <!-- Promotional Price -->
                <div>
                    <label for="promotional_price" class="block mb-2 text-sm font-medium text-gray-900">Preço promocional:</label>
                    <input type="number" id="promotional_price" name="promotional_price" step="0.01" min="0" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
                </div>

                <!-- Supplier/Origin -->
                <div>
                    <label for="supplier" class="block mb-2 text-sm font-medium text-gray-900">Fornecedor/Origem:</label>
                    <input type="text" id="supplier" name="supplier" value="{{ old('supplier', $supplier ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5" placeholder="Nome do fornecedor ou origem">
                </div>
            </div>

            <!-- Notes -->
            <div class="mb-6">
                <label for="notes" class="block mb-2 text-sm font-medium text-gray-900">Notas e descrição:</label>
                <textarea id="notes" name="notes" rows="3" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500"></textarea>
            </div>

            <!-- Tracks Section -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-900">Faixas: importante</h3>
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3">Faixa</th>
                                <th scope="col" class="px-6 py-3">Duração</th>
                                <th scope="col" class="px-6 py-3">YouTube URL</th>
                                <th scope="col" class="px-6 py-3">Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tracks as $track)
                                <tr class="bg-white border-b">
                                    <td class="px-6 py-4">{{ $track->name }}</td>
                                    <td class="px-6 py-4">{{ $track->duration }}</td>
                                    <td class="px-6 py-4">
                                        <input type="url" name="track_youtube_urls[{{ $track->id }}]" value="{{ $track->youtube_url }}" 
                                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 youtube-url-input">
                                    </td>
                                    <td class="px-6 py-4">
                                        <button type="button" class="text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-4 py-2"
                                                @click="searchYouTube('{{ $track->name }}', $event.target.closest('tr').querySelector('.youtube-url-input'))">
                                            <svg class="w-4 h-4 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
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
                <button type="submit" class="text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5">
                    Completar cadastro
                </button>
            </div>
        </form>
    </div>

    <!-- YouTube Search Modal -->
    <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModal"></div>

            <div x-show="showModal" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Select YouTube Video</h3>
                    <div class="space-y-4">
                        <template x-for="result in youtubeResults" :key="result.id.videoId">
                            <div @click="selectVideo(result)" class="p-4 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100">
                                <h4 x-text="result.snippet.title" class="font-medium text-gray-900"></h4>
                                <p x-text="result.snippet.description" class="text-sm text-gray-500"></p>
                            </div>
                        </template>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" @click="closeModal" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Category Add Modal -->
    <div x-show="showCategoryModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showCategoryModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showCategoryModal = false"></div>

            <div x-show="showCategoryModal" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('admin.cat-style-shop.store') }}" method="POST" id="addCategoryForm">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Nova Categoria Interna</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label for="category_name" class="block mb-2 text-sm font-medium text-gray-900">Nome da Categoria:</label>
                                <input type="text" id="category_name" name="nome" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
                            </div>
                            
                            <div>
                                <label for="category_slug" class="block mb-2 text-sm font-medium text-gray-900">Slug (opcional):</label>
                                <input type="text" id="category_slug" name="slug" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
                                <p class="mt-1 text-sm text-gray-500">Deixe em branco para gerar automaticamente a partir do nome</p>
                            </div>
                            
                            <div>
                                <label for="parent_id" class="block mb-2 text-sm font-medium text-gray-900">Categoria Pai (opcional):</label>
                                <select id="parent_id" name="parent_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
                                    <option value="">Nenhuma (categoria principal)</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" id="submitCategoryBtn" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-primary-600 text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Adicionar Categoria
                        </button>
                        <button type="button" @click="showCategoryModal = false" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('vinylCompleteForm', () => ({
        showModal: false,
        youtubeResults: [],
        activeInputField: null,
        showCategoryModal: false,

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

// JavaScript para o modal de categoria
document.addEventListener('DOMContentLoaded', function() {
    const submitCategoryBtn = document.getElementById('submitCategoryBtn');
    const addCategoryForm = document.getElementById('addCategoryForm');
    
    if (submitCategoryBtn && addCategoryForm) {
        submitCategoryBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            const formData = new FormData(addCategoryForm);
            const categoryName = formData.get('nome');
            
            if (!categoryName || categoryName.trim() === '') {
                alert('Por favor, informe o nome da categoria');
                return;
            }
            
            // Mostrar indicador de carregamento
            submitCategoryBtn.innerHTML = '<svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Processando...';
            submitCategoryBtn.disabled = true;
            
            fetch(addCategoryForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                // Restaurar botão
                submitCategoryBtn.innerHTML = 'Adicionar Categoria';
                submitCategoryBtn.disabled = false;
                
                if (data.success) {
                    // Adiciona a nova categoria à lista de categorias
                    const categoriesContainer = document.querySelector('.grid.grid-cols-2.gap-4');
                    
                    if (categoriesContainer) {
                        const newCategoryDiv = document.createElement('div');
                        newCategoryDiv.className = 'flex items-center';
                        newCategoryDiv.innerHTML = `
                            <input type="checkbox" name="category_ids[]" value="${data.category.id}"
                                class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500">
                            <label class="ml-2 text-sm font-medium text-gray-900">${data.category.nome}</label>
                        `;
                        
                        categoriesContainer.appendChild(newCategoryDiv);
                        
                        // Adiciona a categoria ao select do modal também
                        const parentIdSelect = document.getElementById('parent_id');
                        if (parentIdSelect) {
                            const option = document.createElement('option');
                            option.value = data.category.id;
                            option.textContent = data.category.nome;
                            parentIdSelect.appendChild(option);
                        }
                        
                        // Limpa o formulário
                        addCategoryForm.reset();
                        
                        // Fecha o modal usando Alpine
                        const alpineRoot = document.querySelector('[x-data]');
                        if (alpineRoot && alpineRoot.__x) {
                            alpineRoot.__x.$data.showCategoryModal = false;
                        }
                        
                        // Mostra mensagem de sucesso
                        if (typeof Toastify === 'function') {
                            Toastify({
                                text: "Categoria adicionada com sucesso!",
                                duration: 3000,
                                gravity: "top",
                                position: 'right',
                                backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
                                stopOnFocus: true
                            }).showToast();
                        } else {
                            alert("Categoria adicionada com sucesso!");
                        }
                    }
                } else {
                    // Mostra mensagem de erro
                    if (typeof Toastify === 'function') {
                        Toastify({
                            text: data.message || "Erro ao adicionar categoria",
                            duration: 3000,
                            gravity: "top",
                            position: 'right',
                            backgroundColor: "linear-gradient(to right, #ff5f6d, #ffc371)",
                            stopOnFocus: true
                        }).showToast();
                    } else {
                        alert(data.message || "Erro ao adicionar categoria");
                    }
                }
            })
            .catch(error => {
                // Restaurar botão
                submitCategoryBtn.innerHTML = 'Adicionar Categoria';
                submitCategoryBtn.disabled = false;
                
                console.error('Erro:', error);
                if (typeof Toastify === 'function') {
                    Toastify({
                        text: "Erro ao processar a solicitação",
                        duration: 3000,
                        gravity: "top",
                        position: 'right',
                        backgroundColor: "linear-gradient(to right, #ff5f6d, #ffc371)",
                        stopOnFocus: true
                    }).showToast();
                } else {
                    alert("Erro ao processar a solicitação");
                }
            });
        });
    }
});
</script>
@endpush
