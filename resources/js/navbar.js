// Função para atualizar o carrinho
function updateCartPreview(items, count, total) {
    const cartCount = document.querySelector('.cart-count');
    const cartItems = document.querySelector('.cart-items');
    const cartTotal = document.querySelector('.cart-total');

    if (cartCount) cartCount.textContent = count;

    if (cartItems) {
        if (items.length > 0) {
            cartItems.innerHTML = items.map(item => `
                <div class="flex py-3 items-center">
                    <img src="${item.image || '/placeholder.jpg'}" class="w-12 h-12 object-cover rounded" alt="${item.name}">
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium text-gray-900">${item.name}</p>
                        <p class="text-sm text-gray-500">R$ ${Number(item.price).toFixed(2)}</p>
                    </div>
                </div>
            `).join('');
        } else {
            cartItems.innerHTML = '<div class="py-3"><p class="text-sm text-gray-500">Seu carrinho está vazio</p></div>';
        }
    }

    if (cartTotal) cartTotal.textContent = `R$ ${Number(total).toFixed(2)}`;
}

// Função para carregar dados do navbar
function loadNavbarData() {
    fetch('/navbar/data')
        .then(response => response.json())
        .then(data => {
            updateCategories(data.categories);
            updateCartPreview(data.cartItems, data.cartCount, data.cartTotal);
            updateUserMenu(data.user);
        })
        .catch(error => console.error('Erro ao carregar dados do navbar:', error));
}

// Função para atualizar as categorias no mega menu
function updateCategories(categories) {
    const megaMenu = document.querySelector('.mega-menu .grid');
    if (!megaMenu) return;

    let html = '';
    // Agrupa as categorias em colunas de 6 itens
    const itemsPerColumn = 6;
    for (let i = 0; i < categories.length; i += itemsPerColumn) {
        const columnCategories = categories.slice(i, i + itemsPerColumn);
        html += `
            <div>
                <ul class="space-y-2">
                    ${columnCategories.map(category => `
                        <li>
                            <a href="/categoria/${category.slug}" class="text-gray-600 hover:text-gray-900">
                                ${category.name}
                            </a>
                        </li>
                    `).join('')}
                </ul>
            </div>
        `;
    }

    megaMenu.innerHTML = html;
}

// Função para atualizar o menu do usuário
function updateUserMenu(user) {
    // Implemente a lógica para atualizar o menu do usuário
}

// Atualiza o carrinho quando necessário
document.addEventListener('cart:updated', updateCartPreview);

// Inicializa os dados do navbar quando a página carrega
document.addEventListener('DOMContentLoaded', loadNavbarData);
