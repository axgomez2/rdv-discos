@extends('layouts.admin')

@section('content')
    <div class="p-4 bg-gray-50 min-h-screen">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Playlists</h1>
                <p class="mt-1 text-sm text-gray-600">Gerencie suas playlists e faixas</p>
            </div>
            <a href="{{ route('admin.playlists.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors duration-150 ease-in-out">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nova Playlist
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($playlists as $playlist)
                <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300 overflow-hidden">
                    <div class="relative pb-48 overflow-hidden">
                        <img class="absolute inset-0 h-full w-full object-cover" 
                             src="{{ $playlist->image_url }}" 
                             alt="{{ $playlist->name }}">
                        <div class="absolute inset-0 bg-black bg-opacity-20"></div>
                        @if($playlist->is_active)
                            <span class="absolute top-4 right-4 inline-flex items-center bg-green-100 text-green-800 text-xs font-medium px-2.5 py-1 rounded-full">
                                <span class="w-2 h-2 mr-1 bg-green-500 rounded-full"></span>
                                Ativa
                            </span>
                        @else
                            <span class="absolute top-4 right-4 inline-flex items-center bg-red-100 text-red-800 text-xs font-medium px-2.5 py-1 rounded-full">
                                <span class="w-2 h-2 mr-1 bg-red-500 rounded-full"></span>
                                Inativa
                            </span>
                        @endif
                    </div>
                    
                    <div class="p-6">
                        <div class="flex items-baseline">
                            <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full uppercase font-semibold tracking-wide">
                                {{ $playlist->quantidadeFaixas() }} faixas
                            </span>
                        </div>

                        <h3 class="mt-4 text-xl font-semibold leading-tight text-gray-900">
                            {{ $playlist->name }}
                        </h3>
                        
                        <p class="mt-2 text-gray-600 text-sm line-clamp-2">
                            {{ $playlist->bio ?? 'Sem descrição disponível.' }}
                        </p>

                        <div class="mt-4 flex items-center space-x-2">
                            @if($playlist->instagram_url)
                                <a href="{{ $playlist->instagram_url }}" target="_blank" class="text-pink-600 hover:text-pink-700">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.897 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.897-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z"/>
                                    </svg>
                                </a>
                            @endif
                            @if($playlist->youtube_url)
                                <a href="{{ $playlist->youtube_url }}" target="_blank" class="text-red-600 hover:text-red-700">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                    </svg>
                                </a>
                            @endif
                            @if($playlist->facebook_url)
                                <a href="{{ $playlist->facebook_url }}" target="_blank" class="text-blue-600 hover:text-blue-700">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                    </svg>
                                </a>
                            @endif
                            @if($playlist->soundcloud_url)
                                <a href="{{ $playlist->soundcloud_url }}" target="_blank" class="text-orange-600 hover:text-orange-700">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M11.56 8.87V17h8.76c1.85 0 3.35-1.5 3.35-3.35 0-1.85-1.5-3.35-3.35-3.35-.22 0-.44.02-.65.06C19.02 6.39 16.86 4 14.38 4c-2.63 0-4.78 2.37-4.78 5.37v.5h1.96zM0 17h2.13V9.76H0V17zm3.37 0h2.13V9.76H3.37V17zm2.25 0h2.13V9.76H5.62V17zm2.25 0H10V9.76H7.87V17z"/>
                                    </svg>
                                </a>
                            @endif
                        </div>

                        <div class="mt-6 flex items-center justify-end space-x-3 border-t pt-4">
                            <a href="{{ route('admin.playlists.edit', $playlist) }}" 
                               class="text-sm font-medium text-primary-600 hover:text-primary-700 transition-colors duration-150">
                                Editar
                            </a>
                            <form action="{{ route('admin.playlists.destroy', $playlist) }}" 
                                  method="POST" 
                                  class="inline-block" 
                                  x-data 
                                  @submit.prevent="if (confirm('Tem certeza que deseja excluir esta playlist?')) $el.submit()">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="text-sm font-medium text-red-600 hover:text-red-700 transition-colors duration-150">
                                    Excluir
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="text-center py-12 bg-white rounded-xl shadow-sm">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhuma playlist encontrada</h3>
                        <p class="mt-1 text-sm text-gray-500">Comece criando uma nova playlist.</p>
                        <div class="mt-6">
                            <a href="{{ route('admin.playlists.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors duration-150 ease-in-out">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Criar Playlist
                            </a>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

        @if ($playlists->hasPages())
            <div class="mt-6">
                {{ $playlists->links() }}
            </div>
        @endif
    </div>
@endsection
