@extends('layouts.admin')

@section('title', 'Relatório de Discos')

@section('content')
<div class="container px-6 mx-auto grid">
    <div class="flex justify-between items-center">
        <h2 class="my-6 text-2xl font-semibold text-gray-700">
            Relatório de Discos
        </h2>
        <a href="{{ route('admin.reports.index') }}" class="px-4 py-2 text-sm text-blue-600 bg-blue-100 rounded-lg hover:bg-blue-200">
            &larr; Voltar para Relatórios
        </a>
    </div>
    
    <!-- Cards de estatísticas -->
    <div class="grid gap-6 mb-8 md:grid-cols-2 xl:grid-cols-4">
        <!-- Total de discos -->
        <div class="flex items-center p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800">
            <div class="p-3 mr-4 text-orange-500 bg-orange-100 rounded-full dark:text-orange-100 dark:bg-orange-500">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm0-2a6 6 0 100-12 6 6 0 000 12z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div>
                <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">
                    Total de Discos
                </p>
                <p class="text-lg font-semibold text-gray-700 dark:text-gray-200">
                    {{ $totalDiscs }}
                </p>
            </div>
        </div>
        
        <!-- Discos disponíveis -->
        <div class="flex items-center p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800">
            <div class="p-3 mr-4 text-green-500 bg-green-100 rounded-full dark:text-green-100 dark:bg-green-500">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div>
                <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">
                    Discos Disponíveis
                </p>
                <p class="text-lg font-semibold text-gray-700 dark:text-gray-200">
                    {{ $availableDiscs }}
                </p>
            </div>
        </div>
        
        <!-- Discos indisponíveis -->
        <div class="flex items-center p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800">
            <div class="p-3 mr-4 text-red-500 bg-red-100 rounded-full dark:text-red-100 dark:bg-red-500">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div>
                <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">
                    Discos Indisponíveis
                </p>
                <p class="text-lg font-semibold text-gray-700 dark:text-gray-200">
                    {{ $unavailableDiscs }}
                </p>
            </div>
        </div>
        
        <!-- Lucro potencial -->
        <div class="flex items-center p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800">
            <div class="p-3 mr-4 text-blue-500 bg-blue-100 rounded-full dark:text-blue-100 dark:bg-blue-500">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div>
                <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">
                    Lucro Potencial
                </p>
                <p class="text-lg font-semibold text-gray-700 dark:text-gray-200">
                    R$ {{ number_format($potentialProfit, 2, ',', '.') }}
                </p>
            </div>
        </div>
    </div>
    
    <!-- Resumo de valores -->
    <div class="p-4 bg-white rounded-lg shadow-md dark:bg-gray-800 mb-8">
        <h3 class="text-lg font-medium text-gray-700 mb-4">Resumo Financeiro</h3>
        <div class="grid gap-4 grid-cols-1 md:grid-cols-3">
            <div class="p-3 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-500">Valor Total de Compra</p>
                <p class="text-xl font-bold text-gray-700">R$ {{ number_format($totalBuyValue, 2, ',', '.') }}</p>
            </div>
            <div class="p-3 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-500">Valor Total de Venda</p>
                <p class="text-xl font-bold text-gray-700">R$ {{ number_format($totalSellValue, 2, ',', '.') }}</p>
            </div>
            <div class="p-3 bg-green-50 rounded-lg">
                <p class="text-sm text-gray-500">Lucro Potencial Total</p>
                <p class="text-xl font-bold text-green-700">R$ {{ number_format($potentialProfit, 2, ',', '.') }}</p>
            </div>
        </div>
    </div>
    
    <!-- Estatísticas por Fornecedor -->
    @if(count($supplierStats) > 0)
    <div class="p-4 bg-white rounded-lg shadow-md dark:bg-gray-800 mb-8">
        <h3 class="text-lg font-medium text-gray-700 mb-4">Estatísticas por Fornecedor</h3>
        <div class="overflow-x-auto">
            <table class="w-full whitespace-nowrap">
                <thead>
                    <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800">
                        <th class="px-4 py-3">Fornecedor</th>
                        <th class="px-4 py-3">Total de Discos</th>
                        <th class="px-4 py-3">Disponíveis</th>
                        <th class="px-4 py-3">Valor de Compra</th>
                        <th class="px-4 py-3">Valor de Venda</th>
                        <th class="px-4 py-3">Lucro Potencial</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800">
                    @foreach($supplierStats as $stat)
                    <tr class="text-gray-700 dark:text-gray-400">
                        <td class="px-4 py-3">{{ $stat->supplier }}</td>
                        <td class="px-4 py-3">{{ $stat->total_discs }}</td>
                        <td class="px-4 py-3">{{ $stat->available }}</td>
                        <td class="px-4 py-3">R$ {{ number_format($stat->total_buy, 2, ',', '.') }}</td>
                        <td class="px-4 py-3">R$ {{ number_format($stat->total_sell, 2, ',', '.') }}</td>
                        <td class="px-4 py-3 font-medium text-green-600">R$ {{ number_format($stat->total_sell - $stat->total_buy, 2, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
    
    <!-- Lista de discos -->
    <div class="p-4 bg-white rounded-lg shadow-md dark:bg-gray-800">
        <h3 class="text-lg font-medium text-gray-700 mb-4">Lista de Discos</h3>
        <div class="overflow-x-auto">
            <table class="w-full whitespace-nowrap">
                <thead>
                    <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800">
                        <th class="px-4 py-3">Disco</th>
                        <th class="px-4 py-3">Fornecedor</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Valor de Compra</th>
                        <th class="px-4 py-3">Valor de Venda</th>
                        <th class="px-4 py-3">Lucro</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800">
                    @foreach($discs as $disc)
                    <tr class="text-gray-700 dark:text-gray-400">
                        <td class="px-4 py-3">
                            <div class="flex items-center text-sm">
                                <div class="relative hidden w-8 h-8 mr-3 rounded-full md:block">
                                    <img class="object-cover w-full h-full rounded-full" src="{{ asset($disc->vinylMaster->cover_image ?? 'images/placeholder.jpg') }}" alt="{{ $disc->vinylMaster->title }}">
                                </div>
                                <div>
                                    <p class="font-semibold">{{ $disc->vinylMaster->title }}</p>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">{{ $disc->catalog_number }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm">{{ $disc->supplier ?? 'Não informado' }}</td>
                        <td class="px-4 py-3 text-xs">
                            <span class="px-2 py-1 font-semibold leading-tight rounded-full {{ $disc->in_stock ? 'text-green-700 bg-green-100' : 'text-red-700 bg-red-100' }}">
                                {{ $disc->in_stock ? 'Disponível' : 'Indisponível' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm">R$ {{ number_format($disc->buy_price, 2, ',', '.') }}</td>
                        <td class="px-4 py-3 text-sm">R$ {{ number_format($disc->price, 2, ',', '.') }}</td>
                        <td class="px-4 py-3 text-sm font-medium {{ ($disc->price - $disc->buy_price) > 0 ? 'text-green-600' : 'text-red-600' }}">
                            R$ {{ number_format($disc->price - $disc->buy_price, 2, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
