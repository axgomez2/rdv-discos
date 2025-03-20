<x-app-layout>
    <div class="py-12 bg-gray-100">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header Section with Cover Image -->
            <div class="relative rounded-lg overflow-hidden mb-8 h-64 md:h-96">
                <img src="{{ $playlist->image_url }}" alt="{{ $playlist->name }}" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/50 to-transparent"></div>
                <div class="absolute bottom-0 left-0 right-0 p-6 text-white">
                    <h1 class="text-3xl md:text-4xl font-bold mb-2">{{ $playlist->name }}</h1>
                    <p class="text-lg text-gray-200 line-clamp-2">{{ $playlist->bio }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column - Social Info -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-semibold mb-4">{{ __('Connect') }}</h2>

                        <!-- Social Links -->
                        <div class="space-y-4">
                            @if($playlist->instagram_url)
                            <a href="{{ $playlist->instagram_url }}" target="_blank"
                               class="flex items-center space-x-3 text-gray-700 hover:text-pink-600 transition-colors">
                                <i class="fab fa-instagram text-2xl"></i>
                                <span>Instagram</span>
                            </a>
                            @endif

                            @if($playlist->youtube_url)
                            <a href="{{ $playlist->youtube_url }}" target="_blank"
                               class="flex items-center space-x-3 text-gray-700 hover:text-red-600 transition-colors">
                                <i class="fab fa-youtube text-2xl"></i>
                                <span>YouTube</span>
                            </a>
                            @endif

                            @if($playlist->facebook_url)
                            <a href="{{ $playlist->facebook_url }}" target="_blank"
                               class="flex items-center space-x-3 text-gray-700 hover:text-blue-600 transition-colors">
                                <i class="fab fa-facebook text-2xl"></i>
                                <span>Facebook</span>
                            </a>
                            @endif

                            @if($playlist->soundcloud_url)
                            <a href="{{ $playlist->soundcloud_url }}" target="_blank"
                               class="flex items-center space-x-3 text-gray-700 hover:text-orange-600 transition-colors">
                                <i class="fab fa-soundcloud text-2xl"></i>
                                <span>SoundCloud</span>
                            </a>
                            @endif
                        </div>

                        <!-- Bio Section -->
                        <div class="mt-6">
                            <h3 class="text-lg font-semibold mb-2">{{ __('About') }}</h3>
                            <p class="text-gray-600">{{ $playlist->bio }}</p>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Tracks List -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-semibold mb-6">{{ __('Discos') }}</h2>

                        <div class="space-y-4">
                            @if($playlist->tracks->count() > 0)
                                @foreach($playlist->tracks as $track)
                                <div class="flex items-center space-x-4 p-3 hover:bg-gray-50 rounded-lg transition-colors">
                                    <!-- Vinyl Cover -->
                                    <div class="w-12 h-12 flex-shrink-0">
                                        @if($track->vinylMaster->cover_image)
                                            <img src="{{ asset('storage/' . $track->vinylMaster->cover_image) }}"
                                                alt="{{ $track->vinylMaster->title }}"
                                                class="w-full h-full object-cover rounded">
                                        @else
                                            <div class="w-full h-full bg-gray-200 flex items-center justify-center rounded">
                                                <i class="fas fa-record-vinyl text-gray-400"></i>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Track Info -->
                                    <div class="flex-grow">
                                        <h3 class="font-medium text-gray-900">{{ $track->vinylMaster->title }}</h3>
                                        <p class="text-sm text-gray-500">{{ $track->vinylMaster->artists->pluck('name')->implode(', ') }}</p>
                                    </div>

                                    <!-- Link to Vinyl -->
                                    @if($track->vinylMaster->slug)
                                        <a href="{{ route('site.vinyl.show', ['artistSlug' => $track->vinylMaster->artists->first()->slug ?? 'artist', 'titleSlug' => $track->vinylMaster->slug]) }}"
                                           class="text-blue-600 hover:text-blue-800">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                    @endif
                                </div>
                                @endforeach
                            @else
                                <div class="py-8 text-center">
                                    <div class="mb-4">
                                        <i class="fas fa-record-vinyl text-gray-400 text-5xl"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhum disco cadastrado</h3>
                                    <p class="text-gray-500">Este DJ ainda não tem discos cadastrados em sua playlist.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
