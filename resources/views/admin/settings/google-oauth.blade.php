@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Configurações do Google OAuth</h1>
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
        <form action="{{ route('admin.settings.google-oauth.update') }}" method="POST">
            @csrf
            
            <div class="mb-8">
                <h2 class="text-lg font-semibold mb-4 border-b pb-2">Como obter credenciais do Google OAuth</h2>
                <ol class="list-decimal pl-6 space-y-2 text-gray-700">
                    <li>Acesse o <a href="https://console.developers.google.com/" target="_blank" class="text-blue-600 hover:underline">Google Cloud Console</a></li>
                    <li>Crie um novo projeto ou selecione um existente</li>
                    <li>No menu lateral, acesse "APIs e Serviços" > "Credenciais"</li>
                    <li>Clique em "Criar Credenciais" e selecione "ID do Cliente OAuth"</li>
                    <li>Selecione "Aplicativo da Web" como tipo de aplicativo</li>
                    <li>Adicione o domínio do seu site em "Origens JavaScript autorizadas"</li>
                    <li>Adicione a URL de redirecionamento em "URIs de redirecionamento autorizados" (exemplo: https://seu-site.com.br/auth/google/callback)</li>
                    <li>Clique em "Criar" e copie o "ID do cliente" e "Chave secreta"</li>
                </ol>
            </div>
            
            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label for="client_id" class="block text-sm font-medium text-gray-700 mb-1">
                        ID do Cliente <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="client_id"
                        id="client_id"
                        value="{{ $settings['google_client_id'] ?? old('client_id') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        required
                    >
                    @error('client_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="client_secret" class="block text-sm font-medium text-gray-700 mb-1">
                        Chave Secreta <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="client_secret"
                        id="client_secret"
                        value="{{ $settings['google_client_secret'] ?? old('client_secret') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        required
                    >
                    @error('client_secret')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="redirect" class="block text-sm font-medium text-gray-700 mb-1">
                        URL de Redirecionamento <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="url"
                        name="redirect"
                        id="redirect"
                        value="{{ $settings['google_redirect'] ?? old('redirect', url('/auth/google/callback')) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        required
                    >
                    <p class="text-xs text-gray-500 mt-1">
                        Esta URL deve ser adicionada às URIs de redirecionamento autorizados no console do Google.
                    </p>
                    @error('redirect')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="flex items-center">
                    <input
                        type="checkbox"
                        name="enabled"
                        id="enabled"
                        value="1"
                        {{ (isset($settings['google_enabled']) && $settings['google_enabled'] == 'true') ? 'checked' : '' }}
                        class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                    >
                    <label for="enabled" class="ml-2 block text-sm text-gray-900">
                        Ativar login com Google
                    </label>
                </div>
            </div>
            
            <div class="mt-8 border-t pt-4 flex justify-end">
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
@endsection
