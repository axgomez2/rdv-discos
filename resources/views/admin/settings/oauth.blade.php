@extends('admin.layouts.app')

@section('title', 'Configurações de Autenticação OAuth')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Google OAuth</h4>
                    <span id="google-oauth-status" class="badge {{ $googleSettings['enabled'] ? 'badge-success' : 'badge-danger' }}" style="margin-left: 10px;">
                        {{ $googleSettings['enabled'] ? 'Ativo' : 'Inativo' }}
                    </span>
                </div>
                <div class="card-body">
                    @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                    @endif

                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('admin.store-settings.oauth.google.save') }}" method="POST">
                        @csrf
                        
                        <div class="form-group row mb-4">
                            <div class="col-md-12">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="enabled" name="enabled" {{ $googleSettings['enabled'] ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="enabled">Ativar autenticação com Google</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row mb-4">
                            <label class="col-form-label col-md-3">Client ID <span class="text-danger">*</span></label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" name="client_id" value="{{ old('client_id', $googleSettings['client_id']) }}" required>
                                <small class="form-text text-muted">
                                    Obtenha no <a href="https://console.cloud.google.com/apis/credentials" target="_blank">Google Cloud Console</a>
                                </small>
                            </div>
                        </div>

                        <div class="form-group row mb-4">
                            <label class="col-form-label col-md-3">Client Secret <span class="text-danger">*</span></label>
                            <div class="col-md-9">
                                <input type="password" class="form-control" name="client_secret" value="{{ old('client_secret', $googleSettings['client_secret']) }}" required>
                                <small class="form-text text-muted">Esta informação é sensível e será armazenada de forma criptografada.</small>
                            </div>
                        </div>

                        <div class="form-group row mb-4">
                            <label class="col-form-label col-md-3">URL de Redirecionamento <span class="text-danger">*</span></label>
                            <div class="col-md-9">
                                <input type="url" class="form-control" name="redirect" value="{{ old('redirect', $googleSettings['redirect']) }}" required>
                                <small class="form-text text-muted">
                                    Configure exatamente esta URL no console do Google: <code>{{ route('auth.google.callback') }}</code>
                                </small>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-9 offset-md-3">
                                <button type="submit" class="btn btn-primary">Salvar Configurações</button>
                                <button type="button" class="btn btn-info ml-2" id="test-connection">Testar Conexão</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Instruções de Configuração do Google OAuth</h4>
                </div>
                <div class="card-body">
                    <ol class="mb-0">
                        <li class="mb-2">Acesse o <a href="https://console.cloud.google.com/" target="_blank">Google Cloud Console</a></li>
                        <li class="mb-2">Crie um novo projeto ou selecione um existente</li>
                        <li class="mb-2">No menu lateral, vá para "APIs & Services" > "Credentials"</li>
                        <li class="mb-2">Clique em "Create Credentials" > "OAuth client ID"</li>
                        <li class="mb-2">Selecione "Web application" como tipo de aplicação</li>
                        <li class="mb-2">Adicione um nome para identificar seu cliente OAuth</li>
                        <li class="mb-2">Em "Authorized JavaScript origins", adicione a URL base do seu site: <code>{{ url('/') }}</code></li>
                        <li class="mb-2">Em "Authorized redirect URIs", adicione exatamente: <code>{{ route('auth.google.callback') }}</code></li>
                        <li class="mb-2">Clique em "Create" e você receberá o Client ID e Client Secret</li>
                        <li>Não compartilhe o Client Secret com ninguém. Ele deve permanecer confidencial.</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('test-connection').addEventListener('click', function() {
            const button = this;
            const originalText = button.innerHTML;
            const statusBadge = document.getElementById('google-oauth-status');
            
            button.disabled = true;
            button.innerHTML = 'Testando...';
            
            fetch('{{ route("admin.store-settings.oauth.google.test") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Sucesso: ' + data.message);
                        
                        // Atualizar o status na interface
                        if (data.status === 'active') {
                            statusBadge.className = 'badge badge-success';
                            statusBadge.textContent = 'Ativo';
                        } else {
                            statusBadge.className = 'badge badge-danger';
                            statusBadge.textContent = 'Inativo';
                        }
                    } else {
                        alert('Erro: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Erro ao testar a conexão: ' + error);
                })
                .finally(() => {
                    button.disabled = false;
                    button.innerHTML = originalText;
                });
        });
    });
</script>
@endpush
