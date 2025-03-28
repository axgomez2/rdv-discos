@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Configurações do PagSeguro</h1>
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
        <form action="{{ route('admin.store-settings.pagseguro.update') }}" method="POST">
            @csrf
            
            <div class="mb-8">
                <h2 class="text-lg font-semibold mb-4 border-b pb-2">Como obter credenciais do PagSeguro</h2>
                <ol class="list-decimal pl-6 space-y-2 text-gray-700">
                    <li>Acesse sua <a href="https://acesso.pagseguro.uol.com.br/" target="_blank" class="text-blue-600 hover:underline">conta do PagSeguro</a></li>
                    <li>No menu superior, acesse "Venda Online" > "Integrações"</li>
                    <li>Acesse "Vendas via API" ou "Checkout Transparente" (dependendo da integração desejada)</li>
                    <li>No menu lateral, acesse "Credenciais"</li>
                    <li>Copie o "Email de cadastro" e "Token"</li>
                    <li>Para ambiente sandbox, visite <a href="https://sandbox.pagseguro.uol.com.br/" target="_blank" class="text-blue-600 hover:underline">https://sandbox.pagseguro.uol.com.br/</a></li>
                </ol>
            </div>
            
            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        Email da Conta PagSeguro <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        value="{{ $settings['pagseguro_email'] ?? old('email') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        required
                    >
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="token" class="block text-sm font-medium text-gray-700 mb-1">
                        Token <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="token"
                        id="token"
                        value="{{ $settings['pagseguro_token'] ?? old('token') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        required
                    >
                    @error('token')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="flex items-center">
                    <input
                        type="checkbox"
                        name="sandbox"
                        id="sandbox"
                        value="1"
                        {{ (isset($settings['pagseguro_sandbox']) && $settings['pagseguro_sandbox'] == 'true') ? 'checked' : '' }}
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
                        {{ (isset($settings['pagseguro_enabled']) && $settings['pagseguro_enabled'] == 'true') ? 'checked' : '' }}
                        class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                    >
                    <label for="enabled" class="ml-2 block text-sm text-gray-900">
                        Ativar pagamentos via PagSeguro
                    </label>
                </div>
            </div>
            
            <div class="mt-8 border-t pt-4 flex justify-end space-x-3">
                <button
                    type="button"
                    onclick="testConnection('pagseguro')"
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
                email: formData.get('email'),
                token: formData.get('token'),
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
