<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Endereços Cadastrados') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600">
            {{ __("Gerencie seus endereços de entrega e cobrança") }}
        </p>
    </header>

    <div class="mt-6">
        @if($user->addresses->isNotEmpty())
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                @foreach($user->addresses as $address)
                <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm hover:bg-gray-50">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ $address->type }}</h3>
                            @if($address->is_default)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    {{ __('Endereço Principal') }}
                                </span>
                            @endif
                        </div>
                        <div class="flex space-x-2">
                            <button type="button" class="text-blue-600 hover:text-blue-800">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                                </svg>
                            </button>
                            <button type="button" class="text-red-600 hover:text-red-800">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <div class="text-sm text-gray-600">
                        <p class="mb-1">{{ $address->street }}, {{ $address->number }}</p>
                        @if($address->complement)
                            <p class="mb-1">{{ $address->complement }}</p>
                        @endif
                        <p class="mb-1">{{ $address->neighborhood }}</p>
                        <p class="mb-1">{{ $address->city }} - {{ $address->state }}</p>
                        <p class="mb-1">CEP: {{ substr($address->zip_code, 0, 5) }}-{{ substr($address->zip_code, 5) }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="flex flex-col items-center justify-center p-8 text-center bg-white border border-gray-200 rounded-lg shadow-sm">
                <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                </svg>
                <p class="text-lg font-medium text-gray-900">Nenhum endereço cadastrado</p>
                <p class="text-sm text-gray-600 mt-1">Adicione um endereço para facilitar suas compras</p>
            </div>
        @endif

        <button type="button" 
            class="mt-6 inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition ease-in-out duration-150" 
            onclick="openAddressModal()">
            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 01-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path>
            </svg>
            {{ __('Adicionar novo endereço') }}
        </button>
    </div>
</section>
