<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Checkout</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100">
    <!-- Header -->
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <a href="{{ route('site.home') }}" class="flex items-center">
                    <img src="{{ asset('assets/images/logo_embaixada.png') }}" alt="Logo" class="h-12">
                </a>
                <div class="flex items-center space-x-4">
                    @auth
                        <span class="text-sm text-gray-600">{{ Auth::user()->name }}</span>
                    @else
                        <a href="{{ route('login') }}" class="text-sm text-primary-600 hover:text-primary-500">Login</a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Checkout Progress -->
    <div class="bg-white border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <nav class="flex items-center justify-center" aria-label="Progress">
                <ol role="list" class="flex items-center space-x-5 sm:space-x-8">
                    <li>
                        <a href="{{ route('site.cart.index') }}" class="flex items-center">
                            <span class="flex-shrink-0 w-6 h-6 flex items-center justify-center rounded-full {{ Route::is('site.cart.*') ? 'bg-primary-600' : 'bg-gray-300' }}">
                                <span class="text-white text-sm">1</span>
                            </span>
                            <span class="ml-2 text-sm font-medium {{ Route::is('site.cart.*') ? 'text-primary-600' : 'text-gray-500' }}">Carrinho</span>
                        </a>
                    </li>

                    <li>
                        <div class="flex items-center">
                            <span class="flex-shrink-0 w-6 h-6 flex items-center justify-center rounded-full {{ Route::is('site.checkout.*') ? 'bg-primary-600' : 'bg-gray-300' }}">
                                <span class="text-white text-sm">2</span>
                            </span>
                            <span class="ml-2 text-sm font-medium {{ Route::is('site.checkout.*') ? 'text-primary-600' : 'text-gray-500' }}">Checkout</span>
                        </div>
                    </li>

                    <li>
                        <div class="flex items-center">
                            <span class="flex-shrink-0 w-6 h-6 flex items-center justify-center rounded-full {{ Route::is('site.orders.*') ? 'bg-primary-600' : 'bg-gray-300' }}">
                                <span class="text-white text-sm">3</span>
                            </span>
                            <span class="ml-2 text-sm font-medium {{ Route::is('site.orders.*') ? 'text-primary-600' : 'text-gray-500' }}">Confirmação</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="text-center text-sm text-gray-500">
                <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Todos os direitos reservados.</p>
                <p class="mt-2">
                    <a href="#" class="text-primary-600 hover:text-primary-500">Termos de Uso</a>
                    <span class="mx-2">|</span>
                    <a href="#" class="text-primary-600 hover:text-primary-500">Política de Privacidade</a>
                </p>
            </div>
        </div>
    </footer>

    <!-- Toast Messages -->
    <div id="toast-container" class="fixed bottom-4 right-4 z-50"></div>

    @stack('scripts')
</body>
</html>
