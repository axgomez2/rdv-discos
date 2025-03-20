<x-app-layout>
    <div class="max-w-screen-xl mx-auto px-4 py-8">
        <!-- Cabeçalho da Categoria -->
        <div class="mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <!-- Breadcrumb corrigido -->
                    <nav class="flex" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-3">
                            <li class="inline-flex items-center">
                                <a href="{{ route('site.home') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                                    <svg class="w-3 h-3 mr-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/>
                                    </svg>
                                    Home
                                </a>
                            </li>
                            <li aria-current="page">
                                <div class="flex items-center">
                                    <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                                    </svg>
                                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">{{ $category->nome }}</span>
                                </div>
                            </li>
                        </ol>
                    </nav>

                    <!-- Título da Categoria -->
                    <div class="mt-4">
                        <h1 class="text-2xl font-bold text-gray-900">{{ $category->nome }}</h1>
                        <p class="text-sm text-gray-500 mt-1">{{ $vinyls->total() }} discos encontrados</p>
                    </div>
                </div>

                <!-- Ordenação -->
                <div class="flex items-center">
                    <label for="sort" class="sr-only">Ordenar por</label>
                    <select id="sort" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5">
                        <option value="newest">Mais recentes</option>
                        <option value="price_asc">Menor preço</option>
                        <option value="price_desc">Maior preço</option>
                        <option value="artist_asc">Artista A-Z</option>
                        <option value="artist_desc">Artista Z-A</option>
                        <option value="name_asc">Título A-Z</option>
                        <option value="name_desc">Título Z-A</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Grid de Produtos -->
        @if($vinyls->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                @foreach($vinyls as $vinyl)
                    @include('components.site.vinyl-card', ['vinyl' => $vinyl])
                @endforeach
            </div>

            <!-- Paginação -->
            <div class="mt-8">
                {{ $vinyls->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <div class="flex flex-col items-center">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum disco encontrado</h3>
                    <p class="mt-1 text-sm text-gray-500">Não encontramos nenhum disco na categoria {{ $category->nome }}.</p>
                    <div class="mt-6">
                        <a href="{{ route('site.home') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Voltar para home
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sortSelect = document.getElementById('sort');
            if (sortSelect) {
                sortSelect.addEventListener('change', function() {
                    const url = new URL(window.location.href);
                    url.searchParams.set('sort', this.value);
                    window.location.href = url.toString();
                });

                // Set initial value from URL
                const urlParams = new URLSearchParams(window.location.search);
                const sortParam = urlParams.get('sort');
                if (sortParam) {
                    sortSelect.value = sortParam;
                }
            }
        });
    </script>
    @endpush
</x-app-layout>

