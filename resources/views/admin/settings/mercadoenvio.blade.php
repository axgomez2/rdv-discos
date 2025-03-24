@extends('admin.layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-3">
            @include('admin.settings.partials.sidebar')
        </div>
        <div class="col-md-9">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Configurações do Mercado Envio</h5>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('admin.settings.mercadoenvio.update') }}" method="POST">
                        @csrf
                        <div class="alert alert-info">
                            <p><i class="fas fa-info-circle"></i> Configure suas credenciais do Mercado Envio abaixo.</p>
                            <p>Para obter suas credenciais, acesse a <a href="https://www.mercadolivre.com.br/developers/" target="_blank">área de desenvolvedores do Mercado Livre</a>.</p>
                        </div>

                        <div class="form-group mb-3">
                            <label for="api_key">API Key</label>
                            <input type="text" name="api_key" id="api_key" class="form-control @error('api_key') is-invalid @enderror" 
                                value="{{ old('api_key', $settings['mercadoenvio_api_key'] ?? '') }}" required>
                            @error('api_key')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Chave de API fornecida pelo Mercado Envio</small>
                        </div>

                        <div class="form-group mb-3">
                            <label for="secret_key">Secret Key</label>
                            <input type="password" name="secret_key" id="secret_key" class="form-control @error('secret_key') is-invalid @enderror" 
                                value="{{ old('secret_key', $settings['mercadoenvio_secret_key'] ?? '') }}" required>
                            @error('secret_key')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Chave secreta fornecida pelo Mercado Envio</small>
                        </div>

                        <div class="form-group mb-3">
                            <label for="seller_id">Seller ID</label>
                            <input type="text" name="seller_id" id="seller_id" class="form-control @error('seller_id') is-invalid @enderror" 
                                value="{{ old('seller_id', $settings['mercadoenvio_seller_id'] ?? '') }}" required>
                            @error('seller_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">ID do vendedor no Mercado Livre</small>
                        </div>

                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" id="sandbox" name="sandbox" 
                                value="1" {{ old('sandbox', $settings['mercadoenvio_sandbox'] ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="sandbox">Modo Sandbox (Ambiente de Testes)</label>
                            <div><small class="text-muted">Ative esta opção para usar o ambiente de testes do Mercado Envio</small></div>
                        </div>

                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" id="enabled" name="enabled" 
                                value="1" {{ old('enabled', $settings['mercadoenvio_enabled'] ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="enabled">Habilitar Mercado Envio</label>
                            <div><small class="text-muted">Ative esta opção para habilitar o Mercado Envio como opção de frete</small></div>
                        </div>

                        <hr>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.store-settings.index') }}" class="btn btn-secondary">Voltar</a>
                            <button type="submit" class="btn btn-primary">Salvar Configurações</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
