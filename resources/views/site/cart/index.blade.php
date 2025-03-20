<x-app-layout>
    <section class="bg-white py-8 antialiased md:py-16">
        <div class="mx-auto max-w-screen-xl px-4 2xl:px-0">
            <h2 class="text-xl font-semibold text-gray-900 sm:text-2xl">Carrinho de Compras</h2>

            <div class="mt-6 sm:mt-8 md:gap-6 lg:flex lg:items-start xl:gap-8">
                <div class="mx-auto w-full flex-none lg:max-w-2xl xl:max-w-4xl">
                    <div class="space-y-6">
                        @foreach($cart->items as $item)
                            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm md:p-6">
                                <div class="space-y-4 md:flex md:items-center md:justify-between md:gap-6 md:space-y-0">
                                    <a href="#" class="shrink-0 md:order-1">
                                        <img class="h-20 w-20 object-cover" src="{{ $item->product->productable->vinylSec->cover_image ?? asset('images/placeholder.jpg') }}" alt="{{ $item->product->productable->title }}">
                                    </a>

                                    <label for="counter-input-{{ $item->id }}" class="sr-only">Escolher quantidade:</label>
                                    <div class="flex items-center justify-between md:order-3 md:justify-end">
                                        <div class="flex items-center">
                                            <button type="button" data-item-id="{{ $item->id }}" class="decrement-button inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-md border border-gray-300 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-100">
                                                <svg class="h-2.5 w-2.5 text-gray-900" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
                                                </svg>
                                            </button>
                                            <input type="text" id="counter-input-{{ $item->id }}" data-item-id="{{ $item->id }}" class="quantity-input w-10 shrink-0 border-0 bg-transparent text-center text-sm font-medium text-gray-900 focus:outline-none focus:ring-0" value="{{ $item->quantity }}" required />
                                            <button type="button" data-item-id="{{ $item->id }}" class="increment-button inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-md border border-gray-300 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-100">
                                                <svg class="h-2.5 w-2.5 text-gray-900" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16" />
                                                </svg>
                                            </button>
                                        </div>
                                        <div class="text-end md:order-4 md:w-32">
                                            <p class="text-base font-bold text-gray-900">R$ {{ number_format($item->product->price * $item->quantity, 2, ',', '.') }}</p>
                                        </div>
                                    </div>

                                    <div class="w-full min-w-0 flex-1 space-y-4 md:order-2 md:max-w-md">
                                        <a href="#" class="text-base font-medium text-gray-900 hover:underline">{{ $item->product->productable->title }}</a>

                                        <div class="flex items-center gap-4">
                                            <form action="{{ route('site.cart.items.destroy', $item->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center text-sm font-medium text-red-600 hover:underline">
                                                    Remover
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mx-auto mt-6 max-w-4xl flex-1 space-y-6 lg:mt-0 lg:w-full">
                    <div class="space-y-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm sm:p-6">
                        <p class="text-xl font-semibold text-gray-900">Resumo do Pedido</p>

                        <div class="space-y-4">
                            <div class="space-y-2">
                                <dl class="flex items-center justify-between gap-4">
                                    <dt class="text-base font-normal text-gray-500">Subtotal</dt>
                                    <dd class="text-base font-medium text-gray-900">R$ {{ number_format($subtotal, 2, ',', '.') }}</dd>
                                </dl>

                                <dl class="flex items-center justify-between gap-4">
                                    <dt class="text-base font-normal text-gray-500">Frete</dt>
                                    <dd class="text-base font-medium text-gray-900">
                                        @if($address || session('shipping_postal_code'))
                                            @php
                                                if ($address) {
                                                    if (is_object($address)) {
                                                        $postalCode = trim($address->zip_code);
                                                    } else {
                                                        $postalCode = trim($address);
                                                    }
                                                } else {
                                                    $postalCode = session('shipping_postal_code');
                                                }
                                            @endphp

                                            @if(isset($shippingOptions) && count($shippingOptions) > 0)
                                                <select name="shipping_option" id="shipping_option" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                                    @foreach($shippingOptions as $option)
                                                        <option value="{{ $option['id'] }}">
                                                            {{ $option['name'] }} - {{ $option['delivery_time'] }} dias - R$ {{ number_format($option['price'], 2, ',', '.') }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @else
                                                <p class="text-sm text-gray-500">Calculando frete...</p>
                                            @endif

                                            <p class="mt-2 text-sm text-gray-600">
                                                Entrega para:
                                                @if(is_object($address))
                                                    {{ $address->street }}, {{ $address->number }} - {{ $address->city }}/{{ $address->state }}
                                                @else
                                                    CEP: {{ $postalCode }}
                                                @endif
                                            </p>
                                        @else
                                            <button type="button" onclick="openAddressModal()" class="text-blue-600 hover:underline">
                                                Adicionar endereço para calcular frete
                                            </button>
                                        @endif
                                    </dd>
                                </dl>

                                <dl class="flex items-center justify-between gap-4">
                                    <dt class="text-base font-normal text-gray-500">Impostos</dt>
                                    <dd class="text-base font-medium text-gray-900">R$ {{ number_format($tax, 2, ',', '.') }}</dd>
                                </dl>
                            </div>

                            <dl class="flex items-center justify-between gap-4 border-t border-gray-200 pt-2">
                                <dt class="text-base font-bold text-gray-900">Total</dt>
                                <dd class="text-base font-bold text-gray-900">R$ {{ number_format($total, 2, ',', '.') }}</dd>
                            </dl>
                        </div>

                        <a href="{{ route('site.checkout.index') }}" class="flex w-full items-center justify-center rounded-lg bg-primary-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-primary-800 focus:outline-none focus:ring-4 focus:ring-primary-300">
                            Finalizar Compra
                        </a>

                        <div class="flex items-center justify-center gap-2">
                            <span class="text-sm font-normal text-gray-500">ou</span>
                            <a href="{{ route('site.home') }}" class="inline-flex items-center gap-2 text-sm font-medium text-primary-700 underline hover:no-underline">
                                Continuar Comprando
                                <svg class="h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12H5m14 0-4 4m4-4-4-4" />
                                </svg>
                            </a>
                        </div>
                    </div>

                    <div class="space-y-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm sm:p-6">
                        <form class="space-y-4" action="{{ route('site.cart.updatePostalCode') }}" method="POST">
                            @csrf
                            <div>
                                <label for="postal_code" class="mb-2 block text-sm font-medium text-gray-900">Digite seu CEP</label>
                                <input type="text" id="postal_code" name="postal_code" class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500" placeholder="00000-000" required />
                            </div>
                            <button type="submit" class="flex w-full items-center justify-center rounded-lg bg-primary-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-primary-800 focus:outline-none focus:ring-4 focus:ring-primary-300">
                                Calcular Frete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @include('profile.partials.address-modal')
    </section>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const quantityInputs = document.querySelectorAll('.quantity-input');
            const incrementButtons = document.querySelectorAll('.increment-button');
            const decrementButtons = document.querySelectorAll('.decrement-button');

            function updateQuantity(itemId, newQuantity) {
                fetch(`/cart/update/${itemId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ quantity: newQuantity })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    }
                });
            }

            quantityInputs.forEach(input => {
                input.addEventListener('change', function() {
                    const itemId = this.dataset.itemId;
                    const newQuantity = parseInt(this.value);
                    if (newQuantity > 0) {
                        updateQuantity(itemId, newQuantity);
                    }
                });
            });

            incrementButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const itemId = this.dataset.itemId;
                    const input = document.querySelector(`#counter-input-${itemId}`);
                    const newQuantity = parseInt(input.value) + 1;
                    input.value = newQuantity;
                    updateQuantity(itemId, newQuantity);
                });
            });

            decrementButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const itemId = this.dataset.itemId;
                    const input = document.querySelector(`#counter-input-${itemId}`);
                    const newQuantity = Math.max(1, parseInt(input.value) - 1);
                    input.value = newQuantity;
                    updateQuantity(itemId, newQuantity);
                });
            });

            // Add shipping option change handler
            const shippingSelect = document.getElementById('shipping_option');
            if (shippingSelect) {
                shippingSelect.addEventListener('change', function() {
                    fetch('{{ route("site.cart.updateShipping") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            shipping_option: this.value
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.reload();
                        }
                    });
                });
            }
        });
    </script>
    @endpush
</x-app-layout>

