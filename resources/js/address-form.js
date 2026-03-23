/**
 * Sistema de Formulário de Endereço com CEP
 * Integra com CepHelper e MaskSystem
 * Vertex Solutions LTDA © 2025
 */

/**
 * Inicializa todos os formulários de endereço na página
 */
export function initAddressForms() {
    document.querySelectorAll("[data-address-form]").forEach((form) => {
        initAddressForm(form);
    });
}

/**
 * Inicializa um formulário de endereço específico
 * @param {HTMLElement} formElement - Elemento do formulário
 */
function initAddressForm(formElement) {
    const cepInput = formElement.querySelector('input[data-mask="cep"]');

    if (!cepInput) {
        return;
    }

    const cepId = cepInput.id;
    const cityInput = formElement.querySelector(
        `#${cepId.replace("zip_code", "city")}`
    );
    const stateInput = formElement.querySelector(
        `#${cepId.replace("zip_code", "state")}`
    );
    const addressInput = formElement.querySelector(
        `#${cepId.replace("zip_code", "address")}`
    );
    const neighborhoodInput = formElement.querySelector(
        `#${cepId.replace("zip_code", "neighborhood")}`
    );
    const loadingEl = document.getElementById(`${cepId}-loading`);
    const errorEl = document.getElementById(`${cepId}-error`);
    const successEl = document.getElementById(`${cepId}-success`);

    let timeout;

    // Busca CEP ao sair do campo
    cepInput.addEventListener("blur", async function () {
        clearTimeout(timeout);

        timeout = setTimeout(async () => {
            const cep = this.value;
            const cepLimpo = cep.replace(/\D/g, "");

            // Limpa mensagens anteriores
            if (errorEl) {
                errorEl.classList.add("hidden");
                errorEl.textContent = "";
            }
            if (successEl) {
                successEl.classList.add("hidden");
                successEl.textContent = "";
            }

            if (cepLimpo.length !== 8) {
                if (cepLimpo.length > 0) {
                    if (errorEl) {
                        errorEl.textContent = "CEP deve ter 8 dígitos";
                        errorEl.classList.remove("hidden");
                    }
                    cepInput.classList.add(
                        "border-red-500",
                        "dark:border-red-500"
                    );
                    cepInput.classList.remove(
                        "border-green-500",
                        "dark:border-green-500"
                    );
                }
                return;
            }

            // Mostra loading
            if (loadingEl) loadingEl.classList.remove("hidden");
            cepInput.classList.remove("border-red-500", "dark:border-red-500");

            try {
                // Busca endereço diretamente
                const endereco = await window.CepHelper.buscarCep(cep);

                if (endereco) {
                    // Preenche campos
                    if (cityInput) cityInput.value = endereco.cidade || "";
                    if (stateInput) stateInput.value = endereco.uf || "";
                    // Preenche logradouro e bairro apenas se não estiverem preenchidos
                    if (addressInput && !addressInput.value)
                        addressInput.value = endereco.logradouro || "";
                    if (neighborhoodInput && !neighborhoodInput.value)
                        neighborhoodInput.value = endereco.bairro || "";

                    // Mostra sucesso
                    if (successEl) {
                        successEl.textContent = `CEP encontrado: ${endereco.cidade} - ${endereco.uf}`;
                        successEl.classList.remove("hidden");
                    }

                    cepInput.classList.add(
                        "border-green-500",
                        "dark:border-green-500"
                    );
                    cepInput.classList.remove(
                        "border-red-500",
                        "dark:border-red-500"
                    );

                    // Dispara evento customizado
                    cepInput.dispatchEvent(
                        new CustomEvent("cep-encontrado", {
                            detail: endereco,
                        })
                    );
                } else {
                    if (errorEl) {
                        errorEl.textContent = "CEP não encontrado";
                        errorEl.classList.remove("hidden");
                    }
                    cepInput.classList.add(
                        "border-red-500",
                        "dark:border-red-500"
                    );
                }
            } catch (error) {
                console.error("Erro ao buscar CEP:", error);
                if (errorEl) {
                    errorEl.textContent =
                        "Erro ao buscar CEP. Tente novamente.";
                    errorEl.classList.remove("hidden");
                }
                cepInput.classList.add("border-red-500", "dark:border-red-500");
            } finally {
                if (loadingEl) loadingEl.classList.add("hidden");
            }
        }, 500);
    });

    // Validação em tempo real
    cepInput.addEventListener("input", function () {
        const cep = this.value.replace(/\D/g, "");

        if (errorEl) errorEl.classList.add("hidden");
        if (successEl) successEl.classList.add("hidden");

        if (cep.length === 8) {
            this.classList.remove("border-red-500", "dark:border-red-500");
        }
    });
}

// Inicialização automática
if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initAddressForms);
} else {
    initAddressForms();
}

// Exporta para uso global
window.AddressForm = {
    init: initAddressForms,
    initForm: initAddressForm,
};
