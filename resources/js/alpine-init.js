/**
 * Inicialização do Alpine.js e Flowbite para dropdowns
 * Alpine.js é importado diretamente no app.js
 */

document.addEventListener('DOMContentLoaded', function() {
    // Garante que Flowbite está inicializado
    if (typeof window.Flowbite !== 'undefined') {
        try {
            if (window.Flowbite.init) {
                window.Flowbite.init();
            }
        } catch (e) {
            console.warn('Erro ao inicializar Flowbite:', e);
        }
    }
});
