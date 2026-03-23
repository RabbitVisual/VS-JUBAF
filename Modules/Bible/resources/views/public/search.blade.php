@extends('homepage::components.layouts.master')

@section('title', 'Buscar na Bíblia – Bíblia Online')

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
</style>
@endpush

@php
    $bibleSearchConfig = [
        'apiBase' => $apiBase,
        'defaultVersionAbbr' => $versions->isNotEmpty() ? $versions->first()->abbreviation : '',
        'versions' => $versions->map(fn($v) => ['abbreviation' => $v->abbreviation, 'name' => $v->name])->values()->toArray(),
    ];
@endphp
<script>
window.__bibleSearchConfig = @json($bibleSearchConfig);
document.addEventListener('alpine:init', function() {
    Alpine.data('bibleSearch', function() {
        var c = window.__bibleSearchConfig;
        var versionAbbr = '';
        try { versionAbbr = localStorage.getItem('bible_public_version') || c.defaultVersionAbbr; } catch(e) { versionAbbr = c.defaultVersionAbbr; }
        return {
            searchQuery: '',
            searchResults: null,
            searchLoading: false,
            searchDebounce: null,
            versionAbbr: versionAbbr,
            apiBase: c.apiBase,
            versions: c.versions,
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
            }
        };
    });
});
</script>
@section('content')
<div class="bible-public-container min-h-screen pb-24"
     x-data="bibleSearch()">
    {{-- Header --}}
    <header class="sticky top-0 z-30 bg-white/95 dark:bg-slate-950/95 backdrop-blur-md border-b border-gray-200 dark:border-slate-800">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 py-4">
            <div class="flex items-center justify-between gap-4">
                <a href="{{ route('bible.public.index') }}"
                   class="flex items-center gap-2 text-gray-500 hover:text-gray-900 dark:text-slate-400 dark:hover:text-white transition-colors shrink-0">
                    <span class="w-9 h-9 rounded-full bg-gray-100 dark:bg-slate-800 flex items-center justify-center">
                        <x-icon name="chevron-left" class="w-5 h-5" />
                    </span>
                    <span class="hidden sm:inline text-sm font-bold">Voltar</span>
                </a>
                <h1 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white" style="font-family: 'Merriweather', Georgia, serif;">Buscar na Bíblia</h1>
                <div class="w-9 h-9 shrink-0" aria-hidden="true"></div>
            </div>
            <div class="mt-3">
                <label for="search-input" class="sr-only">Buscar por referência ou texto</label>
                <div class="relative">
                    <x-icon name="magnifying-glass" class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                    <input id="search-input"
                           type="search"
                           x-model="searchQuery"
                           @input.debounce.300ms="doSearch()"
                           placeholder="Ex.: João 3:16 ou palavra..."
                           class="w-full pl-10 pr-4 py-3 rounded-xl bg-gray-100 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-900 dark:text-white placeholder-gray-500 focus:ring-2 focus:ring-indigo-500"
                           autofocus>
                </div>
            </div>
            <div class="mt-2">
                <label for="search-version" class="sr-only">Versão para links</label>
                <select id="search-version"
                        x-model="versionAbbr"
                        @change="try { localStorage.setItem('bible_public_version', versionAbbr); } catch(e) {}"
                        class="w-full appearance-none pl-4 pr-10 py-2 bg-gray-100 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg text-sm font-bold text-gray-900 dark:text-white">
                    <option value="">Selecione a versão para abrir os links</option>
                    <template x-for="v in versions" :key="v.abbreviation">
                        <option :value="v.abbreviation" x-text="v.name + ' (' + v.abbreviation + ')'"></option>
                    </template>
                </select>
            </div>
        </div>
    </header>

    <main class="max-w-2xl mx-auto px-4 sm:px-6 py-6">
        <template x-if="searchLoading">
            <div class="flex items-center justify-center py-12">
                <svg class="animate-spin h-8 w-8 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            </div>
        </template>
        <template x-if="!searchLoading && searchResults && Array.isArray(searchResults)">
            <ul class="space-y-2">
                <template x-for="(item, i) in searchResults" :key="i">
                    <li>
                        <a :href="versionAbbr && item.book_number && item.chapter_number ? ('/biblia-online/versao/' + versionAbbr + '/livro/' + item.book_number + '/capitulo/' + item.chapter_number + (item.verse_number ? '#v' + item.verse_number : '')) : '#'"
                           class="block p-4 rounded-xl bg-white/90 dark:bg-slate-900/90 border border-gray-200 dark:border-slate-700 hover:border-indigo-300 dark:hover:border-indigo-600 transition-colors">
                            <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400" x-text="item.reference || ''"></span>
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
                    <a :href="versionAbbr && searchResults.book_number && searchResults.chapter_number ? ('/biblia-online/versao/' + versionAbbr + '/livro/' + searchResults.book_number + '/capitulo/' + searchResults.chapter_number + '#v' + v.verse_number) : '#'"
                       class="block p-4 rounded-xl bg-white/90 dark:bg-slate-900/90 border border-gray-200 dark:border-slate-700 hover:border-indigo-300 dark:hover:border-indigo-600 transition-colors">
                        <span class="bible-verse-num mr-2" x-text="v.verse_number"></span>
                        <span class="text-gray-700 dark:text-slate-300 font-serif" x-text="v.text"></span>
                    </a>
                </template>
            </div>
        </template>
        <template x-if="!searchLoading && searchQuery.length >= 2 && searchResults && !Array.isArray(searchResults) && searchResults.type !== 'exact'">
            <p class="text-center text-gray-500 dark:text-slate-400 py-8">Nenhum resultado encontrado.</p>
        </template>
        <template x-if="!searchLoading && searchQuery.length < 2 && !searchResults">
            <p class="text-center text-gray-500 dark:text-slate-400 py-12">Digite ao menos 2 caracteres para buscar por referência (ex.: João 3:16) ou por texto.</p>
        </template>
    </main>
</div>
@endsection
