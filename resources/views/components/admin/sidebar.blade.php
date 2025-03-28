<aside x-data="{ open: true }"
           @toggle-sidebar.window="open = !open"
           :class="{'translate-x-0': open, '-translate-x-full': !open}"
           class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform bg-white border-r border-gray-200 lg:translate-x-0">
        <div class="h-full px-3 pb-4 overflow-y-auto bg-white">

            <ul class="space-y-2 font-medium">
                <!-- Dashboard -->
                <li>
                    <a href="{{ route('admin.dashboard') }}"
                       class="flex items-center p-2 text-gray-900 rounded-lg transition duration-75 group
                              {{ request()->routeIs('admin.dashboard') ? 'bg-gray-100' : 'hover:bg-gray-100' }}">
                        <svg class="w-5 h-5 text-gray-500 transition duration-75 group-hover:text-gray-900"
                             aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                             fill="currentColor" viewBox="0 0 22 21">
                            <path d="M16.975 11H10V4.025a1 1 0 0 0-1.066-.998 8.5 8.5 0 1 0 9.039 9.039.999.999 0 0 0-1-1.066h.002Z"/>
                            <path d="M12.5 0c-.157 0-.311.01-.565.027A1 1 0 0 0 11 1.02V10h8.975a1 1 0 0 0 1-.935c.013-.188.028-.374.028-.565A8.51 8.51 0 0 0 12.5 0Z"/>
                        </svg>
                        <span class="ml-3">Início</span>
                    </a>
                </li>

                <!-- Vinyls -->
                <li>
                    <a href="{{ route('admin.vinyls.index') }}"
                       class="flex items-center p-2 text-gray-900 rounded-lg transition duration-75 group
                              {{ request()->routeIs('admin.vinyls.*') ? 'bg-gray-100' : 'hover:bg-gray-100' }}">
                        <svg class="w-5 h-5 text-gray-500 transition duration-75 group-hover:text-gray-900"
                             aria-hidden="true" fill="none" stroke-linecap="round"
                             stroke-linejoin="round" stroke-width="2"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                        <span class="ml-3">Vinyls</span>
                    </a>
                </li>

                <!-- Playlists -->
                <li>
                    <a href="{{ route('admin.playlists.index') }}"
                       class="flex items-center p-2 text-gray-900 rounded-lg transition duration-75 group
                              {{ request()->routeIs('admin.playlists.*') ? 'bg-gray-100' : 'hover:bg-gray-100' }}">
                        <svg class="w-5 h-5 text-gray-500 transition duration-75 group-hover:text-gray-900"
                             aria-hidden="true" fill="none" stroke-linecap="round"
                             stroke-linejoin="round" stroke-width="2"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                        </svg>
                        <span class="ml-3">Playlists</span>
                    </a>
                </li>

                <!-- Equipments -->
                <li>
                    <a href="{{ route('admin.equipments.index') }}"
                       class="flex items-center p-2 text-gray-900 rounded-lg transition duration-75 group
                              {{ request()->routeIs('admin.equipments.*') ? 'bg-gray-100' : 'hover:bg-gray-100' }}">
                        <svg class="w-5 h-5 text-gray-500 transition duration-75 group-hover:text-gray-900"
                             aria-hidden="true" fill="none" stroke-linecap="round"
                             stroke-linejoin="round" stroke-width="2"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                            <path d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                        </svg>
                        <span class="ml-3">Equipamentos</span>
                    </a>
                </li>

                <!-- Etiquetas -->
                <li>
                    <a href="{{ route('admin.shipping.index') }}"
                       class="flex items-center p-2 text-gray-900 rounded-lg transition duration-75 group
                              {{ request()->routeIs('admin.equipments.*') ? 'bg-gray-100' : 'hover:bg-gray-100' }}">
                        <svg class="w-5 h-5 text-gray-500 transition duration-75 group-hover:text-gray-900"
                             aria-hidden="true" fill="none" stroke-linecap="round"
                             stroke-linejoin="round" stroke-width="2"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                            <path d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                        </svg>
                        <span class="ml-3">Etiquetas</span>
                    </a>
                </li>

                <!-- Clube de Compra -->
                <li>
                    <a href="{{ route('admin.subscriptions.index') }}"
                       class="flex items-center p-2 text-gray-900 rounded-lg transition duration-75 group
                              {{ request()->routeIs('admin.subscriptions.*') ? 'bg-gray-100' : 'hover:bg-gray-100' }}">
                        <svg class="w-5 h-5 text-gray-500 transition duration-75 group-hover:text-gray-900"
                             aria-hidden="true" fill="none" stroke-linecap="round"
                             stroke-linejoin="round" stroke-width="2"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                            <path d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                        </svg>
                        <span class="ml-3">clube de compra</span>
                    </a>
                </li>

                <!-- configurações da loja -->
                <li>
                    <a href="{{ route('admin.store-settings.index') }}"
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md
                              {{ request()->routeIs('admin.store-settings.*') ? 'bg-gray-100' : 'hover:bg-gray-100' }}">
                        <svg class="w-5 h-5 text-gray-500 transition duration-75 group-hover:text-gray-900"
                             aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                             fill="currentColor" viewBox="0 0 20 20">
                            <path d="M5 4a1 1 0 00-2 0v7.268a2 2 0 000 3.464V16a1 1 0 102 0v-1.268a2 2 0 000-3.464V4a1 1 0 011-1z" />
                            <path d="M11 4a1 1 0 10-2 0v1.268a2 2 0 000 3.464V16a1 1 0 11-2 0V8.732a2 2 0 000-3.464V4z" />
                        </svg>
                        <span class="ml-3">Configurações da loja</span>
                    </a>
                </li>

                <!-- Configurações gerais de produtos -->
                <li>
                    <a href="{{ route('admin.settings.index') }}"
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md
                       {{ request()->routeIs('admin.settings.*') && !request()->routeIs('admin.store-settings.*') ? 'bg-gray-100' : 'hover:bg-gray-100' }}">
                        <svg class="w-5 h-5 text-gray-500 transition duration-75 group-hover:text-gray-900"
                             aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                             fill="currentColor" viewBox="0 0 20 20">
                            <path d="M8 0a1 1 0 00-0.707 0.293l-7 7a1 1 0 000 1.414l7 7a1 1 0 001.414 0l7-7a1 1 0 000-1.414l-7-7a1 1 0 00-0.707-0.293zM8 2.414l5.586 5.586-5.586 5.586-5.586-5.586 5.586-5.586z"/>
                        </svg>
                        <span class="ml-3">Configurações de produtos</span>
                    </a>
                </li>

                <!-- Orders -->
                <li>
                    <a href="#"
                       class="flex items-center p-2 text-gray-900 rounded-lg transition duration-75 group
                              {{ request()->routeIs('admin.orders.*') ? 'bg-gray-100' : 'hover:bg-gray-100' }}">
                        <svg class="w-5 h-5 text-gray-500 transition duration-75 group-hover:text-gray-900"
                             aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                             fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 14H4V6h16v12zM6 10h2v2H6v-2zm0 4h8v2h-8v-2zm10 0h2v2h-2v-2zm-6-4h8v2h-8v-2z"/>
                        </svg>
                        <span class="ml-3">Pedidos</span>
                    </a>
                </li>

                <!-- Customers -->
                <li>
                    <a href="{{ route('admin.customers.index') }}"
                       class="flex items-center p-2 text-gray-900 rounded-lg transition duration-75 group
                              {{ request()->routeIs('admin.customers.*') ? 'bg-gray-100' : 'hover:bg-gray-100' }}">
                        <svg class="w-5 h-5 text-gray-500 transition duration-75 group-hover:text-gray-900"
                             aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                             fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm0 2a5 5 0 1 1 0-10 5 5 0 0 1 0 10zm0-15a1 1 0 0 1 1 1v2a1 1 0 0 1-2 0V3a1 1 0 0 1 1-1zm0 16a1 1 0 0 1 1 1v2a1 1 0 0 1-2 0v-2a1 1 0 0 1 1-1zm7-7a1 1 0 0 1-1 1h-2a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1zM3 12a1 1 0 0 1 1-1h2a1 1 0 0 1 0 2H4a1 1 0 0 1-1-1z"/>
                        </svg>
                        <span class="ml-3">Clientes</span>
                    </a>
                </li>

                <!-- Relatórios -->
                <li>
                    <a href="{{ route('admin.reports.index') }}"
                       class="flex items-center p-2 text-gray-900 rounded-lg transition duration-75 group
                              {{ request()->routeIs('admin.reports.*') ? 'bg-gray-100' : 'hover:bg-gray-100' }}">
                        <svg class="w-5 h-5 text-gray-500 transition duration-75 group-hover:text-gray-900"
                             aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                             fill="currentColor" viewBox="0 0 24 24">
                            <path d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span class="ml-3">Relatórios</span>
                    </a>
                </li>

                <!-- Internal Categories -->
                <li>
                    <a href="{{ route('admin.cat-style-shop.index') }}"
                       class="flex items-center p-2 text-gray-900 rounded-lg transition duration-75 group
                              {{ request()->routeIs('admin.cat-style-shop.*') ? 'bg-gray-100' : 'hover:bg-gray-100' }}">
                        <svg class="w-5 h-5 text-gray-500 transition duration-75 group-hover:text-gray-900"
                             aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                             fill="currentColor" viewBox="0 0 24 24">
                            <path d="M10 3H4a1 1 0 00-1 1v6a1 1 0 00 1 1h6a1 1 0 00 1-1V4a1 1 0 00-1-1zm10 0H4V4h16v10h-6a1 1 0 00-1-1V4z"/>
                        </svg>
                        <span class="ml-3">Categorias Internas</span>
                    </a>
                </li>
            </ul>

        </div>
    </aside>