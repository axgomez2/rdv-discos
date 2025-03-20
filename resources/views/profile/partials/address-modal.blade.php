<div id="addressModal" class="fixed z-50 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        
        <!-- Modal positioning -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="addressForm" action="{{ route('address.store') }}" method="POST">
                @csrf
                <!-- Modal header -->
                <div class="flex items-start justify-between p-5 border-b rounded-t">
                    <h3 class="text-xl font-semibold text-gray-900" id="modal-title">
                        {{ __('Adicionar novo endereço') }}
                    </h3>
                    <button type="button" onclick="closeAddressModal()" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
                
                <!-- Modal body -->
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="col-span-2">
                            <label for="type" class="block mb-2 text-sm font-medium text-gray-900">{{ __('Tipo de endereço') }}</label>
                            <select id="type" name="type" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                <option value="Casa">{{ __('Casa - Principal') }}</option>
                                <option value="Trabalho">{{ __('Comercial') }}</option>
                                <option value="Entrega">{{ __('Entrega somente') }}</option>
                                <option value="Cobrança">{{ __('Cobrança') }}</option>
                            </select>
                        </div>
                        
                        <div class="col-span-2 sm:col-span-1">
                            <label for="zip_code" class="block mb-2 text-sm font-medium text-gray-900">{{ __('CEP') }}</label>
                            <div class="relative">
                                <div class="flex absolute inset-y-0 left-0 items-center pl-3 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <input type="text" name="zip_code" id="zip_code" required maxlength="8" placeholder="00000000" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5">
                            </div>
                            <p class="mt-1 text-sm text-gray-500">{{ __('Digite apenas números') }}</p>
                        </div>
                        
                        <div class="col-span-2">
                            <label for="street" class="block mb-2 text-sm font-medium text-gray-900">{{ __('Logradouro') }}</label>
                            <input type="text" name="street" id="street" required placeholder="Av./Rua" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        </div>
                        
                        <div class="col-span-1">
                            <label for="number" class="block mb-2 text-sm font-medium text-gray-900">{{ __('Número') }}</label>
                            <input type="text" name="number" id="number" required placeholder="123" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        </div>
                        
                        <div class="col-span-1">
                            <label for="complement" class="block mb-2 text-sm font-medium text-gray-900">{{ __('Complemento') }}</label>
                            <input type="text" name="complement" id="complement" placeholder="Apto 101" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        </div>
                        
                        <div class="col-span-2">
                            <label for="neighborhood" class="block mb-2 text-sm font-medium text-gray-900">{{ __('Bairro') }}</label>
                            <input type="text" name="neighborhood" id="neighborhood" required placeholder="Bairro" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        </div>
                        
                        <div>
                            <label for="city" class="block mb-2 text-sm font-medium text-gray-900">{{ __('Cidade') }}</label>
                            <input type="text" name="city" id="city" required placeholder="São Paulo" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        </div>
                        
                        <div>
                            <label for="state" class="block mb-2 text-sm font-medium text-gray-900">{{ __('UF') }}</label>
                            <input type="text" name="state" id="state" required maxlength="2" placeholder="SP" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        </div>
                        
                        <div class="col-span-2">
                            <div class="flex items-center">
                                <input id="is_default" name="is_default" type="checkbox" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                                <label for="is_default" class="ml-2 text-sm font-medium text-gray-900">{{ __('Definir como endereço principal') }}</label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Modal footer -->
                <div class="flex items-center justify-end p-6 space-x-2 border-t border-gray-200 rounded-b">
                    <button type="button" onclick="closeAddressModal()" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10">
                        {{ __('Cancelar') }}
                    </button>
                    <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                        {{ __('Salvar endereço') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openAddressModal() {
    document.getElementById('addressModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closeAddressModal() {
    document.getElementById('addressModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
    document.getElementById('addressForm').reset();
}

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('addressForm');
    form.addEventListener('submit', function(event) {
        event.preventDefault();

        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeAddressModal();
                // Reload the page to show the new address
                window.location.reload();
            } else {
                console.error('Falha ao salvar endereço:', data.message);
                alert('Falha ao salvar endereço: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Ocorreu um erro: ' + error.message);
        });
    });
});

document.getElementById('zip_code').addEventListener('blur', function() {
    let cep = this.value.replace(/\D/g, '');
    if (cep.length === 8) {
        fetch(`https://viacep.com.br/ws/${cep}/json/`)
            .then(response => response.json())
            .then(data => {
                if (!data.erro) {
                    document.getElementById('street').value = data.logradouro;
                    document.getElementById('neighborhood').value = data.bairro;
                    document.getElementById('city').value = data.localidade;
                    document.getElementById('state').value = data.uf;
                }
            })
            .catch(error => console.error('Erro:', error));
    }
});
</script>
