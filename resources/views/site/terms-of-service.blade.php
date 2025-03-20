@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Termos de Uso</h1>

        <div class="prose prose-lg max-w-none">
            <p class="mb-4">Última atualização: {{ date('d/m/Y') }}</p>

            <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">1. Aceitação dos Termos</h2>
            <p class="mb-4">Ao acessar e usar o site da Embaixada da Dance Music, você concorda com estes termos de uso. Se você não concordar com qualquer parte destes termos, por favor, não use nosso site.</p>

            <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">2. Uso do Site</h2>
            <p class="mb-4">Ao usar nosso site, você concorda em:</p>
            <ul class="list-disc pl-6 mb-4">
                <li>Fornecer informações verdadeiras e precisas</li>
                <li>Manter a confidencialidade de sua conta</li>
                <li>Não usar o site para fins ilegais</li>
                <li>Não interferir com a segurança do site</li>
                <li>Não copiar ou distribuir conteúdo sem autorização</li>
            </ul>

            <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">3. Contas de Usuário</h2>
            <p class="mb-4">Para comprar em nossa loja, você precisa:</p>
            <ul class="list-disc pl-6 mb-4">
                <li>Criar uma conta com informações precisas</li>
                <li>Ter mais de 18 anos ou supervisão de responsável</li>
                <li>Manter suas informações de login seguras</li>
                <li>Ser responsável por todas as atividades em sua conta</li>
            </ul>

            <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">4. Produtos e Serviços</h2>
            <p class="mb-4">Sobre nossos produtos e serviços:</p>
            <ul class="list-disc pl-6 mb-4">
                <li>Nos esforçamos para manter preços e descrições precisos</li>
                <li>Reservamos o direito de limitar quantidades</li>
                <li>Produtos podem estar sujeitos à disponibilidade</li>
                <li>Preços podem ser alterados sem aviso prévio</li>
            </ul>

            <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">5. Pagamentos e Reembolsos</h2>
            <p class="mb-4">Nossa política de pagamentos inclui:</p>
            <ul class="list-disc pl-6 mb-4">
                <li>Aceitamos diversos métodos de pagamento</li>
                <li>Todas as transações são processadas de forma segura</li>
                <li>Reembolsos seguem nossa política específica</li>
                <li>Cancelamentos devem seguir nossas diretrizes</li>
            </ul>

            <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">6. Propriedade Intelectual</h2>
            <p class="mb-4">Todo o conteúdo do site é protegido por direitos autorais e não pode ser usado sem autorização.</p>

            <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">7. Limitação de Responsabilidade</h2>
            <p class="mb-4">Não nos responsabilizamos por:</p>
            <ul class="list-disc pl-6 mb-4">
                <li>Danos indiretos ou consequentes</li>
                <li>Interrupções no serviço</li>
                <li>Ações de terceiros</li>
                <li>Problemas técnicos fora de nosso controle</li>
            </ul>

            <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">8. Modificações dos Termos</h2>
            <p class="mb-4">Reservamos o direito de modificar estes termos a qualquer momento. Alterações entram em vigor imediatamente após publicação.</p>

            <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">9. Contato</h2>
            <p class="mb-4">Para dúvidas sobre estes termos, entre em contato:</p>
            <ul class="list-none pl-6 mb-4">
                <li>Email: legal@embaixadadancemusic.com.br</li>
                <li>Telefone: (11) 1234-5678</li>
                <li>Endereço: Rua Exemplo, 123 - São Paulo/SP</li>
            </ul>
        </div>
    </div>
</div>
@endsection
