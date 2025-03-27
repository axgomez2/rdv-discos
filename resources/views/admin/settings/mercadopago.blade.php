@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Configurações do MercadoPago</h1>
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

    <div class="bg-white shadow-md rounded-lg p-6">
        <form action="{{ route('admin.store-settings.mercadopago.update') }}" method="POST">
            @csrf
            
            <div class="mb-8">
                <h2 class="text-lg font-semibold mb-4 border-b pb-2">Como obter credenciais do MercadoPago</h2>
                <ol class="list-decimal pl-6 space-y-2 text-gray-700">
                    <li>Acesse sua <a href="https://www.mercadopago.com.br/developers/panel/app" target="_blank" class="text-blue-600 hover:underline">conta do MercadoPago</a></li>
                    <li>Clique em "Criar aplicação" ou selecione uma aplicação existente</li>
                    <li>Preencha as informações necessárias sobre a sua aplicação</li>
                    <li>Após a criação, acesse as "Credenciais" da aplicação</li>
                    <li>Copie a "Public Key" e o "Access Token" para uso na integração</li>
                    <li>Para ambiente de testes, utilize "Test Public Key" e "Test Access Token"</li>
                </ol>
            </div>
            
            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label for="public_key" class="block text-sm font-medium text-gray-700 mb-1">
                        Public Key <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="public_key"
                        id="public_key"
                        value="{{ $settings['mercadopago_public_key'] ?? old('public_key') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        required
                    >
                    @error('public_key')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="access_token" class="block text-sm font-medium text-gray-700 mb-1">
                        Access Token <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="access_token"
                        id="access_token"
                        value="{{ $settings['mercadopago_access_token'] ?? old('access_token') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        required
                    >
                    @error('access_token')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="flex items-center">
                    <input
                        type="checkbox"
                        name="sandbox"
                        id="sandbox"
                        value="1"
                        {{ (isset($settings['mercadopago_sandbox']) && $settings['mercadopago_sandbox'] == 'true') ? 'checked' : '' }}
                        class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                    >
                    <label for="sandbox" class="ml-2 block text-sm text-gray-900">
                        Usar ambiente de testes (Sandbox)
                    </label>
                </div>
                
                <div class="flex items-center">
                    <input
                        type="checkbox"
                        name="enabled"
                        id="enabled"
                        value="1"
                        {{ (isset($settings['mercadopago_enabled']) && $settings['mercadopago_enabled'] == 'true') ? 'checked' : '' }}
                        class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                    >
                    <label for="enabled" class="ml-2 block text-sm text-gray-900">
                        Ativar pagamentos via MercadoPago
                    </label>
                </div>
            </div>
            
            <div class="mt-8 border-t pt-4 flex justify-end space-x-3">
                <button
                    type="button"
                    onclick="testConnection('mercadopago')"
                    class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
                >
                    Testar Conexão
                </button>
                <button
                    type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                    Salvar Configurações
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function testConnection(service) {
        const form = document.querySelector('form');
        const formData = new FormData(form);
        
        // Mostrar loading
        Swal.fire({
            title: 'Testando conexão...',
            text: 'Por favor, aguarde enquanto testamos a conexão com o serviço.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        axios.post('{{ route("admin.store-settings.test-connection") }}', {
                service: service,
                public_key: formData.get('public_key'),
                access_token: formData.get('access_token'),
                sandbox: formData.get('sandbox') ? true : false
            })
            .then(response => {
                if (response.data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Conexão bem-sucedida!',
                        text: response.data.message
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Falha na conexão',
                        text: response.data.message
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro ao testar conexão',
                    text: 'Ocorreu um erro ao testar a conexão. Verifique as credenciais e tente novamente.'
                });
                console.error(error);
            });
    }
</script>
@endpush
@endsection
