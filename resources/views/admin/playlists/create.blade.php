@extends('layouts.admin')

@section('content') 
<div class="p-4">
    <div class="mb-4">
        <h1 class="text-2xl font-semibold text-gray-900 sm:text-3xl">Nova Playlist</h1>
    </div>

    <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
        <form action="{{ route('admin.playlists.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Coluna Esquerda -->
                <div class="space-y-6">
                    <!-- Nome -->
                    <div>
                        <label for="name" class="block mb-2 text-sm font-medium text-gray-900">Nome</label>
                        <input type="text" id="name" name="name"
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5" required>
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Slug -->
                    <div>
                        <label for="slug" class="block mb-2 text-sm font-medium text-gray-900">Slug</label>
                        <div class="flex items-center">
                            <input type="text" id="slug" name="slug" readonly
                                   class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
                            <button type="button" id="toggle-slug" class="ml-2 text-sm text-blue-600">
                                Editar
                            </button>
                        </div>
                        @error('slug')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Biografia -->
                    <div>
                        <label for="bio" class="block mb-2 text-sm font-medium text-gray-900">Biografia</label>
                        <textarea id="bio" name="bio" rows="4"
                                  class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500"></textarea>
                        @error('bio')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Links Sociais -->
                    <div class="space-y-4">
                        <!-- Instagram URL -->
                        <div>
                            <label for="instagram_url" class="block mb-2 text-sm font-medium text-gray-900">Instagram</label>
                            <input type="url" id="instagram_url" name="instagram_url"
                                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
                            @error('instagram_url')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <!-- YouTube URL -->
                        <div>
                            <label for="youtube_url" class="block mb-2 text-sm font-medium text-gray-900">YouTube</label>
                            <input type="url" id="youtube_url" name="youtube_url"
                                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
                            @error('youtube_url')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <!-- Facebook URL -->
                        <div>
                            <label for="facebook_url" class="block mb-2 text-sm font-medium text-gray-900">Facebook</label>
                            <input type="url" id="facebook_url" name="facebook_url"
                                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
                            @error('facebook_url')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <!-- SoundCloud URL -->
                        <div>
                            <label for="soundcloud_url" class="block mb-2 text-sm font-medium text-gray-900">SoundCloud</label>
                            <input type="url" id="soundcloud_url" name="soundcloud_url"
                                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
                            @error('soundcloud_url')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Status Ativo -->
                    <div class="flex items-center">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="is_active" name="is_active" value="1" checked class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600"></div>
                            <span class="ml-3 text-sm font-medium text-gray-900">Ativa</span>
                        </label>
                    </div>
                </div>

                <!-- Coluna Direita -->
                <div class="space-y-6">
                    <!-- Upload de Imagem -->
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900" for="image">Imagem da Playlist</label>
                        <div class="flex items-center justify-center w-full">
                            <label for="image" class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                                <div id="image-preview-container" class="w-full h-full relative">
                                    <img id="image-preview" src="" class="w-full h-full object-cover rounded-lg" alt="Preview" style="display:none;">
                                    <div id="upload-placeholder" class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-8 h-8 mb-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                                        </svg>
                                        <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Clique para fazer upload</span></p>
                                        <p class="text-xs text-gray-500">PNG, JPG ou GIF (máx. 2MB)</p>
                                    </div>
                                </div>
                                <input type="file" id="image" name="image" class="hidden" accept="image/*">
                            </label>
                        </div>
                        @error('image')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Seleção de Discos -->
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900">Selecione os Discos (máximo 10)</label>

                        <!-- Contador de discos selecionados -->
                        <div class="mb-3 flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700" id="vinyl-counter">0 de 10 discos selecionados</span>
                            <span class="text-xs text-blue-600 cursor-pointer hover:underline" id="clear-all-vinyls">Limpar todos</span>
                        </div>

                        <!-- Container dos discos selecionados -->
                        <div id="selected-vinyls-grid" class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-4">
                            <!-- Os discos serão exibidos aqui -->
                        </div>

                        <!-- Campo de busca com ícone -->
                        <div class="mt-4 relative">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                                    </svg>
                                </div>
                                <input type="text" id="vinyl-search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 p-2.5" placeholder="Buscar discos por título ou artista...">
                                <div id="search-spinner" class="absolute inset-y-0 right-0 flex items-center pr-3 hidden">
                                    <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-primary-600"></div>
                                </div>
                            </div>
                            <div id="search-results" class="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg hidden">
                                <!-- Resultados da busca serão exibidos aqui -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botões -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('admin.playlists.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    Cancelar
                </a>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    Criar Playlist
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');
    const toggleSlugButton = document.getElementById('toggle-slug');
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('image-preview');
    const uploadPlaceholder = document.getElementById('upload-placeholder');
    const vinylSearchInput = document.getElementById('vinyl-search');
    const searchResults = document.getElementById('search-results');
    const searchSpinner = document.getElementById('search-spinner');
    const selectedVinylsGrid = document.getElementById('selected-vinyls-grid');
    const vinylCounter = document.getElementById('vinyl-counter');
    const clearAllVinylsButton = document.getElementById('clear-all-vinyls');
    const selectedVinyls = new Set();

    // Gera o slug automaticamente
    nameInput.addEventListener('input', function() {
        if (slugInput.readOnly) {
            slugInput.value = this.value
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/(^-|-$)+/g, '');
        }
    });

    // Toggle edição do slug
    toggleSlugButton.addEventListener('click', function() {
        slugInput.readOnly = !slugInput.readOnly;
        slugInput.classList.toggle('bg-gray-100');
        this.textContent = slugInput.readOnly ? 'Editar' : 'Bloquear';
    });

    // Preview da imagem
    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.src = e.target.result;
                imagePreview.style.display = 'block';
                uploadPlaceholder.style.display = 'none';
            }
            reader.readAsDataURL(file);
        } else {
            imagePreview.style.display = 'none';
            uploadPlaceholder.style.display = 'flex';
        }
    });

    // Atualiza o contador de discos
    function updateVinylCounter() {
        vinylCounter.textContent = `${selectedVinyls.size} de 10 discos selecionados`;
    }

    // Limpa todos os discos selecionados
    clearAllVinylsButton.addEventListener('click', function() {
        selectedVinyls.clear();
        selectedVinylsGrid.innerHTML = '';
        updateVinylCounter();
    });

    // Renderiza um disco selecionado
    function renderSelectedVinyl(vinyl) {
        const div = document.createElement('div');
        div.className = 'flex items-center p-2 bg-gray-50 rounded-lg';
        div.innerHTML = `
            <input type="hidden" name="vinyls[]" value="${vinyl.id}">
            <img src="${vinyl.capa}" alt="${vinyl.titulo}" class="w-12 h-12 object-cover rounded">
            <div class="ml-3 flex-1">
                <p class="text-sm font-medium text-gray-900 truncate">${vinyl.titulo}</p>
                <p class="text-xs text-gray-500 truncate">${vinyl.artista}</p>
            </div>
            <button type="button" class="ml-2 text-gray-400 hover:text-gray-500" onclick="this.parentElement.remove(); selectedVinyls.delete(${vinyl.id}); updateVinylCounter();">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        `;
        return div;
    }

    // Renderiza um resultado da busca
    function renderSearchResult(vinyl) {
        const div = document.createElement('div');
        div.className = 'flex items-center p-2 hover:bg-gray-50 cursor-pointer';
        div.innerHTML = `
            <img src="${vinyl.capa}" alt="${vinyl.titulo}" class="w-10 h-10 object-cover rounded">
            <div class="ml-3">
                <p class="text-sm font-medium text-gray-900">${vinyl.titulo}</p>
                <p class="text-xs text-gray-500">${vinyl.artista}</p>
            </div>
        `;
        div.addEventListener('click', function() {
            if (selectedVinyls.size >= 10) {
                alert('Você já selecionou o número máximo de discos (10)');
                return;
            }
            if (!selectedVinyls.has(vinyl.id)) {
                selectedVinyls.add(vinyl.id);
                selectedVinylsGrid.appendChild(renderSelectedVinyl(vinyl));
                updateVinylCounter();
            }
            searchResults.classList.add('hidden');
            vinylSearchInput.value = '';
        });
        return div;
    }

    // Busca de discos
    let searchTimeout;
    vinylSearchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();
        
        if (query.length < 2) {
            searchResults.classList.add('hidden');
            return;
        }

        searchTimeout = setTimeout(() => {
            searchSpinner.classList.remove('hidden');
            searchResults.classList.remove('hidden');
            searchResults.innerHTML = '<div class="p-4 text-sm text-gray-500">Buscando...</div>';

            fetch(`/admin/playlists/search-vinyls?query=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    searchResults.innerHTML = '';
                    if (data.length === 0) {
                        searchResults.innerHTML = '<div class="p-4 text-sm text-gray-500">Nenhum disco encontrado</div>';
                    } else {
                        data.forEach(vinyl => {
                            searchResults.appendChild(renderSearchResult(vinyl));
                        });
                    }
                })
                .catch(error => {
                    console.error('Erro na busca:', error);
                    searchResults.innerHTML = '<div class="p-4 text-sm text-red-500">Erro ao buscar discos</div>';
                })
                .finally(() => {
                    searchSpinner.classList.add('hidden');
                });
        }, 300);
    });

    // Fecha resultados ao clicar fora
    document.addEventListener('click', function(e) {
        if (!searchResults.contains(e.target) && e.target !== vinylSearchInput) {
            searchResults.classList.add('hidden');
        }
    });
});
</script>
@endpush
