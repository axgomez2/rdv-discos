@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl font-semibold text-gray-900 mb-6">Editar DJ</h1>

    <form action="{{ route('admin.djs.update', $dj) }}" method="POST" enctype="multipart/form-data" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        @csrf
        @method('PUT')
        @if ($errors->any())
            <div class="mb-4">
                <ul class="text-red-600">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="mb-4">
            <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Nome</label>
            <input type="text" name="name" id="name" value="{{ $dj->name }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
        </div>
        <div class="mb-4">
            <label for="social_media" class="block text-gray-700 text-sm font-bold mb-2">Rede Social</label>
            <input type="text" name="social_media" id="social_media" value="{{ $dj->social_media }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>
        <div class="mb-4">
            <label for="bio" class="block text-gray-700 text-sm font-bold mb-2">Biografia</label>
            <textarea name="bio" id="bio" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>{{ $dj->bio }}</textarea>
        </div>
        <div class="mb-4">
            <label for="image" class="block text-gray-700 text-sm font-bold mb-2">Imagem</label>
            @if($dj->image)
                <img src="{{ asset('storage/' . $dj->image) }}" alt="{{ $dj->name }}" class="mb-2 w-32 h-32 object-cover">
            @endif
            <input type="file" name="image" id="image" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Recomendações de Discos</label>
            @for ($i = 0; $i < 10; $i++)
                <div class="mb-2">
                    <select name="recommendations[]" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <option value="">Selecione um disco</option>
                        @foreach($vinyls as $vinyl)
                            <option value="{{ $vinyl->id }}" {{ in_array($vinyl->id, $recommendations) && array_search($vinyl->id, $recommendations) == $i ? 'selected' : '' }}>
                                {{ $vinyl->title }} - {{ $vinyl->artist }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endfor
        </div>
        <div class="flex items-center justify-between">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Atualizar
            </button>
        </div>
    </form>
</div>
@endsection
