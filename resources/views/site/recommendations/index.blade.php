<x-app-layout>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6">DJs em Destaque</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($activeDJs as $dj)
                <div class="bg-white shadow-md rounded-lg overflow-hidden">
                    @if($dj->image)
                        <img src="{{ asset('storage/' . $dj->image) }}" alt="{{ $dj->name }}" class="w-full h-48 object-cover">
                    @else
                        <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                            <span class="text-gray-500">Sem imagem</span>
                        </div>
                    @endif
                    <div class="p-4">
                        <h3 class="text-xl font-semibold mb-2">{{ $dj->name }}</h3>
                        <p class="text-gray-600 mb-4">{{ Str::limit($dj->bio, 100) }}</p>
                        <h4 class="text-lg font-semibold mb-2">Recomendações:</h4>
                        <ul class="list-disc list-inside mb-4">
                            @foreach($dj->recommendations->take(3) as $recommendation)
                                <li>{{ $recommendation->title }} - {{ $recommendation->artists->pluck('name')->join(', ') }}</li>
                            @endforeach
                        </ul>
                        <a href="{{ route('site.recommendations.show', $dj->id) }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Ver Todas as Recomendações</a>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-6 text-center">
            <a href="{{ route('site.recommendations.index') }}" class="bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600">Ver Todos os DJs</a>
        </div>
    </div>
</x-app-layout>
