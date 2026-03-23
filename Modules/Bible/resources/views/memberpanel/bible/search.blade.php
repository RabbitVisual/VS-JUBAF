@extends('memberpanel::components.layouts.master')

@section('title', 'Busca Bíblica Inteligente')

@push('styles')
<style>
    /* Highlight da palavra buscada */
    mark {
        background-color: rgba(250, 204, 21, 0.3); /* Amarelo Amber-400 com opacidade */
        color: inherit;
        padding: 0 2px;
        border-radius: 4px;
        font-weight: 800;
        border-bottom: 2px solid rgba(250, 204, 21, 0.8);
    }

    /* Custom Scrollbar for results if needed */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background-color: rgba(156, 163, 175, 0.5);
        border-radius: 20px;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-slate-950 transition-colors pb-20"
     x-data="bibleSearch()">

    <!-- Sticky Header with Search Input -->
    <div class="sticky top-0 z-40 bg-white/90 dark:bg-slate-950/90 backdrop-blur-xl border-b border-gray-200 dark:border-slate-800 shadow-sm pt-4 pb-6 px-4 transition-colors duration-200">
        <div class="max-w-3xl mx-auto space-y-4">
            <!-- Navigation & Title -->
            <div class="flex items-center justify-between">
                <a href="{{ route('memberpanel.bible.index') }}" class="group inline-flex items-center text-xs font-bold text-gray-500 hover:text-blue-600 dark:text-slate-400 dark:hover:text-blue-400 uppercase tracking-widest transition-colors">
                    <div class="w-6 h-6 rounded-full bg-gray-100 dark:bg-slate-800 flex items-center justify-center mr-2 group-hover:scale-110 transition-transform">
                        <x-icon name="arrow-left" class="w-3 h-3" />
                    </div>
                    Voltar para Bíblia
                </a>
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                    <h1 class="text-xs font-black text-indigo-500 uppercase tracking-widest">Concordância</h1>
                </div>
            </div>

            <!-- Search Bar Container -->
            <div class="relative group" data-tour="bible-search-input">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none z-10">
                   <x-icon name="magnifying-glass" class="w-5 h-5 text-gray-400 dark:text-slate-500 group-focus-within:text-indigo-500 transition-colors" />
                </div>

                <input type="text"
                       x-model="query"
                       @input.debounce.300ms="performSearch()"
                       placeholder="Digite uma palavra (ex: Amor, Fé, Esperança)..."
                       class="w-full bg-gray-100 dark:bg-slate-900 border-2 border-transparent focus:border-indigo-500 dark:focus:border-indigo-500 text-gray-900 dark:text-white text-lg font-bold rounded-2xl py-4 pl-12 pr-12 shadow-inner focus:shadow-xl focus:shadow-indigo-500/10 focus:bg-white dark:focus:bg-slate-900 transition-all placeholder:font-medium placeholder:text-gray-400 dark:placeholder:text-slate-600 outline-none">

                <!-- Loading Spinner -->
                <div x-show="loading" class="absolute right-4 top-1/2 -translate-y-1/2 text-indigo-500">
                    <x-icon name="spinner" class="w-5 h-5 animate-spin" />
                </div>

                <!-- Clear Button -->
                <button x-show="query.length > 0 && !loading"
                        @click="query = ''; results = []; hasSearched = false"
                        class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-500 transition-colors"
                        x-cloak>
                    <x-icon name="xmark" class="w-5 h-5" />
                </button>
            </div>
        </div>
    </div>

    <main class="max-w-3xl mx-auto px-4 mt-8">

        <!-- Empty State (Initial) -->
        <div x-show="results.length === 0 && !hasSearched" class="text-center py-24 opacity-60">
            <div class="w-24 h-24 bg-gray-100 dark:bg-slate-900 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner">
                <x-icon name="book-bible" class="w-10 h-10 text-gray-300 dark:text-slate-700" />
            </div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2 tracking-tight">O que você procura hoje?</h3>
            <p class="text-sm font-medium text-gray-500 dark:text-slate-400 max-w-sm mx-auto">
                Digite uma palavra-chave, tema ou referência para encontrar versículos na Bíblia.
            </p>
        </div>

        <!-- Empty State (No Results) -->
        <div x-show="results.length === 0 && hasSearched && !loading" x-cloak class="text-center py-20">
            <div class="inline-flex bg-red-50 dark:bg-red-900/20 p-5 rounded-full mb-6">
                <x-icon name="magnifying-glass" class="w-8 h-8 text-red-500" />
            </div>
            <p class="text-xl font-black text-gray-900 dark:text-white tracking-tight mb-2">
                Nada encontrado para "<span x-text="query" class="text-red-500"></span>"
            </p>
            <p class="text-sm text-gray-500 dark:text-slate-400 font-medium">
                Verifique a ortografia ou tente usar sinônimos.
            </p>
        </div>

        <!-- Results List -->
        <div class="space-y-4" data-tour="bible-search-results">
            <template x-for="verse in results" :key="verse.id">
                <a :href="'{{ route('memberpanel.bible.index') }}?book=' + verse.book_number + '&chapter=' + verse.chapter_number + '#v' + verse.verse_number"
                   class="block bg-white dark:bg-slate-900 p-6 rounded-2xl border border-gray-100 dark:border-slate-800 hover:border-indigo-500 dark:hover:border-indigo-500 shadow-sm hover:shadow-xl hover:shadow-indigo-500/10 transition-all group relative overflow-hidden">

                    <!-- Decorative Hover Gradient -->
                    <div class="absolute inset-0 bg-linear-to-r from-indigo-500/0 via-indigo-500/0 to-indigo-500/5 translate-x-full group-hover:translate-x-0 transition-transform duration-500 pointer-events-none"></div>

                    <!-- Header: Reference -->
                    <div class="flex justify-between items-center mb-4 relative z-10">
                        <div class="flex items-center gap-3">
                             <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 font-black text-xs">
                                <span x-text="verse.book_abbreviation || String(verse.book_name).substring(0,3).toUpperCase()"></span>
                             </span>
                             <div>
                    <h4 class="text-sm font-bold text-gray-900 dark:text-white leading-tight">
                        <span x-text="verse.book_name"></span> <span x-text="verse.chapter_number"></span>:<span x-text="verse.verse_number"></span>
                    </h4>
                    <p class="text-[10px] font-bold text-gray-400 dark:text-slate-500 uppercase tracking-wider mt-1">
                        {{ $version->abbreviation ?? 'NVI' }}
                    </p>
                             </div>
                        </div>
                        <div class="w-8 h-8 rounded-full bg-gray-50 dark:bg-slate-800 flex items-center justify-center group-hover:bg-indigo-500 group-hover:text-white transition-colors duration-300">
                            <x-icon name="chevron-right" class="w-4 h-4 text-gray-400 dark:text-slate-600 group-hover:text-white" />
                        </div>
                    </div>

                    <!-- Content: Verse Text -->
                    <p class="text-lg text-gray-700 dark:text-slate-300 font-serif leading-relaxed relative z-10" x-html="highlightText(verse.text)"></p>
                </a>
            </template>
        </div>

    </main>
