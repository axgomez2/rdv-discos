@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-3xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">
                {{ isset($package) ? 'Editar Pacote' : 'Novo Pacote' }}
            </h1>
            <a href="{{ route('admin.subscription-packages.index') }}"
               class="text-gray-600 hover:text-gray-900">
                Voltar
            </a>
        </div> 

        <div class="bg-white shadow-md rounded-lg p-6">
            <form action="{{ isset($package)
                ? route('admin.subscription-packages.update', $package)
                : route('admin.subscription-packages.store') }}"
                  method="POST">
                @csrf
                @if(isset($package))
                    @method('PUT')
                @endif

                <div class="space-y-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Nome</label>
                        <input type="text"
                               name="name"
                               id="name"
                               value="{{ old('name', $package->name ?? '') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700">Categoria</label>
                        <input type="text"
                               name="category"
                               id="category"
                               value="{{ old('category', $package->category ?? '') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               required>
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
                                  required>{{ old('description', $package->description ?? '') }}</textarea>
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
                                   value="{{ old('price', $package->price ?? '') }}"
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
                               value="{{ old('vinyl_quantity', $package->vinyl_quantity ?? '') }}"
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
                               {{ old('is_active', $package->is_active ?? true) ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="is_active" class="ml-2 block text-sm text-gray-700">
                            Pacote Ativo
                        </label>
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit"
                            class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        {{ isset($package) ? 'Atualizar Pacote' : 'Criar Pacote' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
