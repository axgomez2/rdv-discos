@extends('layouts.site')

@section('title', 'Boleto de Pagamento')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h2 class="h4 mb-0">Pagamento por Boleto</h2>
                </div>

                <div class="card-body">
                    <div class="alert alert-info mb-4">
                        <h4>Instruções</h4>
                        <p>O boleto foi gerado com sucesso. Clique no botão abaixo para visualizá-lo ou imprimi-lo.</p>
                        <ul>
                            <li>O pagamento será confirmado em até 3 dias úteis.</li>
                            <li>O seu pedido será processado somente após a confirmação do pagamento.</li>
                            <li>O boleto tem validade de 3 dias úteis.</li>
                        </ul>
                    </div>

                    <div class="d-grid gap-2">
                        <a href="{{ $boletoUrl }}" target="_blank" class="btn btn-lg btn-success">
                            <i class="fas fa-file-invoice-dollar me-2"></i> Visualizar/Imprimir Boleto
                        </a>
                    </div>

                    <hr>

                    <div class="mt-4">
                        <h4>Resumo do Pedido</h4>
                        <p><strong>Número do Pedido:</strong> #{{ $order->id }}</p>
                        <p><strong>Data:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                        <p><strong>Total:</strong> R$ {{ number_format($order->total, 2, ',', '.') }}</p>
                        <p><strong>Status:</strong> {{ $order->status }}</p>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <a href="{{ route('site.orders.show', $order->id) }}" class="btn btn-primary">
                            <i class="fas fa-eye me-2"></i> Ver Detalhes do Pedido
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
