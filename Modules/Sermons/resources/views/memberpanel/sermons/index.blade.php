@extends('memberpanel::components.layouts.master')

@section('title', 'Sermões')

@push('styles')
    @vite(['Modules/Sermons/resources/assets/sass/app.scss'])
@endpush

@section('content')
<div class="space-y-8 pb-12" data-tour="sermons-list">
    <!-- Hero Section -->
    <div class="relative overflow-hidden bg-slate-900 rounded-3xl shadow-2xl border border-slate-800">
         <div class="absolute inset-0 opacity-40 pointer-events-none">
            <div class="absolute -top-24 -left-20 w-96 h-96 bg-blue-600 rounded-full blur-[100px]"></div>
            <div class="absolute top-1/2 right-40 w-80 h-80 bg-purple-600 rounded-full blur-[100px]"></div>
        </div>

        <div class="relative px-8 py-10 flex flex-col md:flex-row items-center justify-between gap-8 z-10">
            <div class="flex-1 space-y-2">
                 <p class="text-blue-200/80 font-bold uppercase tracking-widest text-xs">Edificação Espiritual</p>
                <h1 class="text-3xl md:text-4xl font-black text-white tracking-tight">
                    Sermões e Estudos
                </h1>
                <p class="text-slate-300 font-medium max-w-xl">
                    Explore nossa biblioteca de mensagens, estudos bíblicos e devocionais.
                </p>
            </div>

             <div class="flex flex-wrap items-center gap-3">
                 <a href="{{ route('memberpanel.sermons.my-favorites') }}"
                    class="inline-flex items-center px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white border border-white/20 rounded-xl font-bold transition-all backdrop-blur-sm"
                    data-tour="sermons-favorites-link">
                    <x-icon name="heart" class="w-5 h-5 mr-2" />
                    Meus Favoritos
                </a>
                <a href="{{ route('memberpanel.sermons.create') }}"
                    class="inline-flex items-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white shadow-lg shadow-blue-600/30 rounded-xl font-bold transition-all hover:-translate-y-0.5">
                    <x-icon name="plus" class="w-5 h-5 mr-2" />
                    Criar Sermão
                </a>
            </div>
        </div>
    </div>

    <!-- Featured Sermons -->
    @if ($featuredSermons->count() > 0)
        <div>
            <h2 class="text-xl font-black text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                <x-icon name="star" class="w-6 h-6 text-yellow-500" />
                Destaques
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach ($featuredSermons as $sermon)
                    <a href="{{ route('memberpanel.sermons.show', $sermon) }}"
                        class="group relative bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-all duration-300 hover:-translate-y-1 overflow-hidden flex flex-col h-full">

                        <div class="h-48 overflow-hidden relative">
                             @if($sermon->cover_image)
                                <img src="{{ asset('storage/' . $sermon->cover_image) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                             @else
                                <div class="w-full h-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center">
                                    <x-icon name="photograph" class="w-12 h-12 text-gray-300" />
                                </div>
                             @endif
                             <div class="absolute inset-0 bg-linear-to-t from-black/60 to-transparent"></div>
                             <div class="absolute bottom-4 left-4 flex gap-2">
                                @if ($sermon->category)
                                    <span class="px-2.5 py-1 text-[10px] font-black rounded-lg uppercase tracking-wide bg-blue-600 text-white shadow-lg">
                                        {{ $sermon->category->name }}
                                    </span>
                                @endif
                             </div>
                        </div>

                        <div class="p-6 flex flex-col h-full relative z-10">
                            <h3 class="text-lg font-black text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors mb-2 line-clamp-2">
                                {{ $sermon->title }}
                            </h3>

                            @if ($sermon->subtitle)
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 line-clamp-2 leading-relaxed">
                                    {{ $sermon->subtitle }}
                                </p>
                            @endif

                            <div class="mt-auto pt-4 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                <div class="flex items-center gap-1.5 text-blue-600 dark:text-blue-400">
                                    <x-icon name="star" class="w-3.5 h-3.5 fill-current" />
                                    Destaque
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <x-icon name="calendar" class="w-3.5 h-3.5" />
                                    {{ $sermon->published_at?->format('d/m') }}
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Sidebar Filters -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 sticky top-6">
                <h3 class="text-lg font-black text-gray-900 dark:text-white mb-6 uppercase tracking-wide flex items-center gap-2">
                    <x-icon name="filter" class="w-5 h-5 text-gray-400" />
                    Filtros
                </h3>

                <form method="GET" action="{{ route('memberpanel.sermons.index') }}" class="space-y-5">
                    <div>
                         <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                            Buscar
                        </label>
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}"
                                class="w-full pl-10 pr-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white font-medium focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all placeholder-gray-400"
                                placeholder="Título, pregador...">
                            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                <x-icon name="search" class="w-5 h-5" />
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                            Categoria
                        </label>
                        <select name="category_id"
                            class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white font-medium focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                            <option value="">Todas as categorias</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                            Tags
                        </label>
                         <select name="tag_id"
                            class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white font-medium focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                            <option value="">Todas as tags</option>
                            @foreach ($tags as $tag)
                                <option value="{{ $tag->id }}" {{ request('tag_id') == $tag->id ? 'selected' : '' }}>
                                    {{ $tag->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="pt-2 flex flex-col gap-2">
                        <button type="submit"
                            class="w-full px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-blue-600/20 hover:-translate-y-0.5">
                            Filtrar Resultados
                        </button>
                        @if (request()->hasAny(['search', 'category_id', 'tag_id', 'featured']))
                            <a href="{{ route('memberpanel.sermons.index') }}"
                                class="w-full px-4 py-2.5 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 font-bold rounded-xl transition-colors text-center">
                                Limpar Filtros
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Sermons List -->
        <div class="lg:col-span-3">
             @if ($sermons->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach ($sermons as $sermon)
                         <a href="{{ route('memberpanel.sermons.show', $sermon) }}"
                           class="group bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-xl hover:border-blue-500/30 dark:hover:border-blue-500/30 transition-all duration-300 hover:-translate-y-1 flex flex-col h-full relative overflow-hidden">

                            <!-- Cover Image Thumbnail -->
                            <div class="h-32 w-full overflow-hidden relative border-b border-gray-100 dark:border-gray-700">
                                @if($sermon->cover_image)
                                    <img src="{{ asset('storage/' . $sermon->cover_image) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full bg-slate-50 dark:bg-slate-900/50 flex items-center justify-center">
                                         <x-icon name="photograph" class="w-8 h-8 text-gray-300" />
                                    </div>
                                @endif
                                <div class="absolute top-3 right-3">
                                     <span class="text-[10px] font-black text-white bg-black/40 backdrop-blur-md px-2 py-1 rounded-full uppercase tracking-tighter">
                                        {{ $sermon->published_at?->format('d M') }}
                                     </span>
                                </div>
                            </div>

                            <div class="p-5 flex flex-col h-full">
                                <!-- Badges -->
                                <div class="flex items-center gap-2 mb-3">
                                    @if($sermon->category)
                                        <span class="px-2 py-0.5 text-[9px] font-black rounded-lg uppercase tracking-widest"
                                              style="background-color: {{ $sermon->category->color ?? '#3B82F6' }}15; color: {{ $sermon->category->color ?? '#3B82F6' }}">
                                            {{ $sermon->category->name }}
                                        </span>
                                    @endif
                                </div>

                                <!-- Title & Subtitle -->
                                <div class="mb-3">
                                    <h3 class="text-base font-black text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors line-clamp-2 mb-1">
                                        {{ $sermon->title }}
                                    </h3>
                                    @if($sermon->subtitle)
                                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 line-clamp-1 italic">{{ $sermon->subtitle }}</p>
                                    @endif
                                </div>

                                <!-- Footer -->
                                <div class="mt-auto pt-3 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <img src="{{ $sermon->user->avatar_url }}" alt="{{ $sermon->user->name }}" class="w-5 h-5 rounded-full object-cover">
                                        @if($sermon->series)
                                            <span class="text-[9px] font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wide">
                                                {{ Str::limit($sermon->series->title, 15) }}
                                            </span>
                                        @else
                                            <span class="text-[9px] font-bold text-gray-400 uppercase tracking-wide">
                                                {{ Str::limit($sermon->user->name, 12) }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-1.5 text-[10px] font-bold text-gray-400">
                                         <x-icon name="eye" class="w-3.5 h-3.5" />
                                         {{ number_format($sermon->views) }}
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $sermons->links() }}
                </div>
            @else
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-12 text-center col-span-full">
                    <div class="w-20 h-20 bg-amber-50 dark:bg-amber-900/20 rounded-full flex items-center justify-center mx-auto mb-6">
                        <x-icon name="book-open" class="w-10 h-10 text-amber-500" />
                    </div>
                    <h3 class="text-xl font-black text-gray-900 dark:text-white mb-2">Nenhum sermão encontrado</h3>
                    <p class="text-gray-500 dark:text-gray-400 max-w-md mx-auto mb-6">
                        @if (request()->hasAny(['search', 'category_id', 'tag_id', 'featured']))
                            Tente ajustar os filtros ou busque por outro termo.
                        @else
                            Que tal começar o seu? Explore a biblioteca ou crie seu primeiro sermão.
                        @endif
                    </p>
                    @if (request()->hasAny(['search', 'category_id', 'tag_id', 'featured']))
                        <a href="{{ route('memberpanel.sermons.index') }}"
                            class="inline-flex items-center px-6 py-3 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-white font-bold rounded-xl transition-all">
                            Limpar filtros
                        </a>
                    @else
                        <a href="{{ route('memberpanel.sermons.create') }}"
                            class="inline-flex items-center px-6 py-3 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-xl shadow-lg transition-all hover:-translate-y-0.5">
                            <x-icon name="plus" class="w-5 h-5 mr-2" />
                            Criar meu primeiro sermão
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
    @vite(['Modules/Sermons/resources/assets/js/app.js'])
@endpush

