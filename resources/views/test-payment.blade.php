<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste de Pagamento MercadoPago</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    
    <!-- SDK MercadoPago.js v2 -->
    <script src="https://sdk.mercadopago.com/js/v2"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Teste de Pagamento</h1>
        
        @if(isset($result['success']) && $result['success'])
            <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
                Preferência de pagamento criada com sucesso!
            </div>
            
            <div class="mb-6">
                <h2 class="text-lg font-semibold mb-2">Detalhes:</h2>
                <div class="bg-gray-100 p-3 rounded">
                    <p><strong>Preference ID:</strong> {{ $preferenceId }}</p>
                    <p><strong>Valor:</strong> R$ 99,99</p>
                </div>
            </div>
            
            <div class="mb-6">
                <h2 class="text-lg font-semibold mb-2">Botão de Pagamento:</h2>
                <div id="wallet_container"></div>
            </div>
            
            <div class="mt-4">
                <a href="{{ $result['init_point'] }}" target="_blank" class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 block text-center">
                    Pagar com MercadoPago (Redirect)
                </a>
            </div>
        @else
            <div class="mb-4 p-4 bg-red-100 text-red-800 rounded">
                Erro ao criar preferência de pagamento: 
                {{ $result['message'] ?? 'Erro desconhecido' }}
            </div>
        @endif
        
        <div class="mt-6">
            <a href="{{ url('/test-mercadopago') }}" class="bg-gray-600 text-white py-2 px-4 rounded hover:bg-gray-700 block text-center">
                Voltar
            </a>
        </div>
    </div>
    
    @if(isset($result['success']) && $result['success'])
    <script>
        // Inicializar o SDK do MercadoPago
        const mp = new MercadoPago('{{ $publicKey }}', {
            locale: 'pt-BR'
        });
        
        // Renderizar botão de pagamento
        mp.bricks().create("wallet", "wallet_container", {
            initialization: {
                preferenceId: '{{ $preferenceId }}',
            },
        });
    </script>
    @endif
</body>
</html>
