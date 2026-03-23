@extends('memberpanel::components.layouts.master')

@section('title', $book->name . ' - ' . $version->name)

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-slate-950 transition-colors pb-12">

        <!-- Sticky Header -->
        <div class="sticky top-0 z-30 bg-white/90 dark:bg-slate-950/90 backdrop-blur-xl border-b border-gray-200 dark:border-slate-800 transition-colors duration-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                 <div class="flex items-center justify-between">
                    <!-- Breadcrumbs / Title -->
                    <div class="flex items-center gap-4">
                        <a href="{{ route('memberpanel.bible.read', $version->abbreviation) }}"
                           class="flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 dark:bg-slate-800 hover:bg-gray-200 dark:hover:bg-slate-700 text-gray-500 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                            <x-icon name="arrow-left" class="w-4 h-4" />
                        </a>
                        <div>
                             <h1 class="text-xl font-black text-gray-900 dark:text-white tracking-tight leading-tight">{{ $book->name }}</h1>
                             <p class="text-xs font-bold text-gray-400 dark:text-slate-500 uppercase tracking-widest mt-1">
                                 {{ $book->testament == 'old' ? 'Antigo Testamento' : 'Novo Testamento' }} • {{ $chapters->count() }} Capítulos
                             </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8 space-y-6">

            <!-- Stats / Info Card -->
            <div class="bg-linear-to-br from-indigo-500 to-purple-600 rounded-3xl p-8 text-white shadow-xl shadow-indigo-500/20 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none"></div>
                <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div>
                        <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/20 rounded-lg text-xs font-bold uppercase tracking-widest mb-3 backdrop-blur-sm">
                            <x-icon name="book" class="w-3 h-3" />
                            <span>Visão Geral</span>
                        </div>
                        <h2 class="text-3xl font-black tracking-tight mb-2">{{ $book->name }}</h2>
                        <p class="text-indigo-100 font-medium max-w-xl">
                            Explore os capítulos deste livro. Selecione um abaixo para iniciar sua leitura na versão {{ $version->abbreviation }}.
                        </p>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="text-center bg-white/10 rounded-2xl p-4 backdrop-blur-sm">
                            <span class="block text-3xl font-black">{{ $chapters->count() }}</span>
                            <span class="text-[10px] uppercase tracking-widest text-indigo-200">Capítulos</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chapters Grid -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-slate-800 p-6 shadow-sm">
                @if($chapters->isEmpty())
                     <div class="text-center py-20">
                        <div class="w-20 h-20 bg-gray-50 dark:bg-slate-900 rounded-full flex items-center justify-center mx-auto mb-6">
                            <x-icon name="triangle-exclamation" class="w-8 h-8 text-gray-300 dark:text-slate-700" />
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Sem Capítulos</h3>
                        <p class="text-gray-500 dark:text-slate-400">Não há capítulos disponíveis para este livro nesta versão.</p>
                    </div>
                @else
                    <div class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 lg:grid-cols-10 xl:grid-cols-12 gap-3">
                        @foreach($chapters as $chapter)
                            <a href="{{ route('memberpanel.bible.chapter', ['version' => $version->abbreviation, 'book' => $book->book_number, 'chapter' => $chapter->chapter_number]) }}"
                                class="group flex flex-col items-center justify-center aspect-square rounded-2xl border border-gray-100 dark:border-slate-700 bg-white dark:bg-slate-800 hover:border-indigo-500 dark:hover:border-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 hover:shadow-lg hover:shadow-indigo-500/10 transition-all duration-200">
                                <span class="text-lg font-black text-gray-700 dark:text-slate-300 group-hover:text-indigo-600 dark:group-hover:text-white transition-colors">
                                    {{ $chapter->chapter_number }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </div>
@endsection

