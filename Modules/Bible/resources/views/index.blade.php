@extends('memberpanel::components.layouts.master')

@section('title', $selectedBook->name . ' ' . $chapterNumber . ' - Bíblia Online')

@push('styles')
<style>
    /* Estilos Visuais Apenas - SEM OVERRIDES DE LAYOUT */
    .bible-reader-container {
        font-family: 'Merriweather', 'Georgia', serif;
        background-color: #fdfdfb;
        background-image: url("https://www.transparenttextures.com/patterns/paper-fibers.png");
    }
    .dark .bible-reader-container {
        background-color: #1a1a1a;
        background-image: none;
    }

    /* Previne flash de conteúdo não carregado do Alpine */
    [x-cloak] { display: none !important; }

    /* Coluna de Leitura */
    .reading-column {
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        max-width: 800px;
        line-height: 2.1;
    }
    .compare-mode .reading-column {
        max-width: 800px !important;
        margin-left: auto;
        margin-right: auto;
    }

    /* Versículos */
    .verse-item {
        display: block;
        margin-bottom: 1.5rem;
        padding: 0.75rem;
        cursor: pointer;
        transition: all 0.2s;
        border-radius: 1rem;
        border: 1px solid transparent;
    }
    .verse-item:hover {
        background-color: rgba(124, 58, 237, 0.05);
        border-color: rgba(124, 58, 237, 0.1);
        transform: translateX(4px);
    }
    .dark .verse-item:hover {
        background-color: rgba(139, 92, 246, 0.1);
    }

    /* Número do Versículo */
    .verse-number {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        flex-shrink: 0;
        font-family: sans-serif;
        font-size: 11px;
        font-weight: 800;
        margin-right: 1rem;
        color: white;
        background: linear-gradient(135deg, #7c3aed, #6d28d9);
        border-radius: 50%;
        user-select: none;
        vertical-align: text-top;
        box-shadow: 0 2px 4px rgba(124, 58, 237, 0.3);
    }

    .chapter-heading {
        font-family: 'Playfair Display', serif;
    }

    .nav-overlay {
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
    }

    .book-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
        gap: 0.75rem;
    }
</style>
@endpush

