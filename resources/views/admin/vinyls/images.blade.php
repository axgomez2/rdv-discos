@extends('layouts.admin')

@section('title', 'Gerenciar Imagens')

@section('content')
<div class="p-4">
    <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-900">
                Gerenciar imagens de: {{ $vinylMaster->title }}
            </h2>
            <a href="{{ route('admin.vinyls.index') }}"
               class="text-gray-900 bg-white border border-gray-300 hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5">
                Voltar para lista
            </a>
        </div>

        <!-- Upload Form -->
        <div class="mb-8">
            <form action="{{ route('admin.vinyl.images.store', $vinylMaster->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="flex items-center justify-center w-full">
                    <label for="images" class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <svg class="w-8 h-8 mb-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                            </svg>
                            <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Clique para fazer upload</span> ou arraste e solte</p>
                            <p class="text-xs text-gray-500">PNG, JPG ou GIF (serão cortadas em quadrado)</p>
                        </div>
                        <input id="images" name="images[]" type="file" class="hidden" multiple accept="image/*" />
                    </label>
                </div>
                @error('images')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
                @error('images.*')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror

                <div class="mt-4 flex justify-end">
                    <button type="submit" class="text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5">
                        Upload e Cortar Imagens
                    </button>
                </div>
            </form>
        </div>

        <!-- Image Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($images as $image)
                <div class="bg-white border border-gray-200 rounded-lg shadow group">
                    <div class="relative">
                        <img src="{{ asset('storage/' . $image->file_path) }}"
                             alt="{{ $image->file_name }}"
                             class="h-48 w-full rounded-t-lg object-cover">
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-opacity flex items-center justify-center opacity-0 group-hover:opacity-100">
                            <a href="{{ asset('storage/' . $image->file_path) }}"
                               target="_blank"
                               class="text-white bg-gray-800 bg-opacity-75 hover:bg-opacity-100 font-medium rounded-lg text-sm px-3 py-2 mr-2">
                                Ver
                            </a>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">
                                    {{ $image->file_name }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    {{ $image->created_at->format('d/m/Y') }}
                                </p>
                            </div>
                            <form action="{{ route('admin.vinyl.images.destroy', [$vinylMaster->id, $image->id]) }}"
                                  method="POST"
                                  class="flex-shrink-0 ml-2">
                                @csrf
                                @method('DELETE')
                                <button type="button"
                                        class="inline-flex items-center p-1.5 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-800 focus:ring-4 focus:ring-red-300 delete-button"
                                        data-image-name="{{ $image->file_name }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    <span class="sr-only">Excluir imagem</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Modal de Confirmação -->
    <div id="delete-image-dialog"
         class="fixed inset-0 flex items-center justify-center z-50 hidden"
         role="dialog"
         aria-modal="true"
         aria-labelledby="dialog-title">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75" id="modal-backdrop"></div>
        <div class="relative bg-white rounded-lg shadow-xl max-w-lg w-full p-6">
            <h2 id="dialog-title" class="sr-only">Confirmação de exclusão</h2>
            <div class="absolute top-0 right-0 pt-4 pr-4">
                <button type="button" class="text-gray-400 hover:text-gray-500" id="close-modal">
                    <span class="sr-only">Fechar</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <p class="mb-4 text-gray-900" id="modal-message"></p>
            <div class="flex justify-end space-x-4">
                <button type="button"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                        id="cancel-delete-button">
                    Cancelar
                </button>
                <button type="button"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                        id="confirm-delete-button">
                    Confirmar
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('delete-image-dialog');
    const modalMessage = document.getElementById('modal-message');
    const confirmButton = document.getElementById('confirm-delete-button');
    const cancelButton = document.getElementById('cancel-delete-button');
    const closeButton = document.getElementById('close-modal');
    const modalBackdrop = document.getElementById('modal-backdrop');

    let currentForm = null;
    let lastFocusedElement = null;

    // Handle delete button clicks
    document.querySelectorAll('.delete-button').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const imageName = this.dataset.imageName;
            currentForm = this.closest('form');
            lastFocusedElement = this;

            modalMessage.textContent = `Tem certeza que deseja excluir a imagem "${imageName}"?`;
            modal.classList.remove('hidden');
            confirmButton.focus();
        });
    });

    // Handle confirm button click
    confirmButton.addEventListener('click', function() {
        if (currentForm) {
            currentForm.submit();
        }
        closeModal();
    });

    // Handle cancel button click
    cancelButton.addEventListener('click', closeModal);
    closeButton.addEventListener('click', closeModal);
    modalBackdrop.addEventListener('click', closeModal);

    // Handle escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeModal();
        }
    });

    function closeModal() {
        modal.classList.add('hidden');
        if (lastFocusedElement) {
            lastFocusedElement.focus();
        }
        currentForm = null;
        lastFocusedElement = null;
    }
});
</script>

@if(session('success'))
<script>
Toastify({
    text: "{{ session('success') }}",
    duration: 3000,
    gravity: "top",
    position: "right",
    backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)"
}).showToast();
</script>
@endif

@if(session('error'))
<script>
Toastify({
    text: "{{ session('error') }}",
    duration: 3000,
    gravity: "top",
    position: "right",
    backgroundColor: "linear-gradient(to right, #FF5F6D, #FFC371)"
}).showToast();
</script>
@endif
@endpush
