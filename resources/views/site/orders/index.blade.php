<x-app-layout>
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <x-breadcrumb :items="[
            ['label' => 'Meus Pedidos', 'url' => route('site.orders.index')]
        ]" />
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Meus Pedidos</h1>

        @if($orders->count() > 0)
            <div class="overflow-x-auto" x-data="{
                openDetails: null,
                toggleDetails(id) {
                    this.openDetails = this.openDetails === id ? null : id;
                }
            }">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3">Pedido</th>
                            <th scope="col" class="px-6 py-3">Data</th>
                            <th scope="col" class="px-6 py-3">Status</th>
                            <th scope="col" class="px-6 py-3">Total</th>
                            <th scope="col" class="px-6 py-3">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr class="bg-white border-b hover:bg-gray-50">
                                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                    #{{ $order->id }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $order->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                                        @if($order->status == 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($order->status == 'processing') bg-blue-100 text-blue-800
                                        @elseif($order->status == 'shipped') bg-purple-100 text-purple-800
                                        @elseif($order->status == 'delivered') bg-green-100 text-green-800
                                        @elseif($order->status == 'cancelled') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ $order->status_label }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    R$ {{ number_format($order->total, 2, ',', '.') }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex space-x-2">
                                        <button @click="toggleDetails({{ $order->id }})" class="font-medium text-blue-600 hover:underline">
                                            <span x-show="openDetails !== {{ $order->id }}">Ver detalhes</span>
                                            <span x-show="openDetails === {{ $order->id }}">Ocultar detalhes</span>
                                        </button>
                                        <a href="{{ route('site.orders.show', $order->id) }}" class="font-medium text-blue-600 hover:underline">
                                            Ver completo
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <tr x-show="openDetails === {{ $order->id }}" x-cloak class="bg-gray-50">
                                <td colspan="5" class="px-6 py-4">
                                    <div class="space-y-4">
                                        <div>
                                            <h3 class="font-semibold text-gray-900 mb-2">Itens do Pedido</h3>
                                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                                @foreach($order->items as $item)
                                                    <div class="bg-white p-3 rounded border border-gray-200 flex items-center space-x-3">
                                                        <div class="flex-shrink-0 w-12 h-12 bg-gray-200 rounded-md overflow-hidden">
                                                            <!-- Imagem do produto se disponível -->
                                                        </div>
                                                        <div class="flex-1 min-w-0">
                                                            <p class="text-sm font-medium text-gray-900 truncate">
                                                                {{ $item->product->name ?? 'Produto' }}
                                                            </p>
                                                            <p class="text-sm text-gray-500">
                                                                {{ $item->quantity }} x R$ {{ number_format($item->price, 2, ',', '.') }}
                                                            </p>
                                                        </div>
                                                        <div class="text-sm font-semibold text-gray-900">
                                                            R$ {{ number_format($item->total, 2, ',', '.') }}
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <h3 class="font-semibold text-gray-900 mb-2">Informações de Pagamento</h3>
                                                <div class="bg-white p-3 rounded border border-gray-200">
                                                    <p class="text-sm text-gray-700"><span class="font-medium">Método:</span> {{ $order->payment_method_label }}</p>
                                                    <p class="text-sm text-gray-700"><span class="font-medium">Status:</span> {{ $order->payment_status_label }}</p>
                                                    @if($order->transaction_code)
                                                        <p class="text-sm text-gray-700"><span class="font-medium">Código da Transação:</span> {{ $order->transaction_code }}</p>
                                                    @endif
                                                </div>
                                            </div>

                                            <div>
                                                <h3 class="font-semibold text-gray-900 mb-2">Endereço de Entrega</h3>
                                                @if($order->shippingAddress)
                                                    <div class="bg-white p-3 rounded border border-gray-200">
                                                        <p class="text-sm text-gray-700">{{ $order->shippingAddress->street }}, {{ $order->shippingAddress->number }}</p>
                                                        @if($order->shippingAddress->complement)
                                                            <p class="text-sm text-gray-700">{{ $order->shippingAddress->complement }}</p>
                                                        @endif
                                                        <p class="text-sm text-gray-700">{{ $order->shippingAddress->neighborhood }} - {{ $order->shippingAddress->city }}/{{ $order->shippingAddress->state }}</p>
                                                        <p class="text-sm text-gray-700">CEP: {{ $order->shippingAddress->zipcode }}</p>
                                                    </div>
                                                @else
                                                    <p class="text-sm text-gray-500">Endereço não disponível</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-4">
                    {{ $orders->links() }}
                </div>
            </div>
        @else
            <div class="text-center py-12">
                <div class="flex justify-center mb-4">
                    <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                </div>
                <h2 class="text-xl font-semibold text-gray-800 mb-2">Você ainda não realizou nenhum pedido</h2>
                <p class="text-gray-600 mb-6">Explore nossa loja e encontre produtos incríveis para adicionar ao seu carrinho.</p>
                <div class="flex justify-center space-x-4">
                    <a href="{{ route('site.vinyls.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                        </svg>
                        Ver Discos
                    </a>
                    <a href="{{ route('site.equipments.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-gray-700 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Ver Equipamentos
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
</x-app-layout>

@push('styles')
<style>
    [x-cloak] { display: none !important; }
</style>
@endpush
