@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl font-semibold text-gray-900 mb-6">DJs</h1>

    <div class="mb-4">
        <a href="{{ route('admin.djs.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Adicionar Novo DJ
        </a>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Nome
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Ações
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($djs as $dj)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $dj->name }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $dj->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $dj->is_active ? 'Ativo' : 'Inativo' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('admin.djs.show', $dj) }}" class="text-indigo-600 hover:text-indigo-900 mr-2">Ver</a>
                            <a href="{{ route('admin.djs.edit', $dj) }}" class="text-yellow-600 hover:text-yellow-900 mr-2">Editar</a>
                            <a href="{{ route('admin.djs.manage-vinyls', $dj) }}" class="text-blue-600 hover:text-blue-900 mr-2">Gerenciar discos</a>
                            <form action="{{ route('admin.djs.toggle-active', $dj) }}" method="POST" class="inline">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="text-{{ $dj->is_active ? 'red' : 'green' }}-600 hover:text-{{ $dj->is_active ? 'red' : 'green' }}-900 mr-2">
                                    {{ $dj->is_active ? 'Desativar' : 'Ativar' }}
                                </button>
                            </form>
                            <form action="{{ route('admin.djs.destroy', $dj) }}" method="POST" class="inline" onsubmit="return confirm('Tem certeza que deseja excluir este DJ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Excluir</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
