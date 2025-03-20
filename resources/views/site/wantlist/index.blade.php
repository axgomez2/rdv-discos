<x-app-layout>
    <div class="py-6 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Breadcrumb -->
            <x-breadcrumb :items="[
                ['label' => 'Lista de Desejos']
            ]" />

            <div class="mt-6">
                <h1 class="text-3xl font-bold text-gray-900 mb-6">Minha Lista de Desejos</h1>

                @if($wantlistItems->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        @foreach($wantlistItems as $item)
                            @if($item->product_type === 'App\\Models\\VinylMaster')
                                <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200 hover:shadow-md transition-shadow duration-300">
                                    <div class="relative">
                                        <img src="{{ $item->product->cover_url ?? asset('images/no-image.jpg') }}" alt="{{ $item->product->title }}" class="w-full h-48 object-cover">
                                        <div class="absolute top-2 right-2">
                                            <form action="{{ route('site.wantlist.remove', $item->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="bg-white rounded-full p-1.5 text-gray-700 hover:text-red-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-4">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Indisponível
                                            </span>
                                        </div>
                                    </div>
                                    <div class="p-4">
                                        <h3 class="text-lg font-semibold text-gray-900 line-clamp-1">{{ $item->product->title }}</h3>
                                        <p class="text-sm text-gray-600 mt-1 line-clamp-1">
                                            {{ $item->product->artists->pluck('name')->join(', ') }}
                                        </p>
                                        <div class="mt-4 flex justify-between items-center">
                                            <span class="text-gray-500 text-sm">Fora de estoque</span>
                                            <a href="{{ route('site.vinyl.show', ['artistSlug' => $item->product->artists->first()->slug ?? 'artista', 'titleSlug' => $item->product->slug]) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                                Ver detalhes
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @elseif($item->product_type === 'App\\Models\\Equipment')
                                <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200 hover:shadow-md transition-shadow duration-300">
                                    <div class="relative">
                                        <img src="{{ $item->product->image_url ?? asset('images/no-image.jpg') }}" alt="{{ $item->product->name }}" class="w-full h-48 object-cover">
                                        <div class="absolute top-2 right-2">
                                            <form action="{{ route('site.wantlist.remove', $item->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="bg-white rounded-full p-1.5 text-gray-700 hover:text-red-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-4">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Indisponível
                                            </span>
                                        </div>
                                    </div>
                                    <div class="p-4">
                                        <h3 class="text-lg font-semibold text-gray-900 line-clamp-1">{{ $item->product->name }}</h3>
                                        <p class="text-sm text-gray-600 mt-1 line-clamp-1">
                                            {{ $item->product->brand }}
                                        </p>
                                        <div class="mt-4 flex justify-between items-center">
                                            <span class="text-gray-500 text-sm">Fora de estoque</span>
                                            <a href="{{ route('site.equipments.show', $item->product->slug) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                                Ver detalhes
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    <div class="mt-8">
                        {{ $wantlistItems->links() }}
                    </div>
                @else
                    <div class="bg-white p-8 rounded-lg shadow-sm text-center">
                        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        <h2 class="text-xl font-semibold text-gray-700 mb-2">Sua lista de desejos está vazia</h2>
                        <p class="text-gray-500 mb-6">Quando um produto que você adicionar aos favoritos estiver fora de estoque, ele aparecerá aqui na sua lista de desejos.</p>
                        <div class="flex justify-center space-x-4">
                            <a href="{{ route('site.vinyls.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                                </svg>
                                Explorar Discos
                            </a>
                            <a href="{{ route('site.equipments.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-gray-700 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path>
                                </svg>
                                Ver Equipamentos
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
