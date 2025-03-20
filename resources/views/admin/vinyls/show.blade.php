@extends('layouts.admin')

@section('content')
<div class="p-4">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-semibold text-gray-900 sm:text-3xl dark:text-white">Detalhes do Disco: {{ $vinyl->title }}</h1>
        <a href="{{ route('admin.vinyls.index') }}" class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700">
            Voltar para Lista
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Basic Information -->
        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Informações Básicas</h2>
            <dl class="grid grid-cols-1 gap-x-4 gap-y-3">
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Título</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $vinyl->title }}</dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Artistas</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                        @foreach($vinyl->artists as $artist)
                            {{ $artist->name }}@if(!$loop->last), @endif
                        @endforeach
                    </dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Gêneros</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                        @foreach($vinyl->genres as $genre)
                            <span class="bg-blue-100 text-blue-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">
                                {{ $genre->name }}
                            </span>
                        @endforeach
                    </dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Estilos</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                        @foreach($vinyl->styles as $style)
                            <span class="bg-purple-100 text-purple-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-purple-900 dark:text-purple-300">
                                {{ $style->name }}
                            </span>
                        @endforeach
                    </dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Gravadora</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $vinyl->recordLabel->name ?? 'N/A' }}</dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Ano de Lançamento</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $vinyl->release_year }}</dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Descrição</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $vinyl->description }}</dd>
                </div>
            </dl>
        </div>

        <!-- Product Details -->
        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Detalhes do Produto</h2>
            <dl class="grid grid-cols-1 gap-x-4 gap-y-3">
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Peso</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $vinyl->vinylSec->weight->name ?? 'N/A' }}</dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Dimensão</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $vinyl->vinylSec->dimension->name ?? 'N/A' }}</dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Quantidade em Estoque</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $vinyl->vinylSec->quantity ?? 0 }}</dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Preço</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">R$ {{ number_format($vinyl->vinylSec->price ?? 0, 2, ',', '.') }}</dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Preço de Compra</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">R$ {{ number_format($vinyl->vinylSec->buy_price ?? 0, 2, ',', '.') }}</dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Preço Promocional</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">R$ {{ number_format($vinyl->vinylSec->promotional_price ?? 0, 2, ',', '.') }}</dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                    <dd class="mt-1 text-sm">
                        <div class="flex flex-wrap gap-2">
                            @if($vinyl->vinylSec->is_promotional)
                                <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">
                                    Em Promoção
                                </span>
                            @endif
                            @if($vinyl->vinylSec->in_stock)
                                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">
                                    Em Estoque
                                </span>
                            @else
                                <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">
                                    Fora de Estoque
                                </span>
                            @endif
                        </div>
                    </dd>
                </div>
            </dl>
        </div>

        <!-- Statistics -->
        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Estatísticas</h2>
            <dl class="grid grid-cols-2 gap-4">
                <div class="p-4 bg-gray-50 rounded-lg dark:bg-gray-700">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Cliques no Card</dt>
                    <dd class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">{{ $cardClicks }}</dd>
                </div>
                <div class="p-4 bg-gray-50 rounded-lg dark:bg-gray-700">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Na Wishlist</dt>
                    <dd class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">{{ $wishlistCount }}</dd>
                </div>
                @if(!$vinyl->vinylSec->in_stock)
                    <div class="p-4 bg-gray-50 rounded-lg dark:bg-gray-700">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Na Want List</dt>
                        <dd class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">{{ $wantListCount }}</dd>
                    </div>
                @endif
                <div class="p-4 bg-gray-50 rounded-lg dark:bg-gray-700">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Em Carrinhos Incompletos</dt>
                    <dd class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">{{ $incompleteCartsCount }}</dd>
                </div>
            </dl>
        </div>

        <!-- Tracks -->
        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Faixas</h2>
            <div class="relative overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">#</th>
                            <th scope="col" class="px-6 py-3">Título</th>
                            <th scope="col" class="px-6 py-3">Duração</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vinyl->tracks as $track)
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <td class="px-6 py-4">{{ $loop->iteration }}</td>
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $track->title }}</td>
                                <td class="px-6 py-4">{{ $track->duration }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="mt-6 flex justify-end gap-2">
        <a href="{{ route('admin.vinyls.edit', $vinyl->id) }}"
           class="text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800">
            Editar Disco
        </a>
        <form action="{{ route('admin.vinyls.destroy', $vinyl->id) }}"
              method="POST"
              x-data
              @submit.prevent="if (confirm('Tem certeza que deseja excluir este disco?')) $el.submit()">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-red-600 dark:hover:bg-red-700 focus:outline-none dark:focus:ring-red-800">
                Excluir Disco
            </button>
        </form>
    </div>
</div>
@endsection
