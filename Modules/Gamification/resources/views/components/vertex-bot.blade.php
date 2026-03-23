@props([
    'insight' => [],
    'insightKey' => null,
    'dismissUrl' => null,
    'tourId' => null,
    'verseRecommendation' => null,
])

@php
    $levelStyles = [
        'info'    => 'bg-gradient-to-br from-slate-800 to-slate-900 dark:from-slate-900 dark:to-slate-950 border-slate-500/30 shadow-xl shadow-slate-900/20',
        'success' => 'bg-gradient-to-br from-emerald-800/95 to-slate-900 dark:from-emerald-900/90 dark:to-slate-950 border-emerald-400/30 shadow-xl shadow-emerald-900/20',
        'warning' => 'bg-gradient-to-br from-amber-800/95 to-slate-900 dark:from-amber-900/90 dark:to-slate-950 border-amber-400/30 shadow-xl shadow-amber-900/20',
        'danger'  => 'bg-gradient-to-br from-rose-800/95 to-slate-900 dark:from-rose-900/90 dark:to-slate-950 border-rose-400/30 shadow-xl shadow-rose-900/20',
    ];
    $levelAccent = [
        'info'    => 'text-sky-300',
        'success' => 'text-emerald-300',
        'warning' => 'text-amber-300',
        'danger'  => 'text-rose-300',
    ];
    $level = $insight['level'] ?? 'info';
    $bubbleClass = $levelStyles[$level] ?? $levelStyles['info'];
    $accentClass = $levelAccent[$level] ?? $levelAccent['info'];
    $key = $insightKey ?? $insight['insight_key'] ?? null;
    $storageKey = 'cbav_elias_dismissed_'.($key ?? 'generic');
    $medalUnlocked = $insight['medal'] ?? null;
    $content = $insight['content'] ?? '';
    $showAnalysisLink = Route::has('memberpanel.cbav-bot.analysis');
    $showTourLink = ! empty($tourId);
@endphp

@if($content !== '')
{{-- CBAV Bot: Elias como personagem, balão de fala animado --}}
<div
    x-data="{
        showBubble: true,
        dismissed: false,
        fullText: '',
        displayedText: '',
        index: 0,
        isTyping: true,
        storageKey: '{{ $storageKey }}',
        init() {
            try {
                if (window.localStorage && localStorage.getItem(this.storageKey) === '1') {
                    this.dismissed = true;
                    this.showBubble = false;
                    return;
                }
            } catch (e) {}
            const el = this.$el.querySelector('[data-vertex-content]');
            this.fullText = el ? el.textContent : '';
            const type = () => {
                if (this.index < this.fullText.length) {
                    this.displayedText += this.fullText[this.index];
                    this.index++;
                    setTimeout(type, 20);
                } else {
                    this.isTyping = false;
                }
            };
            setTimeout(type, 400);
        },
        dismissLocally() {
            this.dismissed = true;
            this.showBubble = false;
            try {
                if (window.localStorage) {
                    localStorage.setItem(this.storageKey, '1');
                }
            } catch (e) {}
        }
    }"
    class="fixed z-50 flex flex-row items-end gap-0 max-w-[calc(100vw-2rem)] sm:max-w-[26rem]
           bottom-4 right-4 sm:bottom-8 sm:right-auto sm:left-[17rem]"
