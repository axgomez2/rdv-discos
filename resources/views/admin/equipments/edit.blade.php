@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center mb-6">
        <a href="{{ route('admin.equipments.index') }}" class="mr-4 text-gray-600 hover:text-gray-900">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-800">Editar Equipamento</h1>
    </div>

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('admin.equipments.update', $equipment->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nome <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required value="{{ old('name', $equipment->name) }}">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="brand_id" class="block text-sm font-medium text-gray-700 mb-1">Marca <span class="text-red-500">*</span></label>
                    <select name="brand_id" id="brand_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
                        <option value="">Selecione uma marca</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" {{ (old('brand_id', $equipment->brand_id) == $brand->id) ? 'selected' : '' }}>{{ $brand->name }}</option>
                        @endforeach
                    </select>
                    @error('brand_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Categoria <span class="text-red-500">*</span></label>
                    <select name="equipment_category_id" id="category_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
                        <option value="">Selecione uma categoria</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ (old('equipment_category_id', $equipment->equipment_category_id) == $category->id) ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('equipment_category_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Preço (R$) <span class="text-red-500">*</span></label>
                    <input type="number" name="price" id="price" step="0.01" min="0" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required value="{{ old('price', $equipment->price) }}">
                    @error('price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="buy_price" class="block text-sm font-medium text-gray-700 mb-1">Preço de Compra (R$)</label>
                    <input type="number" name="buy_price" id="buy_price" step="0.01" min="0" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" value="{{ old('buy_price', $equipment->buy_price) }}">
                    @error('buy_price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="promotional_price" class="block text-sm font-medium text-gray-700 mb-1">Preço Promocional (R$)</label>
                    <input type="number" name="promotional_price" id="promotional_price" step="0.01" min="0" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" value="{{ old('promotional_price', $equipment->promotional_price) }}">
                    @error('promotional_price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">Quantidade <span class="text-red-500">*</span></label>
                    <input type="number" name="quantity" id="quantity" min="0" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required value="{{ old('quantity', $equipment->quantity) }}">
                    @error('quantity')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="sku" class="block text-sm font-medium text-gray-700 mb-1">SKU <span class="text-red-500">*</span></label>
                    <input type="text" name="sku" id="sku" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required value="{{ old('sku', $equipment->sku) }}">
                    @error('sku')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="weight_id" class="block text-sm font-medium text-gray-700 mb-1">Peso</label>
                    <select name="weight_id" id="weight_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                        <option value="">Selecione um peso</option>
                        @foreach($weights as $weight)
                            <option value="{{ $weight->id }}" {{ (old('weight_id', $equipment->weight_id) == $weight->id) ? 'selected' : '' }}>{{ $weight->value }} {{ $weight->unit }}</option>
                        @endforeach
                    </select>
                    @error('weight_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="dimension_id" class="block text-sm font-medium text-gray-700 mb-1">Dimensão</label>
                    <select name="dimension_id" id="dimension_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                        <option value="">Selecione uma dimensão</option>
                        @foreach($dimensions as $dimension)
                            <option value="{{ $dimension->id }}" {{ (old('dimension_id', $equipment->dimension_id) == $dimension->id) ? 'selected' : '' }}>{{ $dimension->width }}x{{ $dimension->height }}x{{ $dimension->depth }} {{ $dimension->unit }}</option>
                        @endforeach
                    </select>
                    @error('dimension_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex space-x-4">
                    <div class="flex items-center">
                        <input type="checkbox" name="is_new" id="is_new" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" value="1" {{ old('is_new', $equipment->is_new) ? 'checked' : '' }}>
                        <label for="is_new" class="ml-2 block text-sm text-gray-700">Novo</label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" name="is_promotional" id="is_promotional" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" value="1" {{ old('is_promotional', $equipment->is_promotional) ? 'checked' : '' }}>
                        <label for="is_promotional" class="ml-2 block text-sm text-gray-700">Promocional</label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" name="in_stock" id="in_stock" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" value="1" {{ old('in_stock', $equipment->in_stock) ? 'checked' : '' }}>
                        <label for="in_stock" class="ml-2 block text-sm text-gray-700">Em estoque</label>
                    </div>
                </div>

                <div class="col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
                    <div class="flex mb-2">
                        <button type="button" id="generateDescription" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            Gerar descrição com IA
                        </button>
                    </div>
                    <textarea name="description" id="description" rows="6" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">{{ old('description', $equipment->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="col-span-2">
                    <label for="specifications" class="block text-sm font-medium text-gray-700 mb-1">Especificações</label>
                    <textarea name="specifications" id="specifications" rows="4" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">{{ old('specifications', is_array($equipment->specifications) ? implode("\n", array_map(function($v, $k) { return "$k: $v"; }, $equipment->specifications, array_keys($equipment->specifications))) : $equipment->specifications) }}</textarea>
                    <p class="mt-1 text-xs text-gray-500">Insira as especificações no formato: "Chave: Valor" (uma por linha)</p>
                    @error('specifications')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Imagens Atuais</label>
                    @if($equipment->media->count() > 0)
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 my-3">
                            @foreach($equipment->media as $media)
                                <div class="relative bg-gray-100 rounded-lg overflow-hidden">
                                    <img src="{{ asset($media->file_path) }}" alt="{{ $media->alt_text }}" class="h-32 w-full object-cover">
                                    <div class="absolute inset-0 bg-black bg-opacity-0 hover:bg-opacity-30 transition-opacity flex items-center justify-center opacity-0 hover:opacity-100">
                                        <button type="button" class="delete-media p-1.5 bg-red-500 text-white rounded-full" data-id="{{ $media->id }}">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500 my-2">Nenhuma imagem cadastrada.</p>
                    @endif

                    <label class="block text-sm font-medium text-gray-700 mt-4 mb-1">Adicionar Novas Imagens</label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4h-12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="images" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none">
                                    <span>Arraste imagens ou clique para selecionar</span>
                                    <input id="images" name="images[]" type="file" class="sr-only" multiple accept="image/*">
                                </label>
                            </div>
                            <p class="text-xs text-gray-500">PNG, JPG, GIF até 10MB</p>
                        </div>
                    </div>
                    <div id="image-preview" class="grid grid-cols-4 gap-4 mt-4"></div>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <a href="{{ route('admin.equipments.index') }}" class="bg-gray-200 text-gray-700 py-2 px-4 rounded-lg mr-2 hover:bg-gray-300 transition-colors">Cancelar</a>
                <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors">Atualizar Equipamento</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Imagem preview
        const inputImages = document.getElementById('images');
        const previewContainer = document.getElementById('image-preview');

        inputImages.addEventListener('change', function() {
            previewContainer.innerHTML = '';

            if (this.files) {
                Array.from(this.files).forEach(file => {
                    if (!file.type.match('image.*')) return;

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const div = document.createElement('div');
                        div.className = 'relative';

                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'h-24 w-full object-cover rounded';
                        div.appendChild(img);

                        previewContainer.appendChild(div);
                    }
                    reader.readAsDataURL(file);
                });
            }
        });

        // Deletar imagem
        const deleteButtons = document.querySelectorAll('.delete-media');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                if (confirm('Tem certeza que deseja excluir esta imagem?')) {
                    const mediaId = this.getAttribute('data-id');
                    const mediaContainer = this.closest('.relative');

                    // Fazer a requisição AJAX para deletar a imagem
                    fetch(`{{ url('admin/equipamentos/media') }}/${mediaId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Remover o elemento da página se a exclusão foi bem-sucedida
                            mediaContainer.remove();

                            // Exibir mensagem de sucesso
                            const successMessage = document.createElement('div');
                            successMessage.className = 'bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4';
                            successMessage.innerHTML = '<p>Imagem excluída com sucesso!</p>';

                            const container = document.querySelector('.container');
                            container.insertBefore(successMessage, container.firstChild);

                            // Remover a mensagem após 3 segundos
                            setTimeout(() => {
                                successMessage.remove();
                            }, 3000);
                        } else {
                            // Exibir mensagem de erro
                            alert('Erro ao excluir imagem: ' + (data.message || 'Erro desconhecido'));
                        }
                    })
                    .catch(error => {
                        console.error('Erro:', error);
                        alert('Erro ao excluir imagem. Verifique o console para mais detalhes.');
                    });
                }
            });
        });

        // Botão para gerar descrição com IA
        const generateButton = document.getElementById('generateDescription');
        const nameInput = document.getElementById('name');
        const categorySelect = document.getElementById('category_id');
        const brandSelect = document.getElementById('brand_id');
        const descriptionTextarea = document.getElementById('description');

        generateButton.addEventListener('click', function() {
            const name = nameInput.value;
            const categoryId = categorySelect.value;
            const categoryName = categorySelect.options[categorySelect.selectedIndex]?.text || '';
            const brandName = brandSelect.options[brandSelect.selectedIndex]?.text || '';

            if (!name) {
                alert('Por favor, insira o nome do equipamento primeiro.');
                return;
            }

            // Indicar carregamento
            this.disabled = true;
            this.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Gerando...';

            // Requisição para o endpoint de geração de descrição
            fetch('{{ route('admin.equipments.generateDescription') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    name: name,
                    brand: brandName,
                    category: categoryName
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    descriptionTextarea.value = data.description;
                } else {
                    alert('Erro ao gerar descrição: ' + (data.message || 'Erro desconhecido'));
                }

                // Restaurar botão
                this.disabled = false;
                this.innerHTML = '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg> Gerar descrição com IA';
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao gerar descrição. Verifique o console para mais detalhes.');

                // Restaurar botão
                this.disabled = false;
                this.innerHTML = '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg> Gerar descrição com IA';
            });
        });

        // Formatar especificações JSON ao carregar a página
        const specificationsArea = document.getElementById('specifications');
        try {
            const specs = JSON.parse(specificationsArea.value);
            if (specs && typeof specs === 'object') {
                let formattedSpecs = '';
                for (const [key, value] of Object.entries(specs)) {
                    formattedSpecs += `${key}: ${value}\n`;
                }
                specificationsArea.value = formattedSpecs;
            }
        } catch (e) {
            // Se não for um JSON válido, manter o valor original
            console.log('Especificações não estão em formato JSON');
        }
    });
</script>
@endpush
@endsection
