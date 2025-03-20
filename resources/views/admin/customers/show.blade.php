@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">Detalhes do Cliente</h1>

    <div class="bg-white shadow-md rounded-lg overflow-hidden p-6">
        <div class="grid grid-cols-2 gap-4">
    <div>
        <p class="text-sm font-medium text-gray-500">Nome</p>
        <p class="mt-1 text-lg text-gray-900">{{ $customer->name }}</p>
    </div>
    <div>
        <p class="text-sm font-medium text-gray-500">Email</p>
        <p class="mt-1 text-lg text-gray-900">{{ $customer->email }}</p>
    </div>
    <div>
        <p class="text-sm font-medium text-gray-500">Telefone</p>
        <p class="mt-1 text-lg text-gray-900">{{ $customer->phone ?? 'N/A' }}</p>
    </div>
    <div>
        <p class="text-sm font-medium text-gray-500">Endereço</p>
        <p class="mt-1 text-lg text-gray-900">{{ $customer->address ?? 'N/A' }}</p>
    </div>
    <div>
        <p class="text-sm font-medium text-gray-500">Data de Registro</p>
        <p class="mt-1 text-lg text-gray-900">{{ $customer->created_at->format('d/m/Y H:i:s') }}</p>
    </div>
    <div>
        <p class="text-sm font-medium text-gray-500">Última Atualização</p>
        <p class="mt-1 text-lg text-gray-900">{{ $customer->updated_at->format('d/m/Y H:i:s') }}</p>
    </div>
</div>

        <div class="mt-6">
            <a href="{{ route('admin.customers.edit', $customer) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Editar Cliente
            </a>
            <a href="{{ route('admin.customers.index') }}" class="ml-3 inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Voltar para Lista
            </a>
        </div>
    </div>
</div>
@endsection