>
    <span data-vertex-content class="hidden">{{ $content }}</span>

    {{-- Elias: avatar do bot (personagem bíblico) com animação de "falando" --}}
    <div
        x-show="showBubble && !dismissed"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-90"
        x-transition:enter-end="opacity-100 scale-100"
        class="relative shrink-0 w-16 h-20 sm:w-20 sm:h-24 flex items-end justify-center self-end"
    >
        <div
            class="relative w-full h-full flex items-end justify-center rounded-b-xl overflow-visible"
            :class="{ 'cbav-avatar-talking': $data.isTyping }"
        >
            <img
                src="{{ asset('images/CBAVBOT.png') }}"
                alt="Elias — CBAV Bot"
                class="cbav-elias-avatar w-full h-full object-contain object-bottom drop-shadow-lg transition-transform duration-300"
                style="max-height: 6rem; filter: drop-shadow(0 4px 12px rgba(0,0,0,0.2));"
                onerror="this.classList.add('hidden'); const fb = this.nextElementSibling; if(fb) { fb.classList.remove('hidden'); fb.classList.add('flex'); }"
            />
            <div class="hidden w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500 to-amber-700 items-center justify-center shadow-lg border border-amber-400/40 shrink-0">
                <x-icon name="book-bible" style="duotone" class="w-6 h-6 text-white" />
            </div>
        </div>
    </div>

    {{-- Balão de fala com rabo apontando para Elias --}}
    <div
        x-show="showBubble && !dismissed"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95 translate-x-2"
        x-transition:enter-end="opacity-100 scale-100 translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-x-0"
        x-transition:leave-end="opacity-0 translate-x-2"
        class="relative rounded-2xl rounded-bl-md border backdrop-blur-xl p-5 {{ $bubbleClass }} max-w-[18rem] sm:max-w-md
               {{ $medalUnlocked ? 'ring-2 ring-amber-400/60 shadow-amber-500/10' : '' }} shadow-xl"
        style="font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;"
    >
        {{-- Rabo do balão (triângulo) apontando para Elias --}}
        <div class="absolute -left-2 bottom-8 w-0 h-0 border-t-[8px] border-t-transparent border-b-[8px] border-b-transparent border-r-[10px] border-r-slate-700 dark:border-r-slate-800 shadow-sm"></div>

        {{-- Cabeçalho: Elias · CBAV Bot --}}
        <div class="flex items-center gap-2 mb-3">
            <span class="text-[11px] font-bold uppercase tracking-widest text-amber-200/90 dark:text-amber-300/90" style="font-family: 'Poppins', sans-serif;">
                Elias
            </span>
            <span class="text-slate-500 dark:text-slate-400">·</span>
            <span class="text-[10px] text-slate-400 dark:text-slate-500">Seu guia no painel</span>
        </div>

        {{-- Mensagem --}}
        <p class="text-[15px] leading-relaxed text-white/95 dark:text-slate-100 min-h-[2.25rem] font-medium" x-text="displayedText"></p>

        {{-- Recomendação de leitura do dia: livro + capítulo, 1 por 24h, link para o capítulo --}}
        @if(!empty($verseRecommendation['title']) && !empty($verseRecommendation['url']))
        <div class="mt-4 pt-3 border-t border-white/10">
            <p class="text-[11px] font-bold uppercase tracking-wider text-amber-200/90 dark:text-amber-300/90 mb-1.5">Recomendo</p>
            <p class="text-[13px] leading-snug text-white/90 dark:text-slate-200 mb-2">
                Leitura de <span class="font-semibold text-amber-200 dark:text-amber-300">{{ $verseRecommendation['title'] }}</span>. Fica fixa no dashboard por 24h.
            </p>
            <a href="{{ $verseRecommendation['url'] }}"
               class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-300 dark:text-amber-400 hover:text-amber-200 dark:hover:text-amber-300 transition-colors">
                <x-icon name="book-bible" class="w-3.5 h-3.5" />
                Ler agora
            </a>
        </div>
        @endif

        {{-- Ações --}}
        <div class="mt-4 flex flex-wrap items-center gap-x-4 gap-y-2">
            @if($showAnalysisLink)
                <a href="{{ route('memberpanel.cbav-bot.analysis') }}"
                   class="inline-flex items-center gap-1.5 text-xs font-semibold text-amber-300 dark:text-amber-400 hover:text-amber-200 dark:hover:text-amber-300 transition-colors">
                    <x-icon name="circle-info" class="w-3.5 h-3.5" />
                    Ver análise
                </a>
            @endif
            @if($showTourLink)
                <button type="button"
                        @click="if (window.startVertexTour) window.startVertexTour('{{ $tourId }}');"
                        class="inline-flex items-center gap-1.5 text-xs font-semibold text-amber-300 dark:text-amber-400 hover:text-amber-200 dark:hover:text-amber-300 transition-colors text-left">
                    <x-icon name="route" class="w-3.5 h-3.5" />
                    Tour desta página
                </button>
            @endif
        </div>

        @if(Route::has('memberpanel.cbav-bot.dismiss'))
        <button
            type="button"
            @click="
                const key = {{ json_encode($key) }};
                if (key) {
                    fetch('{{ route('memberpanel.cbav-bot.dismiss') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || ''
                        },
                        body: JSON.stringify({ insight_key: key })
                    }).then(() => {
                        dismissLocally();
                    }).catch(() => {
                        dismissLocally();
                    });
                }
                else {
                    dismissLocally();
                }
            "
            class="mt-3 inline-flex items-center gap-1.5 text-[11px] font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500 hover:text-white dark:hover:text-slate-300 transition-colors"
        >
            <x-icon name="check" class="w-3 h-3" />
            Entendi
        </button>
        @endif

        @if($medalUnlocked)
        <div class="mt-4 pt-3 border-t border-amber-400/20 flex items-center gap-2">
            <x-icon name="medal" class="w-4 h-4 text-amber-400 shrink-0" />
            <span class="text-xs font-medium text-amber-200/90 dark:text-amber-300/90">Medalha desbloqueada: {{ $medalUnlocked['title'] ?? 'Conquista' }}</span>
        </div>
        @endif
    </div>
</div>

<style>
    @keyframes cbav-talking {
        0%, 100% { transform: scale(1) translateY(0); }
        50% { transform: scale(1.03) translateY(-2px); }
    }
    .cbav-avatar-talking {
        animation: cbav-talking 0.6s ease-in-out infinite;
    }
</style>
@endif
