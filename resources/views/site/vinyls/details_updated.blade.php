<x-app-layout>
    <div class="font-sans p-4 bg-gray-100" x-data="{
        isFavorite: {{ $vinyl->inWishlist() ? 'true' : 'false' }},
        currentImage: 0,
        images: [
            '{{ asset('storage/' . $vinyl->cover_image) }}',
        ],
        toggleFavorite() {
            this.isFavorite = !this.isFavorite;
            fetch('/api/toggle-favorite', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                },
                body: JSON.stringify({
                    id: {{ $vinyl->id }},
                    type: 'App\\Models\\VinylMaster'
                })
            });
        },
        nextImage() {
            this.currentImage = (this.currentImage + 1) % this.images.length;
        },
        prevImage() {
            this.currentImage = (this.currentImage - 1 + this.images.length) % this.images.length;
        },
        setImage(index) {
            this.currentImage = index;
        }
    }"
    x-init="
        document.addEventListener('alpine:init', () => {
            Alpine.data('audioPlayer', audioPlayerData);
        });
    "
    >
        <div class="lg:max-w-6xl max-w-xl mx-auto bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="grid items-start grid-cols-1 lg:grid-cols-2 gap-8 max-lg:gap-12 max-sm:gap-8">
                <div class="w-full sticky top-0">
                    <div class="flex flex-col gap-4">
                        <div class="bg-white shadow p-4 mt-6 relative">
                            <img :src="images[currentImage]" alt="{{ $vinyl->title }}"
                                class="w-full aspect-square object-cover object-center" />
                            <button @click="toggleFavorite"
                                    class="absolute top-6 right-6 p-2 bg-white rounded-full shadow-md hover:bg-gray-100 transition-colors duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" :class="{ 'text-red-500 fill-current': isFavorite, 'text-gray-400': !isFavorite }" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                            </button>
                            <button @click="prevImage" class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-white rounded-full p-2 shadow-md hover:bg-gray-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>
                            <button @click="nextImage" class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-white rounded-full p-2 shadow-md hover:bg-gray-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>
                        <div class="bg-white p-2 w-full max-w-full overflow-auto">
                            <div class="flex justify-between flex-row gap-4 shrink-0">
                                <template x-for="(image, index) in images" :key="index">
                                    <img :src="image" :alt="`Product ${index + 1}`"
                                        class="w-16 h-16 aspect-square object-cover object-top cursor-pointer shadow-md"
                                        :class="{ 'border-b-2 border-black': currentImage === index, 'border-b-2 border-transparent': currentImage !== index }"
                                        @click="setImage(index)" />
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="w-full">
                    <div class="mt-8">
                        <h3 class="text-2xl sm:text-3xl font-bold text-gray-800">{{ $vinyl->artists->pluck('name')->implode(', ') }}</h3>
                        <h3 class="text-xl sm:text-2xl text-gray-700 mt-2">{{ $vinyl->title }}</h3>
                        <div class="mt-4 space-y-2">
                            <p class="text-base text-gray-600"><span class="font-semibold">Label:</span> {{ $vinyl->recordLabel->name }}</p>
                            <p class="text-base text-gray-600"><span class="font-semibold">Ano:</span> {{ $vinyl->release_year }}</p>
                            <p class="text-base text-gray-600"><span class="font-semibold">País:</span> {{ $vinyl->country }}</p>
                        </div>

                        <div class="mt-6">
                            <h2 class="text-xl font-semibold mb-2">Gênero:</h2>
                            <div class="flex flex-wrap gap-2">
                                @foreach($vinyl->genres as $genre)
                                    <span class="px-3 py-1 bg-amber-100 text-amber-800 rounded-full text-sm">{{ $genre->name }}</span>
                                @endforeach
                                @foreach($vinyl->styles as $style)
                                    <span class="px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full text-sm">{{ $style->name }}</span>
                                @endforeach
                            </div>
                        </div>
                        <div class="flex items-center flex-wrap gap-2 mt-8">
                            @if($vinyl->vinylSec->is_promotional == 1)
                            <p class="text-gray-500 text-base"><strike>R$ {{ number_format($vinyl->vinylSec->price * 1.2, 2, ',', '.') }}</strike></p>
                            <h4 class="text-stone-800 text-2xl sm:text-3xl font-bold">R$ {{ number_format($vinyl->vinylSec->price, 2, ',', '.') }}</h4>
                            <div class="flex py-1 px-2 bg-amber-600 font-semibold ml-4">
                                <span class="text-white text-sm">oferta</span>
                            </div>
                            @else
                            <h4 class="text-stone-800 text-2xl sm:text-3xl font-bold">R$ {{ number_format($vinyl->vinylSec->price, 2, ',', '.') }}</h4>
                            @endif
                        </div>

                        <div class="mt-8 mr-4 flex flex-wrap gap-4">
                            <button type="button"
                                class="btn btn-outline btn-primary flex-grow"
                                @click="$dispatch('add-to-cart', { id: {{ $vinyl->product->id }}, quantity: 1 })"
                            >
                                Adicinar ao carrinho
                            </button>
                            <button type="button"
                                class="btn btn-primary flex-grow"
                            >
                                Compre agora
                            </button>
                        </div>
                        @if($vinyl->description)
                            <div class="mt-8">
                                <h2 class="text-2xl font-bold mb-4">Descrição</h2>
                                <p class="text-gray-700">{{ $vinyl->description }}</p>
                            </div>
                        @endif

                        <hr class="my-8 border-gray-300" />

                        <h2 class="text-2xl font-bold mb-4">LISTA DE FAIXAS:</h2>
                        <div class="overflow-x-auto">
                            <table class="table w-full">
                                <thead>
                                    <tr>
                                        <th class="text-left">#</th>
                                        <th class="text-left">Faixa</th>
                                        <th class="text-left">Duração</th>
                                        <th class="text-left">##</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($vinyl->tracks as $track)
                                        <tr class="hover:bg-gray-100">
                                            <td>{{ $track->position }}</td>
                                            <td>{{ $track->name }}</td>
                                            <td>{{ $track->duration ?? 'N/A' }}</td>
                                            <td>
                                                @if($track->youtube_url)
                                                <button
                                                onclick="window.audioPlayer.loadTrack({{ json_encode([
                                                    'id' => $track->id,
                                                    'name' => $track->name,
                                                    'artist' => $vinyl->artists->pluck('name')->implode(', '),
                                                    'youtube_url' => $track->youtube_url,
                                                    'cover_url' => asset('storage/' . $vinyl->cover_image),
                                                    'vinyl_title' => $vinyl->title
                                                ]) }})"
                                                class="btn btn-sm btn-circle btn-ghost track-play-button"
                                                title="Play track"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                            @else
                                                <span class="text-gray-500">N/A</span>
                                            @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <hr class="my-8 border-gray-300" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
