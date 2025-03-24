@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Configurações do Melhor Envio</h1>
        <a href="{{ route('admin.store-settings.index') }}" class="px-4 py-2 bg-gray-200 rounded-md text-gray-700 hover:bg-gray-300">
            Voltar para configurações da loja
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Sidebar com informações -->
        <div class="lg:col-span-1">
            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-lg font-semibold mb-4 border-b pb-2">Sobre o Melhor Envio</h2>
                <div class="space-y-4 text-gray-700">
                    <p>O Melhor Envio é uma plataforma de gestão de fretes que oferece:</p>
                    <ul class="list-disc pl-5 space-y-1">
                        <li>Cotação automática de fretes</li>
                        <li>Integração com diversas transportadoras</li>
                        <li>Impressão de etiquetas</li>
                        <li>Rastreamento de pacotes</li>
                        <li>Gestão de notas fiscais</li>
                    </ul>
                    
                    <h3 class="font-medium text-lg mt-6 mb-2">Como obter credenciais</h3>
                    <ol class="list-decimal pl-5 space-y-1">
                        <li>Acesse sua <a href="https://melhorenvio.com.br/developers" target="_blank" class="text-blue-600 hover:underline">conta no Melhor Envio</a></li>
                        <li>No canto superior direito, clique em seu avatar e acesse "API"</li>
                        <li>Clique em "Gerar nova aplicação"</li>
                        <li>Preencha as informações necessárias</li>
                        <li>Selecione os escopos: <span class="font-semibold">Shipping, Cart, User, Coupon, Tag</span></li>
                        <li>Clique em "Salvar" e copie as credenciais</li>
                    </ol>
                    
                    <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-md">
                        <h4 class="font-medium">Importante!</h4>
                        <p class="text-sm mt-1">Você deve configurar a URL de callback como:</p>
                        <code class="block mt-1 p-2 bg-gray-100 text-sm rounded">{{ url('/admin/settings/melhorenvio/callback') }}</code>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Formulário de configuração -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow-md rounded-lg p-6">
                <form action="{{ route('admin.settings.melhorenvio.update') }}" method="POST">
                    @csrf
                    
                    <!-- Seção de API -->
                    <div class="mb-6">
                        <h2 class="text-lg font-semibold mb-4 border-b pb-2">Credenciais da API</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="client_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    Client ID <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    name="client_id"
                                    id="client_id"
                                    value="{{ $settings['melhorenvio_client_id'] ?? old('client_id') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    required
                                >
                                @error('client_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="client_secret" class="block text-sm font-medium text-gray-700 mb-1">
                                    Client Secret <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="password"
                                    name="client_secret"
                                    id="client_secret"
                                    value="{{ $settings['melhorenvio_client_secret'] ?? old('client_secret') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    required
                                >
                                @error('client_secret')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <label for="token" class="block text-sm font-medium text-gray-700 mb-1">
                                Token de Acesso
                            </label>
                            <textarea
                                name="token"
                                id="token"
                                rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 font-mono text-sm"
                                placeholder="O token será gerado automaticamente após a autenticação"
                                readonly
                            >{{ $settings['melhorenvio_token'] ?? old('token') }}</textarea>
                            <p class="mt-1 text-sm text-gray-500">Este token é gerado automaticamente após a autorização. Não edite manualmente.</p>
                        </div>
                    </div>
                    
                    <!-- Seção de configurações -->
                    <div class="mb-6">
                        <h2 class="text-lg font-semibold mb-4 border-b pb-2">Configurações Gerais</h2>
                        
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <input
                                    type="checkbox"
                                    name="sandbox"
                                    id="sandbox"
                                    value="1"
                                    {{ (isset($settings['melhorenvio_sandbox']) && $settings['melhorenvio_sandbox'] == 'true') ? 'checked' : '' }}
                                    class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                >
                                <label for="sandbox" class="ml-2 block text-sm text-gray-700">
                                    Modo Sandbox (ambiente de testes)
                                </label>
                            </div>
                            
                            <div class="flex items-center">
                                <input
                                    type="checkbox"
                                    name="enabled"
                                    id="enabled"
                                    value="1"
                                    {{ (isset($settings['melhorenvio_enabled']) && $settings['melhorenvio_enabled'] == 'true') ? 'checked' : '' }}
                                    class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                >
                                <label for="enabled" class="ml-2 block text-sm text-gray-700">
                                    Habilitar Melhor Envio
                                </label>
                            </div>
                            
                            <div class="flex items-center">
                                <input
                                    type="checkbox"
                                    name="auto_approve"
                                    id="auto_approve"
                                    value="1"
                                    {{ (isset($settings['melhorenvio_auto_approve']) && $settings['melhorenvio_auto_approve'] == 'true') ? 'checked' : '' }}
                                    class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                >
                                <label for="auto_approve" class="ml-2 block text-sm text-gray-700">
                                    Aprovar pedidos automaticamente
                                </label>
                            </div>
                            
                            <div class="flex items-center">
                                <input
                                    type="checkbox"
                                    name="insurance"
                                    id="insurance"
                                    value="1"
                                    {{ (isset($settings['melhorenvio_insurance']) && $settings['melhorenvio_insurance'] == 'true') ? 'checked' : '' }}
                                    class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                >
                                <label for="insurance" class="ml-2 block text-sm text-gray-700">
                                    Adicionar seguro automaticamente aos envios
                                </label>
                            </div>
                            
                            <div class="flex items-center">
                                <input
                                    type="checkbox"
                                    name="receipt"
                                    id="receipt"
                                    value="1"
                                    {{ (isset($settings['melhorenvio_receipt']) && $settings['melhorenvio_receipt'] == 'true') ? 'checked' : '' }}
                                    class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                >
                                <label for="receipt" class="ml-2 block text-sm text-gray-700">
                                    Adicionar aviso de recebimento
                                </label>
                            </div>
                            
                            <div class="flex items-center">
                                <input
                                    type="checkbox"
                                    name="own_hand"
                                    id="own_hand"
                                    value="1"
                                    {{ (isset($settings['melhorenvio_own_hand']) && $settings['melhorenvio_own_hand'] == 'true') ? 'checked' : '' }}
                                    class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                >
                                <label for="own_hand" class="ml-2 block text-sm text-gray-700">
                                    Entrega com mãos próprias
                                </label>
                            </div>
                        </div>
                        
                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="timeout" class="block text-sm font-medium text-gray-700 mb-1">
                                    Timeout da API (segundos)
                                </label>
                                <input
                                    type="number"
                                    name="timeout"
                                    id="timeout"
                                    value="{{ $settings['melhorenvio_timeout'] ?? '30' }}"
                                    min="10"
                                    max="60"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                >
                            </div>
                            
                            <div>
                                <label for="additional_days" class="block text-sm font-medium text-gray-700 mb-1">
                                    Dias adicionais para prazo de entrega
                                </label>
                                <input
                                    type="number"
                                    name="additional_days"
                                    id="additional_days"
                                    value="{{ $settings['melhorenvio_additional_days'] ?? '0' }}"
                                    min="0"
                                    max="30"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                >
                                <p class="mt-1 text-xs text-gray-500">Dias extras a adicionar ao prazo informado pelas transportadoras</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Seção de dados do remetente -->
                    <div class="mb-6">
                        <h2 class="text-lg font-semibold mb-4 border-b pb-2">Dados do Remetente</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="from_name" class="block text-sm font-medium text-gray-700 mb-1">
                                    Nome/Razão Social <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    name="from_name"
                                    id="from_name"
                                    value="{{ $settings['melhorenvio_from_name'] ?? old('from_name') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    required
                                >
                            </div>
                            
                            <div>
                                <label for="from_document" class="block text-sm font-medium text-gray-700 mb-1">
                                    CPF/CNPJ <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    name="from_document"
                                    id="from_document"
                                    value="{{ $settings['melhorenvio_from_document'] ?? old('from_document') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    required
                                >
                            </div>
                            
                            <div>
                                <label for="from_phone" class="block text-sm font-medium text-gray-700 mb-1">
                                    Telefone <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    name="from_phone"
                                    id="from_phone"
                                    value="{{ $settings['melhorenvio_from_phone'] ?? old('from_phone') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    required
                                >
                            </div>
                            
                            <div>
                                <label for="from_email" class="block text-sm font-medium text-gray-700 mb-1">
                                    E-mail <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="email"
                                    name="from_email"
                                    id="from_email"
                                    value="{{ $settings['melhorenvio_from_email'] ?? old('from_email') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    required
                                >
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                            <div>
                                <label for="from_postal_code" class="block text-sm font-medium text-gray-700 mb-1">
                                    CEP <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    name="from_postal_code"
                                    id="from_postal_code"
                                    value="{{ $settings['melhorenvio_from_postal_code'] ?? old('from_postal_code') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    required
                                >
                            </div>
                            
                            <div>
                                <label for="from_state" class="block text-sm font-medium text-gray-700 mb-1">
                                    Estado <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    name="from_state"
                                    id="from_state"
                                    value="{{ $settings['melhorenvio_from_state'] ?? old('from_state') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    required
                                >
                            </div>
                            
                            <div>
                                <label for="from_city" class="block text-sm font-medium text-gray-700 mb-1">
                                    Cidade <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    name="from_city"
                                    id="from_city"
                                    value="{{ $settings['melhorenvio_from_city'] ?? old('from_city') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    required
                                >
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div>
                                <label for="from_address" class="block text-sm font-medium text-gray-700 mb-1">
                                    Endereço/Logradouro <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    name="from_address"
                                    id="from_address"
                                    value="{{ $settings['melhorenvio_from_address'] ?? old('from_address') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    required
                                >
                            </div>
                            
                            <div>
                                <label for="from_number" class="block text-sm font-medium text-gray-700 mb-1">
                                    Número <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    name="from_number"
                                    id="from_number"
                                    value="{{ $settings['melhorenvio_from_number'] ?? old('from_number') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    required
                                >
                            </div>
                            
                            <div>
                                <label for="from_complement" class="block text-sm font-medium text-gray-700 mb-1">
                                    Complemento
                                </label>
                                <input
                                    type="text"
                                    name="from_complement"
                                    id="from_complement"
                                    value="{{ $settings['melhorenvio_from_complement'] ?? old('from_complement') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                >
                            </div>
                            
                            <div>
                                <label for="from_district" class="block text-sm font-medium text-gray-700 mb-1">
                                    Bairro <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    name="from_district"
                                    id="from_district"
                                    value="{{ $settings['melhorenvio_from_district'] ?? old('from_district') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    required
                                >
                            </div>
                        </div>
                    </div>
                    
                    <!-- Seção de serviços -->
                    <div class="mb-6">
                        <h2 class="text-lg font-semibold mb-4 border-b pb-2">Serviços Disponíveis</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <h3 class="font-medium mb-2">Correios</h3>
                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <input
                                            type="checkbox"
                                            name="service_pac"
                                            id="service_pac"
                                            value="1"
                                            {{ (isset($settings['melhorenvio_service_pac']) && $settings['melhorenvio_service_pac'] == 'true') ? 'checked' : '' }}
                                            class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                        >
                                        <label for="service_pac" class="ml-2 block text-sm text-gray-700">
                                            PAC
                                        </label>
                                    </div>
                                    
                                    <div class="flex items-center">
                                        <input
                                            type="checkbox"
                                            name="service_sedex"
                                            id="service_sedex"
                                            value="1"
                                            {{ (isset($settings['melhorenvio_service_sedex']) && $settings['melhorenvio_service_sedex'] == 'true') ? 'checked' : '' }}
                                            class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                        >
                                        <label for="service_sedex" class="ml-2 block text-sm text-gray-700">
                                            SEDEX
                                        </label>
                                    </div>
                                    
                                    <div class="flex items-center">
                                        <input
                                            type="checkbox"
                                            name="service_mini"
                                            id="service_mini"
                                            value="1"
                                            {{ (isset($settings['melhorenvio_service_mini']) && $settings['melhorenvio_service_mini'] == 'true') ? 'checked' : '' }}
                                            class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                        >
                                        <label for="service_mini" class="ml-2 block text-sm text-gray-700">
                                            Mini Envios
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <h3 class="font-medium mb-2">Transportadoras</h3>
                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <input
                                            type="checkbox"
                                            name="service_jadlog"
                                            id="service_jadlog"
                                            value="1"
                                            {{ (isset($settings['melhorenvio_service_jadlog']) && $settings['melhorenvio_service_jadlog'] == 'true') ? 'checked' : '' }}
                                            class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                        >
                                        <label for="service_jadlog" class="ml-2 block text-sm text-gray-700">
                                            Jadlog
                                        </label>
                                    </div>
                                    
                                    <div class="flex items-center">
                                        <input
                                            type="checkbox"
                                            name="service_azul"
                                            id="service_azul"
                                            value="1"
                                            {{ (isset($settings['melhorenvio_service_azul']) && $settings['melhorenvio_service_azul'] == 'true') ? 'checked' : '' }}
                                            class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                        >
                                        <label for="service_azul" class="ml-2 block text-sm text-gray-700">
                                            Azul Cargo
                                        </label>
                                    </div>
                                    
                                    <div class="flex items-center">
                                        <input
                                            type="checkbox"
                                            name="service_latam"
                                            id="service_latam"
                                            value="1"
                                            {{ (isset($settings['melhorenvio_service_latam']) && $settings['melhorenvio_service_latam'] == 'true') ? 'checked' : '' }}
                                            class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                        >
                                        <label for="service_latam" class="ml-2 block text-sm text-gray-700">
                                            LATAM Cargo
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-between items-center mt-8">
                        @if(!empty($settings['melhorenvio_client_id']) && !empty($settings['melhorenvio_client_secret']))
                        <button type="button" onclick="authenticateMelhorEnvio()" class="px-5 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Autenticar no Melhor Envio
                        </button>
                        @endif
                        
                        <button type="submit" class="px-5 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                            Salvar Configurações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Função para autenticação no Melhor Envio
    function authenticateMelhorEnvio() {
        const clientId = document.getElementById('client_id').value;
        const redirectUri = "{{ url('/admin/settings/melhorenvio/callback') }}";
        
        if (!clientId) {
            alert('Por favor, preencha o Client ID antes de autenticar.');
            return;
        }
        
        // Configuração dos escopos necessários
        const scopes = [
            'shipping-calculate',
            'shipping-cancel',
            'shipping-checkout',
            'shipping-companies',
            'shipping-generate',
            'shipping-preview',
            'shipping-print',
            'shipping-share',
            'shipping-tracking',
            'cart-read',
            'cart-write',
            'users-read',
            'users-write',
            'webhooks-read',
            'webhooks-write'
        ];
        
        // Construir a URL de autorização
        const authUrl = `https://melhorenvio.com.br/oauth/authorize?client_id=${clientId}&redirect_uri=${encodeURIComponent(redirectUri)}&response_type=code&scope=${scopes.join(' ')}`;
        
        // Abrir a janela de autenticação
        window.open(authUrl, '_blank', 'width=800,height=600');
    }
    
    // Verificar status da autenticação
    document.addEventListener('DOMContentLoaded', function() {
        const token = document.getElementById('token').value;
        const statusElement = document.getElementById('auth_status');
        
        if (token && token.length > 0) {
            if (statusElement) {
                statusElement.textContent = 'Autenticado';
                statusElement.classList.add('bg-green-100', 'text-green-800');
                statusElement.classList.remove('bg-red-100', 'text-red-800');
            }
        } else {
            if (statusElement) {
                statusElement.textContent = 'Não autenticado';
                statusElement.classList.add('bg-red-100', 'text-red-800');
                statusElement.classList.remove('bg-green-100', 'text-green-800');
            }
        }
    });
</script>
@endpush
