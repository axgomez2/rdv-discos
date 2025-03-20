<x-app-layout>
    <div class="py-12 bg-gray-100">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ __('DJ Playlists') }}</h1>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    {{ __('Discover unique playlists curated by our talented DJs and music professionals.') }}
                </p>
            </div>

            <!-- Playlists Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($playlists as $playlist)
                    <x-site.playlist-card :playlist="$playlist" />
                @empty
                    <div class="col-span-full text-center py-12">
                        <div class="text-gray-500">
                            <i class="fas fa-music text-4xl mb-4"></i>
                            <p class="text-xl">{{ __('No playlists available at the moment.') }}</p>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if ($playlists->hasPages())
                <div class="mt-8">
                    {{ $playlists->links() }}
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        // Adicione aqui qualquer JavaScript específico da página se necessário
    </script>
    @endpush
</x-app-layout>
