@extends('memberpanel::components.layouts.master')

@section('title', 'Estudos Bíblicos')

@push('styles')
    @vite(['Modules/Sermons/resources/assets/sass/app.scss'])
@endpush

@section('content')
<div class="space-y-8 pb-12">
    <!-- Hero Section -->
    <div class="relative overflow-hidden bg-slate-900 rounded-3xl shadow-2xl border border-slate-800">
         <div class="absolute inset-0 opacity-40 pointer-events-none">
            <div class="absolute -top-24 -left-20 w-96 h-96 bg-emerald-600 rounded-full blur-[100px]"></div>
             <div class="absolute top-1/2 right-20 w-80 h-80 bg-teal-600 rounded-full blur-[100px]"></div>
        </div>

        <div class="relative px-8 py-10 flex flex-col md:flex-row items-center justify-between gap-8 z-10">
            <div class="flex-1 space-y-2">
                 <p class="text-emerald-200/80 font-bold uppercase tracking-widest text-xs">Aprofundamento</p>
                <h1 class="text-3xl font-black text-white tracking-tight">
                    Estudos Bíblicos
                </h1>
                <p class="text-slate-300 font-medium max-w-xl">
                     Materiais aprofundados para o seu crescimento e maturidade espiritual.
                </p>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
        <form method="GET" action="{{ route('memberpanel.studies.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
             <div class="md:col-span-1">
                 <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Buscar</label>
                <input type="text" name="search" value="{{ request('search') }}"
                    class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white font-medium focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all placeholder-gray-400"
                    placeholder="Título, tema...">
            </div>

            <div class="md:col-span-1">
                 <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Série</label>
                <select name="series_id" class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white font-medium focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                    <option value="">Todas as Séries</option>
                    @foreach($series as $s)
                        <option value="{{ $s->id }}" {{ request('series_id') == $s->id ? 'selected' : '' }}>{{ $s->title }}</option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-1">
                 <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Categoria</label>
                <select name="category_id" class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white font-medium focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                    <option value="">Todas as Categorias</option>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}" {{ request('category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2 md:col-span-1">
                <button type="submit" class="flex-1 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-emerald-600/20 hover:-translate-y-0.5">
                    Filtrar
                </button>
                @if (request()->hasAny(['search', 'series_id', 'category_id']))
                    <a href="{{ route('memberpanel.studies.index') }}" class="px-4 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 font-bold rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                        Limpar
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Studies List -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @forelse($studies as $study)
            <a href="{{ route('memberpanel.studies.show', $study) }}"
               class="group relative bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-xl hover:border-emerald-500/30 dark:hover:border-emerald-500/30 transition-all duration-300 hover:-translate-y-1 flex flex-col md:flex-row gap-6 overflow-hidden">

                <!-- Background Pattern -->
                <div class="absolute top-0 right-0 p-12 opacity-[0.03] group-hover:opacity-[0.06] transition-opacity pointer-events-none">
                    <x-icon name="book-open" class="w-32 h-32 text-emerald-900 dark:text-white" />
                </div>

                <!-- Cover Image (Optional) -->
                @if($study->cover_image)
                    <div class="w-full md:w-40 h-40 flex-shrink-0 bg-gray-100 dark:bg-gray-900 rounded-xl overflow-hidden shadow-inner">
                        <img src="{{ asset('storage/' . $study->cover_image) }}" alt="{{ $study->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                    </div>
                @else
                     <div class="w-full md:w-40 h-40 flex-shrink-0 bg-linear-to-br from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800 rounded-xl flex items-center justify-center text-gray-300 dark:text-gray-600">
                         <x-icon name="document-text" class="w-12 h-12" />
                    </div>
                @endif

                <div class="flex-1 relative z-10 flex flex-col">
                    <div class="flex flex-wrap items-center gap-2 mb-3">
                         @if($study->category)
                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold uppercase tracking-wide"
                                  style="background-color: {{ $study->category->color }}15; color: {{ $study->category->color }}">
                                {{ $study->category->name }}
                            </span>
                        @endif
                         @if($study->series)
                            <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide flex items-center gap-1">
                                <span class="w-1 h-1 rounded-full bg-gray-400"></span>
                                {{ $study->series->title }}
                            </span>
                        @endif
                    </div>

                    <h3 class="text-xl font-black text-gray-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors mb-2 leading-tight">
                        {{ $study->title }}
                    </h3>

                    @if($study->subtitle)
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-300 mb-3 line-clamp-1">{{ $study->subtitle }}</p>
                    @endif

                    <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2 mb-4 leading-relaxed">
                        {{ strip_tags($study->content) }}
                    </p>

                    <div class="mt-auto flex items-center justify-between pt-4 border-t border-gray-100 dark:border-gray-700/50">
                        <div class="flex items-center gap-4 text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wide">
                            <div class="flex items-center gap-1.5">
                                <img src="{{ $study->user->avatar_url }}" alt="{{ $study->user->name }}" class="w-4 h-4 rounded-full object-cover">
                                <span>{{ $study->published_at?->format('d M') }}</span>
                            </div>
                            @if($study->audio_file || $study->audio_url)
                                <span class="flex items-center gap-1 text-emerald-600 dark:text-emerald-400">
                                    <x-icon name="volume-up" class="w-3.5 h-3.5" /> Áudio
                                </span>
                            @endif
                        </div>

                         <span class="text-emerald-600 dark:text-emerald-400 text-xs font-bold uppercase tracking-wider flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                            Ler <x-icon name="arrow-right" class="w-3 h-3" />
                        </span>
                    </div>
                </div>
            </a>
        @empty
            <div class="col-span-full">
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-16 text-center border border-gray-100 dark:border-gray-700 shadow-sm">
                    <div class="mx-auto w-20 h-20 bg-gray-50 dark:bg-gray-700 rounded-full flex items-center justify-center mb-6">
                        <x-icon name="search" class="w-10 h-10 text-gray-400" />
                    </div>
                    <h3 class="text-xl font-black text-gray-900 dark:text-white mb-2">Nenhum estudo encontrado</h3>
                    <p class="text-gray-500 dark:text-gray-400 max-w-sm mx-auto leading-relaxed">
                         Não encontramos resultados para sua busca. Tente alterar os filtros.
                    </p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="pt-6">
        {{ $studies->links() }}
    </div>
</div>
@endsection

