<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="@yield('meta_description', 'A LOJA REFERENCIA EM DISCOS DE DANCE MUSIC.')">
    <meta name="keywords" content="@yield('meta_keywords', 'vinyl, records, music, albums, turntable, audiophile')">
    <meta name="author" content="Your Company Name">
    <meta name="robots" content="index, follow">

    <title>@yield('title', 'EMBAIXADA DANCE MUSIC - 30 ANOS DE MERCADO')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Metatags para compartilhamento social -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('og_title', 'A LOJA REFERENCIA EM DISCOS DE DANCE MUSIC.')">
    <meta property="og:description" content="@yield('og_description', 'Discover and purchase high-quality vinyl records from our extensive collection.')">
    <meta property="og:image" content="@yield('og_image', asset('images/og-image.jpg'))">



    <!-- Stylesheets -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Custom CSS -->
    @stack('styles')

    <!-- CSS para o loading screen -->
    <style>
        #loading-screen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            transition: opacity 0.5s ease-out, visibility 0.5s ease-out;
        }

        .hidden-loading {
            opacity: 0;
            visibility: hidden;
        }
    </style>

    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-XXXXX-Y"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());
        gtag('config', 'UA-XXXXX-Y');
    </script>
</head>

<body>
    <!-- Loading Screen -->
    <div id="loading-screen">
        <div role="status" class="flex flex-col items-center">
            <img src="{{ asset('assets/images/logo.png') }}" alt="Embaixada Dance Music" class="h-20 mb-4">
            <div class="flex items-center justify-center">
                <svg aria-hidden="true" class="w-12 h-12 text-gray-300 animate-spin fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
                    <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
                </svg>
                <span class="sr-only">Carregando...</span>
            </div>
            <p class="mt-2 text-gray-700 text-lg">Carregando...</p>
        </div>
    </div>

    <div class="bg-white">
        @include('components.site.nav4')
    </div>

    <!-- Page Content -->
    <main class=" bg-white">
        <div>
            {{ $slot }}
        </div>
    </main>

    <!-- Player e Footer -->
    @include('components.site.audio-player')
    @include('site.footer')

    <!-- Modals -->
    @include('components.site.login-modal')
    @include('components.site.register-modal')

    <!-- Scripts -->
    <script src="https://www.youtube.com/iframe_api"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="{{ asset('assets/js/audio-player.js') }}" defer></script>
    <script src="{{ asset('assets/js/cart.js') }}"></script>
    <script src="{{ asset('assets/js/toast.js') }}" type="module"></script>
    <script src="{{ asset('js/wishlist.js') }}"></script>
    <script src="https://unpkg.com/flowbite@1.6.6/dist/flowbite.js"></script>

    <script>
        function showLoginToast() {
            showToast("Por favor, faça login para continuar", "info");
            // Dispatch event to open login modal
            window.dispatchEvent(new CustomEvent('open-login-modal'));
        }

        // Função para alternar entre mostrar/ocultar senha
        function togglePasswordVisibility(inputId) {
            const passwordInput = document.getElementById(inputId);
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);

            // Alterar o ícone do olho
            const icon = event.currentTarget.querySelector('.password-toggle-icon');
            if (type === 'text') {
                // Mostrar o ícone de olho tachado quando a senha estiver visível
                icon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                `;
            } else {
                // Mostrar o ícone de olho normal quando a senha estiver oculta
                icon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                `;
            }
        }

        // Função para gerar uma senha forte
        function generateStrongPassword() {
            const length = 12; // Comprimento da senha
            const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-+=<>?";
            let password = "";

            // Garante que pelo menos um caractere de cada tipo esteja incluído
            password += charset.match(/[a-z]/)[0]; // Minúscula
            password += charset.match(/[A-Z]/)[0]; // Maiúscula
            password += charset.match(/[0-9]/)[0]; // Número
            password += charset.match(/[^a-zA-Z0-9]/)[0]; // Caractere especial

            // Preenche o resto da senha com caracteres aleatórios
            for (let i = password.length; i < length; i++) {
                password += charset.charAt(Math.floor(Math.random() * charset.length));
            }

            // Embaralha a senha para torná-la mais aleatória
            password = password.split('').sort(() => 0.5 - Math.random()).join('');

            // Define a senha nos campos correspondentes
            document.getElementById("password").value = password;
            document.getElementById("password_confirmation").value = password;

            // Atualiza a indicação de força da senha
            const strengthIndicator = document.getElementById("password-strength");
            strengthIndicator.textContent = "Senha forte";
            strengthIndicator.className = "text-xs text-green-600";

            // Opcional: Tornar a senha visível temporariamente
            document.getElementById("password").type = "text";
            document.getElementById("password_confirmation").type = "text";

            // Volta para o modo senha após 3 segundos
            setTimeout(() => {
                document.getElementById("password").type = "password";
                document.getElementById("password_confirmation").type = "password";
            }, 3000);
        }

        // Script para controlar o loading screen
        document.addEventListener('DOMContentLoaded', function() {
            // Aguarda um pouco para garantir que componentes adicionais sejam carregados
            setTimeout(function() {
                const loadingScreen = document.getElementById('loading-screen');
                if (loadingScreen) {
                    loadingScreen.classList.add('hidden-loading');
                    // Remove o elemento do DOM após a transição
                    setTimeout(function() {
                        loadingScreen.remove();
                        // Depois de remover o loading, disparamos um evento personalizado
                        // para informar que a página está pronta
                        window.dispatchEvent(new CustomEvent('page-fully-loaded'));
                    }, 500);
                }
            }, 800); // Aumentado para 800ms para dar mais tempo aos componentes
        });

        // Fallback: se algo der errado, forçamos a remoção após 5 segundos
        window.addEventListener('load', function() {
            setTimeout(function() {
                const loadingScreen = document.getElementById('loading-screen');
                if (loadingScreen && !loadingScreen.classList.contains('hidden-loading')) {
                    loadingScreen.classList.add('hidden-loading');
                    setTimeout(function() {
                        loadingScreen.remove();
                        window.dispatchEvent(new CustomEvent('page-fully-loaded'));
                    }, 500);
                }
            }, 5000);
        });

        // Função para exibir notificações toast
        window.showToast = function(message, type = 'success') {
            Toastify({
                text: message,
                duration: 3000,
                close: true,
                gravity: "top",
                position: "right",
                stopOnFocus: true,
                className: type === 'error' ? 'bg-red-600' : 'bg-green-600',
                style: {
                    background: type === 'error' ? '#dc2626' : '#16a34a',
                    borderRadius: '8px',
                    boxShadow: '0 4px 6px rgba(0, 0, 0, 0.1)',
                    padding: '12px 20px',
                    fontSize: '14px',
                    fontWeight: '500'
                },
                onClick: function(){}
            }).showToast();
        };

        // Exibir mensagens flash como toasts
        @if (session('success'))
            window.showToast("{{ session('success') }}", 'success');
        @endif

        @if (session('error'))
            window.showToast("{{ session('error') }}", 'error');
        @endif
    </script>

    @stack('scripts')
</body>
</html>
