<div class="bg-white shadow-md hover:shadow-lg transition-shadow duration-300 rounded-lg overflow-hidden">
    <div class="flex flex-col sm:flex-row">
        <div class="sm:w-48 h-48 flex-shrink-0">
            <img
                src="{{ $vinyl->cover_image }}"
                alt="{{ $vinyl->title }} by {{ $vinyl->artists->pluck('name')->implode(', ') }}"
                class="w-full h-full object-cover object-center"
                onerror="this.src='https://placehold.co/600x400?text=imagem+não+disponivel'"
            />
        </div>
        <div class="flex-grow p-4 flex flex-col justify-between">
            <div>
                <div class="flex justify-between items-start">
                    <div>
                        <h2 class="text-lg font-semibold line-clamp-1">
                            {{ $vinyl->artists->pluck('name')->implode(', ') }}
                        </h2>
                        <p class="text-sm text-gray-600">{{ $vinyl->title }}</p>
                    </div>
                    @if($vinyl->tracks->whereNotNull('youtube_url')->count() > 0)
                        <button
                            class="play-button btn btn-circle btn-sm btn-primary"
                            onclick="window.audioPlayer.loadTrack({{ json_encode($vinyl->tracks->whereNotNull('youtube_url')->first()) }})"
                        >
                            <i class="fas fa-play text-xs"></i>
                        </button>
                    @else
                        <span class="text-xs text-gray-500">Áudio indisponível</span>
                    @endif
                </div>
                <div class="mt-2 text-sm text-gray-500">
                    <p>{{ $vinyl->recordLabel->name }} • {{ $vinyl->release_year }}</p>
                    <p>Faixas: {{ $vinyl->tracks->count() }}</p>
                </div>
            </div>
            <div class="mt-4 flex items-center justify-between">
                <div>
                    <p class="text-lg font-bold text-red-500">Indisponível</p>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('site.vinyl.show', ['artistSlug' => $vinyl->artists->first()->slug, 'titleSlug' => $vinyl->slug]) }}" class="btn btn-outline btn-sm">
                        <i class="fas fa-eye mr-2"></i> Ver detalhes
                    </a>
                    <button
                        type="button"
                        class="btn btn-primary btn-sm"
                        onclick="notifyWhenAvailable({{ $vinyl->id }})"
                    >
                        <i class="fas fa-bell mr-2"></i>
                        <span>Avisar quando disponível</span>
                    </button>
                    <button
                        type="button"
                        title="Remover da Wantlist"
                        class="wantlist-button btn btn-circle btn-sm btn-outline"
                        onclick="toggleWantlist({{ $vinyl->id }}, 'App\\Models\\VinylMaster', this)"
                        data-product-id="{{ $vinyl->id }}"
                        data-product-type="App\Models\VinylMaster"
                        data-in-wantlist="true"
                    >
                        <i class="fas fa-bookmark text-blue-500"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

