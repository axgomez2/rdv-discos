<nav class="bg-white border-b border-gray-800">
    <!-- Top Navigation Bar -->
    <div class="max-w-screen-xl mx-auto px-4 py-2.5">
      <div class="flex items-center justify-between">
        <!-- Logo -->
        <div class="flex items-center">
          <a href="{{ route('site.home') }}" class="flex items-center">
            <img
                src="{{ asset('assets/images/logo.png') }}"
                alt="Logo"
                class="h-14 sm:h-20 md:h-20 lg:h-16 mt-2 mb-2"
            >
          </a>
        </div>

        <!-- Search Bar -->
        <div class="flex-1 max-w-3xl mx-4 hidden md:block">
          <form class="flex" action="{{ route('site.search') }}" method="GET">
            <div class="relative w-full">
              <input
                  type="search"
                  name="q"
                  class="block p-2.5 w-full z-20 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                  placeholder="O que você está procurando?"
              >
              <button
                  type="submit"
                  class="absolute top-0 right-0 h-full p-2.5 text-sm font-medium text-white bg-pink-600 rounded-r-lg border border-pink-600 hover:bg-yellow-700 focus:ring-4 focus:outline-none focus:ring-blue-300"
              >
                <i class="fa-solid fa-magnifying-glass"></i>
              </button>
            </div>
          </form>
        </div>

        <!-- Right Icons -->
        <div class="flex items-center space-x-4">
          <!-- Favorites -->
          @auth
          <a
              href="{{ route('site.wishlist.index') }}"
              class="relative inline-flex items-center p-2 text-gray-500 hover:text-gray-900"
          >
              <i class="fa-regular fa-heart text-xl"></i>
              @if($wishlistCount > 0)
                  <span
                      data-wishlist-count
                      class="absolute inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 border-2 border-white rounded-full -top-2 -right-2"
                  >
                      {{ $wishlistCount }}
                  </span>
              @endif
          </a>
          @else
          <button
              type="button"
              class="relative inline-flex items-center p-2 text-gray-500 hover:text-gray-900"
              @click="$dispatch('open-login-modal')"
          >
              <i class="fa-regular fa-heart text-xl"></i>
          </button>
          @endauth

          <!-- Cart -->
          @auth
          <button
              type="button"
              data-dropdown-toggle="cart-dropdown"
              class="relative inline-flex items-center p-2 text-gray-500 hover:text-gray-900"
          >
              <i class="fa-solid fa-cart-shopping text-xl"></i>
              @if($cartCount > 0)
                  <span
                      data-cart-count
                      class="absolute inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 border-2 border-white rounded-full -top-2 -right-2"
                  >
                      {{ $cartCount }}
                  </span>
              @endif
          </button>
          @else
          <button
              type="button"
              class="relative inline-flex items-center p-2 text-gray-500 hover:text-gray-900"
              onclick="showLoginToast()"
          >
              <i class="fa-solid fa-cart-shopping text-xl"></i>
          </button>
          @endauth

          @auth
          <!-- Dropdown do Usuário Autenticado -->
          <div class="relative" x-data="{ open: false }">
              <button
                  type="button"
                  @click="open = !open"
                  @keydown.escape.window="open = false"
                  @click.away="open = false"
                  class="flex items-center text-yellow-300 hover:text-yellow-900"
              >
                  <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="w-6 h-6"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke="currentColor"
                  >
                      <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M16 7a4 4 0
                             11-8 0 4 4 0
                             018 0zM12 14a7 7 0 00-7
                             7h14a7 7 0 00-7-7z"
                      />
                  </svg>
              </button>

              <!-- Dropdown menu -->
              <div
                  x-show="open"
                  x-transition:enter="transition ease-out duration-100"
                  x-transition:enter-start="transform opacity-0 scale-95"
                  x-transition:enter-end="transform opacity-100 scale-100"
                  x-transition:leave="transition ease-in duration-75"
                  x-transition:leave-start="transform opacity-100 scale-100"
                  x-transition:leave-end="transform opacity-0 scale-95"
                  class="absolute right-0 z-50 w-60 mt-2 origin-top-right bg-white rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
              >
                  <!-- User Info -->
                  <div class="px-4 py-3 border-b border-gray-100">
                      <p class="text-sm font-semibold text-gray-900">
                          {{ Auth::user()->name }}
                      </p>
                      <p class="text-sm text-gray-500 truncate">
                          {{ Auth::user()->email }}
                      </p>
                  </div>

                  <!-- Main Menu Items -->
                  <div class="py-2">
                      <a
                          href="{{ route('site.orders.index') }}"
                          class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                      >
                          <svg
                              xmlns="http://www.w3.org/2000/svg"
                              class="w-4 h-4 mr-3"
                              fill="none"
                              viewBox="0 0 24 24"
                              stroke="currentColor"
                          >
                              <path
                                  stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M16 11V7a4 4 0
                                     00-8 0v4M5 9h14l1
                                     12H4L5 9z"
                              />
                          </svg>
                          Meus Pedidos
                      </a>

                      <a
                          href="{{ route('site.wishlist.index') }}"
                          class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                      >
                          <svg
                              xmlns="http://www.w3.org/2000/svg"
                              class="w-4 h-4 mr-3"
                              fill="none"
                              viewBox="0 0 24 24"
                              stroke="currentColor"
                          >
                              <path
                                  stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M4.318 6.318a4.5
                                     4.5 0 000 6.364L12
                                     20.364l7.682-7.682a4.5
                                     4.5 0 00-6.364-6.364L12
                                     7.636l-1.318-1.318a4.5
                                     4.5 0 00-6.364 0z"
                              />
                          </svg>
                          favoritos
                      </a>

                      <a
                          href="{{ route('site.wantlist.index') }}"
                          class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                      >
                          <svg
                              xmlns="http://www.w3.org/2000/svg"
                              class="w-4 h-4 mr-3"
                              fill="none"
                              viewBox="0 0 24 24"
                              stroke="currentColor"
                          >
                              <path
                                  stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M16 15v-1a4 4 0
                                     00-4-4H8m0 0l3 3m-3-3l3-3m9
                                     14V5a2 2 0 00-2-2H6a2 2 0
                                     00-2 2v16l4-2 4 2 4-2 4
                                     2z"
                              />
                          </svg>
                          wantlist
                      </a>
                  </div>

                  <!-- Settings Section -->
                  <div class="py-2 border-t border-gray-100">
                      <a
                          href="{{ route('profile.edit') }}"
                          class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                      >
                          <svg
                              xmlns="http://www.w3.org/2000/svg"
                              class="w-4 h-4 mr-3"
                              fill="none"
                              viewBox="0 0 24 24"
                              stroke="currentColor"
                          >
                              <path
                                  stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M10.325 4.317c.426-1.756
                                     2.924-1.756 3.35 0a1.724
                                     1.724 0 002.573
                                     1.066c1.543-.94 3.31.826
                                     2.37 2.37a1.724
                                     1.724 0 001.065 2.572
                                     c1.756.426 1.756 2.924
                                     0 3.35a1.724 1.724 0
                                     00-1.066 2.573c.94 1.543
                                     -.826 3.31-2.37 2.37a1.724
                                     1.724 0 00-2.572 1.065c-
                                     .426 1.756-2.924 1.756
                                     -3.35 0a1.724 1.724 0 00-
                                     2.573-1.066c-1.543.94
                                     -3.31-.826-2.37-2.37a1.724
                                     1.724 0 00-1.065-2.572
                                     c-1.756-.426-1.756-2.924
                                     0-3.35a1.724 1.724 0 001.066
                                     -2.573c-.94-1.543.826-3.31
                                     2.37-2.37.996.608 2.296.07
                                     2.572-1.065z"
                              />
                              <path
                                  stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M15 12a3 3 0
                                     11-6 0 3 3 0
                                     016 0z"
                              />
                          </svg>
                          Minha conta
                      </a>
                  </div>



                  <!-- Logout Section -->
                  <div class="py-2 border-t border-gray-100">
                      <form method="POST" action="{{ route('logout') }}">
                          @csrf
                          <button
                              type="submit"
                              class="flex w-full items-center px-4 py-2 text-sm text-pink-700 hover:bg-red-100"
                          >
                              <svg
                                  xmlns="http://www.w3.org/2000/svg"
                                  class="w-4 h-4 mr-3"
                                  fill="none"
                                  viewBox="0 0 24 24"
                                  stroke="currentColor"
                              >
                                  <path
                                      stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M17 16l4-4m0 0l-4-4m4
                                         4H7m6 4v1a3 3 0
                                         01-3 3H6a3 3 0
                                         01-3-3V7a3 3 0
                                         013-3h4a3 3 0
                                         013 3v1"
                                  />
                              </svg>
                              Log Out
                          </button>
                      </form>
                  </div>
              </div>
          </div>
          @else
          <!-- Dropdown do Usuário Deslogado -->
          <div class="relative" x-data="{ open: false }">
              <button
                  type="button"
                  @click="open = !open"
                  @keydown.escape.window="open = false"
                  @click.away="open = false"
                  class="flex items-center text-gray-500 hover:text-gray-900"
              >
                  <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="w-6 h-6"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke="currentColor"
                  >
                      <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M16 7a4 4 0
                             11-8 0 4 4 0
                             018 0zM12 14a7 7 0
                             00-7 7h14a7 7 0
                             00-7-7z"
                      />
                  </svg>
              </button>

              <!-- Guest Dropdown menu -->
              <div
                  x-show="open"
                  x-transition:enter="transition ease-out duration-100"
                  x-transition:enter-start="transform opacity-0 scale-95"
                  x-transition:enter-end="transform opacity-100 scale-100"
                  x-transition:leave="transition ease-in duration-75"
                  x-transition:leave-start="transform opacity-100 scale-100"
                  x-transition:leave-end="transform opacity-0 scale-95"
                  class="absolute right-0 z-50 w-48 mt-2 origin-top-right bg-white rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
              >
                  <div class="py-1">
                      <button
                          @click="$dispatch('open-login-modal'); open = false"
                          class="flex w-full items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                      >
                          <svg
                              xmlns="http://www.w3.org/2000/svg"
                              class="w-4 h-4 mr-3"
                              fill="none"
                              viewBox="0 0 24 24"
                              stroke="currentColor"
                          >
                              <path
                                  stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M11 16l-4-4m0 0l4-4m-4
                                     4h14m-5 4v1a3 3 0
                                     01-3 3H6a3 3 0
                                     01-3-3V7a3 3 0
                                     013-3h7a3 3 0
                                     013 3v1"
                              />
                          </svg>
                          Login
                      </button>
                      <button
                          @click="$dispatch('open-register-modal'); open = false"
                          class="flex w-full items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                      >
                          <svg
                              xmlns="http://www.w3.org/2000/svg"
                              class="w-4 h-4 mr-3"
                              fill="none"
                              viewBox="0 0 24 24"
                              stroke="currentColor"
                          >
                              <path
                                  stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M18 9v3m0 0v3m0-3h3m-3
                                     0h-3m-2-5a4 4 0
                                     11-8 0 4 4 0
                                     018 0zM3 20a6 6 0
                                     0112 0v1H3v-1z"
                              />
                          </svg>
                          cadastro
                      </button>
                  </div>
              </div>
          </div>
          @endauth
        </div>
      </div>
    </div>

    <!-- Botão do Menu Mobile -->
    <button
        data-drawer-target="drawer-navigation"
        data-drawer-toggle="drawer-navigation"
        aria-controls="drawer-navigation"
        type="button"
        class="lg:hidden inline-flex items-center p-2 mt-2 ms-3 text-sm text-gray-500 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200"
    >
      <span class="sr-only">Open main menu</span>
      <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
        <path
            fill-rule="evenodd"
            d="M3 5a1 1 0
               011-1h12a1 1
               0 110 2H4a1 1
               0 01-1-1zM3
               10a1 1 0
               011-1h12a1
               1 0 110
               2H4a1 1
               0 01-1-1zM3
               15a1 1 0
               011-1h12a1
               1 0 110
               2H4a1 1
               0 01-1-1z"
            clip-rule="evenodd"
        ></path>
      </svg>
    </button>

    <!-- Menu Desktop -->
    <div class="border-t border-gray-200 hidden lg:block">
      <div class="max-w-screen-xl mx-auto px-2">
        <div class="flex items-center justify-center">
          <ul class="flex flex-wrap items-center py-3 text-base font-medium text-gray-500 space-x-8">
            <li><a href="{{ route('site.home') }}" class="hover:text-gray-900">Inicio</a></li>

            <!-- Dropdown Dinâmico para Discos -->
            <div class="relative" x-data="{ open: false }" x-init="$el.querySelector('[x-ref=megaMenuButton]').setAttribute('x-ref', 'megaMenuButton')">
                <button
                    x-ref="megaMenuButton"
                    @click="open = !open"
                    @keydown.escape.window="open = false"
                    class="flex items-center text-base font-medium text-gray-500 hover:text-gray-900"
                >
                    <span>Discos</span>
                    <svg
                        class="w-4 h-4 ml-1 transition-transform duration-200"
                        :class="{'rotate-180': open}"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0
                               011.414 0L10 10.586l3.293
                               -3.293a1 1 0
                               111.414 1.414l-4 4a1 1
                               0 01-1.414 0l-4-4a1
                               1 0 010-1.414z"
                            clip-rule="evenodd"
                        />
                    </svg>
                </button>

                <!-- Mega Menu -->
                <div
                    x-show="open"
                    x-cloak
                    @click.away="open = false"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 translate-y-1"
                    class="absolute left-0 z-50 w-screen max-w-screen-xl px-4 mt-4 sm:px-6 lg:px-8"
                >
                    <div class="overflow-hidden bg-white rounded-lg shadow-lg ring-1 ring-black ring-opacity-5">
                        <div class="relative grid gap-8 p-7 lg:grid-cols-4">
                            @if(isset($categories) && $categories->count())
                                @foreach($categories as $category)
                                    <div class="relative p-6 bg-white hover:bg-gray-50 rounded-lg transition-colors duration-200">
                                        <div class="flex items-center mb-4">
                                            <!-- Category Icon (padrão) -->
                                            <span class="flex items-center justify-center w-10 h-10 rounded-lg bg-blue-100">
                                                <svg
                                                    class="w-6 h-6 text-blue-600"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 20l-5.447-2.724A1 1 0
                                                           013 16.382V5.618a1 1 0
                                                           011.447-.894L9 7m0 13l6-3m-6
                                                           3V7m6 10l4.553 2.276A1 1
                                                           0 0021 18.382V7.618a1 1
                                                           0 00-.553-.894L15 4m0
                                                           13V4m0 0L9 7"
                                                    />
                                                </svg>
                                            </span>
                                            <h3 class="ml-3 text-base font-medium text-gray-900">
                                                {{ $category->nome }}
                                            </h3>
                                        </div>

                                        <div class="mt-2 space-y-3">
                                            <a
                                                href="{{ route('vinyls.byCategory', ['slug' => $category->slug]) }}"
                                                class="flex items-center text-sm text-gray-500 hover:text-gray-900"
                                            >
                                                <span>Ver todos os discos</span>
                                                <svg
                                                    class="w-4 h-4 ml-1"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 5l7 7-7 7"
                                                    />
                                                </svg>
                                            </a>
                                            @if($category->subcategories && $category->subcategories->count())
                                                @foreach($category->subcategories->take(3) as $subcategory)
                                                    <a
                                                        href="{{ route('vinyls.bySubcategory', [
                                                            'category' => $category->slug,
                                                            'subcategory' => $subcategory->slug
                                                        ]) }}"
                                                        class="block text-sm text-gray-500 hover:text-gray-900"
                                                    >
                                                        {{ $subcategory->name }}
                                                    </a>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="col-span-4 p-6">
                                    <p class="text-gray-500">Nenhuma categoria encontrada.</p>
                                </div>
                            @endif
                        </div>

                        <!-- Featured Section -->
                        <div class="p-4 bg-gray-50">
                            <div class="grid gap-4 md:grid-cols-2">
                                <a
                                    href="#"
                                    class="flex items-start p-3 rounded-lg hover:bg-gray-100"
                                >
                                    <div class="flex-shrink-0">
                                        <img
                                            class="w-12 h-12 rounded-lg object-cover"
                                            src="{{ asset('images/featured-vinyl.jpg') }}"
                                            alt="Featured Vinyl"
                                        >
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-900">
                                            Lançamentos
                                        </p>
                                        <p class="mt-1 text-sm text-gray-500">
                                            Confira os últimos lançamentos
                                        </p>
                                    </div>
                                </a>

                                <a
                                    href="#"
                                    class="flex items-start p-3 rounded-lg hover:bg-gray-100"
                                >
                                    <div class="flex-shrink-0">
                                        <img
                                            class="w-12 h-12 rounded-lg object-cover"
                                            src="{{ asset('images/sale-vinyl.jpg') }}"
                                            alt="Sale Vinyl"
                                        >
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-900">
                                            Promoções
                                        </p>
                                        <p class="mt-1 text-sm text-gray-500">
                                            Aproveite nossos descontos especiais
                                        </p>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Outros links -->
            <li><a href="#" class="text-base font-medium text-gray-500 hover:text-gray-900">Equipamentos</a></li>
            <li><a href="#" class="text-base font-medium text-gray-500 hover:text-gray-900">Sobre</a></li>
            <li><a href="#" class="text-base font-medium text-gray-500 hover:text-gray-900">Contato</a></li>
            <li><a href="#" class="text-base font-medium text-gray-500 hover:text-gray-900">Ofertas</a></li>
            <li><a href="{{ route('site.orders.index') }}" class="text-base font-medium text-gray-500 hover:text-gray-900">Meus Pedidos</a></li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Drawer Sidebar -->
    <div
        id="drawer-navigation"
        class="fixed top-0 left-0 z-40 w-64 h-screen p-4 overflow-y-auto
               transition-transform -translate-x-full bg-white"
        tabindex="-1"
        aria-labelledby="drawer-navigation-label"
    >
      <h5 id="drawer-navigation-label" class="text-base font-semibold text-gray-500 uppercase">
        Menu
      </h5>
      <button
          type="button"
          data-drawer-hide="drawer-navigation"
          aria-controls="drawer-navigation"
          class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900
                 rounded-lg text-sm w-8 h-8 absolute top-2.5 right-2.5 inline-flex
                 items-center justify-center"
      >
        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
          <path
              stroke="currentColor"
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"
          />
        </svg>
        <span class="sr-only">Close menu</span>
      </button>
      <div class="py-4 overflow-y-auto">
        <ul class="space-y-2 font-medium">
          <li>
            <a
                href="{{ route('site.home') }}"
                class="flex items-center p-2 text-gray-900 rounded-lg hover:bg-gray-100"
            >
              <span class="ml-3">Home</span>
            </a>
          </li>
          <!-- Outros itens do menu -->
        </ul>
      </div>
    </div>

    <!-- Mobile Search - Only visible on mobile -->
    <div class="md:hidden px-4 py-3 border-t border-gray-200">
      <form class="flex" action="{{ route('site.search') }}" method="GET">
        <div class="relative w-full">
          <input
              type="search"
              name="q"
              class="block w-full p-2.5 text-sm text-gray-900 bg-gray-50
                     rounded-lg border border-gray-300 focus:ring-blue-500
                     focus:border-blue-500"
              placeholder="Search..."
          >
          <button
              type="submit"
              class="absolute top-0 right-0 h-full p-2.5 text-sm font-medium text-white
                     bg-blue-600 rounded-r-lg border border-blue-600 hover:bg-blue-700
                     focus:ring-4 focus:outline-none focus:ring-blue-300"
          >
            <i class="fa-solid fa-magnifying-glass"></i>
          </button>
        </div>
      </form>
    </div>
