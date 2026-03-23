/**
 * Sistema Global de Máscaras
 * Vertex Solutions LTDA © 2025
 *
 * Sistema completo de máscaras para formulários
 * Usa imask (instalado via npm)
 */

import IMask from "imask";

/**
 * Configurações de máscaras disponíveis
 */
export const maskConfigs = {
    // CPF: 000.000.000-00
    cpf: {
        mask: "000.000.000-00",
        placeholder: "___.___.___-__",
    },

    // CNPJ: 00.000.000/0000-00
    cnpj: {
        mask: "00.000.000/0000-00",
        placeholder: "__.___.___/____-__",
    },

    // Telefone: Dinâmico - (00) 0000-0000 ou (00) 9 0000-0000 (celular brasileiro)
    phone: {
        mask: [
            { mask: "(00) 0000-0000" }, // Telefone fixo (10 dígitos)
            { mask: "(00) 0 0000-0000" }, // Celular com 9 após DDD (11 dígitos) - formato: (DDD) 9 9988-7777
        ],
        dispatch: function (appended, dynamicMasked) {
            const number = (dynamicMasked.value + appended).replace(/\D/g, "");

            // Se tiver pelo menos 3 dígitos, verifica se o terceiro é 9 (indicando celular)
            if (number.length >= 3) {
                // O terceiro dígito (índice 2) é o primeiro após o DDD
                if (number[2] === "9") {
                    // Retorna a máscara compilada de celular (índice 1)
                    if (
                        dynamicMasked.compiledMasks &&
                        dynamicMasked.compiledMasks[1]
                    ) {
                        return dynamicMasked.compiledMasks[1];
                    }
                    return dynamicMasked.mask[1];
                }
            }

            // Se já tiver mais de 10 dígitos, assume que é celular
            if (number.length > 10) {
                if (
                    dynamicMasked.compiledMasks &&
                    dynamicMasked.compiledMasks[1]
                ) {
                    return dynamicMasked.compiledMasks[1];
                }
                return dynamicMasked.mask[1];
            }

            // Retorna a máscara de telefone fixo (índice 0)
            if (dynamicMasked.compiledMasks && dynamicMasked.compiledMasks[0]) {
                return dynamicMasked.compiledMasks[0];
            }
            return dynamicMasked.mask[0];
        },
        placeholder: "(__) _____-____",
    },

    // Celular: (00) 00000-0000
    cellphone: {
        mask: "(00) 00000-0000",
        placeholder: "(__) _____-____",
    },

    // CEP: 00000-000
    cep: {
        mask: "00000-000",
        placeholder: "_____-___",
    },

    // Data: 00/00/0000
    date: {
        mask: "00/00/0000",
        placeholder: "__/__/____",
    },

    // Hora: 00:00
    time: {
        mask: "00:00",
        placeholder: "__:__",
    },

    // Data e Hora: 00/00/0000 00:00
    datetime: {
        mask: "00/00/0000 00:00",
        placeholder: "__/__/____ __:__",
    },

    // Dinheiro: R$ 0,00
    money: {
        mask: Number,
        scale: 2,
        signed: false,
        thousandsSeparator: ".",
        padFractionalZeros: true,
        normalizeZeros: true,
        radix: ",",
        mapToRadix: ["."],
        min: 0,
        max: 999999999.99,
    },

    // Porcentagem: 0,00%
    percentage: {
        mask: Number,
        scale: 2,
        signed: false,
        thousandsSeparator: "",
        padFractionalZeros: true,
        normalizeZeros: true,
        radix: ",",
        mapToRadix: ["."],
        min: 0,
        max: 100,
    },

    // Número inteiro
    integer: {
        mask: Number,
        scale: 0,
        signed: false,
        thousandsSeparator: ".",
        min: 0,
    },

    // Placa de veículo: ABC-1234 ou ABC-1D23 (Mercosul)
    licensePlate: {
        mask: [
            {
                mask: "AAA-0000",
                regex: /^[A-Z]{3}[0-9]{4}$/,
            },
            {
                mask: "AAA-0A00",
                regex: /^[A-Z]{3}[0-9][A-Z][0-9]{2}$/,
            },
        ],
        dispatch: (appended, dynamicMasked) => {
            const numberMask = dynamicMasked.compiledMasks.find(
                (m) => m.mask === "AAA-0000"
            );
            const mercosulMask = dynamicMasked.compiledMasks.find(
                (m) => m.mask === "AAA-0A00"
            );

            if (appended.length <= 4) {
                return numberMask;
            }

            // Verifica se é placa Mercosul (4º caractere é letra)
            const value = dynamicMasked.value.replace(/\D/g, "");
            if (value.length >= 4 && /[A-Z]/.test(value[3])) {
                return mercosulMask;
            }

            return numberMask;
        },
    },

    // Cartão de crédito: 0000 0000 0000 0000
    creditCard: {
        mask: "0000 0000 0000 0000",
        placeholder: "____ ____ ____ ____",
    },

    // CVV: 000
    cvv: {
        mask: "000",
        placeholder: "___",
    },

    // Validade do cartão: 00/00
    cardExpiry: {
        mask: "MM/YY",
        blocks: {
            MM: {
                mask: IMask.MaskedRange,
                from: 1,
                to: 12,
            },
            YY: {
                mask: "00",
                from: 0,
                to: 99,
            },
        },
    },
};

