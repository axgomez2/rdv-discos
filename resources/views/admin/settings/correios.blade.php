@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Configurações dos Correios</h1>
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
        <form action="{{ route('admin.settings.correios.update') }}" method="POST">
            @csrf
            
            <div class="mb-8">
                <h2 class="text-lg font-semibold mb-4 border-b pb-2">Como obter credenciais dos Correios</h2>
                <ol class="list-decimal pl-6 space-y-2 text-gray-700">
                    <li>Para utilizar a API dos Correios, você precisa ter um contrato com os Correios</li>
                    <li>Entre em contato com os Correios ou acesse o <a href="https://www.correios.com.br/atendimento/para-sua-empresa" target="_blank" class="text-blue-600 hover:underline">Portal dos Correios para Empresas</a></li>
                    <li>Após a contratação, você receberá um usuário, senha e código administrativo (código da empresa)</li>
                    <li>Caso não tenha contrato, preencha apenas usuário e senha para utilizar o cálculo básico de frete</li>
                </ol>
            </div>
            
            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label for="usuario" class="block text-sm font-medium text-gray-700 mb-1">
                        Usuário <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="usuario"
                        id="usuario"
                        value="{{ $settings['correios_usuario'] ?? old('usuario') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        required
                    >
                    @error('usuario')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="senha" class="block text-sm font-medium text-gray-700 mb-1">
                        Senha <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="password"
                        name="senha"
                        id="senha"
                        value="{{ $settings['correios_senha'] ?? old('senha') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        required
                    >
                    @error('senha')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="empresa_codigo" class="block text-sm font-medium text-gray-700 mb-1">
                        Código da Empresa (opcional)
                    </label>
                    <input
                        type="text"
                        name="empresa_codigo"
                        id="empresa_codigo"
                        value="{{ $settings['correios_empresa_codigo'] ?? old('empresa_codigo') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                    <p class="text-xs text-gray-500 mt-1">
                        Informe apenas se você tiver contrato com os Correios.
                    </p>
                    @error('empresa_codigo')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="flex items-center">
                    <input
                        type="checkbox"
                        name="enabled"
                        id="enabled"
                        value="1"
                        {{ (isset($settings['correios_enabled']) && $settings['correios_enabled'] == 'true') ? 'checked' : '' }}
                        class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                    >
                    <label for="enabled" class="ml-2 block text-sm text-gray-900">
                        Ativar integração com Correios
                    </label>
                </div>
            </div>
            
            <div class="mt-8 border-t pt-4 flex justify-end space-x-3">
                <button
                    type="button"
                    onclick="testConnection('correios')"
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
        
        axios.post('{{ route("admin.settings.test-connection") }}', {
                service: service,
                usuario: formData.get('usuario'),
                senha: formData.get('senha'),
                empresa_codigo: formData.get('empresa_codigo')
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
