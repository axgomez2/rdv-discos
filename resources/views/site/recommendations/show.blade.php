<x-app-layout>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6">{{ $dj->name }}</h1>

        <div class="mb-8">
            @if($dj->image)
                <img src="{{ asset('storage/' . $dj->image) }}" alt="{{ $dj->name }}" class="w-full max-w-md mx-auto rounded-lg shadow-md">
            @endif
            <p class="mt-4 text-gray-600">{{ $dj->bio }}</p>
            @if($dj->social_media)
                <p class="mt-2 text-blue-500"><a href="{{ $dj->social_media }}" target="_blank" rel="noopener noreferrer">Redes Sociais</a></p>
            @endif
        </div>

        <h2 class="text-2xl font-semibold mb-4">Recomendações</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($recommendations as $vinyl)
                <div class="bg-white shadow-md rounded-lg overflow-hidden">
                    @if($vinyl->image)
                        <img src="{{ asset('storage/' . $vinyl->image) }}" alt="{{ $vinyl->title }}" class="w-full h-48 object-cover">
                    @else
                        <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                            <span class="text-gray-500">Sem imagem</span>
                        </div>
                    @endif
                    {{-- <div class="p-4">
                        <h3 class="text-xl font-semibold mb-2">{{ $vinyl->title }}</h3>
                        <p class="text-gray-600 mb-2">{{ $vinyl->artists->pluck('name')->join(', ') }}</p>
                        {{-- <a href="{{ route('site.vinyl.show', $vinyl) }}" class="text-blue-500 hover:underline">Ver detalhes</a> --}}
                    </div> --}}
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