/**
 * Aplica máscara a um elemento input
 * @param {HTMLElement|string} element - Elemento ou seletor
 * @param {string} maskType - Tipo de máscara (cpf, phone, etc.)
 * @param {object} options - Opções adicionais
 * @returns {IMask} Instância do IMask
 */
export function applyMask(element, maskType, options = {}) {
    const el =
        typeof element === "string" ? document.querySelector(element) : element;

    if (!el) {
        console.warn(
            `Elemento não encontrado para aplicar máscara: ${maskType}`
        );
        return null;
    }

    const config = maskConfigs[maskType];

    if (!config) {
        console.warn(`Tipo de máscara não encontrado: ${maskType}`);
        return null;
    }

    // Remove máscara anterior se existir
    if (el._maskInstance) {
        el._maskInstance.destroy();
    }

    // Aplica nova máscara
    const maskOptions = {
        ...config,
        ...options,
    };

    const mask = IMask(el, maskOptions);
    el._maskInstance = mask;

    return mask;
}

/**
 * Remove máscara de um elemento
 * @param {HTMLElement|string} element - Elemento ou seletor
 */
export function removeMask(element) {
    const el =
        typeof element === "string" ? document.querySelector(element) : element;

    if (el && el._maskInstance) {
        el._maskInstance.destroy();
        delete el._maskInstance;
    }
}

/**
 * Obtém valor sem máscara
 * @param {HTMLElement|string} element - Elemento ou seletor
 * @returns {string} Valor sem máscara
 */
export function getUnmaskedValue(element) {
    const el =
        typeof element === "string" ? document.querySelector(element) : element;

    if (el && el._maskInstance) {
        return el._maskInstance.unmaskedValue;
    }

    return el ? el.value : "";
}

/**
 * Aplica máscara automática baseada em atributos data-*
 * Inicializa todos os inputs com atributo data-mask
 */
export function initAutoMasks() {
    document.querySelectorAll("[data-mask]").forEach((element) => {
        const maskType = element.getAttribute("data-mask");
        const options = {};

        // Lê opções do atributo data-mask-options (JSON)
        const optionsAttr = element.getAttribute("data-mask-options");
        if (optionsAttr) {
            try {
                Object.assign(options, JSON.parse(optionsAttr));
            } catch (e) {
                console.warn("Erro ao parsear data-mask-options:", e);
            }
        }

        applyMask(element, maskType, options);
    });
}

/**
 * Máscara dinâmica de telefone (detecta se é celular ou fixo)
 * @param {HTMLElement|string} element - Elemento ou seletor
 * @returns {IMask} Instância do IMask
 */