@section('content')
<div class="bible-reader-container min-h-screen transition-colors duration-500"
     x-data="{
        compareMode: false,
        compareVersion: '',
        compareLoading: false,
        compareData: null,
        verseModal: false,
        selectedVerse: null,
        verseComparisons: [],

        async compareChapter(v2) {
            this.compareLoading = true;
            this.compareMode = true;
            this.compareVersion = v2;
            try {
                const res = await fetch(`{{ route('bible.compare') }}?v1={{ $selectedVersion->abbreviation }}&v2=${v2}&book_number={{ $selectedBook->book_number }}&chapter={{ $chapterNumber }}`);
                this.compareData = await res.json();
            } catch(e) {
                console.error(e);
            } finally {
                this.compareLoading = false;
            }
        },

        async compareVerse(verseNumber) {
            this.selectedVerse = verseNumber;
            this.verseModal = true;
            this.verseComparisons = [];

            const versions = ['ARA', 'ARC', 'NVI', 'NTLH', 'KJV'];

            // Promise.all para carregar tudo em paralelo (muito mais rápido)
            const promises = versions
                .filter(v => v !== '{{ $selectedVersion->abbreviation }}')
                .map(v =>
                    fetch(`{{ route('bible.compare') }}?v1={{ $selectedVersion->abbreviation }}&v2=${v}&book_number={{ $selectedBook->book_number }}&chapter={{ $chapterNumber }}&verse=${verseNumber}`)
                    .then(r => r.json())
                    .then(data => {
                        if(data.v2 && data.v2.verses.length > 0) {
                            return { version: v, text: data.v2.verses[0].text };
                        }
                        return null;
                    })
                    .catch(() => null)
                );

            const results = await Promise.all(promises);
            this.verseComparisons = results.filter(r => r !== null);
        }
     }">

    <header class="sticky top-0 z-40 bg-white/80 dark:bg-gray-900/90 nav-overlay border-b border-gray-200/50 dark:border-gray-800 px-4 py-3 mb-8 shadow-sm">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-2">
                <button @click="$dispatch('open-book-modal')" class="group flex items-center px-3 py-1.5 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-all">
                    <div class="text-left">
                        <p class="text-[9px] uppercase tracking-widest text-gray-400 font-bold leading-none mb-1">Livro</p>
                        <h2 class="text-base font-black text-gray-900 dark:text-white leading-none flex items-center">
                            {{ $selectedBook->name }}
                            <x-icon name="chevron-down" style="duotone" class="w-3 h-3 ml-1 text-gray-400" />
                        </h2>
                    </div>
                </button>

                <div class="w-px h-6 bg-gray-200 dark:bg-gray-700 mx-1"></div>

                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="text-left px-3 py-1.5 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-all">
                        <p class="text-[9px] uppercase tracking-widest text-gray-400 font-bold leading-none mb-1">Versão</p>
                        <h2 class="text-sm font-bold text-purple-600 dark:text-purple-400 leading-none flex items-center">
                            {{ $selectedVersion->abbreviation }}
                            <x-icon name="chevron-down" style="duotone" class="w-3 h-3 ml-1" />
                        </h2>
                    </button>
                    <div x-show="open"
                         @click.away="open = false"
                         x-cloak
                         x-transition
                         class="absolute top-full left-0 mt-2 w-48 bg-white dark:bg-gray-800 shadow-xl rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden py-1 z-50">
                        @foreach($versions as $v)
                            <a href="?version={{ $v->abbreviation }}&book={{ $selectedBook->id }}&chapter={{ $chapterNumber }}"
                               class="block px-4 py-2 text-sm {{ $selectedVersion->id === $v->id ? 'bg-purple-50 text-purple-700 font-bold' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                                {{ $v->name }} ({{ $v->abbreviation }})
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="hidden md:flex bg-gray-100 dark:bg-gray-800 p-1 rounded-lg">
                    <a href="{{ route('member.bible.search') }}" class="px-3 py-1 rounded-md text-xs font-bold text-gray-500 hover:text-purple-600 transition-all flex items-center gap-1">
                        <x-icon name="magnifying-glass" style="duotone" class="w-3.5 h-3.5" />
                        Buscar
                    </a>
                    <div class="w-px h-3 bg-gray-300 dark:bg-gray-600 my-1 mx-1"></div>
                    <button @click="compareMode = !compareMode" :class="compareMode ? 'bg-white dark:bg-gray-700 shadow-sm text-purple-600' : 'text-gray-500'" class="px-3 py-1 rounded-md text-xs font-bold transition-all">
                        Comparar
                    </button>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 pb-32">
        <div x-show="compareMode && !compareData" x-cloak x-transition class="mb-8 p-6 bg-purple-50 dark:bg-purple-900/10 rounded-3xl border border-purple-100 dark:border-purple-800 text-center animate-fade-in-down">
            <h4 class="text-purple-700 dark:text-purple-400 font-bold mb-4">Escolha a versão para comparar</h4>
            <div class="flex flex-wrap justify-center gap-2">
                @foreach($versions as $v)
                    @if($v->abbreviation !== $selectedVersion->abbreviation)
                        <button @click="compareChapter('{{ $v->abbreviation }}')" class="px-4 py-2 bg-white dark:bg-gray-800 rounded-xl text-sm font-bold shadow-sm hover:border-purple-400 border border-transparent transition-all">
                            {{ $v->abbreviation }}
                        </button>
                    @endif
                @endforeach
            </div>
        </div>

        <div class="mx-auto" :class="compareMode ? 'compare-mode' : 'reading-column'">

            <div class="max-w-full">
                <div x-show="!compareMode" class="mb-12 flex flex-wrap justify-center gap-2">
                    @for($i = 1; $i <= $selectedBook->total_chapters; $i++)
                        <a href="?version={{ $selectedVersion->abbreviation }}&book={{ $selectedBook->id }}&chapter={{ $i }}"
                        class="w-9 h-9 flex items-center justify-center rounded-lg text-sm font-bold transition-all
                        {{ $chapterNumber == $i
                            ? 'bg-purple-600 text-white shadow-lg shadow-purple-500/30'
                            : 'text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700' }}">
                            {{ $i }}
                        </a>
                    @endfor
                </div>

                <div class="text-center mb-12">
                    <h1 class="chapter-heading text-6xl md:text-8xl font-black text-gray-900/5 dark:text-white/5 uppercase tracking-tighter -mb-6 md:-mb-10 select-none">
                        {{ $chapterNumber }}
                    </h1>
                    <div class="relative">
                        <h2 class="chapter-heading text-4xl md:text-5xl font-bold text-gray-900 dark:text-white leading-tight">{{ $selectedBook->name }}</h2>
                    </div>
                </div>

                <div class="relative mt-10">
                    <div x-show="!compareMode" class="absolute -left-8 top-0 bottom-0 w-px bg-linear-to-b from-purple-200/50 via-transparent to-transparent hidden lg:block"></div>

                    @if($chapter && $chapter->verses->count() > 0)
                        <div class="max-w-none">
                            @foreach($chapter->verses as $index => $verse)
                                <div class="verse-group mb-12">
                                    <div class="verse-item mb-1!"
                                        @click="compareVerse({{ $verse->verse_number }})"
                                        id="v{{ $verse->verse_number }}">
                                        <span class="verse-number">{{ $verse->verse_number }}</span>
                                        <template x-if="compareMode">
                                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest mr-2">{{ $selectedVersion->abbreviation }}:</span>
                                        </template>
                                        <span class="text-gray-800 dark:text-gray-200 text-lg md:text-xl leading-relaxed">
                                            {{ $verse->text }}
                                        </span>
                                    </div>

                                    <template x-if="compareMode && compareData">
                                        <div class="space-y-4">
                                            <template x-for="cv in compareData.v2.verses.filter(v => v.verse_number == {{ $verse->verse_number }})" :key="cv.id">
                                                <div class="pl-12 py-3 border-l-2 border-purple-100 dark:border-purple-800/50 bg-gray-50/30 dark:bg-gray-800/10 rounded-r-xl">
                                                    <span class="text-[10px] font-black text-purple-600 dark:text-purple-400 uppercase tracking-widest mr-2" x-text="compareData.v2.abbreviation + ':'"></span>
                                                    <span class="text-gray-600 dark:text-gray-400 text-lg italic leading-relaxed" x-text="cv.text"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div x-show="compareMode && compareLoading" x-cloak x-transition class="flex flex-col items-center justify-center py-20 pointer-events-none sticky bottom-32">
                <div class="bg-white/80 dark:bg-gray-900/80 backdrop-blur p-6 rounded-3xl shadow-2xl border border-gray-100 dark:border-gray-800 flex flex-col items-center">
                    <svg class="animate-spin h-8 w-8 text-purple-600 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Intercalando Versões...</p>
                </div>
            </div>
        </div>
    </main>

    <div x-show="verseModal"
         x-cloak
         class="fixed inset-0 z-70 flex items-center justify-end"
         role="dialog"
         aria-modal="true">

        <div x-show="verseModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm"
             @click="verseModal = false"></div>

        <div x-show="verseModal"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full"
             class="relative bg-white dark:bg-gray-900 w-full max-w-md h-full shadow-2xl overflow-y-auto border-l border-gray-100 dark:border-gray-800 p-6 z-10">

            <div class="flex items-center justify-between mb-8 sticky top-0 bg-white dark:bg-gray-900 pb-4 border-b border-gray-100 dark:border-gray-800 z-20">
                <div>
                    <h3 class="text-[10px] font-black text-purple-600 uppercase tracking-widest mb-1">Comparação</h3>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $selectedBook->name }} {{ $chapterNumber }}:<span x-text="selectedVerse"></span></p>
                </div>
                <button @click="verseModal = false" class="p-2 bg-gray-50 dark:bg-gray-800 rounded-full hover:bg-gray-100 transition-colors">
                    <x-icon name="xmark" style="duotone" class="w-5 h-5 text-gray-500" />
                </button>
            </div>

            <div class="space-y-6 pb-10">
                <div class="p-5 bg-purple-50 dark:bg-purple-900/10 rounded-2xl border border-purple-100 dark:border-purple-800/30">
                    <div class="flex justify-between items-center mb-3">
                        <span class="inline-block px-2 py-1 bg-purple-600 text-white text-[10px] font-black tracking-widest uppercase rounded">{{ $selectedVersion->abbreviation }}</span>
                        <span class="text-[10px] font-bold text-purple-400">Principal</span>
                    </div>
                    <p class="text-lg leading-relaxed text-purple-900 dark:text-purple-100" x-text="document.getElementById('v' + selectedVerse)?.innerText?.replace(selectedVerse, '').trim()"></p>
                </div>

                <template x-for="item in verseComparisons" :key="item.version">
                    <div class="p-5 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm">
                        <span class="inline-block px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-300 text-[10px] font-black tracking-widest uppercase rounded mb-3" x-text="item.version"></span>
                        <p class="text-lg leading-relaxed text-gray-700 dark:text-gray-300" x-text="item.text"></p>
                    </div>
                </template>

                <div x-show="verseComparisons.length === 0" class="text-center py-12">
                    <svg class="animate-spin h-6 w-6 text-purple-600 mx-auto mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Buscando traduções...</p>
                </div>
            </div>
        </div>
    </div>

    <div x-data="{ open: false }"
         @open-book-modal.window="open = true"
         x-show="open"
         x-cloak
         class="fixed inset-0 z-60 overflow-y-auto"
         role="dialog"
         aria-modal="true">

        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm"
                 x-show="open"
                 x-transition.opacity
                 @click="open = false"></div>

            <div class="relative bg-white dark:bg-gray-800 w-full max-w-4xl rounded-4xl shadow-2xl overflow-hidden border border-gray-100 dark:border-gray-700"
                 x-show="open"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95">

                <div class="p-8 overflow-y-auto max-h-[85vh]">
                    <div class="flex items-center justify-between mb-8 sticky top-0 bg-white dark:bg-gray-800 z-10 pb-4 border-b border-gray-100 dark:border-gray-700">
                        <h3 class="text-2xl font-black text-gray-900 dark:text-white">Livros</h3>
                        <button @click="open = false" class="p-2 bg-gray-100 dark:bg-gray-700 rounded-full hover:bg-gray-200 transition-colors">
                            <x-icon name="xmark" style="duotone" class="w-5 h-5" />
                        </button>
                    </div>

                    <div class="space-y-10">
                        <div>
                            <h4 class="text-xs font-black text-purple-600 uppercase tracking-widest mb-4">Velho Testamento</h4>
                            <div class="book-grid">
                                @foreach($books->where('testament', 'old') as $b)
                                    <a href="?version={{ $selectedVersion->abbreviation }}&book={{ $b->id }}"
                                       class="px-3 py-2 rounded-xl text-sm font-bold transition-all text-center border border-transparent
                                       {{ $selectedBook->id == $b->id ? 'bg-purple-600 text-white shadow-lg shadow-purple-200' : 'bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:border-purple-300 hover:text-purple-600' }}">
                                        {{ $b->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <h4 class="text-xs font-black text-emerald-600 uppercase tracking-widest mb-4">Novo Testamento</h4>
                            <div class="book-grid">
                                @foreach($books->where('testament', 'new') as $b)
                                    <a href="?version={{ $selectedVersion->abbreviation }}&book={{ $b->id }}"
                                       class="px-3 py-2 rounded-xl text-sm font-bold transition-all text-center border border-transparent
                                       {{ $selectedBook->id == $b->id ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-200' : 'bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:border-emerald-300 hover:text-emerald-600' }}">
                                        {{ $b->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

