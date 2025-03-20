@props(['shippingOptions'])

<div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
    <h3 class="mb-4 text-lg font-medium text-gray-900">Calcular Frete</h3>

    <form action="{{ route('site.cart.updatePostalCode') }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label for="postal_code" class="block text-sm font-medium text-gray-700">CEP</label>
            <div class="mt-1 flex space-x-2">
                <input type="text" name="postal_code" id="postal_code"
                       value="{{ session('shipping_postal_code') }}"
                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                       placeholder="00000-000"
                       maxlength="8"
                       required>
                <button type="submit"
                        class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                    Calcular
                </button>
            </div>
        </div>

        @if(session('shipping_error'))
            <p class="mt-2 text-sm text-red-600">{{ session('shipping_error') }}</p>
        @endif

        @if(!empty($shippingOptions))
            <div class="mt-4 space-y-3">
                <p class="text-sm font-medium text-gray-900">Opções de Envio:</p>
                @foreach($shippingOptions as $option)
                    <div class="flex items-center justify-between">
                        <label class="flex items-center space-x-3">
                            <input type="radio"
                                   name="shipping_option"
                                   value="{{ $option['name'] }}"
                                   {{ session('selected_shipping') === $option['name'] ? 'checked' : '' }}
                                   class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300"
                                   onchange="updateShipping(this.value, {{ $option['price'] }})">
                            <span class="text-sm text-gray-600">{{ $option['name'] }}</span>
                        </label>
                        <span class="font-medium text-gray-900">R$ {{ number_format($option['price'], 2, ',', '.') }}</span>
                    </div>
                @endforeach

                <script>
                    function updateShipping(option, price) {
                        fetch('{{ route("site.cart.updateShipping") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                shipping_option: option
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Dispatch cart update event
                                document.dispatchEvent(new CustomEvent('cart:updated', {
                                    detail: {
                                        count: data.cartCount,
                                        items: data.cartItems,
                                        total: data.cartTotal + price
                                    }
                                }));
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                        });
                    }
                </script>
            </div>
        @endif
    </form>
</div>
