@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Política de Privacidade</h1>

        <div class="prose prose-lg max-w-none">
            <p class="mb-4">Última atualização: {{ date('d/m/Y') }}</p>

            <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">1. Introdução</h2>
            <p class="mb-4">A Embaixada da Dance Music está comprometida em proteger sua privacidade. Esta Política de Privacidade explica como coletamos, usamos e protegemos suas informações pessoais.</p>

            <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">2. Informações que Coletamos</h2>
            <p class="mb-4">Coletamos as seguintes informações:</p>
            <ul class="list-disc pl-6 mb-4">
                <li>Nome completo</li>
                <li>Endereço de e-mail</li>
                <li>Número de telefone</li>
                <li>CPF</li>
                <li>Endereço de entrega</li>
                <li>Informações de pagamento</li>
                <li>Histórico de compras</li>
            </ul>

            <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">3. Como Usamos suas Informações</h2>
            <p class="mb-4">Utilizamos suas informações para:</p>
            <ul class="list-disc pl-6 mb-4">
                <li>Processar seus pedidos</li>
                <li>Enviar atualizações sobre seus pedidos</li>
                <li>Melhorar nossos produtos e serviços</li>
                <li>Enviar comunicações de marketing (com seu consentimento)</li>
                <li>Cumprir obrigações legais</li>
            </ul>

            <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">4. Compartilhamento de Informações</h2>
            <p class="mb-4">Compartilhamos suas informações apenas com:</p>
            <ul class="list-disc pl-6 mb-4">
                <li>Processadores de pagamento</li>
                <li>Serviços de entrega</li>
                <li>Serviços de análise</li>
                <li>Autoridades legais quando exigido por lei</li>
            </ul>

            <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">5. Segurança</h2>
            <p class="mb-4">Implementamos medidas de segurança técnicas e organizacionais para proteger suas informações.</p>

            <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">6. Seus Direitos</h2>
            <p class="mb-4">Você tem direito a:</p>
            <ul class="list-disc pl-6 mb-4">
                <li>Acessar suas informações pessoais</li>
                <li>Corrigir informações incorretas</li>
                <li>Solicitar a exclusão de suas informações</li>
                <li>Retirar seu consentimento</li>
                <li>Receber seus dados em formato portável</li>
            </ul>

            <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">7. Cookies</h2>
            <p class="mb-4">Utilizamos cookies para melhorar sua experiência em nosso site. Você pode controlar os cookies através das configurações do seu navegador.</p>

            <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">8. Contato</h2>
            <p class="mb-4">Para questões sobre esta política ou seus dados pessoais, entre em contato:</p>
            <ul class="list-none pl-6 mb-4">
                <li>Email: privacy@embaixadadancemusic.com.br</li>
                <li>Telefone: (11) 1234-5678</li>
                <li>Endereço: Rua Exemplo, 123 - São Paulo/SP</li>
            </ul>

            <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">9. Atualizações</h2>
            <p class="mb-4">Esta política pode ser atualizada periodicamente. Recomendamos que você revise regularmente para estar ciente de quaisquer alterações.</p>
        </div>
    </div>
</div>
@endsection
