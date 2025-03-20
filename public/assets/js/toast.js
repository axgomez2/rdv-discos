window.showToast = (message, type = "info") => {
    let backgroundColor, textColor, icon
    switch (type) {
      case "success":
        backgroundColor = "#4CAF50"
        textColor = "#FFFFFF"
        icon = '<i class="fas fa-check-circle mr-2"></i>'
        break
      case "error":
        backgroundColor = "#F44336"
        textColor = "#FFFFFF"
        icon = '<i class="fas fa-exclamation-circle mr-2"></i>'
        break
      default:
        backgroundColor = "#2196F3"
        textColor = "#FFFFFF"
        icon = '<i class="fas fa-info-circle mr-2"></i>'
    }

    Toastify({
      text: icon + message,
      duration: 3000,
      close: true,
      gravity: "top",
      position: "right",
      stopOnFocus: true,
      escapeMarkup: false,
      className: "custom-toast",
      style: {
        background: backgroundColor,
        color: textColor,
      },
      offset: {
        x: 20,
        y: 20,
      },
      onClick: () => {}, // Callback after click
    }).showToast()
  }

  const style = document.createElement("style")
  style.textContent = `
      .custom-toast {
          box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
          border-radius: 4px;
          padding: 12px 20px;
          font-size: 14px;
          font-weight: 500;
          animation: slideInRight 0.3s ease-in-out, fadeOut 0.3s ease-in-out 2.7s;
      }
      .custom-toast .toast-close {
          opacity: 0.7;
          font-size: 16px;
          padding-left: 10px;
      }
      .custom-toast .toast-close:hover {
          opacity: 1;
      }
      @keyframes slideInRight {
          from { transform: translateX(100%); }
          to { transform: translateX(0); }
      }
      @keyframes fadeOut {
          from { opacity: 1; }
          to { opacity: 0; }
      }
  `
  document.head.appendChild(style)

