@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-center">
                <svg class="mx-auto h-12 w-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <h2 class="mt-4 text-2xl font-semibold text-gray-900">Pedido Realizado com Sucesso!</h2>
                <p class="mt-2 text-gray-600">Seu pedido #{{ $order->id }} foi confirmado.</p>
            </div>

            <div class="mt-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Detalhes do Pedido</h3>

                <!-- Order Items -->
                <div class="border-t border-gray-200 pt-4">
                    <div class="flow-root">
                        <ul class="-my-6 divide-y divide-gray-200">
                            @foreach($order->items as $item)
                                <li class="py-6 flex">
                                    <div class="flex-shrink-0 w-24 h-24">
                                        <img src="{{ $item->product->productable->vinylSec->cover_image ?? asset('images/placeholder.jpg') }}"
                                             alt="{{ $item->product->name }}"
                                             class="w-full h-full object-cover rounded-md">
                                    </div>
                                    <div class="ml-4 flex-1 flex flex-col">
                                        <div>
                                            <div class="flex justify-between text-base font-medium text-gray-900">
                                                <h4>{{ $item->product->productable->title ?? $item->product->name }}</h4>
                                                <p class="ml-4">R$ {{ number_format($item->price, 2, ',', '.') }}</p>
                                            </div>
                                        </div>
                                        <div class="flex-1 flex items-end justify-between text-sm">
                                            <p class="text-gray-500">Quantidade: {{ $item->quantity }}</p>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="border-t border-gray-200 mt-6 pt-6">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-medium text-gray-900">R$ {{ number_format($order->subtotal, 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm mt-2">
                        <span class="text-gray-600">Frete</span>
                        <span class="font-medium text-gray-900">R$ {{ number_format($order->shipping, 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-base font-medium mt-4 pt-4 border-t border-gray-200">
                        <span class="text-gray-900">Total</span>
                        <span class="text-gray-900">R$ {{ number_format($order->total, 2, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Shipping Information -->
                <div class="border-t border-gray-200 mt-6 pt-6">
                    <h4 class="text-base font-medium text-gray-900 mb-2">Informações de Entrega</h4>
                    <div class="text-sm text-gray-600">
                        <p>{{ $order->address->street }}, {{ $order->address->number }}</p>
                        @if($order->address->complement)
                            <p>{{ $order->address->complement }}</p>
                        @endif
                        <p>{{ $order->address->neighborhood }}</p>
                        <p>{{ $order->address->city }} - {{ $order->address->state }}</p>
                        <p>CEP: {{ $order->address->zip_code }}</p>
                    </div>
                </div>

                <!-- Payment Information -->
                <div class="border-t border-gray-200 mt-6 pt-6">
                    <h4 class="text-base font-medium text-gray-900 mb-2">Método de Pagamento</h4>
                    <p class="text-sm text-gray-600">
                        @switch($order->payment_method)
                            @case('credit_card')
                                Cartão de Crédito
                                @break
                            @case('pix')
                                PIX
                                @break
                            @case('boleto')
                                Boleto Bancário
                                @break
                            @default
                                {{ $order->payment_method }}
                        @endswitch
                    </p>
                </div>

                <!-- Actions -->
                <div class="mt-8 flex justify-center space-x-4">
                    <a href="{{ route('site.orders.show', $order) }}"
                       class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        Ver Detalhes do Pedido
                    </a>
                    <a href="{{ route('site.home') }}"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        Continuar Comprando
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
