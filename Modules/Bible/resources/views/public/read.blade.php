@extends('homepage::components.layouts.master')

@section('title', 'Bíblia ' . $version->abbreviation . ' – Livros')

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
<div class="bible-public-container min-h-screen pb-24">
    {{-- Sticky header --}}
    <header class="sticky top-0 z-30 bg-white/95 dark:bg-slate-950/95 backdrop-blur-md border-b border-gray-200 dark:border-slate-800">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 py-4">
            <div class="flex items-center justify-between gap-4">
                <a href="{{ route('bible.public.index') }}"
                   class="flex items-center gap-2 text-gray-500 hover:text-gray-900 dark:text-slate-400 dark:hover:text-white transition-colors shrink-0">
                    <span class="w-9 h-9 rounded-full bg-gray-100 dark:bg-slate-800 flex items-center justify-center">
                        <x-icon name="chevron-left" class="w-5 h-5" />
                    </span>
                    <span class="hidden sm:inline text-sm font-bold">Voltar</span>
                </a>
                <div class="flex-1 min-w-0 flex items-center justify-center gap-2">
                    <x-icon name="book-bible" class="w-6 h-6 text-indigo-500 dark:text-indigo-400 shrink-0" />
                    <h1 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white truncate" style="font-family: 'Merriweather', Georgia, serif;">Bíblia {{ $version->abbreviation }}</h1>
                </div>
                <div class="w-9 h-9 shrink-0" aria-hidden="true"></div>
            </div>
            {{-- Version switcher --}}
            <div class="mt-3 pt-3 border-t border-gray-100 dark:border-slate-800">
                <label for="version-select" class="sr-only">Trocar versão</label>
                <select id="version-select"
                        onchange="window.location.href = '{{ url('biblia-online/versao') }}/' + this.value"
                        class="w-full appearance-none pl-4 pr-10 py-2.5 bg-gray-100 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl text-sm font-bold text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                    @foreach($versions as $v)
                        <option value="{{ $v->abbreviation }}" {{ $v->id === $version->id ? 'selected' : '' }}>{{ $v->name }} ({{ $v->abbreviation }})</option>
                    @endforeach
                </select>
            </div>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 sm:px-6 py-6 sm:py-8">
        @if(session('error'))
            <div class="mb-6 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 px-4 py-3 text-sm text-red-800 dark:text-red-200">
                {{ session('error') }}
            </div>
        @endif
        {{-- Old Testament – cards estilo livro --}}
        <section class="mb-10">
            <h2 class="flex items-center gap-2 text-xs font-black uppercase tracking-widest text-amber-600 dark:text-amber-400 mb-4">
                <x-icon name="book-open" class="w-4 h-4" />
                Antigo Testamento
            </h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2 sm:gap-3">
                @foreach($oldTestament as $b)
                    <a href="{{ route('bible.public.book', [$version->abbreviation, $b->book_number]) }}"
                       class="flex items-center justify-center p-3 sm:p-4 rounded-xl bg-white/90 dark:bg-slate-900/90 border border-amber-200/60 dark:border-amber-800/40 text-gray-800 dark:text-slate-200 font-bold text-sm sm:text-base hover:border-amber-400 dark:hover:border-amber-500 hover:bg-amber-50/80 dark:hover:bg-amber-900/20 hover:shadow-md active:scale-[0.98] transition-all"
                       style="font-family: 'Merriweather', Georgia, serif;">
                        {{ $b->name }}
                    </a>
                @endforeach
            </div>
        </section>

        {{-- New Testament – cards estilo livro --}}
        <section>
            <h2 class="flex items-center gap-2 text-xs font-black uppercase tracking-widest text-emerald-600 dark:text-emerald-400 mb-4">
                <x-icon name="book-open" class="w-4 h-4" />
                Novo Testamento
            </h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2 sm:gap-3">
                @foreach($newTestament as $b)
                    <a href="{{ route('bible.public.book', [$version->abbreviation, $b->book_number]) }}"
                       class="flex items-center justify-center p-3 sm:p-4 rounded-xl bg-white/90 dark:bg-slate-900/90 border border-emerald-200/60 dark:border-emerald-800/40 text-gray-800 dark:text-slate-200 font-bold text-sm sm:text-base hover:border-emerald-400 dark:hover:border-emerald-500 hover:bg-emerald-50/80 dark:hover:bg-emerald-900/20 hover:shadow-md active:scale-[0.98] transition-all"
                       style="font-family: 'Merriweather', Georgia, serif;">
                        {{ $b->name }}
                    </a>
                @endforeach
            </div>
        </section>
    </main>
</div>
@endsection
