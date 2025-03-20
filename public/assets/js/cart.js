document.addEventListener("DOMContentLoaded", () => {
    // Listener para botões de adicionar ao carrinho
    document.body.addEventListener("click", (event) => {
      const addToCartButton = event.target.closest(".add-to-cart-button")
      if (addToCartButton) {
        const productId = addToCartButton.dataset.productId
        const quantity = Number.parseInt(addToCartButton.dataset.quantity, 10)
        addToCart(productId, quantity, addToCartButton)
      }

      // Listener para botões de favoritos
      const wishlistButton = event.target.closest(".wishlist-button")
      if (wishlistButton) {
        const productId = wishlistButton.dataset.productId
        const productType = wishlistButton.dataset.productType
        // toggleWishlist(wishlistButton, productType, productId)
      }
    })
  })

  function addToCart(productId, quantity, button) {
    button.disabled = true
    const originalText = button.querySelector(".add-to-cart-text").textContent
    button.querySelector(".add-to-cart-text").textContent = "Adicionando..."

    fetch("/carrinho/items", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
        Accept: "application/json",
      },
      body: JSON.stringify({
        product_id: productId,
        quantity: quantity,
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
        console.log("Dados recebidos:", data)
        if (data.success) {
          window.showToast(data.message, "success")
          if (data.cartCount !== undefined) {
            updateCartCount(data.cartCount)
          }
        } else {
          window.showToast(data.message || "Erro ao adicionar ao carrinho", "error")
        }
      })
      .catch((error) => {
        console.error("Error:", error)
        if (error.message === "Unauthorized") {
          window.showToast("Por favor, faça login para adicionar itens ao carrinho.", "error")
        } else {
          window.showToast(
            error.message ||
              "Ocorreu um erro ao adicionar o item ao seu carrinho. Por favor, tente novamente mais tarde.",
            "error",
          )
        }
      })
      .finally(() => {
        button.disabled = false
        button.querySelector(".add-to-cart-text").textContent = originalText
      })
  }

  function updateCartCount(count) {
    // Atualiza o badge do carrinho
    const cartBadge = document.querySelector("[data-cart-count]")
    if (cartBadge) {
      if (count > 0) {
        cartBadge.textContent = count
        cartBadge.classList.remove("hidden")
      } else {
        cartBadge.classList.add("hidden")
      }
    }
  }

  function updateWishlistCount(count) {
    // Atualiza o badge dos favoritos
    const wishlistBadge = document.querySelector("[data-wishlist-count]")
    if (wishlistBadge) {
      if (count > 0) {
        wishlistBadge.textContent = count
        wishlistBadge.classList.remove("hidden")
      } else {
        wishlistBadge.classList.add("hidden")
      }
    }
  }

  // function toggleWishlist(button, type, id) {
  //   fetch(`/wishlist/toggle/${type}/${id}`, {
  //     method: "POST",
  //     headers: {
  //       "Content-Type": "application/json",
  //       "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
  //       Accept: "application/json",
  //     },
  //   })
  //     .then((response) => {
  //       if (!response.ok) {
  //         if (response.status === 401) {
  //           throw new Error("Unauthorized")
  //         }
  //         return response.json().then((err) => {
  //           throw err
  //         })
  //       }
  //       return response.json()
  //     })
  //     .then((data) => {
  //       if (data.success) {
  //         // Atualiza o ícone
  //         const icon = button.querySelector("i")
  //         icon.classList.toggle("fa-regular")
  //         icon.classList.toggle("fa-solid")
  //         icon.classList.toggle("text-red-500")

  //         // Atualiza a contagem
  //         if (data.wishlistCount !== undefined) {
  //           updateWishlistCount(data.wishlistCount)
  //         }

  //         window.showToast(data.message, "success")
  //       } else {
  //         window.showToast(data.message || "Erro ao atualizar favoritos", "error")
  //       }
  //     })
  //     .catch((error) => {
  //       console.error("Error:", error)
  //       if (error.message === "Unauthorized") {
  //         window.showToast("Por favor, faça login para gerenciar seus favoritos.", "error")
  //       } else {
  //         window.showToast(
  //           error.message || "Ocorreu um erro ao atualizar seus favoritos. Por favor, tente novamente mais tarde.",
  //           "error",
  //         )
  //       }
  //     })
  // }
