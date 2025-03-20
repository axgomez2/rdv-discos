@extends('layouts.app')

@section('title', 'Falha no Pagamento')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto bg-white p-8 rounded-lg shadow-md">
        <div class="text-center mb-8">
            <div class="inline-block p-4 rounded-full bg-red-100 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-800">Falha no Pagamento</h1>
            <p class="text-gray-600 mt-2">Não foi possível processar o pagamento do seu pedido.</p>
        </div>

        <div class="bg-red-50 p-4 rounded-lg mb-6">
            <p class="text-sm text-red-800">
                Houve um problema com o seu pagamento. Por favor, verifique seus dados e tente novamente ou escolha outro método de pagamento.
            </p>
        </div>

        <div class="border-t border-gray-200 pt-4 mb-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-3">Resumo do Pedido</h2>
            <div class="flex justify-between mb-2">
                <span class="text-gray-600">Número do Pedido:</span>
                <span class="font-medium">{{ $order->id }}</span>
            </div>
            <div class="flex justify-between mb-2">
                <span class="text-gray-600">Total:</span>
                <span class="font-medium">R$ {{ number_format($order->total, 2, ',', '.') }}</span>
            </div>
            <div class="flex justify-between mb-2">
                <span class="text-gray-600">Status:</span>
                <span class="font-medium text-red-600">Falha</span>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row justify-between">
            <a href="{{ route('site.checkout.index') }}" 
                class="inline-block bg-indigo-600 text-white font-medium px-6 py-3 rounded-lg text-center mb-3 sm:mb-0">
                Tentar novamente
            </a>
            <a href="{{ route('site.home') }}" 
                class="inline-block bg-gray-200 text-gray-800 font-medium px-6 py-3 rounded-lg text-center">
                Voltar para a loja
            </a>
        </div>
    </div>
</div>
@endsection
