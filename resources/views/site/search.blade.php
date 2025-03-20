<x-app-layout>
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">Resultados da busca para: "{{ $query }}"</h1>

    @if($results->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($results as $result)
                @if($result instanceof App\Models\VinylMaster)
                    @include('components.site.vinyl-card', ['vinyl' => $result])
                @elseif($result instanceof App\Models\Equipment)
                    @include('components.site.equipment-card', ['equipment' => $result])
                @endif
            @endforeach
        </div>
    @else
        <p class="text-xl">Nenhum resultado encontrado para sua busca.</p>
    @endif
</div>
</x-app-layout>