</nav>

<!-- Cart Dropdown -->
@auth
<div
    id="cart-dropdown"
    class="hidden z-50 my-4 w-80 bg-white divide-y divide-gray-100 rounded-lg shadow"
>
  <div class="p-4">
    <div class="flex justify-between items-center mb-4">
      <h6 class="text-sm font-medium text-gray-900">
        Carrinho ({{ $cartCount }})
      </h6>
      <a href="{{ route('site.cart.index') }}" class="text-sm font-medium text-blue-600">
        Ver Carrinho
      </a>
    </div>
    <div class="flow-root">
      <ul class="divide-y divide-gray-100">
        @if($cartCount > 0 && $cart)
          @foreach($cart->items->take(4) as $item)
            <li class="flex py-3 items-center">
              <img
                  src="{{ $item->product->image ?? asset('placeholder.png') }}"
                  class="w-12 h-12 object-cover rounded"
                  alt="{{ $item->product->name }}"
              >
              <div class="ml-3">
                <p class="text-sm font-medium text-gray-900">
                  {{ $item->product->name }}
                </p>
                <p class="text-sm text-gray-500">
                  R$ {{ number_format($item->product->price, 2, ',', '.') }}
                </p>
              </div>
            </li>
          @endforeach
        @else
          <li class="py-3">
            <p class="text-sm text-gray-500">Seu carrinho está vazio</p>
          </li>
        @endif
      </ul>
    </div>
    @if($cartCount > 0)
      <div class="mt-4">
        <a
            href="{{ route('site.cart.index') }}"
            class="w-full text-white bg-blue-600 hover:bg-blue-700
                   font-medium rounded-lg text-sm px-5 py-2.5 text-center block"
        >
          Finalizar Compra
        </a>
      </div>
    @endif
  </div>