</div>

<script>
    function bibleSearch() {
        return {
            query: '',
            results: [],
            loading: false,
            hasSearched: false,

            async performSearch() {
                if (this.query.length < 3) {
                    this.results = [];
                    this.hasSearched = false;
                    return;
                }

                this.loading = true;
                this.hasSearched = true;

                try {
                    // Assuming the route name 'member.bible.api.search' is correct based on original file
                    // But usually, memberpanel routes are prefixed. Let's check if the original file used 'member.bible.api.search'.
                    // The original file used: {{ route('member.bible.api.search') }}
                    // We must preserve this route name.
                    const response = await fetch(`{{ route('member.bible.api.search') }}?q=${encodeURIComponent(this.query)}`);
                    this.results = await response.json();
                } catch (error) {
                    console.error('Erro na busca:', error);
                } finally {
                    this.loading = false;
                }
            },

            // Função para grifar a palavra encontrada
            highlightText(text) {
                if (!this.query) return text;
                // Escapa caracteres especiais para usar no Regex
                const safeQuery = this.query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                // Regex para encontrar a palavra ignorando maiúsculas/minúsculas
                const regex = new RegExp(`(${safeQuery})`, 'gi');
                return text.replace(regex, '<mark>$1</mark>');
            }
        };
    }
</script>
@endsection

