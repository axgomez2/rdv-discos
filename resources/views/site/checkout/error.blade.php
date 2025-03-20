@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-center">
                <svg class="mx-auto h-12 w-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <h2 class="mt-4 text-2xl font-semibold text-gray-900">Erro no Processamento</h2>
                <p class="mt-2 text-gray-600">{{ $error ?? 'Ocorreu um erro ao processar seu pedido.' }}</p>
            </div>

            <div class="mt-8 flex justify-center space-x-4">
                <a href="{{ route('site.checkout.index') }}"
                   class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    Tentar Novamente
                </a>
                <a href="{{ route('site.cart.index') }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    Voltar ao Carrinho
                </a>
            </div>

            @if(isset($details))
                <div class="mt-8 border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Detalhes do Erro</h3>
                    <div class="bg-red-50 border-l-4 border-red-400 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-700">{{ $details }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Support Information -->
            <div class="mt-8 border-t border-gray-200 pt-6">
                <h3 class="text-base font-medium text-gray-900 mb-2">Precisa de Ajuda?</h3>
                <p class="text-sm text-gray-600">
                    Se você continuar tendo problemas, entre em contato com nosso suporte:
                </p>
                <div class="mt-3 flex items-center space-x-4">
                    <a href="mailto:suporte@sasembaixada.com" class="text-sm text-primary-600 hover:text-primary-500">
                        suporte@sasembaixada.com
                    </a>
                    <span class="text-gray-300">|</span>
                    <a href="tel:+551199999999" class="text-sm text-primary-600 hover:text-primary-500">
                        (11) 9999-9999
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
