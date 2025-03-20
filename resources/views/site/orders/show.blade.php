<x-app-layout>
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <x-breadcrumb :items="[
            ['label' => 'Meus Pedidos', 'url' => route('site.orders.index')],
            ['label' => 'Pedido #' . $order->id, 'url' => route('site.orders.show', $order->id)]
        ]" />
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 pb-6 border-b border-gray-200">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Pedido #{{ $order->id }}</h1>
                <p class="text-gray-600">Realizado em {{ $order->created_at->format('d/m/Y \à\s H:i') }}</p>
            </div>
            <div class="mt-4 md:mt-0">
                <span class="px-3 py-1.5 text-sm font-semibold rounded-full
                    @if($order->status == 'pending') bg-yellow-100 text-yellow-800
                    @elseif($order->status == 'processing') bg-blue-100 text-blue-800
                    @elseif($order->status == 'shipped') bg-purple-100 text-purple-800
                    @elseif($order->status == 'delivered') bg-green-100 text-green-800
                    @elseif($order->status == 'cancelled') bg-red-100 text-red-800
                    @else bg-gray-100 text-gray-800
                    @endif">
                    {{ $order->status_label }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2">
                <div class="mb-8">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Itens do Pedido</h2>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="space-y-4">
                            @foreach($order->items as $item)
                                <div class="bg-white p-4 rounded-lg border border-gray-200 flex flex-col sm:flex-row items-start sm:items-center">
                                    <div class="flex-shrink-0 w-20 h-20 bg-gray-200 rounded-md overflow-hidden mb-4 sm:mb-0 sm:mr-4">
                                        <!-- Imagem do produto se disponível -->
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="text-base font-medium text-gray-900">
                                            {{ $item->product->name ?? 'Produto' }}
                                        </h3>
                                        <p class="text-sm text-gray-600 mt-1">
                                            Quantidade: {{ $item->quantity }}
                                        </p>
                                        <p class="text-sm text-gray-600">
                                            Preço unitário: R$ {{ number_format($item->price, 2, ',', '.') }}
                                        </p>
                                    </div>
                                    <div class="font-semibold text-gray-900 mt-4 sm:mt-0">
                                        R$ {{ number_format($item->total, 2, ',', '.') }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="mb-8">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Histórico do Pedido</h2>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <ol class="relative border-l border-gray-200 ml-3">
                            <li class="mb-6 ml-6">
                                <span class="absolute flex items-center justify-center w-6 h-6 bg-blue-100 rounded-full -left-3 ring-8 ring-white">
                                    <svg class="w-3 h-3 text-blue-800" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                    </svg>
                                </span>
                                <h3 class="flex items-center mb-1 text-base font-semibold text-gray-900">Pedido Realizado</h3>
                                <time class="block mb-2 text-sm font-normal leading-none text-gray-500">{{ $order->created_at->format('d/m/Y \à\s H:i') }}</time>
                                <p class="text-sm text-gray-600">Seu pedido foi recebido e está aguardando confirmação de pagamento.</p>
                            </li>

                            @if($order->status != 'pending' && $order->status != 'cancelled')
                                <li class="mb-6 ml-6">
                                    <span class="absolute flex items-center justify-center w-6 h-6 bg-blue-100 rounded-full -left-3 ring-8 ring-white">
                                        <svg class="w-3 h-3 text-blue-800" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                                            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                                        </svg>
                                    </span>
                                    <h3 class="flex items-center mb-1 text-base font-semibold text-gray-900">Pagamento Confirmado</h3>
                                    <time class="block mb-2 text-sm font-normal leading-none text-gray-500">{{ $order->updated_at->format('d/m/Y \à\s H:i') }}</time>
                                    <p class="text-sm text-gray-600">Seu pagamento foi confirmado e seu pedido está sendo processado.</p>
                                </li>
                            @endif

                            @if($order->status == 'shipped' || $order->status == 'delivered')
                                <li class="mb-6 ml-6">
                                    <span class="absolute flex items-center justify-center w-6 h-6 bg-blue-100 rounded-full -left-3 ring-8 ring-white">
                                        <svg class="w-3 h-3 text-blue-800" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"></path>
                                            <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1v-1h5a1 1 0 001-1v-3a1 1 0 00-.293-.707l-2-2A1 1 0 0014 7h-1V5a1 1 0 00-1-1H3z"></path>
                                        </svg>
                                    </span>
                                    <h3 class="flex items-center mb-1 text-base font-semibold text-gray-900">Pedido Enviado</h3>
                                    <time class="block mb-2 text-sm font-normal leading-none text-gray-500">{{ $order->updated_at->format('d/m/Y \à\s H:i') }}</time>
                                    <p class="text-sm text-gray-600">Seu pedido foi enviado e está a caminho.</p>
                                </li>
                            @endif

                            @if($order->status == 'delivered')
                                <li class="ml-6">
                                    <span class="absolute flex items-center justify-center w-6 h-6 bg-green-100 rounded-full -left-3 ring-8 ring-white">
                                        <svg class="w-3 h-3 text-green-800" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </span>
                                    <h3 class="flex items-center mb-1 text-base font-semibold text-gray-900">Pedido Entregue</h3>
                                    <time class="block mb-2 text-sm font-normal leading-none text-gray-500">{{ $order->updated_at->format('d/m/Y \à\s H:i') }}</time>
                                    <p class="text-sm text-gray-600">Seu pedido foi entregue com sucesso. Aproveite!</p>
                                </li>
                            @endif

                            @if($order->status == 'cancelled')
                                <li class="ml-6">
                                    <span class="absolute flex items-center justify-center w-6 h-6 bg-red-100 rounded-full -left-3 ring-8 ring-white">
                                        <svg class="w-3 h-3 text-red-800" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    </span>
                                    <h3 class="flex items-center mb-1 text-base font-semibold text-gray-900">Pedido Cancelado</h3>
                                    <time class="block mb-2 text-sm font-normal leading-none text-gray-500">{{ $order->updated_at->format('d/m/Y \à\s H:i') }}</time>
                                    <p class="text-sm text-gray-600">Seu pedido foi cancelado.</p>
                                </li>
                            @endif
                        </ol>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-1">
                <div class="space-y-6">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Resumo do Pedido</h2>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subtotal</span>
                                <span class="text-gray-900">R$ {{ number_format($order->total - $order->shipping_cost - $order->tax, 2, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Frete</span>
                                <span class="text-gray-900">R$ {{ number_format($order->shipping_cost, 2, ',', '.') }}</span>
                            </div>
                            @if($order->tax > 0)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Impostos</span>
                                    <span class="text-gray-900">R$ {{ number_format($order->tax, 2, ',', '.') }}</span>
                                </div>
                            @endif
                            <div class="pt-2 mt-2 border-t border-gray-200">
                                <div class="flex justify-between font-semibold">
                                    <span class="text-gray-900">Total</span>
                                    <span class="text-gray-900">R$ {{ number_format($order->total, 2, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Informações de Pagamento</h2>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Método</span>
                                <span class="text-gray-900">{{ $order->payment_method_label }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Status</span>
                                <span class="text-gray-900">{{ $order->payment_status_label }}</span>
                            </div>
                            @if($order->transaction_code)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Código da Transação</span>
                                    <span class="text-gray-900 break-all">{{ $order->transaction_code }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Endereço de Entrega</h2>
                        @if($order->shippingAddress)
                            <div class="space-y-1">
                                <p class="text-gray-900">{{ $order->shippingAddress->street }}, {{ $order->shippingAddress->number }}</p>
                                @if($order->shippingAddress->complement)
                                    <p class="text-gray-900">{{ $order->shippingAddress->complement }}</p>
                                @endif
                                <p class="text-gray-900">{{ $order->shippingAddress->neighborhood }}</p>
                                <p class="text-gray-900">{{ $order->shippingAddress->city }}/{{ $order->shippingAddress->state }}</p>
                                <p class="text-gray-900">CEP: {{ $order->shippingAddress->zipcode }}</p>
                            </div>
                        @else
                            <p class="text-gray-600">Endereço não disponível</p>
                        @endif
                    </div>

                    @if($order->billingAddress && $order->billingAddress->id != $order->shippingAddress->id)
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Endereço de Cobrança</h2>
                            <div class="space-y-1">
                                <p class="text-gray-900">{{ $order->billingAddress->street }}, {{ $order->billingAddress->number }}</p>
                                @if($order->billingAddress->complement)
                                    <p class="text-gray-900">{{ $order->billingAddress->complement }}</p>
                                @endif
                                <p class="text-gray-900">{{ $order->billingAddress->neighborhood }}</p>
                                <p class="text-gray-900">{{ $order->billingAddress->city }}/{{ $order->billingAddress->state }}</p>
                                <p class="text-gray-900">CEP: {{ $order->billingAddress->zipcode }}</p>
                            </div>
                        </div>
                    @endif

                    @if($order->notes)
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Observações</h2>
                            <p class="text-gray-900">{{ $order->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="mt-8 pt-6 border-t border-gray-200">
            <div class="flex justify-between">
                <a href="{{ route('site.orders.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-gray-700 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Voltar para Meus Pedidos
                </a>

                @if($order->status == 'pending')
                    <button type="button" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Cancelar Pedido
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
</x-app-layout>
