@extends('memberpanel::components.layouts.master')

@section('title', 'Comentários Bíblicos')

@push('styles')
    @vite(['Modules/Sermons/resources/assets/sass/app.scss'])
    <style>
        /* Remove seta nativa do select para exibir apenas o ícone Font Awesome */
        form.commentaries-filters select,
        .commentaries-filters select {
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
            appearance: none !important;
            background-image: none !important;
        }
    </style>
@endpush

@push('scripts')
    @vite(['Modules/Sermons/resources/assets/js/app.js'])
@endpush

@section('content')
<div class="space-y-8 pb-12">
     <!-- Hero Section -->
    <div class="relative overflow-hidden bg-slate-900 rounded-3xl shadow-2xl border border-slate-800">
         <div class="absolute inset-0 opacity-40 pointer-events-none">
            <div class="absolute -top-24 -left-20 w-96 h-96 bg-cyan-600 rounded-full blur-[100px]"></div>
             <div class="absolute top-1/2 right-20 w-80 h-80 bg-blue-600 rounded-full blur-[100px]"></div>
        </div>

        <div class="relative px-8 py-10 flex flex-col md:flex-row items-center justify-between gap-8 z-10">
            <div class="flex-1 space-y-2 text-center md:text-left">
                 <p class="text-cyan-200/80 font-bold uppercase tracking-widest text-xs">Exegese e Compreensão</p>
                <h1 class="text-3xl font-black text-white tracking-tight">
                    Explorador de Comentários
                </h1>
                <p class="text-slate-300 font-medium max-w-xl mx-auto md:mx-0">
                     Navegue por comentários versículo a versículo e aprofunde seu entendimento das Escrituras.
                </p>
            </div>
        </div>
    </div>

    <!-- Interface Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Sidebar Filters -->
        <div class="lg:col-span-1">
             <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 p-6 sticky top-6">
                 <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-6 flex items-center gap-2">
                    <x-icon name="search" class="w-4 h-4" />
                    Referências
                 </h3>

                <form method="GET" action="{{ route('memberpanel.commentaries.index') }}" class="commentaries-filters space-y-5"
                    data-book-id="{{ request('book_id') }}"
                    data-chapter-id="{{ request('chapter_id') }}"
                    data-verse-number="{{ request('verse_number') }}">
                    <div>
                         <label for="bible_version_id" class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-2">Versão da Bíblia</label>
                         <div class="relative">
                             <select name="bible_version_id" id="bible_version_id"
                                 class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white font-medium focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 transition-all appearance-none cursor-pointer">
                                 <option value="">Selecione...</option>
                                 @foreach($bibleVersions as $version)
                                     <option value="{{ $version->id }}" {{ request('bible_version_id') == $version->id ? 'selected' : '' }}>{{ $version->name }}</option>
                                 @endforeach
                             </select>
                             <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-gray-500">
                                <x-icon name="chevron-down" class="w-4 h-4" />
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="book_id" class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-2">Livro</label>
                        <div class="relative">
                            <select name="book_id" id="book_id"
                                class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white font-medium focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 transition-all appearance-none cursor-pointer">
                                <option value="">Selecione a versão...</option>
                            </select>
                            <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-gray-500">
                                <x-icon name="chevron-down" class="w-4 h-4" />
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="chapter_id" class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-2">Capítulo</label>
                        <div class="relative">
                            <select name="chapter_id" id="chapter_id"
                                class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white font-medium focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 transition-all appearance-none cursor-pointer">
                                <option value="">Todos</option>
                            </select>
                            <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-gray-500">
                                <x-icon name="chevron-down" class="w-4 h-4" />
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="verse_number" class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-2">Versículo</label>
                        <div class="relative">
                            <select name="verse_number" id="verse_number"
                                class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white font-medium focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 transition-all appearance-none cursor-pointer">
                                <option value="">Todos</option>
                            </select>
                            <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-gray-500">
                                <x-icon name="chevron-down" class="w-4 h-4" />
                            </div>
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full py-3 px-4 bg-cyan-600 hover:bg-cyan-700 text-white font-bold rounded-xl shadow-lg shadow-cyan-600/20 transition-all hover:-translate-y-0.5 flex items-center justify-center gap-2">
                            <x-icon name="search" class="w-4 h-4" /> Buscar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Results List -->
        <div class="lg:col-span-3 space-y-6">
            @forelse($commentaries as $comment)
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-xl hover:border-cyan-500/30 dark:hover:border-cyan-500/30 transition-all duration-300 group overflow-hidden">
                    <div class="flex flex-col md:flex-row">
                        @if($comment->cover_image)
                            <div class="w-full md:w-48 h-48 md:h-auto overflow-hidden relative border-b md:border-b-0 md:border-r border-gray-100 dark:border-gray-700">
                                <img src="{{ asset('storage/' . $comment->cover_image) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            </div>
                        @endif

                        <div class="flex-1 p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <span class="px-3 py-1 text-sm font-black rounded-lg bg-cyan-50 text-cyan-700 dark:bg-cyan-900/30 dark:text-cyan-300 uppercase tracking-wide">
                                        {{ $comment->reference }}
                                    </span>
                                    @if($comment->is_official)
                                        <span class="flex items-center gap-1 text-[10px] text-green-600 dark:text-green-400 font-black uppercase tracking-wider bg-green-50 dark:bg-green-900/20 px-2 py-0.5 rounded">
                                            <x-icon name="check-circle" class="w-3 h-3" /> Oficial
                                        </span>
                                    @endif
                                </div>
                                <a href="{{ route('memberpanel.commentaries.show', $comment) }}" class="text-cyan-600 dark:text-cyan-400 hover:text-cyan-700 font-bold text-sm flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    Ler completo <x-icon name="arrow-right" class="w-4 h-4" />
                                </a>
                            </div>

                            @if($comment->title)
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3 group-hover:text-cyan-600 dark:group-hover:text-cyan-400 transition-colors">
                                    {{ $comment->title }}
                                </h3>
                            @endif

                            <div class="prose dark:prose-invert max-w-none text-gray-600 dark:text-gray-300 text-sm leading-relaxed line-clamp-3 mb-6">
                                {!! nl2br(e(strip_tags($comment->content))) !!}
                            </div>

                            <div class="pt-4 border-t border-gray-100 dark:border-gray-700 flex justify-between items-center text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                                <div class="flex items-center gap-2">
                                     <img src="{{ $comment->user->avatar_url }}" alt="{{ $comment->user->name }}" class="w-5 h-5 rounded-full object-cover">
                                    {{ $comment->user->name }}
                                </div>
                                <span>{{ $comment->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                @if(request('book_id'))
                    <div class="bg-white dark:bg-gray-800 rounded-2xl p-16 text-center border border-gray-100 dark:border-gray-700 shadow-sm">
                        <div class="mx-auto w-20 h-20 bg-gray-50 dark:bg-gray-700 rounded-full flex items-center justify-center mb-6">
                            <x-icon name="search" class="w-10 h-10 text-gray-400" />
                        </div>
                        <h3 class="text-xl font-black text-gray-900 dark:text-white mb-2">Nenhum comentário encontrado</h3>
                        <p class="text-gray-500 dark:text-gray-400 max-w-sm mx-auto">
                            Não encontramos comentários para esta referência. Tente buscar outro capítulo.
                        </p>
                    </div>
                @else
                    <div class="bg-white dark:bg-gray-800 rounded-2xl p-16 text-center border border-gray-100 dark:border-gray-700 shadow-sm">
                        <div class="mx-auto w-24 h-24 bg-cyan-50 dark:bg-cyan-900/20 rounded-full flex items-center justify-center mb-6 animate-pulse">
                            <x-icon name="book-open" class="w-12 h-12 text-cyan-600 dark:text-cyan-400" />
                        </div>
                        <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-3">Comece sua pesquisa</h3>
                        <p class="text-gray-500 dark:text-gray-400 max-w-md mx-auto leading-relaxed">
                            Selecione uma versão, livro e capítulo ao lado para explorar os comentários disponíveis.
                        </p>
                    </div>
                @endif
            @endforelse
        </div>
    </div>

     @if($commentaries->count() > 0)
        <div class="pt-6">
            {{ $commentaries->links() }}
        </div>
    @endif
</div>
@endsection

