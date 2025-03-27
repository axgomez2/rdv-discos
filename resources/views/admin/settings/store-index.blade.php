@extends('layouts.admin')

@section('content')
<div class="container px-6 mx-auto grid">
    <div class="flex items-center justify-between mb-6">
        <h2 class="my-6 text-2xl font-semibold text-gray-700">
            Configurações da Loja
        </h2>
        <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-gray-200 rounded-md text-gray-700 hover:bg-gray-300">
            Voltar ao Dashboard
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

    <!-- Tabs -->
    <div class="mt-4">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <a href="#" 
                    class="tab-link border-blue-500 text-blue-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm mr-8 active" 
                    data-target="payment-methods">
                    Métodos de Pagamento
                </a>
                <a href="#" 
                    class="tab-link border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm mr-8" 
                    data-target="shipping-methods">
                    Métodos de Envio
                </a>
                <a href="#" 
                    class="tab-link border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm mr-8" 
                    data-target="authentication-methods">
                    Autenticação
                </a>
            </nav>
        </div>
    </div>

    <!-- Tab Content -->
    <div class="tab-content mt-6" id="payment-methods">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- PagSeguro Card -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">PagSeguro</h3>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ isset($settings['pagseguro_enabled']) && $settings['pagseguro_enabled'] == 'true' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ isset($settings['pagseguro_enabled']) && $settings['pagseguro_enabled'] == 'true' ? 'Ativo' : 'Inativo' }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-600 mb-4">
                        Configure o gateway de pagamento PagSeguro para processar pagamentos online.
                    </p>
                    <a href="{{ route('admin.store-settings.pagseguro') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        Configurar
                    </a>
                </div>
            </div>

            <!-- MercadoPago Card -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">MercadoPago</h3>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ isset($settings['mercadopago_enabled']) && $settings['mercadopago_enabled'] == 'true' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ isset($settings['mercadopago_enabled']) && $settings['mercadopago_enabled'] == 'true' ? 'Ativo' : 'Inativo' }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-600 mb-4">
                        Configure o gateway de pagamento MercadoPago para processar pagamentos online.
                    </p>
                    <a href="{{ route('admin.store-settings.mercadopago') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        Configurar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-content mt-6 hidden" id="shipping-methods">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Melhor Envio Card -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Melhor Envio</h3>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ isset($settings['melhorenvio_enabled']) && $settings['melhorenvio_enabled'] == 'true' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ isset($settings['melhorenvio_enabled']) && $settings['melhorenvio_enabled'] == 'true' ? 'Ativo' : 'Inativo' }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-600 mb-4">
                        Configure o serviço Melhor Envio para oferecer frete aos clientes.
                    </p>
                    <a href="{{ route('admin.store-settings.melhorenvio') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        Configurar
                    </a>
                </div>
            </div>

            <!-- Correios Card -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Correios</h3>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ isset($settings['correios_enabled']) && $settings['correios_enabled'] == 'true' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ isset($settings['correios_enabled']) && $settings['correios_enabled'] == 'true' ? 'Ativo' : 'Inativo' }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-600 mb-4">
                        Configure as opções dos Correios para cálculo de frete e rastreamento.
                    </p>
                    <a href="{{ route('admin.store-settings.correios') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        Configurar
                    </a>
                </div>
            </div>

          
        </div>
    </div>

    <div class="tab-content mt-6 hidden" id="authentication-methods">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Google OAuth Card -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Google OAuth</h3>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ isset($settings['google_enabled']) && $settings['google_enabled'] == 'true' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ isset($settings['google_enabled']) && $settings['google_enabled'] == 'true' ? 'Ativo' : 'Inativo' }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-600 mb-4">
                        Configure a autenticação via Google para permitir que usuários façam login com suas contas do Google.
                    </p>
                    <a href="{{ route('admin.store-settings.google-oauth') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        Configurar
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabLinks = document.querySelectorAll('.tab-link');
        const tabContents = document.querySelectorAll('.tab-content');
        
        tabLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Remove active class from all tabs
                tabLinks.forEach(tabLink => {
                    tabLink.classList.remove('active', 'border-blue-500', 'text-blue-600');
                    tabLink.classList.add('border-transparent', 'text-gray-500');
                });
                
                // Add active class to current tab
                this.classList.add('active', 'border-blue-500', 'text-blue-600');
                this.classList.remove('border-transparent', 'text-gray-500');
                
                // Hide all tab contents
                tabContents.forEach(content => {
                    content.classList.add('hidden');
                });
                
                // Show current tab content
                const targetId = this.getAttribute('data-target');
                document.getElementById(targetId).classList.remove('hidden');
            });
        });
    });
</script>
@endpush
@endsection
