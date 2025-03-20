@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Detalhes da Assinatura</h1>
        <a href="{{ route('admin.subscriptions.index') }}" class="text-gray-600 hover:text-gray-900">
            Voltar
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif 

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
        <div class="p-6">
            <h2 class="text-xl font-semibold mb-4">Informações da Assinatura</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-gray-600">Cliente:</p>
                    <p class="font-medium">{{ $subscription->user->name }}</p>
                    <p class="text-sm text-gray-500">{{ $subscription->user->email }}</p>
                </div>
                <div>
                    <p class="text-gray-600">Pacote:</p>
                    <p class="font-medium">{{ $subscription->package->name }}</p>
                    <p class="text-sm text-gray-500">{{ $subscription->package->formatted_price }}/mês</p>
                </div>
                <div>
                    <p class="text-gray-600">Status:</p>
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                        {{ $subscription->status === 'active' ? 'bg-green-100 text-green-800' :
                           ($subscription->status === 'cancelled' ? 'bg-red-100 text-red-800' :
                           'bg-yellow-100 text-yellow-800') }}">
                        {{ ucfirst($subscription->status) }}
                    </span>
                </div>
                <div>
                    <p class="text-gray-600">Próxima Cobrança:</p>
                    <p class="font-medium">{{ $subscription->next_billing_date ? $subscription->next_billing_date->format('d/m/Y') : 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
        <div class="p-6">
            <h2 class="text-xl font-semibold mb-4">Histórico de Envios</h2>
            @if($subscription->shipments->isEmpty())
                <p class="text-gray-500">Nenhum envio registrado.</p>
            @else
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rastreio</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Custo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($subscription->shipments as $shipment)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $shipment->shipping_date->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        {{ $shipment->status === 'delivered' ? 'bg-green-100 text-green-800' :
                                           ($shipment->status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                           'bg-blue-100 text-blue-800') }}">
                                        {{ ucfirst($shipment->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $shipment->tracking_code ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $shipment->formatted_shipping_cost }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    @if($shipment->shipping_label_url)
                                        <a href="{{ $shipment->shipping_label_url }}"
                                           target="_blank"
                                           class="text-blue-600 hover:text-blue-900 mr-3">
                                            Etiqueta
                                        </a>
                                    @else
                                        <form action="{{ route('admin.subscriptions.generate-label', [$subscription, $shipment]) }}"
                                              method="POST"
                                              class="inline">
                                            @csrf
                                            <button type="submit" class="text-blue-600 hover:text-blue-900">
                                                Gerar Etiqueta
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            @if($subscription->status === 'active')
                <div class="mt-4 pt-4 border-t">
                    <h3 class="text-lg font-medium mb-2">Criar Novo Envio</h3>
                    <form action="{{ route('admin.subscriptions.create-shipment', $subscription) }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="shipping_date" class="block text-sm font-medium text-gray-700">Data de Envio</label>
                                <input type="date"
                                       name="shipping_date"
                                       id="shipping_date"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                       required>
                            </div>
                            <div>
                                <label for="address_id" class="block text-sm font-medium text-gray-700">Endereço</label>
                                <select name="address_id"
                                        id="address_id"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        required>
                                    @foreach($subscription->user->addresses as $address)
                                        <option value="{{ $address->id }}">
                                            {{ $address->street }}, {{ $address->number }} - {{ $address->city }}/{{ $address->state }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mt-4">
                            <button type="submit"
                                    class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Criar Envio
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
