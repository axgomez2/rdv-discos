<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste MercadoPago SDK</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Teste de Integração MercadoPago</h1>
        
        <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
            {{ $message }}
        </div>
        
        <div class="mb-6">
            <h2 class="text-lg font-semibold mb-2">Informações do SDK:</h2>
            <div class="bg-gray-100 p-3 rounded">
                <p><strong>Public Key:</strong> {{ $publicKey }}</p>
            </div>
        </div>
        
        <div class="flex flex-col gap-2">
            <a href="{{ url('/test-payment') }}" class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 text-center">
                Testar Criação de Pagamento
            </a>
            <a href="{{ url('/') }}" class="bg-gray-600 text-white py-2 px-4 rounded hover:bg-gray-700 text-center">
                Voltar ao Site
            </a>
        </div>
    </div>
</body>
</html>
