<x-app-layout>

    <!-- Filter Bar - Modern Design -->
    <div class="bg-slate-800 py-6 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Collapsible Filters -->
            <div x-data="{ open: true }">
                <button @click="open = !open" class="flex items-center justify-between w-full px-4 py-2 bg-slate-700 rounded-lg text-white mb-4">
                    <span class="font-medium"><i class="fas fa-filter mr-2"></i> Filtros Avançados</span>
                    <i class="fas" :class="open ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                </button>

                <div x-show="open" x-transition class="bg-slate-700 rounded-lg p-4 mb-4">
                    <form action="{{ route('site.vinyls.promotions') }}" method="GET" class="space-y-4">
                        <!-- Keep search term if present -->
                        @if(request('search'))
                            <input type="hidden" name="search" value="{{ request('search') }}">
                        @endif

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                            <!-- Category Filter -->
                            <div>
                                <label for="category" class="block text-sm font-medium text-white mb-2">Categoria</label>
                                <select name="category" id="category" class="select select-bordered w-full bg-slate-600 text-white border-slate-500">
                                    <option value="">Todas as categorias</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Style Filter -->
                            <div>
                                <label for="style" class="block text-sm font-medium text-white mb-2">Estilo Musical</label>
                                <select name="style" id="style" class="select select-bordered w-full bg-slate-600 text-white border-slate-500">
                                    <option value="">Todos os estilos</option>
                                    @foreach($styles as $style)
                                        <option value="{{ $style->id }}" {{ request('style') == $style->id ? 'selected' : '' }}>{{ $style->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Price Range (For promotional prices) -->
                            <div>
                                <label for="price_range" class="block text-sm font-medium text-white mb-2">Faixa de Preço</label>
                                <div class="flex flex-col space-y-2">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-white text-xs">Min:</span>
                                        <input 
                                            type="range" 
                                            name="min_price" 
                                            id="min_price" 
                                            min="{{ $priceRange->min_price }}" 
                                            max="{{ $priceRange->max_price }}" 
                                            step="0.01" 
                                            value="{{ request('min_price', $priceRange->min_price) }}" 
                                            class="range [--range-shdw:yellow]"
                                        >
                                        <span id="min_price_display" class="text-white text-xs">R$ {{ number_format(request('min_price', $priceRange->min_price), 2, ',', '.') }}</span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="text-white text-xs">Max:</span>
                                        <input 
                                            type="range" 
                                            name="max_price" 
                                            id="max_price" 
                                            min="{{ $priceRange->min_price }}" 
                                            max="{{ $priceRange->max_price }}" 
                                            step="0.01" 
                                            value="{{ request('max_price', $priceRange->max_price) }}" 
                                            class="range [--range-shdw:yellow]"
                                        >
                                        <span id="max_price_display" class="text-white text-xs">R$ {{ number_format(request('max_price', $priceRange->max_price), 2, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Sorting Options -->
                            <div>
                                <label for="sort_by" class="block text-sm font-medium text-white mb-2">Ordenação</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <select name="sort_by" id="sort_by" class="select select-bordered w-full bg-slate-600 text-white border-slate-500 text-sm">
                                        <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Data de adição</option>
                                        <option value="price" {{ request('sort_by') == 'price' ? 'selected' : '' }}>Preço</option>
                                        <option value="release_year" {{ request('sort_by') == 'release_year' ? 'selected' : '' }}>Ano</option>
                                        <option value="title" {{ request('sort_by') == 'title' ? 'selected' : '' }}>Título</option>
                                    </select>
                                    <select name="sort_order" id="sort_order" class="select select-bordered w-full bg-slate-600 text-white border-slate-500 text-sm">
                                        <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Crescente</option>
                                        <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Decrescente</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Apply Filters Button -->
                        <div class="flex justify-end">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-sliders-h mr-2"></i> Aplicar Filtros
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Results Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl sm:text-3xl font-bold text-gray-800">
                <span class="text-red-600"><i class="fas fa-tag mr-2"></i>Ofertas Especiais</span>
                @if(request('search'))
                    <span class="text-lg text-gray-600"> • Resultados para: "{{ request('search') }}"</span>
                @endif
            </h2>
            <span class="text-gray-600">{{ $vinyls->total() }} resultado(s)</span>
        </div>
        
        <div class="divider mb-6"></div>
        
        @if($vinyls->count() > 0)
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
                            <div class="indicator-item indicator-start badge badge-secondary absolute top-2 left-2 text-xs">Oferta</div>
                            
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
                                    <div class="flex items-center">
                                        <p class="text-xs line-through text-gray-400 mr-2">R$ {{ number_format($vinyl->vinylSec->price, 2, ',', '.') }}</p>
                                        <p class="text-sm font-bold text-red-600">R$ {{ number_format($vinyl->vinylSec->promotional_price, 2, ',', '.') }}</p>
                                    </div>
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
            </div>
        @else
            <div class="py-12 text-center bg-gray-50 rounded-lg">
                <i class="fas fa-tag text-4xl text-gray-300 mb-4"></i>
                <p class="text-lg text-gray-500">Nenhuma oferta encontrada com os filtros atuais.</p>
                <a href="{{ route('site.vinyls.promotions') }}" class="mt-4 inline-block btn btn-outline">Limpar Filtros</a>
            </div>
        @endif

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

            if (minPriceInput && maxPriceInput && minPriceDisplay && maxPriceDisplay) {
                minPriceInput.addEventListener('input', function() {
                    minPriceDisplay.textContent = 'R$ ' + parseFloat(this.value).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).replace('.', ',');
                    if (parseFloat(this.value) > parseFloat(maxPriceInput.value)) {
                        maxPriceInput.value = this.value;
                        maxPriceDisplay.textContent = minPriceDisplay.textContent;
                    }
                });

                maxPriceInput.addEventListener('input', function() {
                    maxPriceDisplay.textContent = 'R$ ' + parseFloat(this.value).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).replace('.', ',');
                    if (parseFloat(this.value) < parseFloat(minPriceInput.value)) {
                        minPriceInput.value = this.value;
                        minPriceDisplay.textContent = maxPriceDisplay.textContent;
                    }
                });
            }
        });

        function vinylCard() {
            return {
                playVinylTracks(e) {
                    const button = e.target.closest('button');
                    const vinylId = button.dataset.vinylId;
                    const vinylTitle = button.dataset.vinylTitle;
                    const coverUrl = button.dataset.coverUrl;
                    const artist = button.dataset.artist;
                    const tracks = JSON.parse(button.dataset.tracks);
                    
                    // Dispatch event to player component
                    window.dispatchEvent(new CustomEvent('play-vinyl', {
                        detail: {
                            vinylId,
                            vinylTitle,
                            coverUrl,
                            artist,
                            tracks
                        }
                    }));
                }
            }
        }
    </script>
</x-app-layout>
