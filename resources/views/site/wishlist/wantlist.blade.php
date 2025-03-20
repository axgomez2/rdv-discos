<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <h1 class="text-2xl font-bold mb-6">Minha Wantlist</h1>
            @if($wantlistItems->count() > 0)
                <div class="font-[sans-serif] p-4 mx-auto max-w-[1400px]">
                    <h2 class="font-jersey text-xl sm:text-3xl text-gray-800 mt-3 mb-6">Itens na Wantlist</h2>
                    <ul class="space-y-6">
                        @foreach($wantlistItems as $item)
                            <li>
                                @if($item instanceof App\Models\VinylMaster)
                                    @include('components.site.wantlist-vinyl', ['vinyl' => $item])
                                @elseif($item instanceof App\Models\Equipment)
                                    equipamentos em wantlist
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            @else
                <p class="text-xl text-gray-600">Sua wantlist está vazia.</p>
            @endif
        </div>
    </div>
</x-app-layout>

