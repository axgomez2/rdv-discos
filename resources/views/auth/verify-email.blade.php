<x-app-layout>
    <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto lg:py-12">
        <div class="w-full bg-white rounded-lg shadow dark:border dark:bg-gray-800 dark:border-gray-700 sm:max-w-md p-6">
            <div class="flex items-center justify-center mb-6">
                <svg class="w-12 h-12 text-blue-600 dark:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
            </div>

            <h2 class="text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white text-center mb-4">
                Verifique seu e-mail
            </h2>

            <div class="mb-4 text-sm text-gray-600 dark:text-gray-400 text-center">
                Obrigado por se cadastrar! Antes de começar, você poderia verificar seu endereço de e-mail clicando no link que acabamos de enviar? Se você não recebeu o e-mail, teremos prazer em enviar outro.
            </div>

            @if (session('status') == 'verification-link-sent')
                <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-700 dark:text-green-400" role="alert">
                    <span class="font-medium">Sucesso!</span> Um novo link de verificação foi enviado para o endereço de e-mail fornecido durante o registro.
                </div>
            @endif

            <div class="mt-6 space-y-4" x-data="{ loading: false }">
                <form method="POST" action="{{ route('verification.send') }}" @submit="loading = true">
                    @csrf
                    <button type="submit" class="w-full text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 flex justify-center items-center" :disabled="loading">
                        <svg x-show="loading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="loading ? 'Enviando...' : 'Reenviar e-mail de verificação'">Reenviar e-mail de verificação</span>
                    </button>
                </form>

                <div class="text-sm font-medium text-gray-500 dark:text-gray-400 text-center">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-blue-600 hover:underline dark:text-blue-500">
                            Sair da conta
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
