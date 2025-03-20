<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Route URLs -->
    <meta name="complete-vinyl-url" content="{{ route('admin.vinyls.complete', ['id' => ':id']) }}">
    <meta name="store-vinyl-url" content="{{ route('admin.vinyls.store') }}">
    <meta name="vinyl-index-url" content="{{ route('admin.vinyls.index') }}">
    <meta name="vinyl-search-url" content="{{ route('admin.playlists.search-tracks') }}">

    <title>{{ config('app.name', 'Laravel') }} - Admin</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased bg-gray-50">
    <x-flash-messages />

    <!-- Navbar -->
    <nav class="fixed top-0 z-50 w-full bg-white border-b border-gray-200">
        <div class="px-3 py-3 lg:px-5 lg:pl-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center justify-start">
                    <button x-data @click="$dispatch('toggle-sidebar')" type="button" class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg lg:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200">
                        <span class="sr-only">Open sidebar</span>
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path clip-rule="evenodd" fill-rule="evenodd" d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"></path></svg>
                    </button>
                    <a href="{{ route('admin.dashboard') }}" class="flex ml-2 md:mr-24">
                        <span class="self-center text-xl font-semibold sm:text-2xl whitespace-nowrap">{{ config('app.name', 'Laravel') }}</span>
                    </a>
                </div>
                <div class="flex items-center">
                    <div class="relative ml-3" x-data="{ open: false }">
                        <button @click="open = !open" type="button" class="flex text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-gray-300">
                            <span class="sr-only">Open user menu</span>
                            <img class="w-8 h-8 rounded-full" src="https://www.gravatar.com/avatar/{{ md5(Auth::user()->email) }}?s=200&d=mp" alt="{{ Auth::user()->name }}">
                        </button>
                        <div x-show="open" @click.away="open = false" class="absolute right-0 z-50 mt-2 w-48 text-base list-none bg-white rounded divide-y divide-gray-100 shadow">
                            <div class="py-3 px-4">
                                <span class="block text-sm text-gray-900">{{ Auth::user()->name }}</span>
                                <span class="block text-sm font-medium text-gray-500 truncate">{{ Auth::user()->email }}</span>
                            </div>
                            <ul class="py-1">
                                <li>
                                    <a href="{{ route('profile.edit') }}" class="block py-2 px-4 text-sm text-gray-700 hover:bg-gray-100">Perfil</a>
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left py-2 px-4 text-sm text-gray-700 hover:bg-gray-100">Sair</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <aside x-data="{ open: true }"
           @toggle-sidebar.window="open = !open"
           :class="{'translate-x-0': open, '-translate-x-full': !open}"
           class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform bg-white border-r border-gray-200 lg:translate-x-0">
        <div class="h-full px-3 pb-4 overflow-y-auto bg-white">
            
            <ul class="space-y-2 font-medium">
                <!-- Dashboard -->
                <li>
                    <a href="{{ route('admin.dashboard') }}"
                       class="flex items-center p-2 text-gray-900 rounded-lg transition duration-75 group
                              {{ request()->routeIs('admin.dashboard') ? 'bg-gray-100' : 'hover:bg-gray-100' }}">
                        <svg class="w-5 h-5 text-gray-500 transition duration-75 group-hover:text-gray-900"
                             aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                             fill="currentColor" viewBox="0 0 22 21">
                            <path d="M16.975 11H10V4.025a1 1 0 0 0-1.066-.998 8.5 8.5 0 1 0 9.039 9.039.999.999 0 0 0-1-1.066h.002Z"/>
                            <path d="M12.5 0c-.157 0-.311.01-.565.027A1 1 0 0 0 11 1.02V10h8.975a1 1 0 0 0 1-.935c.013-.188.028-.374.028-.565A8.51 8.51 0 0 0 12.5 0Z"/>
                        </svg>
                        <span class="ml-3">Início</span>
                    </a>
                </li>

                <!-- Vinyls -->
                <li>
                    <a href="{{ route('admin.vinyls.index') }}"
                       class="flex items-center p-2 text-gray-900 rounded-lg transition duration-75 group
                              {{ request()->routeIs('admin.vinyls.*') ? 'bg-gray-100' : 'hover:bg-gray-100' }}">
                        <svg class="w-5 h-5 text-gray-500 transition duration-75 group-hover:text-gray-900"
                             aria-hidden="true" fill="none" stroke-linecap="round"
                             stroke-linejoin="round" stroke-width="2"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                        <span class="ml-3">Vinyls</span>
                    </a>
                </li>

                <!-- Playlists -->
                <li>
                    <a href="{{ route('admin.playlists.index') }}"
                       class="flex items-center p-2 text-gray-900 rounded-lg transition duration-75 group
                              {{ request()->routeIs('admin.playlists.*') ? 'bg-gray-100' : 'hover:bg-gray-100' }}">
                        <svg class="w-5 h-5 text-gray-500 transition duration-75 group-hover:text-gray-900"
                             aria-hidden="true" fill="none" stroke-linecap="round"
                             stroke-linejoin="round" stroke-width="2"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                        </svg>
                        <span class="ml-3">Playlists</span>
                    </a>
                </li>

                <!-- Equipments -->
                <li>
                    <a href="{{ route('admin.equipments.index') }}"
                       class="flex items-center p-2 text-gray-900 rounded-lg transition duration-75 group
                              {{ request()->routeIs('admin.equipments.*') ? 'bg-gray-100' : 'hover:bg-gray-100' }}">
                        <svg class="w-5 h-5 text-gray-500 transition duration-75 group-hover:text-gray-900"
                             aria-hidden="true" fill="none" stroke-linecap="round"
                             stroke-linejoin="round" stroke-width="2"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                            <path d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                        </svg>
                        <span class="ml-3">Equipamentos</span>
                    </a>
                </li>

                <!-- Equipments -->
                <li>
                    <a href="{{ route('admin.shipping.index') }}"
                       class="flex items-center p-2 text-gray-900 rounded-lg transition duration-75 group
                              {{ request()->routeIs('admin.equipments.*') ? 'bg-gray-100' : 'hover:bg-gray-100' }}">
                        <svg class="w-5 h-5 text-gray-500 transition duration-75 group-hover:text-gray-900"
                             aria-hidden="true" fill="none" stroke-linecap="round"
                             stroke-linejoin="round" stroke-width="2"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                            <path d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                        </svg>
                        <span class="ml-3">Etiquetas</span>
                    </a>
                </li>

                <!-- Equipments -->
                <li>
                    <a href="{{ route('admin.subscriptions.index') }}"
                       class="flex items-center p-2 text-gray-900 rounded-lg transition duration-75 group
                              {{ request()->routeIs('admin.equipments.*') ? 'bg-gray-100' : 'hover:bg-gray-100' }}">
                        <svg class="w-5 h-5 text-gray-500 transition duration-75 group-hover:text-gray-900"
                             aria-hidden="true" fill="none" stroke-linecap="round"
                             stroke-linejoin="round" stroke-width="2"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                            <path d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                        </svg>
                        <span class="ml-3">clube de compra</span>
                    </a>
                </li>

                <!-- Settings -->
                <li>
                    <a href="{{ route('admin.settings.index') }}"
                       class="flex items-center p-2 text-gray-900 rounded-lg transition duration-75 group
                              {{ request()->routeIs('admin.settings.*') ? 'bg-gray-100' : 'hover:bg-gray-100' }}">
                        <svg class="w-5 h-5 text-gray-500 transition duration-75 group-hover:text-gray-900"
                             aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                             fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm0 2a5 5 0 1 1 0-10 5 5 0 0 1 0 10zm0-15a1 1 0 0 1 1 1v2a1 1 0 0 1-2 0V3a1 1 0 0 1 1-1zm0 16a1 1 0 0 1 1 1v2a1 1 0 0 1-2 0v-2a1 1 0 0 1 1-1zm7-7a1 1 0 0 1-1 1h-2a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1zM3 12a1 1 0 0 1 1-1h2a1 1 0 0 1 0 2H4a1 1 0 0 1-1-1z"/>
                        </svg>
                        <span class="ml-3">Configurações</span>
                    </a>
                </li>

                <!-- Orders -->
                <li>
                    <a href="#"
                       class="flex items-center p-2 text-gray-900 rounded-lg transition duration-75 group
                              {{ request()->routeIs('admin.orders.*') ? 'bg-gray-100' : 'hover:bg-gray-100' }}">
                        <svg class="w-5 h-5 text-gray-500 transition duration-75 group-hover:text-gray-900"
                             aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                             fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 14H4V6h16v12zM6 10h2v2H6v-2zm0 4h8v2h-8v-2zm10 0h2v2h-2v-2zm-6-4h8v2h-8v-2z"/>
                        </svg>
                        <span class="ml-3">Pedidos</span>
                    </a>
                </li>

                <!-- Customers -->
                <li>
                    <a href="{{ route('admin.customers.index') }}"
                       class="flex items-center p-2 text-gray-900 rounded-lg transition duration-75 group
                              {{ request()->routeIs('admin.customers.*') ? 'bg-gray-100' : 'hover:bg-gray-100' }}">
                        <svg class="w-5 h-5 text-gray-500 transition duration-75 group-hover:text-gray-900"
                             aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                             fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/>
                        </svg>
                        <span class="ml-3">Clientes</span>
                    </a>
                </li>

                <!-- Internal Categories -->
                <li>
                    <a href="{{ route('admin.cat-style-shop.index') }}"
                       class="flex items-center p-2 text-gray-900 rounded-lg transition duration-75 group
                              {{ request()->routeIs('admin.cat-style-shop.*') ? 'bg-gray-100' : 'hover:bg-gray-100' }}">
                        <svg class="w-5 h-5 text-gray-500 transition duration-75 group-hover:text-gray-900"
                             aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                             fill="currentColor" viewBox="0 0 24 24">
                            <path d="M10 3H4a1 1 0 0 0-1 1v6a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1zm10 0h-6a1 1 0 0 0-1 1v6a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1zM10 13H4a1 1 0 0 0-1 1v6a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-6a1 1 0 0 0-1-1zm10 0h-6a1 1 0 0 0-1 1v6a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-6a1 1 0 0 0-1-1z"/>
                        </svg>
                        <span class="ml-3">Categorias Internas</span>
                    </a>
                </li>
            </ul>

        </div>
    </aside>

    <!-- Main content -->
    <div class="p-4 lg:ml-64 mt-14">
        <div class="p-4 border-gray-200 rounded-lg">
            @yield('breadcrumb')
            @yield('content')
        </div>
    </div>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    @stack('scripts')
</body>
</html>
