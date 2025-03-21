@props(['vinyl'])

<div class="bg-white border border-gray-500 rounded-lg shadow hover:shadow-xl transition-all duration-300 group h-full">
    <!-- Card para Desktop (vertical) -->
    <div class="hidden sm:block">
        <div class="relative aspect-square overflow-hidden">
            <img
                src="{{ asset('storage/' . $vinyl->cover_image) }}"
                alt="{{ $vinyl->title }} by {{ $vinyl->artists->pluck('name')->implode(', ') }}"
                class="w-full h-full object-cover object-center group-hover:scale-110 transition-transform duration-300"
                onerror="this.src='{{ asset('assets/images/placeholder.jpg') }}'"
            />
            <div class="absolute top-2 left-2 flex flex-col gap-2">
                <span class="{{ $vinyl->vinylSec->quantity > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} text-xs font-medium px-2.5 py-0.5 rounded-full">
                    {{ $vinyl->vinylSec->quantity > 0 ? 'Disponível' : 'Indisponível' }}
                </span>
                @if($vinyl->vinylSec->is_promotional && $vinyl->vinylSec->promotional_price && $vinyl->vinylSec->promotional_price > 0)
                    <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded">
                        Oferta
                    </span>
                @endif
            </div>
            <button
                type="button"
                class="play-button absolute bottom-2 right-2 inline-flex items-center justify-center w-8 h-8 text-white rounded-full focus:ring-4 focus:ring-blue-300 {{ $vinyl->tracks->contains(function($track) { return !empty($track->youtube_url); }) ? 'bg-blue-700 hover:bg-blue-800' : 'bg-gray-400 cursor-not-allowed' }}"
                data-vinyl-info="{{ json_encode([
                    'tracks' => $vinyl->tracks->map(function($track) {
                        return [
                            'id' => $track->id,
                            'name' => $track->name,
                            'youtube_url' => $track->youtube_url
                        ];
                    }),
                    'artist' => $vinyl->artists->pluck('name')->implode(', '),
                    'coverUrl' => asset('storage/' . $vinyl->cover_image),
                    'vinylTitle' => $vinyl->title
                ]) }}"
                {{ $vinyl->tracks->contains(function($track) { return !empty($track->youtube_url); }) ? '' : 'disabled' }}
                title="{{ $vinyl->tracks->contains(function($track) { return !empty($track->youtube_url); }) ? 'Reproduzir' : 'Áudio não disponível' }}"
            >
                <i class="fas fa-play text-xs"></i>
            </button>
        </div>
        <div class="p-4">
            <a href="{{ route('site.vinyl.show', ['artistSlug' => $vinyl->artists->first()->slug, 'titleSlug' => $vinyl->slug]) }}">
                <h5 class="text-base font-semibold tracking-tight text-gray-900 line-clamp-1">
                    {{ $vinyl->artists->pluck('name')->implode(', ') }}
                </h5>
                <p class="text-sm text-gray-700 line-clamp-1">{{ $vinyl->title }}</p>
            </a>
            <div class="flex justify-between items-start mt-2.5">
                <div>
                    <p class="text-xs text-gray-700">{{ $vinyl->recordLabel->name }} </p>
                    <p class="text-xs text-gray-700">{{ $vinyl->release_year }}</p>
                    @if($vinyl->vinylSec->is_promotional && $vinyl->vinylSec->promotional_price && $vinyl->vinylSec->promotional_price > 0)
                        <p class="text-xs text-gray-500 line-through">
                            R$ {{ number_format($vinyl->vinylSec->price, 2, ',', '.') }}
                        </p>
                        <p class="text-lg font-bold text-gray-900">
                            R$ {{ number_format($vinyl->vinylSec->promotional_price, 2, ',', '.') }}
                        </p>
                    @else
                        <p class="text-lg font-bold text-gray-900">
                            R$ {{ number_format($vinyl->vinylSec->price, 2, ',', '.') }}
                        </p>
                    @endif
                </div>
                <button
                    type="button"
                    title="{{ auth()->check() && $vinyl->inWishlist() ? 'Remover dos favoritos' : (auth()->check() && !$vinyl->vinylSec->quantity > 0 && $vinyl->inWantlist() ? 'Remover da wantlist' : 'Adicionar aos favoritos') }}"
                    class="wishlist-button text-gray-400 hover:text-pink-500 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg text-lg p-2"
                    data-product-id="{{ $vinyl->id }}"
                    data-product-type="{{ get_class($vinyl) }}"
                    data-is-available="{{ json_encode($vinyl->vinylSec->quantity > 0) }}"
                    data-in-wishlist="{{ json_encode(auth()->check() && $vinyl->inWishlist()) }}"
                    data-in-wantlist="{{ json_encode(auth()->check() && !$vinyl->vinylSec->quantity > 0 && $vinyl->inWantlist()) }}"
                    onclick="{{ auth()->check() ? '' : 'showLoginToast(); return false;' }}"
                >
                    <i class="fas {{ auth()->check() && $vinyl->inWantlist() ? 'fa-flag' : 'fa-heart' }} {{ (auth()->check() && $vinyl->inWishlist() || auth()->check() && $vinyl->inWantlist()) ? 'text-red-500' : '' }}"></i>
                </button>
            </div>
            <div class="mt-4">
                <button
                    type="button"
                    class="add-to-cart-button w-full text-gray-900 bg-yellow-400 border border-gray-300 focus:outline-none hover:bg-gray-900 hover:text-yellow-500 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5 {{ !$vinyl->vinylSec->quantity > 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                    data-product-id="{{ $vinyl->product ? $vinyl->product->id : '' }}"
                    data-quantity="1"
                    {{ $vinyl->vinylSec->quantity > 0 && $vinyl->product ? '' : 'disabled' }}
                >
                    {{ $vinyl->vinylSec->quantity > 0 ? 'Adicionar ao Carrinho' : 'Indisponível' }}
                </button>
            </div>
        </div>
    </div>

    <!-- Card para Mobile (horizontal) -->
    <div class="sm:hidden flex">
        <div class="relative w-1/3">
            <img
                src="{{ asset('storage/' . $vinyl->cover_image) }}"
                alt="{{ $vinyl->title }}"
                class="w-full h-full object-cover object-center"
                onerror="this.src='{{ asset('assets/images/placeholder.jpg') }}'"
            />
            <div class="absolute top-1 left-1 flex flex-col gap-1">
                <span class="{{ $vinyl->vinylSec->quantity > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} text-xs font-medium px-2 py-0.5 rounded-full">
                    {{ $vinyl->vinylSec->quantity > 0 ? 'Disponível' : 'Indisponível' }}
                </span>
                @if($vinyl->vinylSec->is_promotional && $vinyl->vinylSec->promotional_price && $vinyl->vinylSec->promotional_price > 0)
                    <span class="bg-red-100 text-red-800 text-xs font-medium px-2 py-0.5 rounded">
                        Oferta
                    </span>
                @endif
            </div>
        </div>
        <div class="w-2/3 p-3 flex flex-col justify-between">
            <div>
                <a href="{{ route('site.vinyl.show', ['artistSlug' => $vinyl->artists->first()->slug, 'titleSlug' => $vinyl->slug]) }}">
                    <h5 class="text-sm font-semibold tracking-tight text-gray-900 line-clamp-1">
                        {{ $vinyl->artists->pluck('name')->implode(', ') }}
                    </h5>
                    <p class="text-xs text-gray-500 line-clamp-1">{{ $vinyl->title }}</p>
                </a>
                <p class="text-xs text-gray-500 mt-1">
                    {{ $vinyl->recordLabel->name }} • {{ $vinyl->release_year }}
                </p>
            </div>
            <div class="flex justify-between items-center mt-2">
                <div>
                    @if($vinyl->vinylSec->is_promotional && $vinyl->vinylSec->promotional_price && $vinyl->vinylSec->promotional_price > 0)
                        <p class="text-xs text-gray-500 line-through">
                            R$ {{ number_format($vinyl->vinylSec->price, 2, ',', '.') }}
                        </p>
                        <p class="text-base font-bold text-gray-900">
                            R$ {{ number_format($vinyl->vinylSec->promotional_price, 2, ',', '.') }}
                        </p>
                    @else
                        <p class="text-base font-bold text-gray-900">
                            R$ {{ number_format($vinyl->vinylSec->price, 2, ',', '.') }}
                        </p>
                    @endif
                </div>
                <div class="flex gap-2">
                    <button
                        type="button"
                        class="play-button inline-flex items-center p-2 text-white rounded-full focus:ring-4 focus:ring-blue-300 {{ $vinyl->tracks->contains(function($track) { return !empty($track->youtube_url); }) ? 'bg-blue-700 hover:bg-blue-800' : 'bg-gray-400 cursor-not-allowed' }}"
                        data-vinyl-info="{{ json_encode([
                            'tracks' => $vinyl->tracks->map(function($track) {
                                return [
                                    'id' => $track->id,
                                    'name' => $track->name,
                                    'youtube_url' => $track->youtube_url
                                ];
                            }),
                            'artist' => $vinyl->artists->pluck('name')->implode(', '),
                            'coverUrl' => asset('storage/' . $vinyl->cover_image),
                            'vinylTitle' => $vinyl->title
                        ]) }}"
                        onclick="{{ $vinyl->tracks->contains(function($track) { return !empty($track->youtube_url); }) ? 'initializeCardPlayer(this)' : '' }}"
                        {{ $vinyl->tracks->contains(function($track) { return !empty($track->youtube_url); }) ? '' : 'disabled' }}
                        title="{{ $vinyl->tracks->contains(function($track) { return !empty($track->youtube_url); }) ? 'Reproduzir' : 'Áudio não disponível' }}"
                    >
                        <i class="fas fa-play text-xs"></i>
                    </button>
                    <button
                        type="button"
                        title="{{ auth()->check() && $vinyl->inWishlist() ? 'Remover dos favoritos' : (auth()->check() && !$vinyl->vinylSec->quantity > 0 && $vinyl->inWantlist() ? 'Remover da wantlist' : 'Adicionar aos favoritos') }}"
                        class="wishlist-button text-gray-400 hover:text-red-500 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg text-sm p-2"
                        data-product-id="{{ $vinyl->id }}"
                        data-product-type="{{ get_class($vinyl) }}"
                        data-is-available="{{ json_encode($vinyl->vinylSec->quantity > 0) }}"
                        data-in-wishlist="{{ json_encode(auth()->check() && $vinyl->inWishlist()) }}"
                        data-in-wantlist="{{ json_encode(auth()->check() && !$vinyl->vinylSec->quantity > 0 && $vinyl->inWantlist()) }}"
                        onclick="{{ auth()->check() ? '' : 'showLoginToast(); return false;' }}"
                    >
                        <i class="fas {{ auth()->check() && $vinyl->inWantlist() ? 'fa-flag' : 'fa-heart' }} {{ (auth()->check() && $vinyl->inWishlist() || auth()->check() && $vinyl->inWantlist()) ? 'text-red-500' : '' }}"></i>
                    </button>
                    <button
                        type="button"
                        class="add-to-cart-button inline-flex items-center p-2 text-gray-900 bg-yellow-500 border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 {{ !$vinyl->vinylSec->quantity > 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                        data-product-id="{{ $vinyl->product ? $vinyl->product->id : '' }}"
                        data-quantity="1"
                        {{ $vinyl->vinylSec->quantity > 0 && $vinyl->product ? '' : 'disabled' }}
                        title="{{ $vinyl->vinylSec->quantity > 0 ? 'Adicionar ao carrinho' : 'Produto indisponível' }}"
                    >
                        <i class="fas fa-shopping-cart"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Função para garantir que o cart-count existe
    function ensureCartCountElement() {
        let cartCountElement = document.getElementById('cart-count');
        if (!cartCountElement) {
            // Encontrar o botão do carrinho
            const cartButton = document.querySelector('button[data-dropdown-toggle="cart-dropdown"]');
            if (cartButton) {
                // Criar elemento de contagem do carrinho se não existir
                cartCountElement = document.createElement('span');
                cartCountElement.id = 'cart-count';
                cartCountElement.setAttribute('data-cart-count', '');
                cartCountElement.className = 'absolute inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 border-2 border-white rounded-full -top-2 -right-2';
                cartButton.appendChild(cartCountElement);
            }
        }
        return cartCountElement;
    }

    // Adiciona eventos aos botões de play
    document.querySelectorAll('.play-button').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            if (this.disabled) return;

            try {
                const vinylData = JSON.parse(this.dataset.vinylInfo);
                if (!vinylData?.tracks?.length) {
                    throw new Error('Dados do vinyl inválidos');
                }

                // Formata as tracks para o player
                const tracks = vinylData.tracks
                    .filter(track => track.youtube_url)
                    .map(track => ({
                        id: track.id,
                        name: track.name,
                        artist: vinylData.artist,
                        youtube_url: track.youtube_url,
                        cover_url: vinylData.coverUrl
                    }));

                if (!tracks.length) {
                    throw new Error('Nenhuma faixa disponível para reprodução');
                }

                // Carrega a playlist no player
                if (window.audioPlayer) {
                    window.audioPlayer.loadPlaylist(tracks);
                }
            } catch (error) {
                console.error('Erro:', error);
                if (window.audioPlayer) {
                    window.audioPlayer.showError(error.message);
                }
            }
        });
    });

    // Adiciona eventos aos botões de carrinho
    document.querySelectorAll('.add-to-cart-button').forEach(button => {
        button.addEventListener('click', function() {
            if (this.disabled) {
                // Se o produto estiver indisponível, mostrar opção para adicionar à wantlist
                const productId = this.getAttribute('data-product-id');
                const isLoggedIn = {{ auth()->check() ? 'true' : 'false' }};
                
                if (isLoggedIn) {
                    // Perguntar se quer adicionar à wantlist
                    if (confirm('Este produto está indisponível. Deseja adicioná-lo à sua lista de desejos para ser notificado quando estiver disponível?')) {
                        // Adicionar à wantlist
                        addToWantlist(productId);
                    }
                } else {
                    // Mostrar toast para fazer login
                    showLoginToast();
                }
                return;
            }
            
            const productId = this.getAttribute('data-product-id');
            const quantity = this.getAttribute('data-quantity');
            
            // Adicionar ao carrinho via AJAX
            fetch('/carrinho/items', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: quantity
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erro na resposta da rede');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Mostrar toast de sucesso
                    showToast(data.message, 'success');
                    
                    // Atualizar contador do carrinho, se existir
                    const cartCountElement = ensureCartCountElement();
                    if (cartCountElement && data.cartCount) {
                        cartCountElement.textContent = data.cartCount;
                        
                        // Tornar visível se estiver escondido
                        if (cartCountElement.parentElement.style.display === 'none') {
                            cartCountElement.parentElement.style.display = '';
                        }
                    }
                } else {
                    // Verificar se a mensagem está relacionada ao estoque
                    if (data.message && data.message.includes('excede o estoque disponível')) {
                        showToast(data.message, 'warning');
                        
                        // Perguntar se quer adicionar à wantlist se estiver logado
                        const isLoggedIn = {{ auth()->check() ? 'true' : 'false' }};
                        if (isLoggedIn) {
                            setTimeout(() => {
                                if (confirm('O estoque é insuficiente. Deseja adicionar este item à sua lista de desejos para ser notificado quando estiver disponível?')) {
                                    // Adicionar à wantlist
                                    addToWantlist(productId);
                                }
                            }, 500);
                        }
                    } else {
                        showToast(data.message || 'Erro ao adicionar produto ao carrinho', 'error');
                    }
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                showToast('Erro ao adicionar produto ao carrinho', 'error');
            });
        });
    });
    
    // Função para adicionar à wantlist
    function addToWantlist(productId) {
        fetch('/wantlist/toggle', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                product_id: productId,
                product_type: 'App\\Models\\VinylMaster'
            })
        })
        .then(response => response.json())
        .then(data => {
            showToast(data.message, 'success');
        })
        .catch(error => {
            console.error('Erro:', error);
            showToast('Erro ao adicionar à wantlist', 'error');
        });
    }
});
</script>
@endpush
