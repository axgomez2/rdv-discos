@extends('layouts.admin')

@section('content')
<div class="p-4">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-semibold text-gray-900 sm:text-3xl">Editar Disco: {{ $vinyl->title }}</h1>
        <a href="{{ route('admin.vinyls.index') }}" class="text-white bg-gray-700 hover:bg-gray-800 focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5">
            Voltar para inicio
        </a>
    </div>

    <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
        <!-- Image Component -->
        <div x-data="{ showDiscogsModal: false, showUploadModal: false }">
            <div class="mb-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-900">Imagem do Disco</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <!-- Current Image -->
                        <div class="mb-4">
                            @if($vinyl->cover_image)
                                <img src="{{ Storage::url($vinyl->cover_image) }}" alt="{{ $vinyl->title }}" class="w-48 h-48 object-cover rounded-lg">
                            @else
                                <div class="w-48 h-48 bg-gray-200 flex items-center justify-center rounded-lg">
                                    <span class="text-gray-500">Sem imagem</span>
                                </div>
                            @endif
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-wrap gap-2">
                            <button @click="showDiscogsModal = true" class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5">
                                Buscar Imagem do Discogs
                            </button>
                            <button @click="showUploadModal = true" class="text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5">
                                Upload Manual
                            </button>
                            @if($vinyl->cover_image)
                                <form action="{{ route('admin.vinyls.remove-image', $vinyl->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja remover esta imagem?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5">
                                        Remover Imagem
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Discogs Modal -->
            <div x-show="showDiscogsModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showDiscogsModal = false"></div>
                    <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">Buscar Imagem do Discogs</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">Deseja buscar a imagem do Discogs para este disco?</p>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <form action="{{ route('admin.vinyls.fetch-discogs-image', $vinyl->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-primary-600 text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:ml-3 sm:w-auto sm:text-sm">
                                    Buscar Imagem
                                </button>
                            </form>
                            <button @click="showDiscogsModal = false" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Cancelar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upload Modal -->
            <div x-show="showUploadModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showUploadModal = false"></div>
                    <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                        <form action="{{ route('admin.vinyls.upload-image', $vinyl->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Upload Manual de Imagem</h3>
                                <div class="mb-4">
                                    <label class="block mb-2 text-sm font-medium text-gray-900" for="image">
                                        Selecione uma imagem
                                    </label>
                                    <input type="file" name="image" id="image" accept="image/*" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50" required>
                                </div>
                            </div>
                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-primary-600 text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:ml-3 sm:w-auto sm:text-sm">
                                    Upload
                                </button>
                                <button type="button" @click="showUploadModal = false" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                    Cancelar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Form -->
        <form action="{{ route('admin.vinyls.update', $vinyl->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Description -->
                <div>
                    <label for="description" class="block mb-2 text-sm font-medium text-gray-900">Descrição</label>
                    <textarea id="description" name="description" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500">{{ old('description', $vinyl->description) }}</textarea>
                </div>

                <!-- Weight -->
                <div>
                    <label for="weight_id" class="block mb-2 text-sm font-medium text-gray-900">Peso</label>
                    <select id="weight_id" name="weight_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5" required>
                        @foreach($weights as $weight)
                            <option value="{{ $weight->id }}" {{ (old('weight_id', $vinyl->vinylSec->weight_id ?? '') == $weight->id) ? 'selected' : '' }}>
                                {{ $weight->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Categories -->
                <div class="md:col-span-2">
                    <label class="block mb-2 text-sm font-medium text-gray-900">Categorias de Estilo da Loja</label>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        @foreach($categories as $category)
                            <div class="flex items-center">
                                <input type="checkbox" name="category_ids[]" value="{{ $category->id }}"
                                       class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500"
                                       {{ in_array($category->id, old('category_ids', $vinyl->catStyleShops->pluck('id')->toArray())) ? 'checked' : '' }}>
                                <label class="ml-2 text-sm font-medium text-gray-900">{{ $category->nome }}</label>
                            </div>
                        @endforeach
                    </div>
                    @error('category_ids')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Dimension -->
                <div>
                    <label for="dimension_id" class="block mb-2 text-sm font-medium text-gray-900">Dimensão</label>
                    <select id="dimension_id" name="dimension_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5" required>
                        @foreach($dimensions as $dimension)
                            <option value="{{ $dimension->id }}" {{ (old('dimension_id', $vinyl->vinylSec->dimension_id ?? '') == $dimension->id) ? 'selected' : '' }}>
                                {{ $dimension->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Quantity -->
                <div>
                    <label for="quantity" class="block mb-2 text-sm font-medium text-gray-900">Quantidade</label>
                    <input type="number" id="quantity" name="quantity" value="{{ old('quantity', $vinyl->vinylSec->quantity ?? 0) }}"
                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5" required min="0">
                </div>

                <!-- Price -->
                <div>
                    <label for="price" class="block mb-2 text-sm font-medium text-gray-900">Preço</label>
                    <input type="number" id="price" name="price" value="{{ old('price', $vinyl->vinylSec->price ?? 0) }}"
                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5" required min="0" step="0.01">
                </div>

                <!-- Buy Price -->
                <div>
                    <label for="buy_price" class="block mb-2 text-sm font-medium text-gray-900">Preço de Compra</label>
                    <input type="number" id="buy_price" name="buy_price" value="{{ old('buy_price', $vinyl->vinylSec->buy_price ?? '') }}"
                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5" min="0" step="0.01">
                </div>

                <!-- Promotional Price -->
                <div>
                    <label for="promotional_price" class="block mb-2 text-sm font-medium text-gray-900">Preço Promocional</label>
                    <input type="number" id="promotional_price" name="promotional_price" value="{{ old('promotional_price', $vinyl->vinylSec->promotional_price ?? '') }}"
                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5" min="0" step="0.01">
                </div>

                <!-- Promotional Status -->
                <div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_promotional" class="sr-only peer" {{ (old('is_promotional', $vinyl->vinylSec->is_promotional ?? false)) ? 'checked' : '' }} value="1">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600"></div>
                        <span class="ml-3 text-sm font-medium text-gray-900">Em Promoção</span>
                    </label>
                </div>

                <!-- Stock Status -->
                <div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="in_stock" class="sr-only peer" {{ (old('in_stock', $vinyl->vinylSec->in_stock ?? false)) ? 'checked' : '' }} value="1">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600"></div>
                        <span class="ml-3 text-sm font-medium text-gray-900">Em Estoque</span>
                    </label>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
                <button type="submit" class="text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5">
                    Atualizar Disco
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