export function applyPhoneMask(element) {
    const el =
        typeof element === "string" ? document.querySelector(element) : element;

    if (!el) return null;

    // Remove máscara anterior
    if (el._maskInstance) {
        el._maskInstance.destroy();
    }

    const mask = IMask(el, {
        mask: [
            {
                mask: "(00) 0000-0000", // Telefone fixo
                startsWith: "0",
            },
            {
                mask: "(00) 00000-0000", // Celular
            },
        ],
        dispatch: (appended, dynamicMasked) => {
            const number = (dynamicMasked.value + appended).replace(/\D/g, "");

            // Se começa com 0, é telefone fixo
            if (number.startsWith("0")) {
                return dynamicMasked.compiledMasks.find(
                    (m) => m.mask === "(00) 0000-0000"
                );
            }

            // Se tem 11 dígitos, é celular
            if (number.length >= 11) {
                return dynamicMasked.compiledMasks.find(
                    (m) => m.mask === "(00) 00000-0000"
                );
            }

            // Por padrão, usa celular
            return dynamicMasked.compiledMasks.find(
                (m) => m.mask === "(00) 00000-0000"
            );
        },
    });

    el._maskInstance = mask;
    return mask;
}

/**
 * Valida CPF
 * @param {string} cpf - CPF com ou sem máscara
 * @returns {boolean} True se válido
 */
export function validateCPF(cpf) {
    cpf = cpf.replace(/\D/g, "");

    if (cpf.length !== 11) return false;
    if (/^(\d)\1+$/.test(cpf)) return false;

    let sum = 0;
    let remainder;

    for (let i = 1; i <= 9; i++) {
        sum += parseInt(cpf.substring(i - 1, i)) * (11 - i);
    }

    remainder = (sum * 10) % 11;
    if (remainder === 10 || remainder === 11) remainder = 0;
    if (remainder !== parseInt(cpf.substring(9, 10))) return false;

    sum = 0;
    for (let i = 1; i <= 10; i++) {
        sum += parseInt(cpf.substring(i - 1, i)) * (12 - i);
    }

    remainder = (sum * 10) % 11;
    if (remainder === 10 || remainder === 11) remainder = 0;
    if (remainder !== parseInt(cpf.substring(10, 11))) return false;

    return true;
}

/**
 * Valida CNPJ
 * @param {string} cnpj - CNPJ com ou sem máscara
 * @returns {boolean} True se válido
 */
export function validateCNPJ(cnpj) {
    cnpj = cnpj.replace(/\D/g, "");

    if (cnpj.length !== 14) return false;
    if (/^(\d)\1+$/.test(cnpj)) return false;

    let length = cnpj.length - 2;
    let numbers = cnpj.substring(0, length);
    const digits = cnpj.substring(length);
    let sum = 0;
    let pos = length - 7;

    for (let i = length; i >= 1; i--) {
        sum += numbers.charAt(length - i) * pos--;
        if (pos < 2) pos = 9;
    }

    let result = sum % 11 < 2 ? 0 : 11 - (sum % 11);
    if (result !== parseInt(digits.charAt(0))) return false;

    length = length + 1;
    numbers = cnpj.substring(0, length);
    sum = 0;
    pos = length - 7;

    for (let i = length; i >= 1; i--) {
        sum += numbers.charAt(length - i) * pos--;
        if (pos < 2) pos = 9;
    }

    result = sum % 11 < 2 ? 0 : 11 - (sum % 11);
    if (result !== parseInt(digits.charAt(1))) return false;

    return true;
}

/**
 * Valida CEP
 * @param {string} cep - CEP com ou sem máscara
 * @returns {boolean} True se válido
 */
export function validateCEP(cep) {
    cep = cep.replace(/\D/g, "");
    return cep.length === 8;
}

/**
 * Formata valor para exibição
 * @param {string|number} value - Valor a formatar
 * @param {string} maskType - Tipo de máscara
 * @returns {string} Valor formatado
 */
export function formatValue(value, maskType) {
    if (!value) return "";

    const config = maskConfigs[maskType];
    if (!config) return String(value);

    // Cria um input temporário para aplicar a máscara
    const tempInput = document.createElement("input");
    tempInput.value = String(value);

    const mask = applyMask(tempInput, maskType);
    const formatted = tempInput.value;

    removeMask(tempInput);

    return formatted;
}

// Inicialização automática quando o DOM estiver pronto
if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initAutoMasks);
} else {
    initAutoMasks();
}

// Exporta para uso global
window.MaskSystem = {
    applyMask,
    removeMask,
    getUnmaskedValue,
    initAutoMasks,
    applyPhoneMask,
    validateCPF,
    validateCNPJ,
    validateCEP,
    formatValue,
    maskConfigs,
};
