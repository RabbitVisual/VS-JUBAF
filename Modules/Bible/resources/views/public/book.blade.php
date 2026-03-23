@extends('homepage::components.layouts.master')

@section('title', $book->name . ' – Bíblia ' . $version->abbreviation)

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
    .bible-chapter-num {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 2.5rem;
        height: 2.5rem;
        font-family: system-ui, sans-serif;
        font-size: 0.95rem;
        font-weight: 800;
        color: white;
        background: linear-gradient(135deg, #4f46e5, #6366f1);
        border-radius: 50%;
        box-shadow: 0 2px 8px rgba(79, 70, 229, 0.35);
    }
    .dark .bible-chapter-num {
        background: linear-gradient(135deg, #4338ca, #4f46e5);
    }
</style>
@endpush

@section('content')
<div class="bible-public-container min-h-screen pb-24">
    {{-- Sticky header --}}
    <header class="sticky top-0 z-30 bg-white/95 dark:bg-slate-950/95 backdrop-blur-md border-b border-gray-200 dark:border-slate-800">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 py-4">
            <div class="flex items-center justify-between gap-4">
                <a href="{{ route('bible.public.read', $version->abbreviation) }}"
                   class="flex items-center gap-2 text-gray-500 hover:text-gray-900 dark:text-slate-400 dark:hover:text-white transition-colors shrink-0">
                    <span class="w-9 h-9 rounded-full bg-gray-100 dark:bg-slate-800 flex items-center justify-center">
                        <x-icon name="chevron-left" class="w-5 h-5" />
                    </span>
                    <span class="hidden sm:inline text-sm font-bold">Livros</span>
                </a>
                <h1 class="flex-1 min-w-0 text-lg sm:text-xl font-black text-gray-900 dark:text-white text-center truncate px-2" style="font-family: 'Merriweather', Georgia, serif;">{{ $book->name }}</h1>
                <div class="w-9 h-9 shrink-0" aria-hidden="true"></div>
            </div>
            <p class="text-center text-xs text-gray-500 dark:text-slate-400 mt-1">
                {{ $book->testament === 'old' ? 'Antigo Testamento' : 'Novo Testamento' }} · {{ $chapters->count() }} capítulos
            </p>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 sm:px-6 py-6 sm:py-8">
        @if(session('error'))
            <div class="mb-6 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 px-4 py-3 text-sm text-red-800 dark:text-red-200">
                {{ session('error') }}
            </div>
        @endif
        @if($chapters->isEmpty())
            <div class="text-center py-16">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-200 dark:bg-slate-700 flex items-center justify-center">
                    <x-icon name="triangle-exclamation" class="w-8 h-8 text-gray-400 dark:text-slate-500" />
                </div>
                <p class="text-gray-500 dark:text-slate-400">Nenhum capítulo disponível para este livro.</p>
            </div>
        @else
            <div class="grid grid-cols-5 sm:grid-cols-6 md:grid-cols-8 lg:grid-cols-10 gap-2 sm:gap-3">
                @foreach($chapters as $ch)
                    <a href="{{ route('bible.public.chapter', [$version->abbreviation, $book->book_number, $ch->chapter_number]) }}"
                       class="aspect-square flex items-center justify-center rounded-xl bg-white/90 dark:bg-slate-900/90 border border-gray-200 dark:border-slate-700 hover:border-indigo-400 dark:hover:border-indigo-500 hover:shadow-md active:scale-95 transition-all group">
                        <span class="bible-chapter-num group-hover:scale-105 transition-transform">{{ $ch->chapter_number }}</span>
                    </a>
                @endforeach
            </div>
        @endif
    </main>
</div>
@endsection
