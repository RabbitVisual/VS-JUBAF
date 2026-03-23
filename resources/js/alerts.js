/**
 * Sistema Global de Alertas e Notificações
 * Vertex Solutions LTDA © 2025
 *
 * Sistema completo de alertas para o sistema
 * Integra com Flowbite e Tailwind CSS
 */

/**
 * Exibe um alerta na tela
 * @param {string} message - Mensagem a ser exibida
 * @param {string} type - Tipo do alerta: success, error, warning, info
 * @param {object} options - Opções adicionais
 */
export function showAlert(message, type = "info", options = {}) {
    const {
        dismissible = true,
        autoClose = true,
        duration = 5000,
        position = "top-right", // top-right, top-left, top-center, bottom-right, bottom-left, bottom-center
    } = options;

    // Remove alertas anteriores se necessário
    if (options.replacePrevious) {
        const existingAlerts = document.querySelectorAll(
            "[data-alert-container]"
        );
        existingAlerts.forEach((alert) => alert.remove());
    }

    // Cria container se não existir
    let container = document.getElementById("alert-container");
    if (!container) {
        container = document.createElement("div");
        container.id = "alert-container";
        container.setAttribute("data-alert-container", "true");

        // Posicionamento
        const positionClasses = {
            "top-right": "fixed top-4 right-4 z-50",
            "top-left": "fixed top-4 left-4 z-50",
            "top-center":
                "fixed top-4 left-1/2 transform -translate-x-1/2 z-50",
            "bottom-right": "fixed bottom-4 right-4 z-50",
            "bottom-left": "fixed bottom-4 left-4 z-50",
            "bottom-center":
                "fixed bottom-4 left-1/2 transform -translate-x-1/2 z-50",
        };

        container.className = `${positionClasses[position]} space-y-2 max-w-md w-full`;
        document.body.appendChild(container);
    }

    // Configurações de estilo por tipo
    const alertConfigs = {
        success: {
            bg: "bg-green-50 dark:bg-green-900/20",
            border: "border-green-200 dark:border-green-800",
            text: "text-green-800 dark:text-green-200",
            icon: "text-green-400",
            iconSvg:
                '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>',
        },
        error: {
            bg: "bg-red-50 dark:bg-red-900/20",
            border: "border-red-200 dark:border-red-800",
            text: "text-red-800 dark:text-red-200",
            icon: "text-red-400",
            iconSvg:
                '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>',
        },
        warning: {
            bg: "bg-yellow-50 dark:bg-yellow-900/20",
            border: "border-yellow-200 dark:border-yellow-800",
            text: "text-yellow-800 dark:text-yellow-200",
            icon: "text-yellow-400",
            iconSvg:
                '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>',
        },
        info: {
            bg: "bg-blue-50 dark:bg-blue-900/20",
            border: "border-blue-200 dark:border-blue-800",
            text: "text-blue-800 dark:text-blue-200",
            icon: "text-blue-400",
            iconSvg:
                '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>',
        },
    };

    const config = alertConfigs[type] || alertConfigs.info;

    // Cria o elemento do alerta
    const alert = document.createElement("div");
    alert.className = `relative flex items-center p-4 border rounded-lg shadow-lg ${config.bg} ${config.border} ${config.text} transition-all duration-300`;
    alert.setAttribute("x-data", "{ show: true }");
    alert.setAttribute("x-show", "show");
    alert.setAttribute(
        "x-transition:enter",
        "transition ease-out duration-300"
    );
    alert.setAttribute(
        "x-transition:enter-start",
        "opacity-0 transform translate-y-2"
    );
    alert.setAttribute(
        "x-transition:enter-end",
        "opacity-100 transform translate-y-0"
    );
    alert.setAttribute("x-transition:leave", "transition ease-in duration-200");
    alert.setAttribute(
        "x-transition:leave-start",
        "opacity-100 transform translate-y-0"
    );
    alert.setAttribute(
        "x-transition:leave-end",
        "opacity-0 transform translate-y-2"
    );

    alert.innerHTML = `
        <div class="flex-shrink-0 ${config.icon}">
            ${config.iconSvg}
        </div>
        <div class="ml-3 flex-1">
            <p class="text-sm font-medium">${message}</p>
        </div>
        ${
            dismissible
                ? `
            <button
                onclick="this.closest('[x-data]').remove()"
                class="ml-auto -mx-1.5 -my-1.5 rounded-lg focus:ring-2 p-1.5 inline-flex h-8 w-8 ${config.bg} hover:opacity-75 transition-opacity"
                aria-label="Fechar"
            >
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        `
                : ""
        }
    `;

    container.appendChild(alert);

    // Auto-close
    if (autoClose) {
        setTimeout(() => {
            if (alert.parentNode) {
                alert.style.opacity = "0";
                alert.style.transform = "translateY(-10px)";
                setTimeout(() => alert.remove(), 200);
            }
        }, duration);
    }

    return alert;
}

/**
 * Exibe mensagem de sucesso
 */
export function showSuccess(message, options = {}) {
    return showAlert(message, "success", options);
}

/**
 * Exibe mensagem de erro
 */
export function showError(message, options = {}) {
    return showAlert(message, "error", {
        ...options,
        autoClose: options.autoClose !== false ? 7000 : false,
    });
}

/**
 * Exibe mensagem de aviso
 */
export function showWarning(message, options = {}) {
    return showAlert(message, "warning", options);
}

/**
 * Exibe mensagem informativa
 */
export function showInfo(message, options = {}) {
    return showAlert(message, "info", options);
}

/**
 * Remove todos os alertas
 */
export function clearAlerts() {
    const container = document.getElementById("alert-container");
    if (container) {
        container.remove();
    }
}

// Exporta para uso global
window.AlertSystem = {
    show: showAlert,
    success: showSuccess,
    error: showError,
    warning: showWarning,
    info: showInfo,
    clear: clearAlerts,
};
