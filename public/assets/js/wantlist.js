function toggleWantlist(productId, productType, button) {
    fetch("/wantlist/toggle", {
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
          updateWantlistButton(button, data.added)
          window.showToast(data.message, data.added ? "success" : "info")
        } else {
          window.showToast(data.message || "Erro ao atualizar Wantlist", "error")
        }
      })
      .catch((error) => {
        console.error("Error:", error)
        if (error.message === "Unauthorized") {
          window.showToast("Por favor, faça login para adicionar à Wantlist.", "error")
        } else {
          window.showToast(
            error.message || "Ocorreu um erro com sua requisição, tente novamente em outro momento.",
            "error",
          )
        }
      })
  }

  function updateWantlistButton(button, added) {
    const icon = button.querySelector("i")
    const text = button.querySelector("span")
    if (added) {
      button.dataset.inWantlist = "true"
      button.title = "Remover da Wantlist"
      icon.classList.remove("fa-bookmark-o")
      icon.classList.add("fa-bookmark")
      text.textContent = "Remover da Wantlist"
    } else {
      button.dataset.inWantlist = "false"
      button.title = "Adicionar à Wantlist"
      icon.classList.remove("fa-bookmark")
      icon.classList.add("fa-bookmark-o")
      text.textContent = "Adicionar à Wantlist"
    }
  }

