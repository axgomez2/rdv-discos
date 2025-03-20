<x-app-layout>

    <div class="font-[sans-serif]e p-4 mx-auto max-w-[1400px] rounded-lg bg-slate-800 mt-5">
        <h2 class="text-xl font-bold mb-6 text-white">Filtros</h2  >

            <form action="{{ route('site.vinyls.index') }}" method="GET" class="space-y-4">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 sm:gap-6">

                    <div>
                        <label for="style" class="block text-sm font-medium text-white">Estilo</label>
                        <select name="style" id="style" class="select select-bordered w-full max-w-xs mt-2">
                            <option value="">Todos os estilos</option>
                            @foreach($styles as $style)
                                <option value="{{ $style->id }}" {{ request('style') == $style->id ? 'selected' : '' }}>{{ $style->name }}</option>
                            @endforeach
                        </select>
                    </div>



                    <div>
                        <label for="price_range" class="block text-sm font-medium text-white">Faixa de Preço</label>
                        <div class="flex items-center space-x-4">
                            <input type="range" name="min_price" id="min_price" min="{{ $priceRange->min_price }}" max="{{ $priceRange->max_price }}" step="0.01" value="{{ request('min_price', default: $priceRange->min_price) }}" class="range mt-5 [--range-shdw:yellow]"">
                            <input type="range" name="max_price" id="max_price" min="{{ $priceRange->min_price }}" max="{{ $priceRange->max_price }}" step="0.01" value="{{ request('max_price', $priceRange->max_price) }}" class="range mt-5 [--range-shdw:yellow]">
                        </div>
                        <div class="flex justify-between ">
                            <span id="min_price_display " class="text-white">R$ {{ number_format(request('min_price', $priceRange->min_price), 2, ',', '.') }}</span>
                            <span id="max_price_display " class="text-white">R$ {{ number_format(request('max_price', $priceRange->max_price), 2, ',', '.') }}</span>
                        </div>
                    </div>
                    <div><label for="sort_by" class="block text-sm font-medium text-white">Ordenar por</label>
                        <select name="sort_by" id="sort_by" class="select select-bordered w-full max-w-xs mt-2">
                            <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Data de adição</option>
                            <option value="price" {{ request('sort_by') == 'price' ? 'selected' : '' }}>Preço</option>
                            <option value="release_year" {{ request('sort_by') == 'release_year' ? 'selected' : '' }}>Ano de lançamento</option>
                            <option value="title" {{ request('sort_by') == 'title' ? 'selected' : '' }}>Título</option>
                        </select></div>
                        <div class="">
                            <label for="sort_order" class="block text-sm font-medium text-white">Ordem</label>
                        <select name="sort_order" id="sort_order" class="select select-bordered w-full max-w-xs mt-2">
                            <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Crescente</option>
                            <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Decrescente</option>
                        </select>
                        </div>
                        <div class=""><button type="submit" class="btn btn-wide mt-7">
                            Aplicar Filtros
                        </button>
                    </div>
                </div>
            </form>

    </div>




<div class="font-[sans-serif]e p-4 mx-auto max-w-[1400px]">
    <h2 class="font-jersey text-xl sm:text-3xl  text-gray-800 mt-3  ">Todos os discos</h2>
    <div class="divider mb-3"></div>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 sm:gap-6">


        @foreach($vinyls as $vinyl)
