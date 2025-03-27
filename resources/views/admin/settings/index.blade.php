@extends('layouts.admin')

@section('content')
<div x-data="settingsManager()" class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6 text-gray-900">Configurações</h1>

    <!-- Tabs Navigation -->
    <div x-data="{ activeTab: 'weights' }">

        <!-- Barra de navegação das abas -->
        <div class="border-b border-gray-200 mb-4">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center text-gray-500">
                <li class="mr-2">
                    <button
                        @click.prevent="activeTab = 'weights'"
                        :class="[
                            activeTab === 'weights'
                                ? 'text-blue-600 border-blue-600'
                                : 'border-transparent hover:text-gray-600 hover:border-gray-300',
                            'inline-block p-4 rounded-t-lg border-b-2'
                        ]"
                    >
                        Pesos
                    </button>
                </li>
                <li class="mr-2">
                    <button
                        @click.prevent="activeTab = 'dimensions'"
                        :class="[
                            activeTab === 'dimensions'
                                ? 'text-blue-600 border-blue-600'
                                : 'border-transparent hover:text-gray-600 hover:border-gray-300',
                            'inline-block p-4 rounded-t-lg border-b-2'
                        ]"
                    >
                        Dimensões
                    </button>
                </li>
                <li class="mr-2">
                    <button
                        @click.prevent="activeTab = 'brands'"
                        :class="[
                            activeTab === 'brands'
                                ? 'text-blue-600 border-blue-600'
                                : 'border-transparent hover:text-gray-600 hover:border-gray-300',
                            'inline-block p-4 rounded-t-lg border-b-2'
                        ]"
                    >
                        Marcas
                    </button>
                </li>
                <li class="mr-2">
                    <button
                        @click.prevent="activeTab = 'equipment-categories'"
                        :class="[
                            activeTab === 'equipment-categories'
                                ? 'text-blue-600 border-blue-600'
                                : 'border-transparent hover:text-gray-600 hover:border-gray-300',
                            'inline-block p-4 rounded-t-lg border-b-2'
                        ]"
                    >
                        Categorias de Equipamentos
                    </button>
                </li>
                
                <!-- Nova aba para serviços externos -->
                <li class="mr-2">
                    <button
                        @click.prevent="activeTab = 'external-services'"
                        :class="[
                            activeTab === 'external-services'
                                ? 'text-blue-600 border-blue-600'
                                : 'border-transparent hover:text-gray-600 hover:border-gray-300',
                            'inline-block p-4 rounded-t-lg border-b-2'
                        ]"
                    >
                        Serviços Externos
                    </button>
                </li>
            </ul>
        </div>

        <!-- Conteúdo de cada tab -->
        <div class="bg-white rounded-lg shadow">

            <!-- Pesos -->
            <div x-show="activeTab === 'weights'" class="p-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-900">Pesos</h2>

                <!-- Formulário para adicionar novo peso -->
                <form @submit.prevent="addWeight" class="space-y-4 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                        <!-- Nome -->
                        <div>
                            <label for="weight_name" class="block mb-2 text-sm font-medium text-gray-900">Nome</label>
                            <input
                                type="text"
                                id="weight_name"
                                x-model="newWeight.name"
                                required
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg
                                       focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            >
                        </div>

                        <!-- Valor -->
                        <div>
                            <label for="weight_value" class="block mb-2 text-sm font-medium text-gray-900">Valor</label>
                            <input
                                type="number"
                                step="0.01"
                                id="weight_value"
                                x-model="newWeight.value"
                                required
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg
                                       focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            >
                        </div>

                        <!-- Unidade -->
                        <div>
                            <label for="weight_unit" class="block mb-2 text-sm font-medium text-gray-900">Unidade</label>
                            <input
                                type="text"
                                id="weight_unit"
                                x-model="newWeight.unit"
                                required
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg
                                       focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            >
                        </div>
                    </div>

                    <!-- Botão Adicionar -->
                    <button
                        type="submit"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg
                               hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300"
                    >
                        Adicionar Peso
                    </button>
                </form>

                <!-- Listagem de pesos -->
                <ul class="divide-y divide-gray-200">
                    <template x-for="weight in weights" :key="weight.id">
                        <li class="py-4 flex justify-between items-center">
                            <!-- Exibição ou edição do peso -->
                            <div class="flex items-center space-x-2">
                                <template x-if="!weight.editing">
                                    <span class="text-sm text-gray-900" x-text="`${weight.name} (${weight.value} ${weight.unit})`"></span>
                                </template>
                                <template x-if="weight.editing">
                                    <div class="flex space-x-2">
                                        <input
                                            type="text"
                                            x-model="weight.name"
                                            class="block w-24 p-1 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                        >
                                        <input
                                            type="number"
                                            step="0.01"
                                            x-model="weight.value"
                                            class="block w-20 p-1 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                        >
                                        <input
                                            type="text"
                                            x-model="weight.unit"
                                            class="block w-16 p-1 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                        >
                                    </div>
                                </template>
                            </div>

                            <!-- Botões de ação -->
                            <div class="inline-flex items-center space-x-2">
                                <button
                                    @click="toggleWeightEdit(weight)"
                                    class="font-medium text-blue-600 hover:underline"
                                    x-text="weight.editing ? 'Salvar' : 'Editar'"
                                ></button>
                                <button
                                    @click="deleteWeight(weight)"
                                    class="font-medium text-red-600 hover:underline"
                                >
                                    Excluir
                                </button>
                            </div>
                        </li>
                    </template>
                </ul>
            </div>

            <!-- Dimensões -->
            <div x-show="activeTab === 'dimensions'" class="p-6" x-cloak>
                <h2 class="text-xl font-semibold mb-4 text-gray-900">Dimensões</h2>

                <!-- Formulário para adicionar nova dimensão -->
                <form @submit.prevent="addDimension" class="space-y-4 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">

                        <!-- Nome -->
                        <div>
                            <label for="dimension_name" class="block mb-2 text-sm font-medium text-gray-900">Nome</label>
                            <input
                                type="text"
                                id="dimension_name"
                                x-model="newDimension.name"
                                required
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg
                                       focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            >
                        </div>

                        <!-- Altura -->
                        <div>
                            <label for="dimension_height" class="block mb-2 text-sm font-medium text-gray-900">Altura</label>
                            <input
                                type="number"
                                step="0.01"
                                id="dimension_height"
                                x-model="newDimension.height"
                                required
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg
                                       focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            >
                        </div>

                        <!-- Largura -->
                        <div>
                            <label for="dimension_width" class="block mb-2 text-sm font-medium text-gray-900">Largura</label>
                            <input
                                type="number"
                                step="0.01"
                                id="dimension_width"
                                x-model="newDimension.width"
                                required
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg
                                       focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            >
                        </div>

                        <!-- Profundidade -->
                        <div>
                            <label for="dimension_depth" class="block mb-2 text-sm font-medium text-gray-900">Profundidade</label>
                            <input
                                type="number"
                                step="0.01"
                                id="dimension_depth"
                                x-model="newDimension.depth"
                                required
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg
                                       focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            >
                        </div>

                        <!-- Unidade -->
                        <div>
                            <label for="dimension_unit" class="block mb-2 text-sm font-medium text-gray-900">Unidade</label>
                            <input
                                type="text"
                                id="dimension_unit"
                                x-model="newDimension.unit"
                                required
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg
                                       focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            >
                        </div>
                    </div>

                    <!-- Botão Adicionar -->
                    <button
                        type="submit"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg
                               hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300"
                    >
                        Adicionar Dimensão
                    </button>
                </form>

                <!-- Listagem de dimensões -->
                <ul class="divide-y divide-gray-200">
                    <template x-for="dimension in dimensions" :key="dimension.id">
                        <li class="py-4 flex justify-between items-center">
                            <!-- Exibição ou edição -->
                            <div class="flex items-center space-x-2">
                                <template x-if="!dimension.editing">
                                    <span class="text-sm text-gray-900"
                                          x-text="`${dimension.name} (${dimension.height}x${dimension.width}x${dimension.depth} ${dimension.unit})`">
                                    </span>
                                </template>
                                <template x-if="dimension.editing">
                                    <div class="flex flex-wrap items-center space-x-2">
                                        <input
                                            type="text"
                                            x-model="dimension.name"
                                            class="w-28 p-1 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                        >
                                        <input
                                            type="number"
                                            step="0.01"
                                            x-model="dimension.height"
                                            class="w-20 p-1 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                        >
                                        <input
                                            type="number"
                                            step="0.01"
                                            x-model="dimension.width"
                                            class="w-20 p-1 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                        >
                                        <input
                                            type="number"
                                            step="0.01"
                                            x-model="dimension.depth"
                                            class="w-20 p-1 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                        >
                                        <input
                                            type="text"
                                            x-model="dimension.unit"
                                            class="w-16 p-1 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                        >
                                    </div>
                                </template>
                            </div>

                            <!-- Botões de ação -->
                            <div class="inline-flex items-center space-x-2">
                                <button
                                    @click="toggleDimensionEdit(dimension)"
                                    class="font-medium text-blue-600 hover:underline"
                                    x-text="dimension.editing ? 'Salvar' : 'Editar'"
                                ></button>
                                <button
                                    @click="deleteDimension(dimension)"
                                    class="font-medium text-red-600 hover:underline"
                                >
                                    Excluir
                                </button>
                            </div>
                        </li>
                    </template>
                </ul>
            </div>

            <!-- Marcas -->
            <div x-show="activeTab === 'brands'" class="p-6" x-cloak>
                <h2 class="text-xl font-semibold mb-4 text-gray-900">Marcas</h2>

                <!-- Formulário nova marca -->
                <form @submit.prevent="addBrand" class="space-y-4 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <!-- Nome -->
                        <div>
                            <label for="brand_name" class="block mb-2 text-sm font-medium text-gray-900">Nome</label>
                            <input
                                type="text"
                                id="brand_name"
                                x-model="newBrand.name"
                                required
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg
                                       focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            >
                        </div>

                        <!-- URL do Logo -->
                        <div>
                            <label for="brand_logo_url" class="block mb-2 text-sm font-medium text-gray-900">URL do Logo</label>
                            <input
                                type="url"
                                id="brand_logo_url"
                                x-model="newBrand.logo_url"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg
                                       focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            >
                        </div>

                        <!-- Descrição -->
                        <div class="md:col-span-2">
                            <label for="brand_description" class="block mb-2 text-sm font-medium text-gray-900">Descrição</label>
                            <textarea
                                id="brand_description"
                                x-model="newBrand.description"
                                rows="3"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg
                                       focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            ></textarea>
                        </div>
                    </div>

                    <!-- Botão Adicionar -->
                    <button
                        type="submit"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg
                               hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300"
                    >
                        Adicionar Marca
                    </button>
                </form>

                <!-- Listagem de marcas -->
                <ul class="divide-y divide-gray-200">
                    <template x-for="brand in brands" :key="brand.id">
                        <li class="py-4 flex justify-between items-center">
                            <!-- Exibição ou edição -->
                            <div class="flex items-center space-x-2">
                                <template x-if="!brand.editing">
                                    <div class="flex items-center">
                                        <img
                                            x-show="brand.logo_url"
                                            :src="brand.logo_url"
                                            alt="Logo"
                                            class="w-8 h-8 mr-2 object-contain"
                                        >
                                        <span class="text-sm font-medium text-gray-900" x-text="brand.name"></span>
                                    </div>
                                </template>
                                <template x-if="brand.editing">
                                    <div class="flex flex-col md:flex-row md:items-center space-x-2">
                                        <input
                                            type="text"
                                            x-model="brand.name"
                                            class="block w-32 p-1 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                        >
                                        <input
                                            type="url"
                                            x-model="brand.logo_url"
                                            class="block w-44 p-1 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                        >
                                        <textarea
                                            x-model="brand.description"
                                            class="block w-full p-1 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                        ></textarea>
                                    </div>
                                </template>
                            </div>

                            <!-- Botões de ação -->
                            <div class="inline-flex items-center space-x-2">
                                <button
                                    @click="toggleBrandEdit(brand)"
                                    class="font-medium text-blue-600 hover:underline"
                                    x-text="brand.editing ? 'Salvar' : 'Editar'"
                                ></button>
                                <button
                                    @click="deleteBrand(brand)"
                                    class="font-medium text-red-600 hover:underline"
                                >
                                    Excluir
                                </button>
                            </div>
                        </li>
                    </template>
                </ul>
            </div>

            <!-- Categorias de Equipamentos -->
            <div x-show="activeTab === 'equipment-categories'" class="p-6" x-cloak>
                <h2 class="text-xl font-semibold mb-4 text-gray-900">Categorias de Equipamentos</h2>

                <!-- Formulário nova categoria -->
                <form @submit.prevent="addEquipmentCategory" class="space-y-4 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <!-- Nome -->
                        <div>
                            <label for="category_name" class="block mb-2 text-sm font-medium text-gray-900">Nome</label>
                            <input
                                type="text"
                                id="category_name"
                                x-model="newEquipmentCategory.name"
                                required
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg
                                       focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            >
                        </div>

                        <!-- Categoria Pai -->
                        <div>
                            <label for="category_parent_id" class="block mb-2 text-sm font-medium text-gray-900">Categoria Pai</label>
                            <select
                                id="category_parent_id"
                                x-model="newEquipmentCategory.parent_id"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg
                                       focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            >
                                <option value="">Nenhuma</option>
                                <template x-for="category in equipmentCategories" :key="category.id">
                                    <option :value="category.id" x-text="category.name"></option>
                                </template>
                            </select>
                        </div>

                        <!-- Descrição -->
                        <div class="md:col-span-2">
                            <label for="category_description" class="block mb-2 text-sm font-medium text-gray-900">Descrição</label>
                            <textarea
                                id="category_description"
                                x-model="newEquipmentCategory.description"
                                rows="3"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg
                                       focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            ></textarea>
                        </div>
                    </div>

                    <!-- Botão Adicionar -->
                    <button
                        type="submit"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg
                               hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300"
                    >
                        Adicionar Categoria de Equipamento
                    </button>
                </form>

                <!-- Listagem de categorias -->
                <ul class="divide-y divide-gray-200">
                    <template x-for="category in equipmentCategories" :key="category.id">
                        <li class="py-4 flex justify-between items-center">
                            <!-- Exibição ou edição -->
                            <div>
                                <template x-if="!category.editing">
                                    <span class="text-sm font-medium text-gray-900">
                                        <span x-text="category.name"></span>
                                        <span
                                            x-show="category.parent_id"
                                            class="text-xs text-gray-500"
                                            x-text="` (Pai: ${getCategoryParentName(category.parent_id)})`"
                                        ></span>
                                    </span>
                                </template>
                                <template x-if="category.editing">
                                    <div class="flex flex-col md:flex-row md:items-center space-x-2">
                                        <input
                                            type="text"
                                            x-model="category.name"
                                            class="w-32 p-1 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                        >
                                        <select
                                            x-model="category.parent_id"
                                            class="p-1 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                        >
                                            <option value="">Nenhuma</option>
                                            <template x-for="parentCategory in equipmentCategories.filter(c => c.id !== category.id)" :key="parentCategory.id">
                                                <option :value="parentCategory.id" x-text="parentCategory.name"></option>
                                            </template>
                                        </select>
                                        <textarea
                                            x-model="category.description"
                                            class="block w-full p-1 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                        ></textarea>
                                    </div>
                                </template>
                            </div>

                            <!-- Botões de ação -->
                            <div class="inline-flex items-center space-x-2">
                                <button
                                    @click="toggleEquipmentCategoryEdit(category)"
                                    class="font-medium text-blue-600 hover:underline"
                                    x-text="category.editing ? 'Salvar' : 'Editar'"
                                ></button>
                                <button
                                    @click="deleteEquipmentCategory(category)"
                                    class="font-medium text-red-600 hover:underline"
                                >
                                    Excluir
                                </button>
                            </div>
                        </li>
                    </template>
                </ul>
            </div>

            <!-- Nova seção para configurações de serviços externos -->
            <div x-show="activeTab === 'external-services'" class="p-6" x-cloak>
                <h2 class="text-xl font-semibold mb-4 text-gray-900">Configurações de Serviços Externos</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Card do Google OAuth -->
                    <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <svg class="w-8 h-8 text-red-500 mr-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M20.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                                </svg>
                                <h3 class="text-lg font-semibold text-gray-900">Google OAuth</h3>
                            </div>
                            
                            <!-- Status indicator -->
                            <span 
                                class="px-2 py-1 text-xs font-semibold rounded-full"
                                :class="{ 
                                    'bg-green-100 text-green-800': '{{ isset($googleSettings['google_enabled']) && $googleSettings['google_enabled'] == 'true' }}',
                                    'bg-red-100 text-red-800': '{{ !isset($googleSettings['google_enabled']) || $googleSettings['google_enabled'] != 'true' }}'
                                }"
                            >
                                {{ isset($googleSettings['google_enabled']) && $googleSettings['google_enabled'] == 'true' ? 'Ativo' : 'Inativo' }}
                            </span>
                        </div>
                        
                        <p class="text-gray-600 mb-4">Configure o login social pelo Google para seus clientes.</p>
                        
                        <a href="{{ route('admin.settings.google-oauth') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Configurar
                        </a>
                    </div>
                    
                    <!-- Card do PagSeguro -->
                    <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <svg class="w-8 h-8 text-green-500 mr-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"/>
                                </svg>
                                <h3 class="text-lg font-semibold text-gray-900">PagSeguro</h3>
                            </div>
                            
                            <!-- Status indicator -->
                            <span 
                                class="px-2 py-1 text-xs font-semibold rounded-full"
                                :class="{ 
                                    'bg-green-100 text-green-800': '{{ isset($pagSeguroSettings['pagseguro_enabled']) && $pagSeguroSettings['pagseguro_enabled'] == 'true' }}',
                                    'bg-red-100 text-red-800': '{{ !isset($pagSeguroSettings['pagseguro_enabled']) || $pagSeguroSettings['pagseguro_enabled'] != 'true' }}'
                                }"
                            >
                                {{ isset($pagSeguroSettings['pagseguro_enabled']) && $pagSeguroSettings['pagseguro_enabled'] == 'true' ? 'Ativo' : 'Inativo' }}
                            </span>
                        </div>
                        
                        <p class="text-gray-600 mb-4">Configure o gateway de pagamento PagSeguro para processar pagamentos.</p>
                        
                        <a href="{{ route('admin.store-settings.pagseguro') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Configurar
                        </a>
                    </div>
                    
                    <!-- Card do MercadoPago -->
                    <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <svg class="w-8 h-8 text-blue-500 mr-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"/>
                                </svg>
                                <h3 class="text-lg font-semibold text-gray-900">MercadoPago</h3>
                            </div>
                            
                            <!-- Status indicator -->
                            <span 
                                class="px-2 py-1 text-xs font-semibold rounded-full"
                                :class="{ 
                                    'bg-green-100 text-green-800': '{{ isset($mercadoPagoSettings['mercadopago_enabled']) && $mercadoPagoSettings['mercadopago_enabled'] == 'true' }}',
                                    'bg-red-100 text-red-800': '{{ !isset($mercadoPagoSettings['mercadopago_enabled']) || $mercadoPagoSettings['mercadopago_enabled'] != 'true' }}'
                                }"
                            >
                                {{ isset($mercadoPagoSettings['mercadopago_enabled']) && $mercadoPagoSettings['mercadopago_enabled'] == 'true' ? 'Ativo' : 'Inativo' }}
                            </span>
                        </div>
                        
                        <p class="text-gray-600 mb-4">Configure o gateway de pagamento MercadoPago para processar pagamentos.</p>
                        
                        <a href="{{ route('admin.store-settings.mercadopago') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Configurar
                        </a>
                    </div>
                    
                    <!-- Card do Melhor Envio -->
                    <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <svg class="w-8 h-8 text-purple-500 mr-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17.73 12.02l3.98-3.98c.39-.39.39-1.02 0-1.41l-4.34-4.34c-.39-.39-1.02-.39-1.41 0l-3.98 3.98L8 2.29C7.8 2.1 7.55 2 7.29 2H3.01c-.55 0-1 .45-1 1v4.28c0 .26.1.51.29.71l3.98 3.98L2.3 16.99c-.39.39-.39 1.02 0 1.41l4.34 4.34c.39.39 1.02.39 1.41 0l3.98-3.98 3.98 3.98c.2.2.45.29.71.29h4.28c.55 0 1-.45 1-1v-4.28c0-.26-.1-.51-.29-.71l-3.98-3.98zM12 9c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm-4.71 1.96L3.66 7.34l3.63-3.63 3.62 3.62-3.62 3.63zM10 13c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1z"></path>
                                </svg>
                                <h3 class="text-lg font-semibold text-gray-900">Melhor Envio</h3>
                            </div>
                            
                            <!-- Status indicator -->
                            <span 
                                class="px-2 py-1 text-xs font-semibold rounded-full"
                                :class="{ 
                                    'bg-green-100 text-green-800': '{{ isset($settings['melhorenvio_enabled']) && $settings['melhorenvio_enabled'] == 'true' }}',
                                    'bg-red-100 text-red-800': '{{ !isset($settings['melhorenvio_enabled']) || $settings['melhorenvio_enabled'] != 'true' }}'
                                }"
                            >
                                {{ isset($settings['melhorenvio_enabled']) && $settings['melhorenvio_enabled'] == 'true' ? 'Ativo' : 'Inativo' }}
                            </span>
                        </div>
                        
                        <p class="text-gray-600 mb-4">Configure o serviço Melhor Envio para gerenciar entregas e fretes.</p>
                        
                        <a href="{{ route('admin.store-settings.melhorenvio') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Configurar
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function settingsManager() {
    return {
        weights: @json($weights),
        dimensions: @json($dimensions),
        brands: @json($brands),
        equipmentCategories: @json($equipmentCategories),

        newWeight: { name: '', value: '', unit: '' },
        newDimension: { name: '', height: '', width: '', depth: '', unit: '' },
        newBrand: { name: '', logo_url: '', description: '' },
        newEquipmentCategory: { name: '', parent_id: '', description: '' },

        // Métodos Pesos
        addWeight() {
            axios.post('{{ route('admin.settings.storeWeight') }}', this.newWeight)
                .then(response => {
                    this.weights.push(response.data);
                    this.newWeight = { name: '', value: '', unit: '' };
                })
                .catch(error => console.error(error));
        },
        toggleWeightEdit(weight) {
            if (weight.editing) {
                this.updateWeight(weight);
            } else {
                weight.editing = true;
            }
        },
        updateWeight(weight) {
            axios.put(`/admin/settings/weights/${weight.id}`, weight)
                .then(() => {
                    weight.editing = false;
                })
                .catch(error => console.error(error));
        },
        deleteWeight(weight) {
            if (confirm('Tem certeza que deseja excluir este peso?')) {
                axios.delete(`/admin/settings/weights/${weight.id}`)
                    .then(() => {
                        this.weights = this.weights.filter(w => w.id !== weight.id);
                    })
                    .catch(error => console.error(error));
            }
        },

        // Métodos Dimensões
        addDimension() {
            axios.post('{{ route('admin.settings.storeDimension') }}', this.newDimension)
                .then(response => {
                    this.dimensions.push(response.data);
                    this.newDimension = { name: '', height: '', width: '', depth: '', unit: '' };
                })
                .catch(error => console.error(error));
        },
        toggleDimensionEdit(dimension) {
            if (dimension.editing) {
                this.updateDimension(dimension);
            } else {
                dimension.editing = true;
            }
        },
        updateDimension(dimension) {
            axios.put(`/admin/settings/dimensions/${dimension.id}`, dimension)
                .then(() => {
                    dimension.editing = false;
                })
                .catch(error => console.error(error));
        },
        deleteDimension(dimension) {
            if (confirm('Tem certeza que deseja excluir esta dimensão?')) {
                axios.delete(`/admin/settings/dimensions/${dimension.id}`)
                    .then(() => {
                        this.dimensions = this.dimensions.filter(d => d.id !== dimension.id);
                    })
                    .catch(error => console.error(error));
            }
        },

        // Métodos Marcas
        addBrand() {
            axios.post('{{ route('admin.settings.storeBrand') }}', this.newBrand)
                .then(response => {
                    this.brands.push(response.data);
                    this.newBrand = { name: '', logo_url: '', description: '' };
                })
                .catch(error => console.error(error));
        },
        toggleBrandEdit(brand) {
            if (brand.editing) {
                this.updateBrand(brand);
            } else {
                brand.editing = true;
            }
        },
        updateBrand(brand) {
            axios.put(`/admin/settings/brands/${brand.id}`, brand)
                .then(() => {
                    brand.editing = false;
                })
                .catch(error => console.error(error));
        },
        deleteBrand(brand) {
            if (confirm('Tem certeza que deseja excluir esta marca?')) {
                axios.delete(`/admin/settings/brands/${brand.id}`)
                    .then(() => {
                        this.brands = this.brands.filter(b => b.id !== brand.id);
                    })
                    .catch(error => console.error(error));
            }
        },

        // Métodos Categorias
        addEquipmentCategory() {
            axios.post('{{ route('admin.settings.storeEquipmentCategory') }}', this.newEquipmentCategory)
                .then(response => {
                    this.equipmentCategories.push(response.data);
                    this.newEquipmentCategory = { name: '', parent_id: '', description: '' };
                })
                .catch(error => console.error(error));
        },
        toggleEquipmentCategoryEdit(category) {
            if (category.editing) {
                this.updateEquipmentCategory(category);
            } else {
                category.editing = true;
            }
        },
        updateEquipmentCategory(category) {
            axios.put(`/admin/settings/equipment-categories/${category.id}`, category)
                .then(() => {
                    category.editing = false;
                })
                .catch(error => console.error(error));
        },
        deleteEquipmentCategory(category) {
            if (confirm('Tem certeza que deseja excluir esta categoria de equipamento?')) {
                axios.delete(`/admin/settings/equipment-categories/${category.id}`)
                    .then(() => {
                        this.equipmentCategories = this.equipmentCategories.filter(c => c.id !== category.id);
                    })
                    .catch(error => console.error(error));
            }
        },
        getCategoryParentName(parentId) {
            const parent = this.equipmentCategories.find(c => c.id === parentId);
            return parent ? parent.name : '';
        }
    }
}
</script>
@endpush
