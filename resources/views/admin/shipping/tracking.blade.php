@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Rastreamento do Pedido #{{ $order->id }}</h1>
        <a href="{{ route('admin.shipping.index') }}" class="text-gray-600 hover:text-gray-900">
            Voltar
        </a>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden p-6">
        <div class="mb-6">
            <h2 class="text-lg font-semibold mb-2">Informações do Pedido</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-gray-600">Cliente:</p>
                    <p class="font-medium">{{ $order->user->name }}</p>
                </div>
                <div>
                    <p class="text-gray-600">Código de Rastreio:</p>
                    <p class="font-medium">{{ $order->tracking_code }}</p>
                </div>
            </div>
        </div>

        <div class="mb-6">
            <h2 class="text-lg font-semibold mb-4">Status do Envio</h2>
            <div class="space-y-4">
                @forelse($trackingInfo['events'] ?? [] as $event)
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="h-4 w-4 rounded-full bg-blue-500 mt-2"></div>
                        </div>
                        <div class="ml-4">
                            <p class="font-medium">{{ $event['status'] }}</p>
                            <p class="text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($event['date'])->format('d/m/Y H:i') }}
                                @if(isset($event['location']))
                                    - {{ $event['location'] }}
                                @endif
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="text-gray-500">
                        Nenhuma informação de rastreamento disponível no momento.
                    </div>
                @endforelse
            </div>
        </div>

        <div>
            <h2 class="text-lg font-semibold mb-2">Endereço de Entrega</h2>
            <div class="text-gray-700">
                <p>{{ $order->shippingAddress->street }}, {{ $order->shippingAddress->number }}</p>
                @if($order->shippingAddress->complement)
                    <p>{{ $order->shippingAddress->complement }}</p>
                @endif
                <p>{{ $order->shippingAddress->neighborhood ?? $order->shippingAddress->district }}</p>
                <p>{{ $order->shippingAddress->city }}/{{ $order->shippingAddress->state }}</p>
                <p>CEP: {{ $order->shippingAddress->zip_code }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
