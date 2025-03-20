@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-3xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Editar Pacote de Assinatura</h1>
            <a href="{{ route('admin.subscription-packages.index') }}"
               class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                <i class="fas fa-arrow-left mr-2"></i>Voltar
            </a>
        </div>
        
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white shadow-md rounded-lg p-6">
            <form action="{{ route('admin.subscription-packages.update', $package) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Nome</label>
                        <input type="text"
                               name="name"
                               id="name"
                               value="{{ old('name', $package->name) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700">Categoria</label>
                        <select name="category"
                                id="category"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                required>
                            <option value="">Selecione uma categoria</option>
                            <option value="Básico" {{ old('category', $package->category) == 'Básico' ? 'selected' : '' }}>Básico</option>
                            <option value="Avançado" {{ old('category', $package->category) == 'Avançado' ? 'selected' : '' }}>Avançado</option>
                            <option value="Premium" {{ old('category', $package->category) == 'Premium' ? 'selected' : '' }}>Premium</option>
                            <option value="VIP" {{ old('category', $package->category) == 'VIP' ? 'selected' : '' }}>VIP</option>
                        </select>
                        @error('category')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Descrição</label>
                        <textarea name="description"
                                  id="description"
                                  rows="4"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                  required>{{ old('description', $package->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700">Preço Mensal</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">R$</span>
                            </div>
                            <input type="number"
                                   name="price"
                                   id="price"
                                   step="0.01"
                                   value="{{ old('price', $package->price) }}"
                                   class="pl-12 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   required>
                        </div>
                        @error('price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="vinyl_quantity" class="block text-sm font-medium text-gray-700">
                            Quantidade de Vinis por Mês
                        </label>
                        <input type="number"
                               name="vinyl_quantity"
                               id="vinyl_quantity"
                               min="1"
                               value="{{ old('vinyl_quantity', $package->vinyl_quantity) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               required>
                        @error('vinyl_quantity')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox"
                               name="is_active"
                               id="is_active"
                               value="1"
                               {{ old('is_active', $package->is_active) ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="is_active" class="ml-2 block text-sm text-gray-700">
                            Pacote Ativo
                        </label>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end">
                    <a href="{{ route('admin.subscription-packages.index') }}"
                       class="px-4 py-2 mr-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                        Cancelar
                    </a>
                    
                    <button type="submit"
                            class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Atualizar Pacote
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
