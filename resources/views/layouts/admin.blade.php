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
    @include('components/admin/sidebar')

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
