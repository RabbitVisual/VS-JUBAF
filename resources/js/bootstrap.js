import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Configura CSRF token automaticamente
const csrfToken = document.querySelector('meta[name="csrf-token"]');
if (csrfToken) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken.getAttribute('content');
}

/**
 * Laravel Echo Configuration
 * Enables real-time broadcasting using Pusher (Production)
 * Desativado após a remoção do módulo de Chat
 */
/*
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: window.Laravel?.pusherKey || import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: window.Laravel?.pusherHost || import.meta.env.VITE_REVERB_HOST,
    wsPort: window.Laravel?.pusherPort || (import.meta.env.VITE_REVERB_PORT ?? 80),
    wssPort: window.Laravel?.pusherPort || (import.meta.env.VITE_REVERB_PORT ?? 443),
    forceTLS: (window.Laravel?.pusherScheme || (import.meta.env.VITE_REVERB_SCHEME ?? 'https')) === 'https',
    enabledTransports: ['ws', 'wss'],
    authEndpoint: '/broadcasting/auth',
});
*/
