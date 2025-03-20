@props(['shippingOptions', 'postalCode' => null])

<div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
    <h3 class="mb-4 text-lg font-medium text-gray-900">Calcular Frete</h3>

    <!-- CEP Input Form -->
    <form action="{{ route('site.cart.updatePostalCode') }}" method="POST" class="space-y-4">
        @csrf
        <div class="space-y-2">
            <label for="postal_code" class="text-sm font-medium text-gray-700">CEP</label>
            <div class="flex space-x-2">
                <input type="text"
                       id="postal_code"
                       name="postal_code"
                       class="flex-grow rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                       placeholder="00000-000"
                       value="{{ $postalCode ?? session('shipping_postal_code') }}"
                       required />
                <button type="submit"
                        class="rounded-md bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                    Calcular
                </button>
            </div>
        </div>
    </form>

    <!-- Error Messages -->
    @if(session('shipping_error'))
        <div class="mt-4 rounded-md bg-red-50 p-4">
            <p class="text-sm text-red-700">{{ session('shipping_error') }}</p>
        </div>
    @endif

    <!-- Shipping Options -->
    @if(isset($shippingOptions) && count($shippingOptions) > 0)
        <div class="mt-4 space-y-3">
            <h4 class="text-sm font-medium text-gray-900">Opções de Envio:</h4>
            @foreach($shippingOptions as $option)
                <div class="flex items-center justify-between rounded-md border border-gray-200 p-3 hover:bg-gray-50 transition-colors duration-150">
                    <div class="flex items-center space-x-3">
                        <input type="radio"
                               name="shipping_option"
                               id="shipping_{{ $option['id'] }}"
                               value="{{ $option['id'] }}"
                               {{ session('selected_shipping_option') == $option['id'] ? 'checked' : '' }}
                               class="h-4 w-4 text-primary-600 focus:ring-primary-500" />
                        <label for="shipping_{{ $option['id'] }}" class="flex flex-col">
                            <span class="text-sm font-medium text-gray-900">{{ $option['name'] }}</span>
                            <span class="text-xs text-gray-500">
                                Entrega em {{ $option['delivery_time'] }} dias úteis
                            </span>
                        </label>
                    </div>
                    <span class="text-sm font-medium text-gray-900">
                        R$ {{ number_format($option['price'], 2, ',', '.') }}
                    </span>
                </div>
            @endforeach
        </div>
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // CEP mask
        const postalCodeInput = document.getElementById('postal_code');
        if (postalCodeInput) {
            postalCodeInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 8) value = value.substr(0, 8);
                if (value.length > 5) {
                    value = value.substr(0, 5) + '-' + value.substr(5);
                }
                e.target.value = value;
            });
        }

        // Shipping option selection
        const shippingOptions = document.querySelectorAll('input[name="shipping_option"]');
        shippingOptions.forEach(option => {
            option.addEventListener('change', function() {
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
                    } else {
                        alert(data.error || 'Erro ao atualizar o frete');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Erro ao atualizar o frete');
                });
            });
        });
    });
</script>
@endpush
