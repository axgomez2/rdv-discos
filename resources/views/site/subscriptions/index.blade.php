@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-center mb-8">Clube de Assinatura de Vinis</h1>

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

    @if($currentSubscription)
        <div class="bg-white shadow-lg rounded-lg p-6 mb-8">
            <h2 class="text-xl font-semibold mb-4">Sua Assinatura Atual</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-gray-600">Pacote: <span class="font-medium">{{ $currentSubscription->package->name }}</span></p>
                    <p class="text-gray-600">Status: <span class="font-medium">{{ $currentSubscription->status }}</span></p>
                    <p class="text-gray-600">Próxima cobrança: <span class="font-medium">{{ $currentSubscription->next_billing_date?->format('d/m/Y') }}</span></p>
                </div>
                <div class="text-right">
                    <form action="{{ route('subscriptions.cancel') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                                class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700"
                                onclick="return confirm('Tem certeza que deseja cancelar sua assinatura?')">
                            Cancelar Assinatura
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($packages as $package)
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-2">{{ $package->name }}</h3>
                    <div class="text-gray-600 mb-4">{{ $package->category }}</div>
                    <div class="text-3xl font-bold mb-4">
                        {{ $package->formatted_price }}<span class="text-lg font-normal">/mês</span>
                    </div>
                    <div class="text-gray-600 mb-6">
                        <p class="mb-2">{{ $package->description }}</p>
                        <p class="font-medium">{{ $package->vinyl_quantity }} vinis por mês</p>
                    </div>

                    @if(!$currentSubscription)
                        <form action="{{ route('subscriptions.subscribe', $package) }}" method="POST">
                            @csrf
                            <button type="submit"
                                    class="w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                                Assinar Agora
                            </button>
                        </form>
                    @else
                        <button class="w-full bg-gray-300 text-gray-600 px-4 py-2 rounded cursor-not-allowed">
                            Você já possui uma assinatura
                        </button>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-12 bg-gray-50 rounded-lg p-6">
        <h2 class="text-2xl font-bold mb-4">Como Funciona</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <h3 class="text-lg font-semibold mb-2">1. Escolha seu Pacote</h3>
                <p class="text-gray-600">Selecione o plano que melhor se adequa ao seu gosto musical e orçamento.</p>
            </div>
            <div>
                <h3 class="text-lg font-semibold mb-2">2. Receba em Casa</h3>
                <p class="text-gray-600">Todo mês você receberá uma seleção cuidadosa de vinis diretamente em sua casa.</p>
            </div>
            <div>
                <h3 class="text-lg font-semibold mb-2">3. Aproveite a Música</h3>
                <p class="text-gray-600">Desfrute de uma experiência musical única com vinis de alta qualidade.</p>
            </div>
        </div>
    </div>
</div>
@endsection
