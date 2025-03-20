@props(['playlist'])

<div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
    <a href="{{ route('site.playlists.show', $playlist->slug) }}">
        <div class="relative aspect-video">
            @if($playlist->image)
                <img src="{{ asset('storage/' . $playlist->image) }}" alt="{{ $playlist->name }}" class="w-full h-full object-cover">
            @else
                <div class="w-full h-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                    <i class="fas fa-headphones text-4xl text-gray-400 dark:text-gray-500"></i>
                </div>
            @endif
            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-4">
                <h3 class="text-lg font-bold text-white">{{ $playlist->name }}</h3>
            </div>
        </div>
    </a>
    <div class="p-4">
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">{{ Str::limit($playlist->bio, 100) }}</p>
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <span class="text-sm text-gray-600 dark:text-gray-300">{{ $playlist->tracks->count() }} discos</span>
            </div>
            <a href="{{ route('site.playlists.show', $playlist->slug) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                Ver discos <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        <div class="mt-4">
            <div class="flex space-x-2">
                @if($playlist->instagram_url)
                    <a href="{{ $playlist->instagram_url }}" target="_blank" class="text-gray-500 hover:text-pink-600 dark:text-gray-400 dark:hover:text-pink-400">
                        <i class="fab fa-instagram"></i>
                    </a>
                @endif
                @if($playlist->youtube_url)
                    <a href="{{ $playlist->youtube_url }}" target="_blank" class="text-gray-500 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400">
                        <i class="fab fa-youtube"></i>
                    </a>
                @endif
                @if($playlist->facebook_url)
                    <a href="{{ $playlist->facebook_url }}" target="_blank" class="text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400">
                        <i class="fab fa-facebook"></i>
                    </a>
                @endif
                @if($playlist->soundcloud_url)
                    <a href="{{ $playlist->soundcloud_url }}" target="_blank" class="text-gray-500 hover:text-orange-600 dark:text-gray-400 dark:hover:text-orange-400">
                        <i class="fab fa-soundcloud"></i>
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
