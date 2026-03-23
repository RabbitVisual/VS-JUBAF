@extends('memberpanel::components.layouts.master')

@section('title', "Dia {$day->day_number}: {$subscription->plan->title}")

@push('styles')
<style>
    /*
     * PROFESSIONAL SACRED BOOK DESIGN - VANILLA JS EDITION
     * 100% LOCAL - NO CDN
     */

    :root {
        --parchment-bg: #fdfaf3;
        --parchment-dark: #1e1e1a;
        --sacred-gold: #c5a059;
        --sacred-red: #8e1a1a;
        --book-text: #2c241e;
        --bible-font: "Georgia", "Merriweather", serif;
    }

    .bible-reader-container {
        font-family: var(--bible-font);
        background-color: var(--parchment-bg);
        background-image:
            radial-gradient(circle at 50% 50%, rgba(200, 170, 110, 0.05) 0%, transparent 100%),
            url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 86c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zm66-3c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zm-46-4c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zm37-39c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM61 58c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM31 29c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM39 18c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zm33 49c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zm-24 30c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zm34-35c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zm-44-17c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zm41-26c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zm-18 1c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zm-24 56c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zm73-31c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zM71 90c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1z' fill='%23c5a059' fill-opacity='0.03' fill-rule='evenodd'/%3E%3C/svg%3E");
        color: var(--book-text);
        min-height: 100vh;
    }

    .dark .bible-reader-container {
        background-color: var(--parchment-dark);
        background-image: none;
        color: #d1d5db;
    }

    .sacred-header {
        border-bottom: 2px solid var(--sacred-gold);
        padding-bottom: 1.5rem;
        margin-bottom: 3rem;
        text-align: center;
    }

    .sacred-title {
        color: var(--sacred-red);
        font-weight: 900;
        text-transform: uppercase;
        font-size: 2.5rem;
    }
    .dark .sacred-title { color: var(--sacred-gold); }

    .verse-row {
        position: relative;
        padding: 0.75rem 1.25rem;
        margin-bottom: 0.25rem;
        border-radius: 0.5rem;
        transition: all 0.2s;
        border: 1px solid transparent;
        line-height: 1.8;
        cursor: pointer;
    }

    .verse-row:hover {
        background: rgba(197, 160, 89, 0.05);
    }

    .verse-selected {
        background: rgba(197, 160, 89, 0.12) !important;
        border-color: var(--sacred-gold) !important;
        box-shadow: 0 4px 12px rgba(197, 160, 89, 0.1);
    }

    .verse-number {
        font-weight: 900;
        color: var(--sacred-gold);
        font-size: 0.8em;
        vertical-align: super;
        margin-right: 0.5rem;
    }

    /* COLORS */
    .highlight-yellow { background-color: rgba(254, 240, 138, 0.6); }
    .highlight-green { background-color: rgba(187, 247, 208, 0.6); }
    .highlight-blue { background-color: rgba(191, 219, 254, 0.6); }
    .highlight-pink { background-color: rgba(251, 207, 232, 0.6); }
    .highlight-purple { background-color: rgba(233, 213, 255, 0.6); }

    .dark .highlight-yellow { background-color: rgba(254, 240, 138, 0.2); }
    .dark .highlight-green { background-color: rgba(187, 247, 208, 0.2); }
    .dark .highlight-blue { background-color: rgba(191, 219, 254, 0.2); }
    .dark .highlight-pink { background-color: rgba(251, 207, 232, 0.2); }
    .dark .highlight-purple { background-color: rgba(233, 213, 255, 0.2); }

    .floating-toolbar:not(.hidden) {
        position: fixed;
        bottom: 2rem;
        left: 50%;
        transform: translateX(-50%);
        background: #1e293b;
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 9999px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3);
        display: flex;
        align-items: center;
        gap: 1rem;
        z-index: 1000;
        border: 1px solid rgba(255, 255, 255, 0.15);
    }

    .sacred-modal {
        background: var(--parchment-bg);
        border: 2px solid var(--sacred-gold);
        border-radius: 1.5rem;
    }
    .dark .sacred-modal { background: #1a1a17; }



    @media (max-width: 768px) {
        .sacred-title { font-size: 1.75rem; }
        .verse-row { padding: 0.6rem 0.75rem; }
    }
</style>
@endpush

@section('content')
<div class="bible-reader-container" id="reader-root">

    <!-- TOP NAVIGATION -->
    <header class="sticky top-0 z-40 bg-[var(--parchment-bg)] dark:bg-[#1a1a17] border-b border-[var(--sacred-gold)] px-6 py-4 flex items-center justify-between">
        <a href="{{ route('member.bible.plans.index') }}" class="text-[var(--sacred-red)] dark:text-[var(--sacred-gold)]">
            <x-icon name="chevron-left" class="w-6 h-6" />
        </a>

        <div class="text-center">
            <span class="text-[10px] uppercase tracking-widest text-[#8b7d6b] block font-bold">Plano de Leitura</span>
            <h2 class="font-black text-xs md:text-sm uppercase tracking-tighter">{{ $subscription->plan->title }}</h2>
        </div>

        <div class="flex items-center gap-4">
             <span class="text-xs font-bold text-[var(--sacred-gold)]">Dia {{ $day->day_number }}</span>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-6 py-12 pb-32">
        @if($subscription->plan->reading_mode === 'digital')
            <div class="sacred-header">
                @if($day->title)
                    <h1 class="sacred-title mb-2">{{ $day->title }}</h1>
                @endif
                <p class="italic text-slate-500 text-sm">Escrituras Sagradas</p>
            </div>

            <div class="space-y-16">
                @foreach($day->contents as $content)
                    @if($content->type === 'scripture')
                        <div class="scripture-block">
                            <div class="flex items-center justify-between gap-4 mb-8">
                                <div class="flex items-center gap-4">
                                    <h3 class="text-2xl font-black text-[var(--sacred-red)] dark:text-[var(--sacred-gold)]">
                                        {{ $content->target_book_name ?? 'Bíblia' }} {{ $content->chapter_start }}
                                    </h3>

                                    <a href="{{ route('memberpanel.bible.index', ['book' => $content->target_book_id, 'chapter' => $content->chapter_start, 'version' => $targetVersion->abbreviation]) }}"
                                       target="_blank"
                                       class="text-[10px] font-black text-[var(--sacred-gold)] uppercase tracking-[0.2em] border border-[var(--sacred-gold)] px-2 py-1 rounded hover:bg-[var(--sacred-gold)] hover:text-white transition-all flex items-center gap-1">
                                        Ler na Bíblia <x-icon name="external-link" class="w-3 h-3" />
                                    </a>
                                </div>
                                <div class="flex-1 h-px bg-[var(--sacred-gold)] opacity-30"></div>
                            </div>

                            <div class="verse-collection">
                                @if($content->verses)
                                    @foreach($content->verses as $chapNum => $verses)
                                        @if($loop->count > 1 || $content->chapter_start != $chapNum)
                                            <div class="text-center my-8 select-none">
                                                <span class="px-4 py-1 border border-[var(--sacred-gold)] text-[10px] font-bold uppercase tracking-widest text-[var(--sacred-gold)] rounded-full">
                                                    Capítulo {{ $chapNum }}
                                                </span>
                                            </div>
                                        @endif

                                        @foreach($verses as $v)
                                            @php
                                                $fav = $userFavorites[$v->id] ?? null;
                                                $hClass = $fav ? 'highlight-' . $fav->color : '';
                                            @endphp
                                            <div class="verse-row {{ $hClass }}"
                                                 data-verse-id="{{ $v->id }}"
                                                 data-ref="{{ $content->target_book_name }} {{ $chapNum }}:{{ $v->verse_number }}"
                                                 data-note="{{ $fav->note ?? '' }}"
                                                 data-color="{{ $fav->color ?? '' }}">

                                                @if($fav && $fav->note)
                                                    <div class="absolute top-2 right-2 text-[var(--sacred-gold)] note-ic">
                                                        <x-icon name="feather" class="w-4 h-4" />
                                                    </div>
                                                @endif

                                                <span class="verse-number">{{ $v->verse_number }}</span>
                                                <span class="verse-text">{{ $v->text }}</span>
                                            </div>
                                        @endforeach
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    @elseif($content->type === 'devotional')
                        <div class="devotional-block p-10 bg-[#fffcf5] dark:bg-[#1a1a17] border-l-4 border-[var(--sacred-gold)]">
                            <div class="prose dark:prose-invert max-w-none">
                                {!! $content->body !!}
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            <div class="mt-20 border-t-2 border-[var(--sacred-gold)] pt-12 text-center" id="reader-footer"
                 x-data="readingCheckIn({{ $isCompleted ? 'true' : 'false' }}, '{{ route('member.bible.reader.complete', [$subscription->id, $day->id]) }}', '{{ route('member.bible.reader.uncomplete', [$subscription->id, $day->id]) }}', '{{ csrf_token() }}')">
                <div class="flex flex-col items-center gap-4">
                    <button type="button"
                            @click="toggleComplete()"
                            :disabled="loading"
                            class="px-12 py-5 font-black rounded-full shadow-xl transition-all hover:scale-105 disabled:opacity-70 disabled:cursor-not-allowed"
                            :class="completed ? 'bg-gray-500 hover:bg-gray-600 text-white' : 'bg-[var(--sacred-red)] text-white hover:bg-[var(--sacred-red)]/90'">
                        <span x-show="!loading">
                            <span x-text="completed ? 'Lido' : 'Concluir Leitura de Hoje'"></span>
                        </span>
                        <span x-show="loading" class="inline-flex items-center gap-2">
                            <x-icon name="spinner" class="w-5 h-5 animate-spin" /> Processando...
                        </span>
                    </button>
                    <button x-show="completed" type="button" @click="toggleComplete()" :disabled="loading"
                            class="text-sm font-bold text-[var(--sacred-gold)] hover:underline">
                        Desmarcar (não li ainda)
                    </button>
                </div>
            </div>
            <script>
                function readingCheckIn(initialCompleted, completeUrl, uncompleteUrl, csrfToken) {
                    return {
                        completed: initialCompleted,
                        loading: false,
                        completeUrl,
                        uncompleteUrl,
                        csrfToken,
                        async toggleComplete() {
                            if (this.loading) return;
                            this.loading = true;
                            try {
                                if (this.completed) {
                                    const r = await fetch(this.uncompleteUrl, {
                                        method: 'POST',
                                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrfToken },
                                        body: JSON.stringify({})
                                    });
                                    const data = await r.json();
                                    if (data.success) this.completed = false;
                                } else {
                                    const r = await fetch(this.completeUrl, {
                                        method: 'POST',
                                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrfToken },
                                        body: JSON.stringify({})
                                    });
                                    const data = await r.json();
                                    if (data.success) this.completed = true;
                                }
                            } catch (e) {
                                console.error(e);
                            }
                            this.loading = false;
                        }
                    };
                }
            </script>
        @else
            <!-- Physical Mode UI -->
            <div x-data="readingTimer()" class="flex flex-col items-center justify-center min-h-[60vh] text-center space-y-8">
                <div class="sacred-header w-full">
                    <h1 class="sacred-title mb-2">{{ $day->title }}</h1>
                    <div class="text-xl font-bold text-[var(--sacred-gold)] mt-4">
                        <p class="uppercase text-xs tracking-widest text-gray-500 mb-2">Alvo de Hoje</p>
                        @foreach($day->contents as $content)
                            @if($content->type === 'scripture')
                                <div class="mb-1">
                                    {{ $content->target_book_name ?? 'Livro' }} {{ $content->chapter_start }}
                                    @if($content->chapter_end != $content->chapter_start)-{{ $content->chapter_end }}@endif
                                    @if($content->verse_start):{{ $content->verse_start }}@endif
                                    @if($content->verse_end)-{{ $content->verse_end }}@endif
                                </div>
                            @endif
                        @endforeach
                    </div>
                    <p class="italic text-slate-500 text-sm mt-4">Leitura Física (Bíblia de Papel)</p>
                </div>

                <div class="text-6xl md:text-8xl font-black font-mono text-[var(--sacred-red)] dark:text-[var(--sacred-gold)] tracking-wider" x-text="formattedTime">
                    00:00:00
                </div>

                <div class="flex gap-6 items-center">
                    <button @click="toggle" class="w-24 h-24 rounded-full flex items-center justify-center text-white font-bold shadow-2xl transition-all hover:scale-105" :class="running ? 'bg-amber-500' : 'bg-[var(--sacred-gold)]'">
                        <x-icon x-show="!running" name="play" class="w-10 h-10 ml-1" />
                        <x-icon x-show="running" name="pause" class="w-10 h-10" />
                    </button>

                    <button @click="stop" class="w-24 h-24 rounded-full bg-red-600 flex items-center justify-center text-white font-bold shadow-2xl transition-all hover:scale-105" :disabled="time === 0 && !running" :class="{'opacity-50 cursor-not-allowed': time === 0 && !running}">
                        <div class="flex flex-col items-center">
                            <div class="w-6 h-6 bg-white rounded-sm mb-1"></div>
                            <span class="text-[10px] uppercase font-black">Salvar</span>
                        </div>
                    </button>
                </div>

                <div class="mt-12">
                    <a href="{{ route('member.bible.plans.pdf', $subscription->plan_id) }}" target="_blank" class="inline-flex items-center px-6 py-3 border-2 border-[var(--sacred-gold)] text-[var(--sacred-gold)] font-bold rounded-lg hover:bg-[var(--sacred-gold)] hover:text-white transition-all gap-2">
                        <x-icon name="document-download" class="w-5 h-5" />
                        Baixar Checklist (PDF)
                    </a>
                </div>

                <form x-ref="timerForm" action="{{ route('member.bible.reader.complete', [$subscription->id, $day->id]) }}" method="POST" class="hidden">
                    @csrf
                    <input type="hidden" name="time_spent" :value="time">
                </form>
            </div>

            <script>
                function readingTimer() {
                    return {
                        time: 0,
                        running: false,
                        interval: null,
                        get formattedTime() {
                            const h = Math.floor(this.time / 3600).toString().padStart(2, '0');
                            const m = Math.floor((this.time % 3600) / 60).toString().padStart(2, '0');
                            const s = (this.time % 60).toString().padStart(2, '0');
                            return `${h}:${m}:${s}`;
                        },
                        toggle() {
                            if (this.running) {
                                clearInterval(this.interval);
                                this.running = false;
                            } else {
                                this.interval = setInterval(() => {
                                    this.time++;
                                }, 1000);
                                this.running = true;
                            }
                        },
                        stop() {
                            if (this.running) clearInterval(this.interval);
                            this.$refs.timerForm.submit();
                        }
                    }
                }
            </script>
        @endif
    </main>

    <!-- FLOATING TOOLBAR (HIDDEN BY DEFAULT) -->
    <div id="floating-toolbar" class="floating-toolbar hidden">
        <span class="hidden md:inline text-xs font-bold mr-2"><span id="selected-count">0</span> selecionados</span>

        <div class="flex gap-2 items-center">
            <button data-color="yellow" class="w-8 h-8 rounded-full border-2 border-white/20 highlight-yellow color-btn"></button>
            <button data-color="green" class="w-8 h-8 rounded-full border-2 border-white/20 highlight-green color-btn"></button>
            <button data-color="blue" class="w-8 h-8 rounded-full border-2 border-white/20 highlight-blue color-btn"></button>
            <button data-color="pink" class="w-8 h-8 rounded-full border-2 border-white/20 highlight-pink color-btn"></button>
            <button data-color="purple" class="w-8 h-8 rounded-full border-2 border-white/20 highlight-purple color-btn"></button>

            <div class="w-px h-6 bg-white/20 mx-1"></div>

            <button id="note-trigger" class="w-10 h-10 flex items-center justify-center bg-white/10 rounded-full text-[var(--sacred-gold)]">
                <x-icon name="feather" class="w-5 h-5" />
            </button>

            <button id="clear-trigger" class="w-10 h-10 flex items-center justify-center bg-white/10 rounded-full text-red-400">
                <x-icon name="eraser" class="w-5 h-5" />
            </button>

            <div class="w-px h-6 bg-white/20 mx-1"></div>

            <button id="cancel-selection" class="text-slate-400 p-2">
                <x-icon name="x" class="w-5 h-5" />
            </button>
        </div>
    </div>

    <!-- SACRED MODAL (HIDDEN BY DEFAULT) -->
    <div id="sacred-modal-overlay" class="fixed inset-0 z-[1100] flex items-center justify-center p-6 hidden">
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" id="modal-backdrop"></div>
        <div class="relative w-full max-w-md sacred-modal p-8 shadow-2xl">
            <div class="flex justify-between items-center mb-6">
                <h4 class="text-[10px] font-black uppercase tracking-widest text-[#8b7d6b]">Anotação Sagrada</h4>
                <button id="modal-close"><x-icon name="x" class="w-5 h-5" /></button>
            </div>

            <p id="modal-ref-display" class="text-xl font-black mb-6 text-[var(--sacred-red)] dark:text-[var(--sacred-gold)]"></p>

            <textarea id="modal-note-area" class="w-full h-48 bg-white/50 dark:bg-black/20 border-2 border-[var(--sacred-gold)] border-opacity-20 rounded-xl p-4 font-serif outline-none mb-6 text-sm" placeholder="O que o Senhor falou ao seu coração?"></textarea>

            <button id="modal-save-btn" class="w-full py-4 bg-[var(--sacred-gold)] text-white font-black rounded-xl hover:brightness-110">
                Salvar Anotação
            </button>
        </div>
    </div>
