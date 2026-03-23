/**
 * Multi-Step Forms Handler - Flowbite v4.0.1 & Tailwind CSS v4.1
 * Handles multi-step login and registration forms
 */

(function() {
    'use strict';

    /**
     * Initialize multi-step form
     * @param {string} formId - Form element ID
     * @param {number} totalSteps - Total number of steps
     */
    function initMultiStepForm(formId, totalSteps) {
        const form = document.getElementById(formId);
        if (!form) return;

        let currentStep = 1;

        // Get step elements
        const stepElements = [];
        const stepButtons = [];
        const prevButtons = [];
        const nextButtons = [];

        for (let i = 1; i <= totalSteps; i++) {
            const stepEl = form.querySelector(`[data-step="${i}"]`);
            const stepBtn = form.querySelector(`[data-step-button="${i}"]`);
            const prevBtn = form.querySelector(`[data-prev-step="${i}"]`);
            const nextBtn = form.querySelector(`[data-next-step="${i}"]`);

            if (stepEl) stepElements.push({ step: i, element: stepEl });
            if (stepBtn) stepButtons.push({ step: i, button: stepBtn });
            if (prevBtn) prevButtons.push({ step: i, button: prevBtn });
            if (nextBtn) nextButtons.push({ step: i, button: nextBtn });
        }

        /**
         * Update step display
         */
        function updateStepDisplay() {
            // Hide all steps
            stepElements.forEach(({ element }) => {
                element.classList.add('hidden');
            });

            // Show current step
            const currentStepEl = stepElements.find(({ step }) => step === currentStep);
            if (currentStepEl) {
                currentStepEl.element.classList.remove('hidden');
            }

            // Update step buttons
            stepButtons.forEach(({ step, button }) => {
                if (step <= currentStep) {
                    button.classList.add('bg-blue-600', 'text-white');
                    button.classList.remove('bg-gray-200', 'text-gray-500', 'dark:bg-gray-700', 'dark:text-gray-400');
                } else {
                    button.classList.remove('bg-blue-600', 'text-white');
                    button.classList.add('bg-gray-200', 'text-gray-500', 'dark:bg-gray-700', 'dark:text-gray-400');
                }
            });

            // Update navigation buttons
            prevButtons.forEach(({ step, button }) => {
                if (step === currentStep) {
                    button.style.display = currentStep > 1 ? 'block' : 'none';
                }
            });

            nextButtons.forEach(({ step, button }) => {
                if (step === currentStep) {
                    button.textContent = currentStep === totalSteps ? 'Finalizar' : 'Próximo';
                }
            });
        }

        /**
         * Go to next step
         */
        function nextStep() {
            if (currentStep < totalSteps) {
                // Validate current step before proceeding
                if (validateStep(currentStep)) {
                    currentStep++;
                    updateStepDisplay();
                }
            } else {
                // Final step - submit form
                if (form.checkValidity()) {
                    form.submit();
                } else {
                    form.reportValidity();
                }
            }
        }

        /**
         * Go to previous step
         */
        function prevStep() {
            if (currentStep > 1) {
                currentStep--;
                updateStepDisplay();
            }
        }

        /**
         * Go to specific step
         */
        function goToStep(step) {
            if (step >= 1 && step <= totalSteps && step <= currentStep) {
                currentStep = step;
                updateStepDisplay();
            }
        }

        /**
         * Validate step
         */
        function validateStep(step) {
            const stepEl = stepElements.find(({ step: s }) => s === step);
            if (!stepEl) return true;

            const inputs = stepEl.element.querySelectorAll('input[required], select[required], textarea[required]');
            let isValid = true;

            inputs.forEach(input => {
                if (!input.checkValidity()) {
                    isValid = false;
                    input.classList.add('border-red-500', 'dark:border-red-500');
                    input.classList.remove('border-gray-300', 'dark:border-gray-600');
                } else {
                    input.classList.remove('border-red-500', 'dark:border-red-500');
                    input.classList.add('border-gray-300', 'dark:border-gray-600');
                }
            });

            // Special validation for password confirmation
            if (step === 2) {
                const password = stepEl.element.querySelector('#password');
                const passwordConfirmation = stepEl.element.querySelector('#password_confirmation');

                if (password && passwordConfirmation) {
                    if (password.value !== passwordConfirmation.value) {
                        isValid = false;
                        passwordConfirmation.classList.add('border-red-500', 'dark:border-red-500');
                        passwordConfirmation.classList.remove('border-gray-300', 'dark:border-gray-600');

                        // Show error message
                        let errorMsg = passwordConfirmation.parentElement.querySelector('.error-message');
                        if (!errorMsg) {
                            errorMsg = document.createElement('p');
                            errorMsg.className = 'mt-1 text-xs text-red-600 dark:text-red-400 error-message';
                            passwordConfirmation.parentElement.appendChild(errorMsg);
                        }
                        errorMsg.textContent = 'As senhas não coincidem';
                    } else {
                        passwordConfirmation.classList.remove('border-red-500', 'dark:border-red-500');
                        passwordConfirmation.classList.add('border-gray-300', 'dark:border-gray-600');

                        const errorMsg = passwordConfirmation.parentElement.querySelector('.error-message');
                        if (errorMsg) {
                            errorMsg.remove();
                        }
                    }
                }
            }

            return isValid;
        }

        // Attach event listeners
        nextButtons.forEach(({ step, button }) => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                nextStep();
            });
        });

        prevButtons.forEach(({ step, button }) => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                prevStep();
            });
        });

        stepButtons.forEach(({ step, button }) => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                goToStep(step);
            });
        });

        // Initialize
        updateStepDisplay();

        // Expose functions
        return {
            nextStep,
            prevStep,
            goToStep,
            getCurrentStep: () => currentStep
        };
    }

    // Initialize forms when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            // Initialize login form (if exists)
            const loginForm = document.getElementById('login-form');
            if (loginForm) {
                initMultiStepForm('login-form', 2);
                // Aplica máscara de CPF se existir
                const cpfInput = loginForm.querySelector('#cpf');
                if (cpfInput && window.MaskSystem) {
                    window.MaskSystem.applyMask(cpfInput, 'cpf');
                }
            }

            // Initialize register form (if exists)
            const registerForm = document.getElementById('register-form');
            if (registerForm) {
                initMultiStepForm('register-form', 3);
            }

            // Aplica máscaras em todos os inputs com data-mask
            if (window.MaskSystem) {
                window.MaskSystem.initAutoMasks();
            }
        });
    } else {
        const loginForm = document.getElementById('login-form');
        if (loginForm) {
            initMultiStepForm('login-form', 2);
        }

        const registerForm = document.getElementById('register-form');
        if (registerForm) {
            initMultiStepForm('register-form', 3);
        }
    }
})();
