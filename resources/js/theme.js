/**
 * Dark Mode Toggle - Flowbite v4.0.1 & Tailwind CSS v4.1 Official Implementation
 * Follows official Flowbite and Tailwind CSS documentation for theme management
 *
 * References:
 * - Flowbite Dark Mode: https://flowbite.com/docs/customize/dark-mode/
 * - Tailwind CSS Dark Mode: https://tailwindcss.com/docs/dark-mode
 */

(function () {
    'use strict';

    const THEME_STORAGE_KEY = 'theme';
    const DARK_CLASS = 'dark';

    /**
     * Get initial theme from localStorage or system preference
     * @returns {string} 'dark' or 'light'
     */
    function getInitialTheme() {
        // Check localStorage first
        const savedTheme = localStorage.getItem(THEME_STORAGE_KEY);
        if (savedTheme === 'dark' || savedTheme === 'light') {
            return savedTheme;
        }

        // Check system preference
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            return 'dark';
        }

        return 'light';
    }

    /**
     * Apply theme to document
     * @param {string} theme - 'dark' or 'light'
     */
    function setTheme(theme) {
        const html = document.documentElement;

        if (theme === 'dark') {
            html.classList.add(DARK_CLASS);
        } else {
            html.classList.remove(DARK_CLASS);
        }

        // Save to localStorage
        localStorage.setItem(THEME_STORAGE_KEY, theme);

        // Dispatch custom event for theme change
        const event = new CustomEvent('theme-change', {
            detail: { theme: theme }
        });
        document.dispatchEvent(event);
    }

    /**
     * Toggle between dark and light theme
     */
    function toggleTheme() {
        const currentTheme = getTheme();
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        setTheme(newTheme);
    }

    /**
     * Get current theme
     * @returns {string} 'dark' or 'light'
     */
    function getTheme() {
        return localStorage.getItem(THEME_STORAGE_KEY) || getInitialTheme();
    }

    // Initialize theme on page load (before DOMContentLoaded to prevent flash)
    const initialTheme = getInitialTheme();
    setTheme(initialTheme);

    // Listen for system theme changes (only if user hasn't manually set a theme)
    if (window.matchMedia) {
        const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');

        // Use addEventListener for better browser support
        if (mediaQuery.addEventListener) {
            mediaQuery.addEventListener('change', (e) => {
                // Only apply system preference if user hasn't manually set a theme
                if (!localStorage.getItem(THEME_STORAGE_KEY)) {
                    setTheme(e.matches ? 'dark' : 'light');
                }
            });
        } else {
            // Fallback for older browsers
            mediaQuery.addListener((e) => {
                if (!localStorage.getItem(THEME_STORAGE_KEY)) {
                    setTheme(e.matches ? 'dark' : 'light');
                }
            });
        }
    }

    // Expose functions globally
    window.toggleTheme = toggleTheme;
    window.setTheme = setTheme;
    window.getTheme = getTheme;

    // Update icons on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            attachEventListeners();
        });
    } else {
        attachEventListeners();
    }

    /**
     * Attach click event listeners to theme toggle buttons
     * Using delegation for robustness
     */
    function attachEventListeners() {
        document.addEventListener('click', (e) => {
            const toggle = e.target.closest('#theme-toggle, #theme-toggle-mobile, [data-theme-toggle]');
            if (toggle) {
                toggleTheme();
            }
        });
    }
})();
