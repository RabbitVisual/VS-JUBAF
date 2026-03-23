@extends('homepage::components.layouts.master')

@section('title', 'Bíblia Online – Leia a Bíblia Sagrada')

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
</style>
@endpush

@section('content')
<div class="bible-public-container min-h-screen py-8 sm:py-12 px-4 sm:px-6 lg:px-8"
     x-data="{ last: (function(){ try { const s = localStorage.getItem('bible_public_last'); return s ? JSON.parse(s) : null; } catch(e) { return null; } })() }">
    <div class="max-w-2xl mx-auto bible-reading-column" style="max-width: 720px;">
        <template x-if="last && last.versionAbbr && last.book_number && last.chapter_number">
            <a :href="'/biblia-online/versao/' + last.versionAbbr + '/livro/' + last.book_number + '/capitulo/' + last.chapter_number"
               class="mb-6 flex items-center gap-3 p-4 rounded-2xl bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200/60 dark:border-indigo-800/40 hover:bg-indigo-100 dark:hover:bg-indigo-900/30 transition-colors group">
                <span class="flex-shrink-0 w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center">
                    <x-icon name="book-open" class="w-5 h-5" />
                </span>
                <div class="min-w-0 flex-1">
                    <span class="text-xs font-bold uppercase text-indigo-600 dark:text-indigo-400">Continuar lendo</span>
                    <p class="font-bold text-gray-900 dark:text-white truncate" x-text="(last.book_name || '') + ' ' + (last.chapter_number || '')"></p>
                </div>
                <x-icon name="chevron-right" class="w-5 h-5 text-indigo-500 group-hover:translate-x-0.5 transition-transform flex-shrink-0" />
            </a>
        </template>
        {{-- Header --}}
        <div class="text-center mb-10 sm:mb-14">
            <div class="inline-flex items-center justify-center w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-white/80 dark:bg-slate-900/80 border border-amber-200/60 dark:border-amber-800/40 shadow-md text-amber-700 dark:text-amber-400 mb-4 sm:mb-6">
                <x-icon name="book-bible" class="w-8 h-8 sm:w-10 sm:h-10" />
            </div>
            <h1 class="text-3xl sm:text-4xl font-black text-gray-900 dark:text-white tracking-tight mb-2" style="font-family: 'Merriweather', Georgia, serif;">Bíblia Online</h1>
            <p class="text-gray-600 dark:text-gray-400 text-sm sm:text-base max-w-md mx-auto">Escolha uma versão e leia a Bíblia Sagrada no celular, tablet ou computador.</p>
            <a href="{{ route('bible.public.search') }}" class="inline-flex items-center gap-2 mt-3 text-sm font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                <x-icon name="magnifying-glass" class="w-4 h-4" />
                Buscar na Bíblia
            </a>
        </div>

        {{-- Version selector – cards estilo livro --}}
        <div class="space-y-3">
            <h2 class="text-sm font-bold uppercase tracking-widest text-gray-500 dark:text-slate-400 px-1">Selecione a versão</h2>
            <ul class="space-y-3">
                @foreach($versions as $v)
                    <li>
                        <a href="{{ route('bible.public.read', $v->abbreviation) }}"
                           class="flex items-center justify-between gap-4 p-4 sm:p-5 rounded-2xl bg-white/90 dark:bg-slate-900/90 border border-gray-200/80 dark:border-slate-700/80 shadow-sm hover:shadow-md hover:border-indigo-300 dark:hover:border-indigo-600/50 active:scale-[0.99] transition-all group">
                            <div class="flex items-center gap-3 min-w-0">
                                <span class="flex-shrink-0 w-12 h-12 rounded-xl flex items-center justify-center text-white font-bold text-sm shadow-md"
                                      style="background: linear-gradient(135deg, #4f46e5, #6366f1);">
                                    {{ strtoupper(substr($v->abbreviation, 0, 2)) }}
                                </span>
                                <div class="min-w-0">
                                    <span class="font-bold text-gray-900 dark:text-white block truncate" style="font-family: 'Merriweather', Georgia, serif;">{{ $v->name }}</span>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ $v->abbreviation }}</span>
                                </div>
                            </div>
                            <x-icon name="chevron-right" class="w-5 h-5 text-gray-400 group-hover:text-indigo-500 flex-shrink-0" />
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        <p class="mt-8 text-center text-xs text-gray-500 dark:text-slate-400">
            Leitura gratuita. Não é necessário cadastro.
        </p>
    </div>
</div>
@endsection
