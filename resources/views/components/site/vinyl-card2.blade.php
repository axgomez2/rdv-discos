<div class="card bg-base-100 shadow-xl hover:shadow-2xl transition-all duration-300 group h-full">
    <figure class="relative aspect-square overflow-hidden">
        <img
            src="{{ asset('storage/' . $vinyl->cover_image) }}"
            alt="{{ $vinyl->title }} by {{ $vinyl->artists->pluck('name')->implode(', ') }}"
            class="w-full h-full object-cover object-center group-hover:scale-110 transition-transform duration-300"
            onerror="this.src='{{ asset('assets/images/placeholder.jpg') }}'"
        />
        @if($vinyl->vinylSec->is_promotional == 1)
            <div class="indicator-item indicator-start badge badge-secondary absolute top-2 left-2 text-xs">Oferta</div>
        @endif
        <button
            class="play-button absolute bottom-2 right-2 btn btn-circle btn-sm btn-primary"
            data-vinyl-id="{{ $vinyl->id }}"
            data-tracks="{{ json_encode($vinyl->tracks) }}"
            data-artist="{{ $vinyl->artists->pluck('name')->implode(', ') }}"
            data-cover-url="{{ asset('storage/' . $vinyl->cover_image) }}"
            data-vinyl-title="{{ $vinyl->title }}"
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
                @if($vinyl->vinylSec->is_promotional == 1)
                    <p class="text-xs text-gray-500 line-through">R$ {{ number_format($vinyl->vinylSec->price * 1.2, 2, ',', '.') }}</p>
                @endif
                <p class="text-sm font-bold">R$ {{ number_format($vinyl->vinylSec->price, 2, ',', '.') }}</p>
            </div>
            <button
                type="button"
                title="{{ $vinyl->inWishlist() ? 'Remover dos favoritos' : 'Adicionar aos favoritos' }}"
                class="wishlist-button btn btn-circle btn-xs btn-outline"
                data-product-id="{{ $vinyl->id }}"
                data-product-type="{{ get_class($vinyl) }}"
                data-in-wishlist="{{ $vinyl->inWishlist() ? 'true' : 'false' }}"
            >
                <i class="fas fa-heart {{ $vinyl->inWishlist() ? 'text-red-500' : 'text-gray-400' }}"></i>
            </button>
        </div>
        <div class="card-actions justify-end mt-2">
            <button
                type="button"
                class="btn btn-primary btn-sm w-full add-to-cart-button"
                data-product-id="{{ $vinyl->product->id }}"
                data-quantity="1"
                {{ $vinyl->vinylSec->quantity > 0 ? '' : 'disabled' }}
            >
                <i class="fas fa-shopping-cart mr-1 text-xs"></i>
                <span class="add-to-cart-text text-xs">
                    {{ $vinyl->vinylSec->quantity > 0 ? 'Adicionar ao Carrinho' : 'Indisponível' }}
                </span>
            </button>
        </div>
    </div>
</div>

