document.addEventListener("DOMContentLoaded", () => {
    document.body.addEventListener("click", (event) => {
      const wishlistButton = event.target.closest(".wishlist-button")
      if (wishlistButton) {
        const productId = wishlistButton.dataset.productId
        const productType = wishlistButton.dataset.productType
        toggleFavorite(productId, productType, wishlistButton)
      }
    })
  })

  function toggleFavorite(productId, productType, button) {
    fetch("/favoritos/toggle", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
        Accept: "application/json",
      },
      body: JSON.stringify({
        product_id: productId,
        product_type: productType,
      }),
    })
      .then((response) => {
        if (!response.ok) {
          if (response.status === 401) {
            throw new Error("Unauthorized")
          }
          return response.json().then((err) => {
            throw err
          })
        }
        return response.json()
      })
      .then((data) => {
        if (data.success) {
          if (window.location.pathname.includes("/favoritos")) {
            // Se estamos na página de favoritos, recarregue a página
            window.location.reload()
          } else {
            updateWishlistButton(button, data.added)
            window.showToast(data.message, data.added ? "success" : "info")
          }
        } else {
          window.showToast(data.message || "Erro ao atualizar favoritos", "error")
        }
      })
      .catch((error) => {
        console.error("Error:", error)
        if (error.message === "Unauthorized") {
          window.showToast("Por favor, faça login para adicionar aos favoritos.", "error")
        } else if (error.errors && error.errors.product_id) {
          window.showToast("Erro: " + error.errors.product_id[0], "error")
        } else {
          window.showToast(
            error.message || "Ocorreu um erro com sua requisição, tente novamente em outro momento.",
            "error",
          )
        }
      })
  }

  function updateWishlistButton(button, added) {
    const icon = button.querySelector("i")
    if (added) {
      button.dataset.inWishlist = "true"
      button.title = "Remover dos favoritos"
      icon.classList.remove("text-gray-400", "hover:text-red-500")
      icon.classList.add("text-red-500")
    } else {
      button.dataset.inWishlist = "false"
      button.title = "Adicionar aos favoritos"
      icon.classList.remove("text-red-500")
      icon.classList.add("text-gray-400", "hover:text-red-500")
    }
  }

