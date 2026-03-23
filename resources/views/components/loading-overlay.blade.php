{{--
    Loading Overlay – Melhores práticas UX (feedback imediato, blur, mensagem contextual, tempo máximo).
    Inspirado em loader tipo Dragonlady: círculo em loop, leve (CSS puro). Sem CDN.
    Uso: <x-loading-overlay /> ou <x-loading-overlay message="Processando pagamento..." />
    Evento com mensagem: window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: 'Salvando...' } }));
--}}
@props(['message' => null])

@php
    $slotContent = trim((string) $slot);
    $defaultMessage = $message ?? ($slotContent !== '' ? $slotContent : __('Carregando...'));
    $messageForA11y = is_string($defaultMessage) ? $defaultMessage : trim(strip_tags($defaultMessage));
@endphp
<div
    x-data="{
        loading: false,
        contextMessage: @js($defaultMessage),
        longWait: false,
        timeout: null,
        minDisplayTimeout: null,
        longWaitTimeout: null,
        shownAt: null,
        minDisplayMs: 1200,
        longWaitMs: 12000,
        maxDisplayMs: 15000,
        init() {
            const self = this;
            const hide = () => {
                self.loading = false;
                self.longWait = false;
                self.shownAt = null;
                if (self.longWaitTimeout) clearTimeout(self.longWaitTimeout);
                self.longWaitTimeout = null;
            };
            const stop = () => {
                if (self.timeout) clearTimeout(self.timeout);
                self.timeout = null;
                if (self.minDisplayTimeout) clearTimeout(self.minDisplayTimeout);
                self.minDisplayTimeout = null;
                if (self.longWaitTimeout) clearTimeout(self.longWaitTimeout);
                self.longWaitTimeout = null;
                if (!self.loading) return;
                const elapsed = self.shownAt ? (Date.now() - self.shownAt) : self.minDisplayMs;
                const remain = self.minDisplayMs - elapsed;
                if (remain <= 0) hide();
                else self.minDisplayTimeout = setTimeout(hide, remain);
            };
            const start = (immediate = false, eventMessage = null) => {
                if (self.timeout) clearTimeout(self.timeout);
                if (self.minDisplayTimeout) clearTimeout(self.minDisplayTimeout);
                if (self.longWaitTimeout) clearTimeout(self.longWaitTimeout);
                self.minDisplayTimeout = null;
                self.longWaitTimeout = null;
                self.shownAt = null;
                self.longWait = false;
                if (eventMessage !== null && eventMessage !== undefined) self.contextMessage = eventMessage;
                const delay = immediate ? 0 : 200;
                self.timeout = setTimeout(() => {
                    self.loading = true;
                    self.shownAt = Date.now();
                    self.longWaitTimeout = setTimeout(() => { if (self.loading) self.longWait = true; }, self.longWaitMs);
                }, delay);
            };

            window.addEventListener('beforeunload', () => start(false));
            window.addEventListener('submit', () => start(false));
            window.addEventListener('loading-overlay:show', (e) => {
                const msg = e.detail && e.detail.message != null ? e.detail.message : null;
                start(true, msg);
            });
            window.addEventListener('loading-overlay:hide', stop);
            window.addEventListener('stop-loading', stop);
            window.addEventListener('pageshow', stop);
            window.addEventListener('load', stop);
            window.addEventListener('DOMContentLoaded', stop);
            window.addEventListener('focus', stop);
            window.addEventListener('visibilitychange', () => { if (document.visibilityState === 'visible') stop(); });

            $watch('loading', v => { if (v) setTimeout(() => { if (self.loading) stop(); }, self.maxDisplayMs); });

            stop();
        }
    }"
    x-show="loading"
    x-cloak
    x-transition:enter="ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="ease-in duration-250"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    role="status"
    aria-live="polite"
    :aria-label="contextMessage"
    class="fixed inset-0 z-[9999] flex items-center justify-center overflow-hidden bg-black/20 dark:bg-black/40 backdrop-blur-sm"
>
    {{-- Card central (glass) – spinner + mensagem – UXMD: centralização, transparência/blur --}}
    <div
        class="flex flex-col items-center justify-center gap-6 rounded-2xl px-8 py-10 shadow-2xl border border-white/20 dark:border-slate-600/30 bg-white/90 dark:bg-slate-900/95 backdrop-blur-md min-w-[200px]"
        aria-hidden="true"
    >
        {{-- Spinner tipo Dragonlady: círculo em loop, animação suave ~1,5s por rotação --}}
        <div class="loading-overlay-spinner relative flex items-center justify-center text-violet-500 dark:text-violet-400">
            <svg class="loading-overlay-ring" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <circle class="loading-overlay-track" cx="32" cy="32" r="28" stroke="currentColor" stroke-width="4" fill="none" opacity="0.15" />
                <circle class="loading-overlay-fill" cx="32" cy="32" r="28" stroke="currentColor" stroke-width="4" fill="none" stroke-linecap="round" stroke-dasharray="88 176" stroke-dashoffset="44" />
            </svg>
        </div>

        {{-- Mensagem contextual (evitar só "Carregando...") – UXMD: microcopy claro --}}
        <p class="text-center text-sm font-semibold text-gray-700 dark:text-slate-200 max-w-[260px]" x-text="contextMessage"></p>

        {{-- Após ~12s: "Está demorando mais" + Fechar – evita spinner infinito (UXMD) --}}
        <div x-show="longWait" class="w-full text-center space-y-3">
            <p class="text-xs text-amber-600 dark:text-amber-400 font-medium">Está demorando mais que o esperado.</p>
            <button
                type="button"
                @click="window.dispatchEvent(new CustomEvent('stop-loading'))"
                class="text-xs font-bold text-gray-600 dark:text-slate-400 hover:text-gray-900 dark:hover:text-slate-200 underline underline-offset-2 transition-colors"
            >
                Fechar e continuar na página
            </button>
        </div>
    </div>

    <span class="sr-only" x-text="contextMessage"></span>
</div>

<style>
    /* Leve e suave: ~1,5s por rotação – UXMD: animação entre 1–2s, não pesada */
    .loading-overlay-spinner .loading-overlay-ring {
        width: 56px;
        height: 56px;
    }
    .loading-overlay-fill {
        transform-origin: 50% 50%;
        animation: loading-overlay-spin 1.5s cubic-bezier(0.5, 0.2, 0.5, 0.8) infinite;
    }
    @keyframes loading-overlay-spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
