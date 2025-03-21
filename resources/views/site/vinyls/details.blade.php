<x-app-layout x-data="{
    isFavorite: {{ $vinyl->inWishlist() ? 'true' : 'false' }},
    isInCart: {{ $vinyl->inCart() ? 'true' : 'false' }},
    quantity: 1,
    maxQuantity: {{ $vinyl->vinylSec ? $vinyl->vinylSec->quantity : 0 }},
    isInStock: {{ $vinyl->vinylSec && $vinyl->vinylSec->quantity > 0 ? 'true' : 'false' }},

    addToCart() {
        if (this.quantity > 0 && this.quantity <= this.maxQuantity) {
            this.isInCart = true;
            // Lógica para adicionar ao carrinho
        }
    },

    toggleFavorite() {
        this.isFavorite = !this.isFavorite;
        // Lógica para adicionar/remover dos favoritos
    }
}">
    <div class="bg-gray-50 py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Breadcrumb -->
            <x-breadcrumb :items="[
                ['label' => 'Discos', 'url' => route('site.vinyls.index')],
                ['label' => $vinyl->title]
            ]" />

            <div class="mt-6 bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
                    <!-- Imagem do Vinil -->
                    <div class="lg:col-span-1">
                        <div class="relative aspect-square rounded-lg overflow-hidden border border-gray-200">
                            <img src="{{ $vinyl->cover_url ?? asset('images/no-image.jpg') }}" alt="{{ $vinyl->title }}" class="w-full h-full object-cover">

                            @if(!$vinyl->vinylSec || $vinyl->vinylSec->quantity <= 0)
                                <div class="absolute top-0 right-0 bg-red-500 text-white px-3 py-1 rounded-bl-lg font-medium text-sm">
                                    Indisponível
                                </div>
                            @endif

                            <button
                                @click="toggleFavorite"
                                class="absolute top-2 left-2 bg-white rounded-full p-2 text-gray-400 hover:text-red-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                data-product-id="{{ $vinyl->id }}"
                                data-product-type="App\Models\VinylMaster"
                                :class="{ 'text-red-500': isFavorite }"
                            >
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                                </svg>
                            </button>
                        </div>

                        <!-- Miniaturas -->
                        @if($vinyl->images && count($vinyl->images) > 0)
                            <div class="mt-4 grid grid-cols-4 gap-2">
                                @foreach($vinyl->images as $image)
                                    <div class="aspect-square rounded-md overflow-hidden border border-gray-200 cursor-pointer hover:border-blue-500">
                                        <img src="{{ $image }}" alt="{{ $vinyl->title }}" class="w-full h-full object-cover">
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Informações do Vinil -->
                    <div class="lg:col-span-2">
                        <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">{{ $vinyl->title }}</h1>

                        <div class="mt-2 flex items-center">
                            <p class="text-lg text-gray-700">
                                {{ $vinyl->artists->pluck('name')->join(', ') }}
                            </p>
                        </div>

                        <div class="mt-4 flex items-center space-x-4">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                                <span class="ml-1 text-gray-600">{{ number_format($vinyl->rating_avg, 1) }} ({{ $vinyl->rating_count }} avaliações)</span>
                            </div>

                            <span class="text-gray-400">|</span>

                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span class="ml-1 text-gray-600">{{ $vinyl->release_year }}</span>
                            </div>
                        </div>

                        <div class="mt-6 border-t border-gray-200 pt-6">
                            <div class="flex items-center justify-between">
                                <p class="text-3xl font-bold text-gray-900">
                                    @if($vinyl->vinylSec && $vinyl->vinylSec->price > 0)
                                        R$ {{ number_format($vinyl->vinylSec->price, 2, ',', '.') }}
                                    @else
                                        <span class="text-gray-500">Preço indisponível</span>
                                    @endif
                                </p>

                                <div class="flex items-center">
                                    <span class="text-sm text-gray-500 mr-2">Quantidade:</span>
                                    <div class="flex items-center border border-gray-300 rounded-md">
                                        <button
                                            @click="quantity > 1 ? quantity-- : quantity"
                                            class="px-3 py-1 text-gray-600 hover:bg-gray-100 focus:outline-none"
                                            :disabled="!isInStock"
                                        >
                                            -
                                        </button>
                                        <input
                                            type="number"
                                            x-model="quantity"
                                            min="1"
                                            :max="maxQuantity"
                                            class="w-12 text-center border-0 focus:ring-0"
                                            :disabled="!isInStock"
                                        >
                                        <button
                                            @click="quantity < maxQuantity ? quantity++ : quantity"
                                            class="px-3 py-1 text-gray-600 hover:bg-gray-100 focus:outline-none"
                                            :disabled="!isInStock"
                                        >
                                            +
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 flex space-x-3">
                                <button
                                    @click="addToCart"
                                    class="flex-1 bg-blue-600 text-white py-3 px-6 rounded-md font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                    :class="{ 'opacity-50 cursor-not-allowed': !isInStock }"
                                    :disabled="!isInStock"
                                    data-product-id="{{ $vinyl->id }}"
                                    data-product-type="App\Models\VinylMaster"
                                >
                                    <span x-text="isInCart ? 'Adicionado ao Carrinho' : (isInStock ? 'Adicionar ao Carrinho' : 'Indisponível')"></span>
                                </button>

                                <button
                                    @click="toggleFavorite"
                                    class="flex items-center justify-center bg-gray-200 text-gray-700 py-3 px-6 rounded-md font-medium hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                                    data-product-id="{{ $vinyl->id }}"
                                    data-product-type="App\Models\VinylMaster"
                                >
                                    <svg class="w-5 h-5 mr-2" :class="{ 'text-red-500': isFavorite }" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span x-text="isFavorite ? 'Remover dos Favoritos' : 'Adicionar aos Favoritos'"></span>
                                </button>
                            </div>

                            <div class="mt-4 text-sm text-gray-500" x-show="isInStock">
                                <span class="font-medium text-green-600">Em estoque</span> -
                                <span>{{ $vinyl->vinylSec ? $vinyl->vinylSec->quantity : 0 }} unidades disponíveis</span>
                            </div>

                            <div class="mt-4 text-sm text-gray-500" x-show="!isInStock">
                                <span class="font-medium text-red-600">Fora de estoque</span> -
                                <span>Adicione aos favoritos para ser notificado quando estiver disponível</span>
                            </div>
                        </div>

                        <!-- Detalhes do Vinil -->
                        <div class="mt-6 border-t border-gray-200 pt-6">
                            <h3 class="text-lg font-medium text-gray-900">Detalhes do Disco</h3>

                            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-500">Gênero</p>
                                    <p class="text-sm font-medium text-gray-900">{{ $vinyl->genres->pluck('name')->join(', ') }}</p>
                                </div>

                                <div>
                                    <p class="text-sm text-gray-500">Gravadora</p>
                                    <p class="text-sm font-medium text-gray-900">{{ $vinyl->recordLabel->name ?? 'Não informado' }}</p>
                                </div>

                                <div>
                                    <p class="text-sm text-gray-500">Ano de Lançamento</p>
                                    <p class="text-sm font-medium text-gray-900">{{ $vinyl->release_year }}</p>
                                </div>

                                <div>
                                    <p class="text-sm text-gray-500">Formato</p>
                                    <p class="text-sm font-medium text-gray-900">{{ $vinyl->vinylSec ? $vinyl->vinylSec->format : 'Não informado' }}</p>
                                </div>

                                <div>
                                    <p class="text-sm text-gray-500">Condição</p>
                                    <p class="text-sm font-medium text-gray-900">{{ $vinyl->vinylSec ? $vinyl->vinylSec->condition : 'Não informado' }}</p>
                                </div>

                                <div>
                                    <p class="text-sm text-gray-500">País</p>
                                    <p class="text-sm font-medium text-gray-900">{{ $vinyl->country ?? 'Não informado' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Descrição -->
                        @if($vinyl->description)
                            <div class="mt-6 border-t border-gray-200 pt-6">
                                <h3 class="text-lg font-medium text-gray-900">Descrição</h3>

                                <div class="mt-4 prose prose-sm text-gray-700">
                                    {!! $vinyl->description !!}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Faixas do Álbum -->
            @if($vinyl->tracks && $vinyl->tracks->count() > 0)
                <div class="mt-8 bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="p-6">
                        <h2 class="text-xl font-bold text-gray-900">Faixas do Álbum</h2>

                        <div class="mt-4 divide-y divide-gray-200">
                            @foreach($vinyl->tracks as $track)
                                <div class="py-3 flex items-center justify-between">
                                    <div class="flex items-center">
                                        <span class="text-gray-500 w-8">{{ $track->position }}</span>
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-900">{{ $track->title }}</h4>
                                            <p class="text-xs text-gray-500">{{ $track->duration_formatted }}</p>
                                        </div>
                                    </div>

                                    @if($track->preview_url)
                                        <button class="text-blue-600 hover:text-blue-800 focus:outline-none">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Produtos Relacionados -->
            @if(isset($relatedVinyls) && $relatedVinyls->count() > 0)
                <div class="mt-8">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Você também pode gostar</h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        @foreach($relatedVinyls as $relatedVinyl)
                            <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200 hover:shadow-md transition-shadow duration-300">
                                <a href="{{ route('site.vinyl.show', [
                                    'artistSlug' => $relatedVinyl->artists->isNotEmpty() ? $relatedVinyl->artists->first()->slug : 'artista', 
                                    'titleSlug' => $relatedVinyl->slug
                                ]) }}">
                                    <div class="relative aspect-square">
                                        <img src="{{ $relatedVinyl->cover_url ?? asset('images/no-image.jpg') }}" alt="{{ $relatedVinyl->title }}" class="w-full h-full object-cover">

                                        @if(!$relatedVinyl->vinylSec || $relatedVinyl->vinylSec->quantity <= 0)
                                            <div class="absolute top-0 right-0 bg-red-500 text-white px-2 py-1 text-xs font-medium">
                                                Indisponível
                                            </div>
                                        @endif
                                    </div>

                                    <div class="p-4">
                                        <h3 class="text-sm font-medium text-gray-900 line-clamp-1">{{ $relatedVinyl->title }}</h3>
                                        <p class="text-xs text-gray-500 mt-1 line-clamp-1">{{ $relatedVinyl->artists->pluck('name')->join(', ') }}</p>

                                        <div class="mt-2">
                                            @if($relatedVinyl->vinylSec && $relatedVinyl->vinylSec->price > 0)
                                                <p class="text-sm font-bold text-gray-900">R$ {{ number_format($relatedVinyl->vinylSec->price, 2, ',', '.') }}</p>
                                            @else
                                                <p class="text-sm text-gray-500">Preço indisponível</p>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
