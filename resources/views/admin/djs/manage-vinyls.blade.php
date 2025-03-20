@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Gerenciar Vinis para {{ $dj->name }}</h1>
        <a href="{{ route('admin.djs.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            Voltar para Lista de DJs
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <h2 class="text-xl font-semibold mb-4">Vinis Recomendados (<span id="recommendationCount">{{ $recommendations->count() }}</span>/10)</h2>
            <div id="recommendedVinyls" class="space-y-2">
                @foreach($recommendations as $recommendation)
                    <div class="flex items-center justify-between bg-white p-2 rounded shadow" data-id="{{ $recommendation->id }}">
                        <span>{{ $recommendation->title }} - {{ $recommendation->artists->pluck('name')->join(', ') }}</span>
                        <button class="removeVinyl text-red-600 hover:text-red-800">Remover</button>
                    </div>
                @endforeach
            </div>
        </div>
        <div>
            <h2 class="text-xl font-semibold mb-4">Adicionar Vinis</h2>
            <div class="mb-4">
                <input type="text" id="vinylSearch" class="w-full p-2 border rounded" placeholder="Buscar vinis...">
            </div>
            <div id="searchResults" class="space-y-2">
                @foreach($allVinyls as $vinyl)
                    <div class="flex items-center justify-between bg-white p-2 rounded shadow" data-id="{{ $vinyl->id }}">
                        <span>{{ $vinyl->title }} - {{ $vinyl->artists->pluck('name')->join(', ') }}</span>
                        @if($recommendations->contains($vinyl))
                            <span class="text-gray-500">Já adicionado</span>
                        @else
                            <button class="addVinyl text-green-600 hover:text-green-800" data-id="{{ $vinyl->id }}">Adicionar</button>
                        @endif
                    </div>
                @endforeach
            </div>
            <div id="paginationLinks" class="mt-4">
                {{ $allVinyls->links() }}
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const recommendedVinyls = document.getElementById('recommendedVinyls');
    const searchResults = document.getElementById('searchResults');
    const vinylSearch = document.getElementById('vinylSearch');
    const recommendationCount = document.getElementById('recommendationCount');
    const paginationLinks = document.getElementById('paginationLinks');

    function updateRecommendationCount() {
        recommendationCount.textContent = recommendedVinyls.children.length;
    }

    function addVinyl(id, title, artistNames) {
        if (recommendedVinyls.children.length >= 10) {
            alert('Você já atingiu o limite de 10 vinis recomendados.');
            return;
        }

        const vinylElement = document.createElement('div');
        vinylElement.className = 'flex items-center justify-between bg-white p-2 rounded shadow';
        vinylElement.dataset.id = id;
        vinylElement.innerHTML = `
            <span>${title} - ${artistNames}</span>
            <button class="removeVinyl text-red-600 hover:text-red-800">Remover</button>
        `;
        recommendedVinyls.appendChild(vinylElement);

        // Atualizar o botão na lista de busca
        const searchResultElement = searchResults.querySelector(`[data-id="${id}"]`);
        if (searchResultElement) {
            searchResultElement.querySelector('button').outerHTML = '<span class="text-gray-500">Já adicionado</span>';
        }

        updateRecommendationCount();
        updateRecommendations();
    }

    function removeVinyl(element) {
        const id = element.dataset.id;
        element.remove();

        // Atualizar o botão na lista de busca
        const searchResultElement = searchResults.querySelector(`[data-id="${id}"]`);
        if (searchResultElement) {
            searchResultElement.querySelector('span:last-child').outerHTML = `
                <button class="addVinyl text-green-600 hover:text-green-800" data-id="${id}">Adicionar</button>
            `;
        }

        updateRecommendationCount();
        updateRecommendations();
    }

    function updateRecommendations() {
        const recommendations = Array.from(recommendedVinyls.children).map((el, index) => ({
            id: el.dataset.id,
            order: index + 1
        }));

        fetch('{{ route("admin.djs.update-recommendations", $dj) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ recommendations })
        }).then(response => response.json())
          .then(data => {
              if (data.success) {
                  console.log('Recomendações atualizadas com sucesso');
              } else {
                  console.error('Erro ao atualizar recomendações');
              }
          });
    }

    searchResults.addEventListener('click', function(e) {
        if (e.target.classList.contains('addVinyl')) {
            const vinylElement = e.target.closest('div');
            const id = e.target.dataset.id;
            const title = vinylElement.firstElementChild.textContent;
            addVinyl(id, title);
        }
    });

    recommendedVinyls.addEventListener('click', function(e) {
        if (e.target.classList.contains('removeVinyl')) {
            removeVinyl(e.target.closest('div'));
        }
    });

    let searchTimeout;
    vinylSearch.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            const searchTerm = this.value.trim();
            if (searchTerm.length > 2) {
                fetch(`{{ route('admin.djs.search-vinyls', $dj) }}?query=${encodeURIComponent(searchTerm)}`)
                    .then(response => response.json())
                    .then(data => {
                        searchResults.innerHTML = '';
                        data.forEach(vinyl => {
                            const vinylElement = document.createElement('div');
                            vinylElement.className = 'flex items-center justify-between bg-white p-2 rounded shadow';
                            vinylElement.dataset.id = vinyl.id;
                            const artistNames = vinyl.artists.map(artist => artist.name).join(', ');
                            vinylElement.innerHTML = `
                                <span>${vinyl.title} - ${artistNames}</span>
                                ${vinyl.is_recommended
                                    ? '<span class="text-gray-500">Já adicionado</span>'
                                    : `<button class="addVinyl text-green-600 hover:text-green-800" data-id="${vinyl.id}">Adicionar</button>`
                                }
                            `;
                            searchResults.appendChild(vinylElement);
                        });
                        paginationLinks.style.display = 'none';
                    });
            } else if (searchTerm.length === 0) {
                // Recarregar a página para mostrar a lista paginada original
                window.location.reload();
            }
        }, 300);
    });

    updateRecommendationCount();
});
</script>
@endsection
