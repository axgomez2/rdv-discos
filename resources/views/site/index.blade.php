<x-app-layout>
<div class="bg-yellow-400">
    <div class=" max-w-7xl mx-auto ">
        <x-breadcrumb />
    </div>

    <div class="mt-4 ">
        <div class=" max-w-7xl mx-auto ">
            <div class="">
                <x-vinyl-carousel :vinyls="$slideVinyls" />
            </div>

        </div>
    </div>

</div>



    <!-- Events & Featured Section -->
    <div class="bg-black ">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="mb-8">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h2 class="text-3xl font-bold text-white mb-2">Últimos Discos Adicionados:</h2>
                        <p class="text-gray-100">clique no botão do card para ouvir os discos:</p>
                    </div>
                    <a href="{{ route('site.vinyls.index') }}" class="group inline-flex items-center text-white hover:text-yellow-700 font-medium">
                        Ver todos os discos
                        <i class="fas fa-arrow-right ml-2 transform group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-4 gap-6">
                    @foreach($latestVinyls as $vinyl)
                        @include('components.site.vinyl-card', ['vinyl' => $vinyl])
                    @endforeach
                </div>
            </div>
        </div>
    </div>




            <!-- Featured Playlists -->
            <div class="bg-yellow-400">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    <div id="playlists">
                        <div class="flex items-center justify-between mb-8">
                            <div>
                                <h2 class="text-3xl font-bold text-gray-900 mb-2">Playlists <b class="">RDV</b></h2>
                                <p class="text-gray-900">Recomendações da loja:</p>
                            </div>
                            <a href="{{ route('site.playlists.index') }}"
                            class="group inline-flex items-center text-gray-800 hover:text-black font-medium">
                                Ver todas
                                <i class="fas fa-arrow-right ml-2 transform group-hover:translate-x-1 transition-transform"></i>
                            </a>
                        </div>
                        <div class="grid md:grid-cols-3 gap-8">
                            @forelse($featuredPlaylists as $playlist)
                                <div class="group bg-gray-50 rounded-2xl overflow-hidden hover:bg-gray-100 transition-all duration-300">
                                    <a href="{{ route('site.playlists.show', $playlist->slug) }}">
                                        <div class="relative aspect-video">
                                            @if($playlist->image)
                                                <img src="{{ asset('storage/' . $playlist->image) }}"
                                                    alt="{{ $playlist->name }}"
                                                    class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center">
                                                    <i class="fas fa-headphones text-4xl text-white"></i>
                                                </div>
                                            @endif
                                            <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300">
                                                <div class="transform scale-75 group-hover:scale-100 transition-transform duration-300">
                                                    <div class="bg-white/20 p-5 rounded-full backdrop-blur-sm">
                                                        <i class="fas fa-play text-3xl text-white"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                    <div class="p-6">
                                        <div class="flex items-start justify-between mb-3">
                                            <h3 class="text-xl font-bold text-gray-900">{{ $playlist->name }}</h3>
                                            <span class="bg-blue-100 text-blue-600 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                                {{ $playlist->tracks->count() }} faixas
                                            </span>
                                        </div>
                                        <p class="text-gray-600 mb-4 line-clamp-2">{{ $playlist->bio }}</p>
                                        <a href="{{ route('site.playlists.show', $playlist->slug) }}"
                                        class="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium">
                                            Ver Playlist
                                            <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                                        </a>
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-3 py-12 text-center">
                                    <p class="text-lg text-gray-500">Nenhuma playlist disponível no momento.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>



        <!-- Events & Featured Section -->
        <div class="bg-black ">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="mb-8">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h2 class="text-3xl font-bold text-white mb-2">Ofertas:</h2>
                            <p class="text-gray-100">clique no botão do card para ouvir os discos:</p>
                        </div>
                        <a href="{{ route('site.vinyls.index') }}" class="group inline-flex items-center text-white hover:text-yellow-700 font-medium">
                            Ver todas as promoções:
                            <i class="fas fa-arrow-right ml-2 transform group-hover:translate-x-1 transition-transform"></i>
                        </a>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-4 gap-6">
                        @foreach($latestVinyls as $vinyl)
                            @include('components.site.vinyl-card', ['vinyl' => $vinyl])
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

          <!-- Newsletter Banner -->
     <div class="bg-gradient-to-r from-yellow-500 via-yellow-400 to-yellow-600 relative overflow-hidden">
        <div class="absolute inset-0 bg-[url('/assets/images/pattern.png')] opacity-10"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 relative">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-black font-medium flex items-center">
                    <span class="bg-white/20 p-1.5 rounded-full mr-3">
                        <i class="fas fa-gift text-black"></i>
                    </span>
                    Receba ofertas exclusivas em primeira mão
                </p>
                <form action="#" method="POST" class="flex-shrink-0 flex gap-2">
                    @csrf
                    <div class="relative">
                        <input type="email" name="email"
                               class="w-64 pl-10 pr-4 py-1.5 rounded-full text-sm border-0 focus:ring-2 focus:ring-white/20"
                               placeholder="Seu e-mail" required>
                        <i class="fas fa-envelope absolute left-3.5 top-2 text-gray-400"></i>
                    </div>
                    <button type="submit"
                            class="px-6 py-1.5 bg-white text-blue-600 text-sm font-medium rounded-full hover:bg-blue-50 transition-colors">
                        cadastre-se
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
    function subscribeUser() {
        if (this.email) {
            // Here you would typically send the email to your server
            alert('Inscrito com o email: ' + this.email);
            this.email = ''; // Clear the input after submission
        } else {
            alert('Por favor, insira um email válido.');
        }
    }
    </script>

</x-app-layout>
