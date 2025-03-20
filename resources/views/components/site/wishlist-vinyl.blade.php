<div class="bg-white shadow-md hover:shadow-lg transition-shadow duration-300 rounded-lg overflow-hidden">
    <div class="flex flex-col sm:flex-row">
        <div class="sm:w-48 h-48 flex-shrink-0">
            <img
                src="{{ $vinyl->cover_image }}"
                alt="{{ $vinyl->title }} by {{ $vinyl->artists->pluck('name')->implode(', ') }}"
                class="w-full h-full object-cover object-center"
                onerror="this.src='https://placehold.co/600x400?text=imagem+não+disponivel'"
            />
        </div>
        <div class="flex-grow p-4 flex flex-col justify-between">
            <div>
                <div class="flex justify-between items-start">
                    <div>
                        <h2 class="text-lg font-semibold line-clamp-1">
                            {{ $vinyl->artists->pluck('name')->implode(', ') }}
                        </h2>
                        <p class="text-sm text-gray-600">{{ $vinyl->title }}</p>
                    </div>
                    <button
                        class="play-button btn btn-circle btn-sm btn-primary"
                        onclick="window.audioPlayer.loadTrack({{ json_encode($vinyl->tracks->first()) }})"
                    >
                        <i class="fas fa-play text-xs"></i>
                    </button>
                </div>
                <div class="mt-2 text-sm text-gray-500">
                    <p>{{ $vinyl->recordLabel->name }} • {{ $vinyl->release_year }}</p>
                    <p>Faixas: {{ $vinyl->tracks->count() }}</p>
                </div>
            </div>
            <div class="mt-4 flex items-center justify-between">
                <div>
                    @if($vinyl->vinylSec && $vinyl->vinylSec->quantity > 0)
                        @if($vinyl->vinylSec->is_promotional == 1)
                            <p class="text-sm text-gray-500 line-through">R$ {{ number_format($vinyl->vinylSec->price, 2, ',', '.') }}</p>
                            <p class="text-lg font-bold text-red-600">R$ {{ number_format($vinyl->vinylSec->promotional_price, 2, ',', '.') }}</p>
                        @else
                            <p class="text-lg font-bold">R$ {{ number_format($vinyl->vinylSec->price, 2, ',', '.') }}</p>
                        @endif
                    @else
                        <p class="text-lg font-bold text-red-500">Indisponível</p>
                    @endif
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('site.vinyl.show', ['artistSlug' => $vinyl->artists->first()->slug, 'titleSlug' => $vinyl->slug]) }}" class="btn btn-outline btn-sm">
                        <i class="fas fa-eye mr-2"></i> Ver detalhes
                    </a>
                    @if($vinyl->vinylSec && $vinyl->vinylSec->quantity > 0)
                        <button
                            type="button"
                            class="btn btn-primary btn-sm"
                            onclick="addToCart({{ $vinyl->product->id }}, 1, this)"
                        >
                            <i class="fas fa-shopping-cart mr-2"></i>
                            <span class="add-to-cart-text">
                                Adicionar
                            </span>
                        </button>
                    @else
                        <button
                            type="button"
                            class="btn btn-disabled btn-sm"
                            disabled
                        >
                            <i class="fas fa-times-circle mr-2"></i> Fora de estoque
                        </button>
                    @endif
                    <button
                        type="button"
                        title="Remover dos favoritos"
                        class="wishlist-button btn btn-circle btn-sm btn-outline"
                        onclick="toggleFavorite({{ $vinyl->id }}, 'App\\Models\\VinylMaster', this)"
                        data-product-id="{{ $vinyl->id }}"
                        data-product-type="App\Models\VinylMaster"
                        data-in-wishlist="true"
                    >
                        <i class="fas fa-heart text-red-500"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
