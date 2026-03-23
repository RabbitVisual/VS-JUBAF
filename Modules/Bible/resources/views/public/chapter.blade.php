@extends('homepage::components.layouts.master')

@section('title', $book->name . ' ' . $chapter->chapter_number . ' – Bíblia ' . $version->abbreviation)

@php
    $bibleChapterConfig = [
        'apiBase' => url('api/v1/bible'),
        'versionAbbr' => $version->abbreviation,
        'bookNumber' => $book->book_number,
        'chapterNumber' => $chapter->chapter_number,
        'bookName' => $book->name,
        'versions' => $versions->map(fn($v) => ['id' => $v->id, 'abbreviation' => $v->abbreviation, 'name' => $v->name])->values()->toArray(),
        'chapterAudioUrl' => $chapterAudioUrl ?? null,
        'versionName' => $version->name,
    ];
@endphp
@push('styles')
<style>
    .bible-public-container {
        font-family: 'Merriweather', Georgia, serif;
        background-color: #fdfdfb;
        background-image: linear-gradient(180deg, #fdfdfb 0%, #f8f6f0 100%);
    }
    .dark .bible-public-container {
        background-color: #1a1a1a;
        background-image: none;
    }
    .bible-verse-num {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 2rem;
        height: 2rem;
        font-family: system-ui, sans-serif;
        font-size: 0.75rem;
        font-weight: 800;
        color: white;
        background: linear-gradient(135deg, #4f46e5, #6366f1);
        border-radius: 50%;
        box-shadow: 0 2px 6px rgba(79, 70, 229, 0.35);
        flex-shrink: 0;
    }
    .dark .bible-verse-num {
        background: linear-gradient(135deg, #4338ca, #4f46e5);
    }
    .bible-reading-column {
        max-width: 720px;
        line-height: 1.9;
    }
    [x-cloak] { display: none !important; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    /* Modo leitura: oculta navbar e footer do site para não atrapalhar */
    body.bible-reading-mode > nav,
    body.bible-reading-mode > footer { display: none !important; }
@endpush

@section('content')
<script>
window.__bibleChapterConfig = @json($bibleChapterConfig);
document.addEventListener('alpine:init', function() {
    Alpine.data('bibleChapter', function() {
        var config = Object.assign({}, window.__bibleChapterConfig);
        return Object.assign(config, {
            booksOpen: false,
            searchOpen: false,
            searchQuery: '',
            searchResults: null,
            searchLoading: false,
            searchDebounce: null,
            compareMode: false,
            compareVersion2: '',
            compareData: null,
            compareLoading: false,
            readingMode: false,
            fontSize: 100,
            fontPanelOpen: false,
            fullscreenActive: false,
            fetchCompare: function() {
                var self = this;
                if (!self.compareVersion2) return;
                self.compareLoading = true;
                self.compareData = null;
                fetch(self.apiBase + '/compare?v1=' + encodeURIComponent(self.versionAbbr) + '&v2=' + encodeURIComponent(self.compareVersion2) + '&book_number=' + self.bookNumber + '&chapter=' + self.chapterNumber)
                    .then(function(r) { return r.json(); })
                    .then(function(json) { if (json.data) self.compareData = json.data; })
                    .catch(function(e) { console.error(e); })
                    .finally(function() { self.compareLoading = false; });
            },
            doSearch: function() {
                var self = this;
                clearTimeout(self.searchDebounce);
                if (self.searchQuery.trim().length < 2) { self.searchResults = null; return; }
                self.searchDebounce = setTimeout(function() {
                    self.searchLoading = true;
                    self.searchResults = null;
                    fetch(self.apiBase + '/search?q=' + encodeURIComponent(self.searchQuery.trim()))
                        .then(function(r) { return r.json(); })
                        .then(function(json) { if (json.data) self.searchResults = json.data; })
                        .catch(function(e) { console.error(e); })
                        .finally(function() { self.searchLoading = false; });
                }, 300);
            },
            saveLastReading: function() {
                try {
                    localStorage.setItem('bible_public_last', JSON.stringify({
                        versionAbbr: this.versionAbbr,
                        book_number: this.bookNumber,
                        chapter_number: this.chapterNumber,
                        book_name: this.bookName
                    }));
                } catch (e) {}
            },
            fontSizeDown: function() {
                this.fontSize = Math.max(80, this.fontSize - 10);
            },
            fontSizeUp: function() {
                this.fontSize = Math.min(140, this.fontSize + 10);
            },
            toggleReadingFullscreen: function() {
                var self = this;
                var el = document.getElementById('bible-reading-mode-container');
                if (!el) return;
                if (!self.fullscreenActive) {
                    if (el.requestFullscreen) el.requestFullscreen();
                    else if (el.webkitRequestFullscreen) el.webkitRequestFullscreen();
                    self.fullscreenActive = true;
                } else {
                    if (document.exitFullscreen) document.exitFullscreen();
                    else if (document.webkitExitFullscreen) document.webkitExitFullscreen();
                    self.fullscreenActive = false;
                }
            },
            exitReadingMode: function() {
                this.readingMode = false;
                this.fullscreenActive = false;
                if (document.fullscreenElement || document.webkitFullscreenElement) {
                    if (document.exitFullscreen) document.exitFullscreen();
                    else if (document.webkitExitFullscreen) document.webkitExitFullscreen();
                }
            }
        });
    });
});
</script>
<div class="bible-public-container min-h-screen pb-28"
     id="bible-public-chapter"
     x-data="bibleChapter()"
     x-init="saveLastReading(); $watch('readingMode', function(v) { document.body.classList.toggle('bible-reading-mode', v); })"
     x-effect="$el.style.setProperty('--bible-font-size', fontSize + '%')"
     :style="'--bible-font-size: ' + fontSize + '%'">

    {{-- Sticky header --}}
    <nav class="sticky top-0 z-40 bg-white/95 dark:bg-slate-950/95 backdrop-blur-md border-b border-gray-200 dark:border-slate-800 transition-all"
         x-show="!readingMode"
         x-transition>
        <div class="max-w-4xl mx-auto px-3 sm:px-6 py-2.5 sm:py-3">
            <div class="flex items-center justify-between gap-2">
                <div class="flex items-center gap-1 sm:gap-2 shrink-0">
                    <button @click="booksOpen = true"
                            type="button"
                            class="flex items-center gap-1.5 p-2 sm:px-3 sm:py-2 rounded-lg text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-800 hover:text-gray-900 dark:hover:text-white transition-colors"
                            aria-label="Abrir lista de livros">
                        <x-icon name="book-open" class="w-5 h-5" />
                        <span class="hidden sm:inline text-sm font-bold">Livros</span>
                    </button>
                    <a href="{{ route('bible.public.book', [$version->abbreviation, $book->book_number]) }}"
                       class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors"
                       aria-label="Voltar ao livro">
                        <x-icon name="chevron-left" class="w-5 h-5" />
                    </a>
                </div>
                <div class="flex-1 min-w-0 flex flex-col sm:flex-row items-center justify-center gap-0.5 sm:gap-2 text-center">
                    <h1 class="text-sm sm:text-base font-black text-gray-900 dark:text-white truncate w-full sm:w-auto">
                        {{ $book->name }} <span class="text-indigo-600 dark:text-indigo-400">{{ $chapter->chapter_number }}</span>
                    </h1>
                    <label for="pub-version" class="sr-only">Versão</label>
                    <select id="pub-version"
                            onchange="window.location.href = '{{ url('biblia-online/versao') }}/' + this.value + '/livro/{{ $book->book_number }}/capitulo/{{ $chapter->chapter_number }}'"
                            class="text-xs sm:text-sm font-bold text-indigo-600 dark:text-indigo-400 bg-transparent border-0 py-1 pr-6 focus:ring-0 cursor-pointer">
                        @foreach($versions as $v)
                            <option value="{{ $v->abbreviation }}" {{ $v->id === $version->id ? 'selected' : '' }}>{{ $v->abbreviation }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center gap-1 shrink-0">
                    <button @click="searchOpen = true; searchResults = null; searchQuery = ''"
                            type="button"
                            class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-slate-800 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors"
                            aria-label="Busca">
                        <x-icon name="magnifying-glass" class="w-5 h-5" />
                    </button>
                    <button @click="compareMode = !compareMode; if(compareMode && compareVersion2) fetchCompare()"
                            :class="compareMode ? 'bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400' : 'text-gray-500 hover:bg-gray-100 dark:hover:bg-slate-800'"
                            class="p-2 rounded-lg transition-colors"
                            type="button"
                            aria-label="Comparar versões">
                        <x-icon name="columns-3" class="w-5 h-5" />
                    </button>
                    <button @click="readingMode = !readingMode"
                            type="button"
                            class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-slate-800 hover:text-indigo-600 transition-colors"
                            :aria-pressed="readingMode"
                            aria-label="Modo leitura">
                        <x-icon name="book-open-reader" class="w-5 h-5" />
                    </button>
                </div>
            </div>
        </div>
    </nav>

    {{-- Chapter pills --}}
    <div class="sticky top-[52px] sm:top-[56px] z-30 bg-white/90 dark:bg-slate-950/90 backdrop-blur border-b border-gray-100 dark:border-slate-800 py-2 px-3 overflow-x-auto scrollbar-hide"
         x-show="!readingMode"
         x-transition>
        <div class="max-w-3xl mx-auto flex gap-1.5 justify-center flex-nowrap sm:flex-wrap min-w-0">
            @for($i = 1; $i <= ($totalChapters ?? 0); $i++)
                <a href="{{ route('bible.public.chapter', [$version->abbreviation, $book->book_number, $i]) }}"
                   class="flex-shrink-0 w-8 h-8 sm:w-9 sm:h-9 flex items-center justify-center rounded-lg text-sm font-bold transition-all
                   {{ $i == $chapter->chapter_number ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-500 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-800' }}">
                    {{ $i }}
                </a>
            @endfor
        </div>
    </div>

    {{-- Font size control (uses parent scope: fontSizeDown, fontSizeUp, fontPanelOpen) --}}
    <div class="fixed right-3 bottom-24 sm:bottom-28 z-20 flex flex-col gap-1 bg-white/95 dark:bg-slate-900/95 backdrop-blur rounded-xl shadow-lg border border-gray-200 dark:border-slate-700 p-1.5"
         x-show="!readingMode"
         x-transition>
        <button @click="fontPanelOpen = !fontPanelOpen" type="button" class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-slate-800" aria-label="Tamanho da fonte">
            <x-icon name="font" class="w-5 h-5" />
        </button>
        <template x-if="fontPanelOpen">
            <div class="flex items-center gap-1 border-t border-gray-200 dark:border-slate-700 pt-2 mt-1">
                <button @click="fontSizeDown()" type="button" class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-slate-800 text-sm font-bold" aria-label="Diminuir fonte">A-</button>
                <span class="text-xs font-bold text-gray-500 min-w-[2.5rem]" x-text="fontSize + '%'"></span>
                <button @click="fontSizeUp()" type="button" class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-slate-800 text-sm font-bold" aria-label="Aumentar fonte">A+</button>
            </div>
        </template>
    </div>

    {{-- Modo leitura: tela cheia acima do navbar (z-[60]) + opção fullscreen API --}}
    <div id="bible-reading-mode-container"
         x-show="readingMode"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         class="fixed inset-0 z-[60] bg-[#fdfdfb] dark:bg-[#1a1a1a] overflow-y-auto"
         :class="{ 'flex flex-col': readingMode }"
         @fullscreenchange.window="fullscreenActive = !!(document.fullscreenElement || document.webkitFullscreenElement)"
         @webkitfullscreenchange.window="fullscreenActive = !!(document.fullscreenElement || document.webkitFullscreenElement)">
        <template x-if="readingMode">
            <div class="flex-1 flex flex-col min-h-full">
                {{-- Barra mínima modo leitura: sair + tela cheia --}}
                <div class="sticky top-0 z-10 flex items-center justify-between px-3 py-2 bg-white/90 dark:bg-slate-900/90 backdrop-blur border-b border-gray-200 dark:border-slate-800">
                    <span class="text-sm font-bold text-gray-500 dark:text-slate-400 truncate" x-text="bookName + ' ' + chapterNumber"></span>
                    <div class="flex items-center gap-1">
                        <button @click="toggleReadingFullscreen()" type="button" class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-slate-800" :aria-pressed="fullscreenActive" aria-label="Tela cheia">
                            <x-icon name="expand" class="w-5 h-5" />
                        </button>
                        <button @click="exitReadingMode()" type="button" class="p-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 font-bold text-sm" aria-label="Sair do modo leitura">Sair</button>
                    </div>
                </div>
                {{-- Controle de fonte dentro do modo leitura --}}
                <div class="flex justify-center gap-2 py-2 border-b border-gray-100 dark:border-slate-800">
                    <button @click="fontSizeDown()" type="button" class="w-9 h-9 rounded-lg bg-gray-100 dark:bg-slate-800 text-sm font-bold" aria-label="Diminuir fonte">A-</button>
                    <span class="text-sm font-bold text-gray-500 min-w-[3rem] flex items-center justify-center" x-text="fontSize + '%'"></span>
                    <button @click="fontSizeUp()" type="button" class="w-9 h-9 rounded-lg bg-gray-100 dark:bg-slate-800 text-sm font-bold" aria-label="Aumentar fonte">A+</button>
                </div>
                {{-- Conteúdo de leitura (mesmo bloco que o main abaixo, duplicado aqui para modo leitura) --}}
                <div class="flex-1 max-w-3xl w-full mx-auto px-4 sm:px-6 py-6 bible-reading-column" :style="'font-size: ' + (fontSize / 100) + 'rem'">
                    @if(!empty($chapterAudioUrl))
                        <div class="mb-6 p-4 rounded-xl bg-gray-50 dark:bg-slate-800/50 border border-gray-100 dark:border-slate-700" aria-label="Ouvir capítulo em áudio">
                            <p class="text-sm font-bold text-gray-600 dark:text-slate-400 mb-3 flex items-center gap-2">
                                <x-icon name="volume-high" class="w-4 h-4 text-indigo-600 dark:text-indigo-400" />
                                Áudio deste capítulo ({{ $version->name }})
                            </p>
                            <audio controls class="w-full max-w-md" preload="metadata" src="{{ $chapterAudioUrl }}">
                                Seu navegador não suporta o elemento de áudio.
                            </audio>
                        </div>
                    @endif
                    @if(!$verses->isEmpty())
                        <div class="space-y-4 sm:space-y-5">
                            @foreach($verses as $verse)
                                <div class="flex gap-3 sm:gap-4 p-3 sm:p-4 rounded-xl hover:bg-gray-100/50 dark:hover:bg-slate-800/30 transition-colors" id="v{{ $verse->verse_number }}">
                                    <span class="bible-verse-num">{{ $verse->verse_number }}</span>
                                    <span class="text-gray-800 dark:text-slate-200 font-serif">{{ $verse->text }}</span>
                                </div>
                            @endforeach
                        </div>
                        @if(isset($previousChapter) || isset($nextChapter))
                            <footer class="mt-10 pt-6 border-t border-gray-200 dark:border-slate-700 flex flex-wrap items-center justify-between gap-4">
                                @if(isset($previousChapter))
                                    <a href="{{ route('bible.public.chapter', [$version->abbreviation, $previousChapter->book->book_number, $previousChapter->chapter_number]) }}"
                                       class="flex items-center gap-2 px-4 py-2.5 rounded-xl border border-gray-200 dark:border-slate-600 text-gray-600 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-800 font-bold text-sm transition-colors">
                                        <x-icon name="chevron-left" class="w-5 h-5" />
                                        <span class="hidden sm:inline">Cap. {{ $previousChapter->chapter_number }}</span>
                                    </a>
                                @else
                                    <span aria-hidden="true"></span>
                                @endif
                                <span class="text-xs font-bold text-gray-400 dark:text-slate-500 uppercase tracking-wider">{{ $book->name }} {{ $chapter->chapter_number }}</span>
                                @if(isset($nextChapter))
                                    <a href="{{ route('bible.public.chapter', [$version->abbreviation, $nextChapter->book->book_number, $nextChapter->chapter_number]) }}"
                                       class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 text-white font-bold text-sm hover:bg-indigo-700 transition-colors">
                                        <span class="hidden sm:inline">Cap. {{ $nextChapter->chapter_number }}</span>
                                        <x-icon name="chevron-right" class="w-5 h-5" />
                                    </a>
                                @else
                                    <span aria-hidden="true"></span>
                                @endif
                            </footer>
                        @endif
                    @endif
                </div>
            </div>
        </template>
    </div>

    {{-- Main content (hidden in reading mode; shown when not reading) --}}
    <main class="max-w-4xl mx-auto px-4 sm:px-6 py-6 sm:py-10"
         x-show="!readingMode"
         x-transition>
        <div class="bible-reading-column mx-auto" :style="'font-size: ' + (fontSize / 100) + 'rem'">

            {{-- Compare mode: choose version then show 1-1, 2-2 --}}
            <template x-if="compareMode && !compareData && !compareLoading">
                <div class="mb-8 p-6 bg-indigo-50 dark:bg-indigo-900/20 rounded-2xl border border-indigo-100 dark:border-indigo-800 text-center">
                    <p class="text-sm font-bold text-indigo-800 dark:text-indigo-200 mb-3">Escolha a segunda versão para comparar</p>
                    <div class="flex flex-wrap justify-center gap-2">
                        @foreach($versions as $v)
                            @if($v->id !== $version->id)
                                <button type="button"
                                        @click="compareVersion2 = '{{ $v->abbreviation }}'; fetchCompare()"
                                        class="px-4 py-2 rounded-xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-600 text-sm font-bold hover:border-indigo-400 dark:hover:border-indigo-500 transition-colors"
                                        x-text="'{{ $v->abbreviation }}'"></button>
                            @endif
                        @endforeach
                    </div>
                </div>
            </template>

            <template x-if="compareMode && compareLoading">
                <div class="flex flex-col items-center justify-center py-12">
                    <svg class="animate-spin h-10 w-10 text-indigo-600 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <span class="text-sm font-bold text-gray-500">Carregando comparação...</span>
                </div>
            </template>

            {{-- Compare view: 1-1, 2-2, 3-3 --}}
            <template x-if="compareMode && compareData && !compareLoading">
                <div class="space-y-6">
                    <template x-for="(v1, idx) in compareData.v1.verses" :key="idx">
                        <div class="border border-gray-200 dark:border-slate-700 rounded-xl overflow-hidden bg-white dark:bg-slate-900 shadow-sm">
                            <div class="px-4 py-2 bg-gray-50 dark:bg-slate-800 border-b border-gray-200 dark:border-slate-700">
                                <span class="bible-verse-num" x-text="v1.verse_number"></span>
                            </div>
                            <div class="p-4 space-y-3">
                                <div>
                                    <span class="text-[10px] font-black uppercase tracking-wider text-indigo-600 dark:text-indigo-400" x-text="compareData.v1.abbreviation"></span>
                                    <p class="text-gray-800 dark:text-slate-200 font-serif leading-relaxed mt-0.5" x-text="v1.text"></p>
                                </div>
                                <div class="pl-4 border-l-2 border-indigo-200 dark:border-indigo-800">
                                    <span class="text-[10px] font-black uppercase tracking-wider text-amber-600 dark:text-amber-400" x-text="compareData.v2.abbreviation"></span>
                                    <p class="text-gray-700 dark:text-slate-300 font-serif leading-relaxed mt-0.5 text-[0.95em]"
                                       x-text="(compareData.v2.verses[idx] || {}).text || '—'"></p>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            {{-- Chapter audio (when available) --}}
            @if(!empty($chapterAudioUrl))
                <div x-show="!compareMode || !compareData" class="mb-8 p-4 rounded-xl bg-gray-50 dark:bg-slate-800/50 border border-gray-100 dark:border-slate-700" aria-label="Ouvir capítulo em áudio">
                    <p class="text-sm font-bold text-gray-600 dark:text-slate-400 mb-3 flex items-center gap-2">
                        <x-icon name="volume-high" class="w-4 h-4 text-indigo-600 dark:text-indigo-400" />
                        Áudio deste capítulo ({{ $version->name }})
                    </p>
                    <audio controls class="w-full max-w-md" preload="metadata" src="{{ $chapterAudioUrl }}">
                        Seu navegador não suporta o elemento de áudio.
                    </audio>
                </div>
            @endif

            {{-- Normal reading (or when not compare) --}}
            @if($verses->isEmpty())
                <div class="text-center py-16">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-200 dark:bg-slate-700 flex items-center justify-center">
                        <x-icon name="triangle-exclamation" class="w-8 h-8 text-gray-400 dark:text-slate-500" />
                    </div>
                    <p class="text-gray-500 dark:text-slate-400">Este capítulo ainda não está disponível nesta versão.</p>
                </div>
            @else
                <div x-show="!compareMode || !compareData" class="space-y-4 sm:space-y-5">
                    @foreach($verses as $verse)
                        <div class="flex gap-3 sm:gap-4 p-3 sm:p-4 rounded-xl hover:bg-gray-100/50 dark:hover:bg-slate-800/30 transition-colors" id="v{{ $verse->verse_number }}">
                            <span class="bible-verse-num">{{ $verse->verse_number }}</span>
                            <p class="flex-1 min-w-0 text-gray-800 dark:text-slate-200 font-serif leading-relaxed pt-0.5">
                                {{ $verse->text }}
                            </p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </main>

    {{-- Footer prev/next --}}
    @if($verses->isNotEmpty())
        <footer class="fixed bottom-0 left-0 right-0 z-30 bg-white/95 dark:bg-slate-950/95 backdrop-blur border-t border-gray-200 dark:border-slate-800 safe-area-pb">
            <div class="max-w-3xl mx-auto px-4 py-3 flex items-center justify-between gap-4">
                @if($previousChapter)
                    <a href="{{ route('bible.public.chapter', [$version->abbreviation, $previousChapter->book->book_number, $previousChapter->chapter_number]) }}"
                       class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-slate-200 font-bold text-sm hover:bg-gray-200 dark:hover:bg-slate-700 transition-colors">
                        <x-icon name="chevron-left" class="w-5 h-5" />
                        <span class="hidden sm:inline">Cap. {{ $previousChapter->chapter_number }}</span>
                    </a>
                @else
                    <span aria-hidden="true"></span>
                @endif
                <span class="text-xs font-bold text-gray-400 dark:text-slate-500 uppercase tracking-wider">{{ $book->name }} {{ $chapter->chapter_number }}</span>
                @if($nextChapter)
                    <a href="{{ route('bible.public.chapter', [$version->abbreviation, $nextChapter->book->book_number, $nextChapter->chapter_number]) }}"
                       class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 text-white font-bold text-sm hover:bg-indigo-700 transition-colors">
                        <span class="hidden sm:inline">Cap. {{ $nextChapter->chapter_number }}</span>
                        <x-icon name="chevron-right" class="w-5 h-5" />
                    </a>
                @else
                    <span aria-hidden="true"></span>
                @endif
            </div>
        </footer>
    @endif

    {{-- Modal Livros --}}
    <div x-show="booksOpen"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         aria-modal="true"
         @books-open.window="booksOpen = true">
        <div class="flex min-h-full items-center justify-center p-4">
            <div x-show="booksOpen" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="booksOpen = false"></div>
            <div x-show="booksOpen" x-transition
                 class="relative bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-gray-200 dark:border-slate-700 w-full max-w-2xl max-h-[85vh] overflow-hidden">
                <div class="p-4 sm:p-6 border-b border-gray-100 dark:border-slate-800 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Livros</h2>
                    <button @click="booksOpen = false" type="button" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-800">
                        <x-icon name="xmark" class="w-5 h-5" />
                    </button>
                </div>
                <div class="p-4 sm:p-6 overflow-y-auto max-h-[70vh] space-y-6">
                    <div>
                        <h3 class="text-xs font-black uppercase tracking-widest text-amber-600 dark:text-amber-400 mb-3">Antigo Testamento</h3>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                            @foreach($oldTestament ?? [] as $b)
                                <a href="{{ route('bible.public.book', [$version->abbreviation, $b->book_number]) }}"
                                   @click="booksOpen = false"
                                   class="p-3 rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-100 dark:border-slate-700 text-sm font-bold text-gray-700 dark:text-slate-200 hover:border-amber-400 dark:hover:border-amber-500 hover:bg-amber-50 dark:hover:bg-amber-900/20 transition-colors text-center">
                                    {{ $b->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                    <div>
                        <h3 class="text-xs font-black uppercase tracking-widest text-emerald-600 dark:text-emerald-400 mb-3">Novo Testamento</h3>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                            @foreach($newTestament ?? [] as $b)
                                <a href="{{ route('bible.public.book', [$version->abbreviation, $b->book_number]) }}"
                                   @click="booksOpen = false"
                                   class="p-3 rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-100 dark:border-slate-700 text-sm font-bold text-gray-700 dark:text-slate-200 hover:border-emerald-400 dark:hover:border-emerald-500 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-colors text-center">
                                    {{ $b->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Drawer Busca --}}
    <div x-show="searchOpen"
         x-cloak
         class="fixed inset-0 z-50 overflow-hidden"
         aria-modal="true">
        <div x-show="searchOpen" x-transition class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="searchOpen = false"></div>
        <div x-show="searchOpen" x-transition:enter="transform transition ease-out duration-200" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
             class="absolute bottom-0 left-0 right-0 bg-white dark:bg-slate-900 rounded-t-2xl shadow-2xl border-t border-gray-200 dark:border-slate-700 max-h-[85vh] flex flex-col">
            <div class="p-4 border-b border-gray-100 dark:border-slate-800 flex items-center gap-3">
                <label for="bible-search-input" class="sr-only">Buscar na Bíblia</label>
                <input id="bible-search-input"
                       type="search"
                       x-model="searchQuery"
                       @input.debounce.300ms="doSearch()"
                       placeholder="Referência (ex: João 3:16) ou palavra..."
                       class="flex-1 px-4 py-3 rounded-xl bg-gray-100 dark:bg-slate-800 border-0 text-gray-900 dark:text-white placeholder-gray-500 focus:ring-2 focus:ring-indigo-500">
                <button @click="searchOpen = false" type="button" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-800">
                    <x-icon name="xmark" class="w-5 h-5" />
                </button>
            </div>
            <div class="flex-1 overflow-y-auto p-4">
                <template x-if="searchLoading">
                    <div class="flex items-center justify-center py-12">
                        <svg class="animate-spin h-8 w-8 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </div>
                </template>
                <template x-if="!searchLoading && searchResults && Array.isArray(searchResults)">
                    <ul class="space-y-2">
                        <template x-for="(item, i) in searchResults" :key="i">
                            <li>
                                <a :href="item.reference ? ('/biblia-online/versao/' + versionAbbr + '/livro/' + (item.book_number || '') + '/capitulo/' + (item.chapter_number || '') + (item.verse_number ? '#v' + item.verse_number : '')) : '#'"
                                   @click="searchOpen = false"
                                   class="block p-4 rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-100 dark:border-slate-700 hover:border-indigo-300 dark:hover:border-indigo-600 transition-colors">
                                    <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400" x-text="item.reference || item.reference"></span>
                                    <p class="text-sm text-gray-700 dark:text-slate-300 mt-1 line-clamp-2" x-text="item.text || ''"></p>
                                </a>
                            </li>
                        </template>
                    </ul>
                </template>
                <template x-if="!searchLoading && searchResults && searchResults.type === 'exact'">
                    <div class="space-y-2">
                        <p class="text-sm font-bold text-indigo-600 dark:text-indigo-400" x-text="searchResults.reference"></p>
                        <template x-for="(v, i) in (searchResults.verses || [])" :key="i">
                            <a :href="'/biblia-online/versao/' + versionAbbr + '/livro/' + (searchResults.book_number || bookNumber) + '/capitulo/' + (searchResults.chapter_number || chapterNumber) + '#v' + v.verse_number"
                               @click="searchOpen = false"
                               class="block p-4 rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-100 dark:border-slate-700 hover:border-indigo-300 transition-colors">
                                <span class="bible-verse-num mr-2" x-text="v.verse_number"></span>
                                <span class="text-gray-700 dark:text-slate-300 font-serif" x-text="v.text"></span>
                            </a>
                        </template>
                    </div>
                </template>
                <template x-if="!searchLoading && searchQuery.length >= 2 && searchResults && !searchResults.length && searchResults.type !== 'exact'">
                    <p class="text-center text-gray-500 dark:text-slate-400 py-8">Nenhum resultado encontrado.</p>
                </template>
            </div>
        </div>
    </div>
</div>
@endsection