</div>

<script>
    (function() {
        // STATE
        let selectedVerses = [];
        let pendingColor = 'yellow';
        let isLoading = false;

        // ELEMENTS
        const toolbar = document.getElementById('floating-toolbar');
        const countSpan = document.getElementById('selected-count');
        const modal = document.getElementById('sacred-modal-overlay');
        const modalRef = document.getElementById('modal-ref-display');
        const modalArea = document.getElementById('modal-note-area');
        const modalSave = document.getElementById('modal-save-btn');
        const modalClose = document.getElementById('modal-close');
        const modalBackdrop = document.getElementById('modal-backdrop');
        const completeForm = document.getElementById('complete-form');
        const readerFooter = document.getElementById('reader-footer');

        // INITIALIZATION
        document.querySelectorAll('.verse-row').forEach(row => {
            row.addEventListener('click', (e) => {
                const id = row.getAttribute('data-verse-id');
                toggleSelection(id, row);
            });
        });

        // AUTO-COMPLETE ON SCROLL (DIGITAL ONLY)
        // Only if not already completed and in digital mode
        @if(!$isCompleted && $subscription->plan->reading_mode === 'digital')
        /*
        // REMOVED AUTO-COMPLETE AS PER REQUEST (Tarefa 2.1)
        // Leaving logic commented out if we want to restore "Scroll to Bottom" detection for stats only, but not auto-submit.
        if (readerFooter && completeForm) {
            let submitted = false;
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting && !submitted) {
                        // Just analytics or UI feedback?
                        // completeForm.submit(); // DISABLED
                        observer.disconnect();
                    }
                });
            }, { threshold: 0.5 });
            observer.observe(readerFooter);
        }
        */
        @endif

        // ACTIONS
        function toggleSelection(id, el) {
            const idx = selectedVerses.indexOf(id);
            if (idx > -1) {
                selectedVerses.splice(idx, 1);
                el.classList.remove('verse-selected');
            } else {
                selectedVerses.push(id);
                el.classList.add('verse-selected');
            }
            updateToolbar();
        }

        function updateToolbar() {
            if (selectedVerses.length > 0) {
                toolbar.classList.remove('hidden');
                countSpan.innerText = selectedVerses.length;
            } else {
                toolbar.classList.add('hidden');
            }
        }

        async function batchSend(color, note = '', type = 'highlight') {
            if (isLoading || selectedVerses.length === 0) return;
            isLoading = true;

            try {
                const response = await fetch('{{ url("/social/bible/favorites/batch") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        verses: selectedVerses,
                        type: type,
                        color: color,
                        note: note
                    })
                });

                if (response.ok) {
                    // UPDATE UI DYNAMICALLY INSTEAD OF RELOAD
                    selectedVerses.forEach(vid => {
                        const el = document.querySelector(`[data-verse-id="${vid}"]`);
                        if (!el) return;

                        // Update Attributes
                        if (color !== undefined) el.setAttribute('data-color', color || '');
                        if (type === 'note') el.setAttribute('data-note', note || '');

                        // Update Classes
                        el.classList.remove('highlight-yellow', 'highlight-green', 'highlight-blue', 'highlight-pink', 'highlight-purple');
                        if (color) el.classList.add('highlight-' + color);

                        // Update Note Icon
                        let noteIc = el.querySelector('.note-ic');
                        const hasNote = (type === 'note' ? (note && note.length > 0) : (el.getAttribute('data-note') && el.getAttribute('data-note').length > 0));

                        if (hasNote) {
                            if (!noteIc) {
                                noteIc = document.createElement('div');
                                noteIc.className = 'absolute top-2 right-2 text-[var(--sacred-gold)] note-ic';
                                // We insert SVG directly or use a placeholder. Since JS can't easily use x-icon blade, we use the SVG string.
                                // But simpler is to innerHTML the SVG.
                                // For now, I'll use a simple SVG string that matches 'feather'.
                                noteIc.innerHTML = '<i class="fa-duotone fa-feather w-4 h-4"></i>';
                                el.appendChild(noteIc);
                            }
                        } else if (noteIc) {
                            noteIc.remove();
                        }
                    });

                    // Clear Selection
                    selectedVerses = [];
                    document.querySelectorAll('.verse-selected').forEach(el => el.classList.remove('verse-selected'));
                    updateToolbar();

                } else {
                    const err = await response.json();
                    alert('Erro ao salvar: ' + (err.message || 'Erro desconhecido'));
                }
            } catch (error) {
                console.error('Fetch error:', error);
                alert('Erro de conexão ao servidor.');
            } finally {
                isLoading = false;
            }
        }

        // TOOLBAR BUTTONS
        document.querySelectorAll('.color-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const color = btn.getAttribute('data-color');
                batchSend(color, '', 'highlight');
            });
        });

        document.getElementById('clear-trigger').addEventListener('click', (e) => {
            e.preventDefault();
            if (confirm('Remover destaques selecionados?')) {
                batchSend(null, null, 'note'); // Usamos note para limpar ambos
            }
        });

        document.getElementById('cancel-selection').addEventListener('click', (e) => {
            e.preventDefault();
            selectedVerses = [];
            document.querySelectorAll('.verse-selected').forEach(el => el.classList.remove('verse-selected'));
            updateToolbar();
        });

        // MODAL LOGIC
        function openModal() {
            if (selectedVerses.length === 1) {
                const el = document.querySelector(`[data-verse-id="${selectedVerses[0]}"]`);
                modalRef.innerText = el.getAttribute('data-ref');
                modalArea.value = el.getAttribute('data-note') || '';
            } else {
                modalRef.innerText = selectedVerses.length + ' Versículos Selecionados';
                modalArea.value = '';
            }
            modal.classList.remove('hidden');
        }

        document.getElementById('note-trigger').addEventListener('click', (e) => {
            e.preventDefault();
            if (selectedVerses.length === 1) {
                const el = document.querySelector(`[data-verse-id="${selectedVerses[0]}"]`);
                pendingColor = el.getAttribute('data-color') || 'yellow';
            } else {
                pendingColor = 'yellow';
            }
            openModal();
        });

        const closeModal = () => modal.classList.add('hidden');
        modalClose.addEventListener('click', (e) => { e.preventDefault(); closeModal(); });
        modalBackdrop.addEventListener('click', closeModal);

        modalSave.addEventListener('click', (e) => {
            e.preventDefault();
            batchSend(pendingColor, modalArea.value, 'note');
            closeModal();
        });

    })();
</script>
@endsection

