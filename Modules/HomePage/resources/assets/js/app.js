import "flowbite";
import AOS from "aos";
import "aos/dist/aos.css";

// Inicializa o AOS (Animações de Scroll)
document.addEventListener("DOMContentLoaded", () => {
    AOS.init({
        duration: 800,
        easing: "ease-out-cubic",
        once: true,
        offset: 50,
    });

    // Inicializar botões de navegação
    initScrollNavigation();
});

// Função para inicializar os botões de navegação
function initScrollNavigation() {
    // Configurações do admin (passadas via data attributes ou variáveis globais)
    const settings = window.homepageSettings || {};

    // Valores padrão mais permissivos - mostrar por padrão se não configurado
    const showScrollToTop =
        settings.show_scroll_to_top === undefined
            ? (window.homepageSettings ? true : false)
            : settings.show_scroll_to_top !== false &&
            settings.show_scroll_to_top !== "0";

    const showScrollToBottom =
        settings.show_scroll_to_bottom === undefined
            ? (window.homepageSettings ? true : false)
            : settings.show_scroll_to_bottom !== false &&
            settings.show_scroll_to_bottom !== "0";
    const buttonPosition = settings.scroll_button_position || "bottom-right";
    const buttonSize = settings.scroll_button_size || "medium";
    const animationType = settings.scroll_animation_type || "smooth";

    // Aplicar configurações de tamanho
    const sizeClasses = {
        small: "w-10 h-10",
        medium: "w-12 h-12",
        large: "w-14 h-14",
    };

    // Aplicar configurações de posição
    const positionClasses = {
        "bottom-right": "bottom-6 right-6",
        "bottom-left": "bottom-6 left-6",
    };

    // Criar botão scroll-to-top
    if (showScrollToTop) {
        const scrollToTopBtn = document.createElement("button");
        scrollToTopBtn.className = `scroll-nav-button scroll-to-top ${sizeClasses[buttonSize]} ${positionClasses[buttonPosition]} transition-all duration-300 transform translate-y-20 opacity-0`;
        scrollToTopBtn.setAttribute("aria-label", "Voltar ao topo");
        scrollToTopBtn.innerHTML = `<i class="fa-duotone fa-solid fa-arrow-up-to-line scroll-nav-icon"></i>`;
        document.body.appendChild(scrollToTopBtn);
    }

    // Criar botão scroll-to-bottom para a seção hero
    if (showScrollToBottom) {
        const scrollToBottomBtn = document.createElement("button");
        scrollToBottomBtn.className = `scroll-nav-button scroll-to-bottom ${sizeClasses[buttonSize]} ${positionClasses[buttonPosition]} transition-all duration-300`;
        scrollToBottomBtn.setAttribute("aria-label", "Rolar para baixo");
        scrollToBottomBtn.innerHTML = `<i class="fa-duotone fa-solid fa-arrow-down-long scroll-nav-icon"></i>`;
        document.body.appendChild(scrollToBottomBtn);
    }

    // Referências aos botões criados
    const scrollToTopBtn = document.querySelector(".scroll-to-top");
    const scrollToBottomBtn = document.querySelector(".scroll-to-bottom");

    // Controle de visibilidade baseado no scroll
    let lastScrollTop = 0;
    const heroSection = document.querySelector(
        "section.relative.bg-gradient-to-br"
    );

    function toggleScrollButtons() {
        const scrollTop =
            window.pageYOffset || document.documentElement.scrollTop;
        const windowHeight = window.innerHeight;

        // Mostrar/esconder botão scroll-to-top
        if (scrollToTopBtn) {
            if (scrollTop > windowHeight * 0.5) {
                scrollToTopBtn.classList.add("visible");
            } else {
                scrollToTopBtn.classList.remove("visible");
            }
        }

        // Mostrar/esconder botão scroll-to-bottom baseado na posição da hero section
        if (scrollToBottomBtn && heroSection) {
            const heroRect = heroSection.getBoundingClientRect();
            const heroVisible =
                heroRect.top < windowHeight && heroRect.bottom > 0;

            if (heroVisible && scrollTop < windowHeight * 0.8) {
                scrollToBottomBtn.classList.remove("hide");
            } else {
                scrollToBottomBtn.classList.add("hide");
            }
        }

        lastScrollTop = scrollTop;
    }

    // Eventos de scroll
    window.addEventListener("scroll", toggleScrollButtons, { passive: true });

    // Eventos de clique
    if (scrollToTopBtn) {
        scrollToTopBtn.addEventListener("click", () => {
            scrollToPosition(0, animationType);
        });
    }

    if (scrollToBottomBtn) {
        scrollToBottomBtn.addEventListener("click", () => {
            // Rolar para a próxima seção após a hero (about section)
            const aboutSection =
                document.querySelector("#sobre") ||
                document.querySelector("section.py-20.bg-white");
            if (aboutSection) {
                const offset = aboutSection.offsetTop - 80; // Offset para margem superior
                scrollToPosition(offset, animationType);
            } else {
                // Fallback: rolar uma tela inteira
                scrollToPosition(window.innerHeight, animationType);
            }
        });
    }

    // Função auxiliar para scroll suave
    function scrollToPosition(position, behavior = "smooth") {
        window.scrollTo({
            top: position,
            behavior: behavior === "smooth" ? "smooth" : "auto",
        });
    }

    // Verificar visibilidade inicial
    toggleScrollButtons();

    // Suporte para navegação por teclado
    document.addEventListener("keydown", (e) => {
        // Tecla Home para ir ao topo
        if (e.key === "Home") {
            e.preventDefault();
            scrollToPosition(0);
        }
        // Tecla End para ir ao final
        if (e.key === "End") {
            e.preventDefault();
            scrollToPosition(document.body.scrollHeight);
        }
    });

    // Animações AOS para os botões
    AOS.refresh();
}

// Lógica extra para o Carousel se necessário (O Flowbite já gerencia via data attributes, mas podemos reforçar)
// Se precisar de lógica customizada do Alpine para a home, adicione aqui.
