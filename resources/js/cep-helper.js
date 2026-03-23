/**
 * Helper para busca de CEP
 * Integra com o sistema de máscaras e API de CEP
 * Vertex Solutions LTDA © 2025
 */

/**
 * Busca endereço por CEP
 * @param {string} cep - CEP com ou sem máscara
 * @returns {Promise<Object|null>} Dados do endereço ou null se não encontrado
 */
export async function buscarCep(cep) {
    // Remove formatação
    const cepLimpo = cep.replace(/\D/g, '');

    if (cepLimpo.length !== 8) {
        throw new Error('CEP inválido');
    }

    try {
        const response = await fetch(`/api/cep/buscar?cep=${cepLimpo}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        if (!response.ok) {
            return null;
        }

        const data = await response.json();

        // Verifica se a resposta tem o formato esperado
        if (data.success && data.data) {
            return data.data;
        }

        // Se não tiver data, retorna null
        return null;
    } catch (error) {
        console.error('Erro ao buscar CEP:', error);
        return null;
    }
}

/**
 * Valida CEP
 * @param {string} cep - CEP com ou sem máscara
 * @returns {Promise<boolean>} True se válido
 */
export async function validarCep(cep) {
    const cepLimpo = cep.replace(/\D/g, '');

    if (cepLimpo.length !== 8) {
        return false;
    }

    try {
        const response = await fetch(`/api/cep/validar?cep=${cepLimpo}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        const data = await response.json();
        return data.valido === true;
    } catch (error) {
        console.error('Erro ao validar CEP:', error);
        return false;
    }
}

/**
 * Busca cidades por UF
 * @param {string} uf - Unidade Federativa (2 caracteres)
 * @returns {Promise<Array>} Lista de cidades
 */
export async function buscarCidadesPorUf(uf) {
    try {
        const response = await fetch(`/api/cep/cidades/${uf.toUpperCase()}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        const data = await response.json();
        return data.success ? data.data : [];
    } catch (error) {
        console.error('Erro ao buscar cidades:', error);
        return [];
    }
}

/**
 * Busca cidades por nome
 * @param {string} cidade - Nome da cidade
 * @returns {Promise<Array>} Lista de cidades
 */
export async function buscarCidadesPorNome(cidade) {
    try {
        const response = await fetch(`/api/cep/cidades?cidade=${encodeURIComponent(cidade)}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        const data = await response.json();
        return data.success ? data.data : [];
    } catch (error) {
        console.error('Erro ao buscar cidades:', error);
        return [];
    }
}

/**
 * Preenche automaticamente campos de endereço ao digitar CEP
 * @param {string} cepInputSelector - Seletor do input de CEP
 * @param {Object} fieldSelectors - Objetos com seletores dos campos a preencher
 * @example
 * autoFillCep('#cep', {
 *   cidade: '#cidade',
 *   uf: '#uf',
 *   logradouro: '#logradouro'
 * });
 */
export function autoFillCep(cepInputSelector, fieldSelectors = {}) {
    const cepInput = document.querySelector(cepInputSelector);

    if (!cepInput) {
        console.warn(`Input de CEP não encontrado: ${cepInputSelector}`);
        return;
    }

    let timeout;

    cepInput.addEventListener('blur', async function() {
        clearTimeout(timeout);

        timeout = setTimeout(async () => {
            const cep = this.value;
            const cepLimpo = cep.replace(/\D/g, '');

            if (cepLimpo.length !== 8) {
                return;
            }

            // Mostra loading
            if (fieldSelectors.loading) {
                const loadingEl = document.querySelector(fieldSelectors.loading);
                if (loadingEl) loadingEl.style.display = 'block';
            }

            try {
                const endereco = await buscarCep(cep);

                if (endereco) {
                    // Preenche cidade
                    if (fieldSelectors.cidade) {
                        const cidadeEl = document.querySelector(fieldSelectors.cidade);
                        if (cidadeEl) cidadeEl.value = endereco.cidade;
                    }

                    // Preenche UF
                    if (fieldSelectors.uf) {
                        const ufEl = document.querySelector(fieldSelectors.uf);
                        if (ufEl) ufEl.value = endereco.uf;
                    }

                    // Dispara evento customizado
                    cepInput.dispatchEvent(new CustomEvent('cep-encontrado', {
                        detail: endereco
                    }));
                } else {
                    // Dispara evento de CEP não encontrado
                    cepInput.dispatchEvent(new CustomEvent('cep-nao-encontrado', {
                        detail: { cep }
                    }));

                    // Mostra mensagem de erro se houver campo de erro
                    if (fieldSelectors.error) {
                        const errorEl = document.querySelector(fieldSelectors.error);
                        if (errorEl) {
                            errorEl.textContent = 'CEP não encontrado';
                            errorEl.classList.remove('hidden');
                        }
                    }
                }
            } catch (error) {
                console.error('Erro ao buscar CEP:', error);
            } finally {
                // Esconde loading
                if (fieldSelectors.loading) {
                    const loadingEl = document.querySelector(fieldSelectors.loading);
                    if (loadingEl) loadingEl.style.display = 'none';
                }
            }
        }, 500); // Aguarda 500ms após parar de digitar
    });
}

// Exporta para uso global
window.CepHelper = {
    buscarCep,
    validarCep,
    buscarCidadesPorUf,
    buscarCidadesPorNome,
    autoFillCep,
};
