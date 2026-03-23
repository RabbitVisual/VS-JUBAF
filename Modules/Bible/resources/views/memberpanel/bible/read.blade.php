@extends('memberpanel::components.layouts.master')

@section('title', 'Bíblia Digital')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-slate-950 transition-colors pb-12">

        <!-- Sticky Header: Search & Navigation -->
        <div class="sticky top-0 z-30 bg-white/90 dark:bg-slate-950/90 backdrop-blur-xl border-b border-gray-200 dark:border-slate-800 transition-colors duration-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col sm:flex-row items-center justify-between py-4 gap-4">
                    <!-- Title -->
                    <div class="flex items-center gap-3">
                         <div class="p-2 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg">
                             <x-icon name="book-bible" class="w-6 h-6 text-indigo-600 dark:text-indigo-400" />
                         </div>
                         <div>
                             <h1 class="text-xl font-black text-gray-900 dark:text-white tracking-tight leading-tight">Bíblia Sagrada</h1>
                             <p class="text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wide mt-1">Leitura & Estudo</p>
                         </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-3 w-full sm:w-auto">
                        <a href="{{ route('memberpanel.bible.search') }}"
                            data-tour="bible-search-link"
                            class="flex-1 sm:flex-none inline-flex items-center justify-center px-4 py-2 text-xs font-bold uppercase tracking-widest text-gray-600 bg-gray-50 border border-gray-200 rounded-xl hover:bg-gray-100 hover:text-indigo-600 dark:bg-slate-900 dark:text-slate-400 dark:border-slate-800 dark:hover:bg-slate-800 dark:hover:text-white transition-all">
                            <x-icon name="magnifying-glass" class="w-4 h-4 mr-2" />
                            Buscar
                        </a>
                        <a href="{{ route('memberpanel.bible.favorites') }}"
                            class="flex-1 sm:flex-none inline-flex items-center justify-center px-4 py-2 text-xs font-bold uppercase tracking-widest text-gray-600 bg-gray-50 border border-gray-200 rounded-xl hover:bg-gray-100 hover:text-red-500 dark:bg-slate-900 dark:text-slate-400 dark:border-slate-800 dark:hover:bg-slate-800 dark:hover:text-red-400 transition-all">
                            <x-icon name="heart" class="w-4 h-4 mr-2" />
                            Favoritos
                        </a>
                       <a href="{{ route('memberpanel.bible.interlinear') }}"
                            class="flex-1 sm:flex-none inline-flex items-center justify-center px-4 py-2 text-xs font-bold uppercase tracking-widest text-white bg-indigo-600 border border-transparent rounded-xl hover:bg-indigo-700 shadow-lg shadow-indigo-500/20 transition-all">
                            <x-icon name="language" class="w-4 h-4 mr-2" />
                            Interlinear
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8 space-y-8">

            <!-- Version Selector Card -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-slate-800 p-6 shadow-sm" data-tour="bible-version">
                <div class="flex flex-col md:flex-row md:items-center gap-6">
                    <div class="flex-1">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Selecione uma Versão</h2>
                        <p class="text-sm text-gray-500 dark:text-slate-400">Escolha a tradução que você deseja ler.</p>
                    </div>
                    <div class="w-full md:w-72">
                        <div class="relative">
                            <select id="version-select" onchange="window.location.href = '{{ route('memberpanel.bible.read', '') }}/' + this.value"
                                class="w-full appearance-none pl-4 pr-10 py-3 bg-gray-50 dark:bg-slate-800 border-2 border-transparent focus:border-indigo-500 dark:focus:border-indigo-500 rounded-xl text-sm font-bold text-gray-900 dark:text-white outline-none transition-all cursor-pointer hover:bg-gray-100 dark:hover:bg-slate-700">
                                <option value="">Selecione...</option>
                                @foreach($versions as $v)
                                    <option value="{{ $v->abbreviation }}" {{ $v->id === $version->id ? 'selected' : '' }}>
                                        {{ $v->abbreviation }} - {{ $v->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                                <x-icon name="chevron-down" class="w-4 h-4" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Books Grid Layout -->
            <div class="grid md:grid-cols-2 gap-8" data-tour="bible-book">

                <!-- Old Testament -->
                <div class="space-y-4">
                    <div class="flex items-center gap-3 px-2">
                         <div class="w-8 h-8 rounded-lg bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center text-amber-600 dark:text-amber-500">
                             <span class="font-black text-xs">AT</span>
                         </div>
                         <h3 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tight">Antigo Testamento</h3>
                         <span class="ml-auto text-xs font-bold text-gray-400 dark:text-slate-500 bg-gray-100 dark:bg-slate-800 px-2 py-1 rounded-md">{{ $oldTestament->count() }} Livros</span>
                    </div>

                    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-slate-800 p-6 shadow-sm">
                        <div class="grid grid-cols-3 sm:grid-cols-4 lg:grid-cols-5 gap-2">
                            @foreach($oldTestament as $book)
                                <a href="{{ route('memberpanel.bible.book', ['version' => $version->abbreviation, 'book' => $book->book_number]) }}"
                                    class="group flex flex-col items-center justify-center p-2 rounded-xl border border-transparent hover:border-amber-200 dark:hover:border-amber-800 bg-gray-50 dark:bg-slate-800 hover:bg-amber-50 dark:hover:bg-amber-900/20 text-center transition-all duration-200"
                                    title="{{ $book->name }}">
                                    <span class="text-xs font-bold text-gray-400 dark:text-slate-500 group-hover:text-amber-600 dark:group-hover:text-amber-400 mb-0.5 transition-colors">{{ $book->abbreviation ?: substr($book->name, 0, 3) }}</span>
                                    <span class="text-[10px] sm:text-xs font-medium text-gray-700 dark:text-slate-300 group-hover:text-gray-900 dark:group-hover:text-white truncate w-full px-1">{{ $book->name }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- New Testament -->
                <div class="space-y-4">
                     <div class="flex items-center gap-3 px-2">
                         <div class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600 dark:text-emerald-500">
                             <span class="font-black text-xs">NT</span>
                         </div>
                         <h3 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tight">Novo Testamento</h3>
                         <span class="ml-auto text-xs font-bold text-gray-400 dark:text-slate-500 bg-gray-100 dark:bg-slate-800 px-2 py-1 rounded-md">{{ $newTestament->count() }} Livros</span>
                    </div>

                    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-slate-800 p-6 shadow-sm">
                        <div class="grid grid-cols-3 sm:grid-cols-4 lg:grid-cols-5 gap-2">
                            @foreach($newTestament as $book)
                                <a href="{{ route('memberpanel.bible.book', ['version' => $version->abbreviation, 'book' => $book->book_number]) }}"
                                    class="group flex flex-col items-center justify-center p-2 rounded-xl border border-transparent hover:border-emerald-200 dark:hover:border-emerald-800 bg-gray-50 dark:bg-slate-800 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 text-center transition-all duration-200"
                                    title="{{ $book->name }}">
                                    <span class="text-xs font-bold text-gray-400 dark:text-slate-500 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 mb-0.5 transition-colors">{{ $book->abbreviation ?: substr($book->name, 0, 3) }}</span>
                                    <span class="text-[10px] sm:text-xs font-medium text-gray-700 dark:text-slate-300 group-hover:text-gray-900 dark:group-hover:text-white truncate w-full px-1">{{ $book->name }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
@endsection