</div>
@endauth






  @push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Dropdown Toggle Script
      document.querySelectorAll('[data-dropdown-toggle]').forEach(trigger => {
        trigger.addEventListener('click', function(e) {
          e.stopPropagation();
          const dropdownId = this.getAttribute('data-dropdown-toggle');
          const dropdown = document.getElementById(dropdownId);
          if (dropdown) {
            dropdown.classList.toggle('hidden');
          }
        });
      });
      // Fecha dropdowns ao clicar fora
      document.addEventListener('click', function() {
        document.querySelectorAll('[id$="-dropdown"]').forEach(dropdown => {
          if (!dropdown.classList.contains('hidden')) {
            dropdown.classList.add('hidden');
          }
        });
      });
    });
  </script>
  <script>
    function showLoginToast() {
      Toastify({
        text: "Você precisa estar logado para acessar essa ação",
        duration: 3000,
        gravity: "bottom", // 'top' ou 'bottom'
        position: "right", // 'left', 'center' ou 'right'
        backgroundColor: "linear-gradient(to right, #FF5F6D, #FFC371)"
      }).showToast();
    }
  </script>

  <!-- Script para evitar que o mega menu abra antes da página carregar -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Inicialização adequada do Alpine para garantir que menus não abram automaticamente
      if (typeof Alpine !== 'undefined') {
        Alpine.start();
      }
    });
  </script>
  @endpush
