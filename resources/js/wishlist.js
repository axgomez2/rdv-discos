document.addEventListener('DOMContentLoaded', function() {
    const wishlistButtons = document.querySelectorAll('.wishlist-button');

    wishlistButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Se não houver ID do produto, provavelmente é um usuário não logado
            if (!this.dataset.productId) {
                Swal.fire({
                    title: 'Atenção!',
                    text: 'Você precisa estar logado para adicionar itens aos favoritos.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Fazer Login',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '/login';
                    }
                });
                return;
            }

            const productId = this.dataset.productId;
            const productType = this.dataset.productType;
            const isAvailable = this.dataset.isAvailable === 'true';
            const icon = this.querySelector('i');

            // Desabilita o botão temporariamente para evitar cliques duplos
            this.disabled = true;

            fetch('/wishlist/toggle-favorite', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    product_id: productId,
                    product_type: productType
                })
            })
            .then(response => {
                if (response.status === 401) {
                    // Usuário não está logado
                    Swal.fire({
                        title: 'Atenção!',
                        text: 'Você precisa estar logado para adicionar itens aos favoritos.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Fazer Login',
                        cancelButtonText: 'Cancelar',
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '/login';
                        }
                    });
                    throw new Error('Unauthorized');
                }
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Atualiza o ícone baseado no status do item
                    if (data.in_wishlist || data.in_wantlist) {
                        icon.classList.add('text-red-500');
                        if (!data.is_in_stock) {
                            icon.classList.remove('fa-heart');
                            icon.classList.add('fa-flag');
                            this.title = 'Item na Wantlist';
                        } else {
                            icon.classList.remove('fa-flag');
                            icon.classList.add('fa-heart');
                            this.title = 'Remover dos favoritos';
                        }
                    } else {
                        icon.classList.remove('text-red-500', 'fa-flag');
                        icon.classList.add('fa-heart');
                        this.title = 'Adicionar aos favoritos';
                    }

                    // Atualiza o contador de wishlist no header (se existir)
                    const wishlistCounter = document.querySelector('.wishlist-count');
                    if (wishlistCounter) {
                        wishlistCounter.textContent = data.wishlistCount;
                    }

                    // Mostra mensagem de sucesso usando SweetAlert2
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer);
                            toast.addEventListener('mouseleave', Swal.resumeTimer);
                        }
                    });

                    Toast.fire({
                        icon: 'success',
                        title: data.message
                    });
                } else {
                    throw new Error(data.message || 'Erro ao processar solicitação');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                if (error.message !== 'Unauthorized') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Ops!',
                        text: 'Erro ao processar sua solicitação',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .finally(() => {
                // Reabilita o botão
                this.disabled = false;
            });
        });
    });
});