<div x-data="vinylCard()" class="card bg-base-100 shadow-xl hover:shadow-2xl transition-all duration-300 group h-full">
    <figure class="relative aspect-square overflow-hidden">
        <img
            src="{{ asset('storage/' . $vinyl->cover_image) }}"
            alt="{{ $vinyl->title }} by {{ $vinyl->artists->pluck('name')->implode(', ') }}"
            class="w-full h-full object-cover object-center group-hover:scale-110 transition-transform duration-300"
            onerror="this.src='{{ asset('assets/images/placeholder.jpg') }}'"
        />
        @if($vinyl->vinylSec && $vinyl->vinylSec->is_promotional == 1)
            <div class="indicator-item indicator-start badge badge-secondary absolute top-2 left-2 text-xs">Oferta</div>
        @endif
        <button
            x-ref="playButton"
            class="play-button absolute bottom-2 right-2 btn btn-circle btn-sm btn-primary"
            @click="playVinylTracks"
            data-vinyl-id="{{ $vinyl->id }}"
            data-vinyl-title="{{ $vinyl->title }}"
            data-cover-url="{{ asset('storage/' . $vinyl->cover_image) }}"
            data-artist="{{ $vinyl->artists->pluck('name')->implode(', ') }}"
            data-tracks="{{ json_encode($vinyl->tracks) }}"
        >
            <i class="fas fa-play text-xs"></i>
        </button>
    </figure>
    <div class="card-body p-3 text-sm">
        <a href="{{ route('site.vinyl.show', ['artistSlug' => $vinyl->artists->first()->slug, 'titleSlug' => $vinyl->slug]) }}" class="block">
            <h2 class="card-title text-base font-semibold line-clamp-1">
                {{ $vinyl->artists->pluck('name')->implode(', ') }}
            </h2>
            <p class="text-xs text-gray-600 line-clamp-1">{{ $vinyl->title }}</p>
        </a>
        <div class="flex justify-between items-center mt-1">
            <div>
                <p class="text-xs text-gray-500">{{ $vinyl->recordLabel->name }} • {{ $vinyl->release_year }}</p>
                @if($vinyl->vinylSec && $vinyl->vinylSec->quantity > 0)
                    @if($vinyl->vinylSec->is_promotional == 1)

                        <p class="text-sm font-bold text-red-600">R$ {{ number_format($vinyl->vinylSec->promotional_price, 2, ',', '.') }}</p>
                    @else
                        <p class="text-sm font-bold">R$ {{ number_format($vinyl->vinylSec->price, 2, ',', '.') }}</p>
                    @endif
                @else
                    <p class="text-sm font-bold text-red-500">Indisponível</p>
                @endif
            </div>
            <button
                type="button"
                title="{{ $vinyl->inWishlist() ? 'Remover dos favoritos' : 'Adicionar aos favoritos' }}"
                class="wishlist-button btn btn-circle btn-xs btn-outline"
                onclick="toggleFavorite({{ $vinyl->id }}, 'App\\Models\\VinylMaster', this)"
                data-in-wishlist="{{ $vinyl->inWishlist() ? 'true' : 'false' }}"
            >
                <i class="fas fa-heart {{ $vinyl->inWishlist() ? 'text-red-500' : 'text-gray-400' }}"></i>
            </button>
        </div>
        <div class="card-actions justify-end mt-2">
            @if($vinyl->vinylSec && $vinyl->vinylSec->quantity > 0)
                <button
                    type="button"
                    class="btn btn-primary btn-sm w-full"
                    onclick="addToCart({{ $vinyl->product->id }}, 1, this)"
                >
                    <i class="fas fa-shopping-cart mr-1 text-xs"></i>
                    <span class="add-to-cart-text text-xs">
                        Adicionar ao Carrinho
                    </span>
                </button>
            @else
                <button
                    type="button"
                    title="{{ $vinyl->inWantlist() ? 'Remover da Wantlist' : 'Adicionar à Wantlist' }}"
                    class="wantlist-button btn btn-outline btn-sm w-full"
                    onclick="toggleWantlist({{ $vinyl->id }}, 'App\\Models\\VinylMaster', this)"
                    data-in-wantlist="{{ $vinyl->inWantlist() ? 'true' : 'false' }}"
                >
                    <i class="fas fa-bookmark mr-1 text-xs"></i>
                    <span class="text-xs">
                        {{ $vinyl->inWantlist() ? 'Remover da Wantlist' : 'Adicionar à Wantlist' }}
                    </span>
                </button>
            @endif
        </div>
    </div>
</div>
@endforeach











                    <!-- Add pagination links here -->
                    {{ $vinyls->links() }}
                </div>
            </div>
        </div>
    </div>






    </div></div>

















        <div class="mt-8">
            {{ $vinyls->links() }}
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const minPriceInput = document.getElementById('min_price');
            const maxPriceInput = document.getElementById('max_price');
            const minPriceDisplay = document.getElementById('min_price_display');
            const maxPriceDisplay = document.getElementById('max_price_display');

            function updatePriceDisplay(input, display) {
                display.textContent = 'R$ ' + parseFloat(input.value).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }

            minPriceInput.addEventListener('input', function() {
                updatePriceDisplay(minPriceInput, minPriceDisplay);
            });

            maxPriceInput.addEventListener('input', function() {
                updatePriceDisplay(maxPriceInput, maxPriceDisplay);
            });
        });
    </script>

</x-app-layout>
