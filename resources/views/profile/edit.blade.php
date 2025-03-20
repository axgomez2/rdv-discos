<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Meu Perfil') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Tabs component -->
            <div class="mb-8">
                <div class="sm:hidden">
                    <label for="tabs" class="sr-only">Selecione uma aba</label>
                    <select id="tabs" name="tabs" class="block w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md">
                        <option selected>Informações Pessoais</option>
                        <option>Senha</option>
                        <option>Endereços</option>
                        <option>Deletar Conta</option>
                    </select>
                </div>
                <div class="hidden sm:block">
                    <div class="border-b border-gray-200">
                        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                            <a href="#profile-info" class="tab-link border-blue-500 text-blue-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" aria-current="page">
                                <svg class="inline-block w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                                Informações Pessoais
                            </a>
                            <a href="#password" class="tab-link border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                <svg class="inline-block w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path></svg>
                                Senha
                            </a>
                            <a href="#addresses" class="tab-link border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                <svg class="inline-block w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path></svg>
                                Endereços
                            </a>
                            <a href="#delete-account" class="tab-link border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                <svg class="inline-block w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                Deletar Conta
                            </a>
                        </nav>
                    </div>
                </div>
            </div>

            <div class="tab-content">
                <!-- Informações Pessoais -->
                <div id="profile-info" class="tab-pane bg-white shadow overflow-hidden sm:rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>

                <!-- Senha -->
                <div id="password" class="tab-pane hidden bg-white shadow overflow-hidden sm:rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>

                <!-- Endereços -->
                <div id="addresses" class="tab-pane hidden bg-white shadow overflow-hidden sm:rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        @include('profile.partials.address-information')
                    </div>
                </div>

                <!-- Deletar Conta -->
                <div id="delete-account" class="tab-pane hidden bg-white shadow overflow-hidden sm:rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('profile.partials.address-modal')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tabs functionality
            const tabLinks = document.querySelectorAll('.tab-link');
            const tabContents = document.querySelectorAll('.tab-pane');
            const tabsSelect = document.getElementById('tabs');
            
            // Function to activate tab
            function activateTab(tabId) {
                // Hide all tabs
                tabContents.forEach(content => {
                    content.classList.add('hidden');
                });
                
                // Show the selected tab
                const selectedTab = document.querySelector(tabId);
                if (selectedTab) {
                    selectedTab.classList.remove('hidden');
                }
                
                // Update active tab style
                tabLinks.forEach(link => {
                    if (link.getAttribute('href') === tabId) {
                        link.classList.remove('border-transparent', 'text-gray-500');
                        link.classList.add('border-blue-500', 'text-blue-600');
                    } else {
                        link.classList.remove('border-blue-500', 'text-blue-600');
                        link.classList.add('border-transparent', 'text-gray-500');
                    }
                });
            }
            
            // Tab click event
            tabLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const tabId = this.getAttribute('href');
                    activateTab(tabId);
                    
                    // Update select for mobile view
                    if (tabsSelect) {
                        const index = Array.from(tabLinks).findIndex(el => el.getAttribute('href') === tabId);
                        tabsSelect.selectedIndex = index;
                    }
                });
            });
            
            // Mobile select change event
            if (tabsSelect) {
                tabsSelect.addEventListener('change', function() {
                    const tabId = tabLinks[this.selectedIndex].getAttribute('href');
                    activateTab(tabId);
                });
            }
            
            // Handle URL hash for direct tab access
            if (window.location.hash) {
                const tabId = window.location.hash;
                const tabLink = document.querySelector(`.tab-link[href="${tabId}"]`);
                
                if (tabLink) {
                    activateTab(tabId);
                    
                    // Update select for mobile view
                    if (tabsSelect) {
                        const index = Array.from(tabLinks).findIndex(el => el.getAttribute('href') === tabId);
                        tabsSelect.selectedIndex = index;
                    }
                }
            }
        });
    </script>
</x-app-layout>
