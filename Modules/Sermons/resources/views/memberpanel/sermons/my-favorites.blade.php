@extends('memberpanel::components.layouts.master')

@section('title', 'Meus Favoritos')

@push('styles')
    @vite(['Modules/Sermons/resources/assets/sass/app.scss'])
@endpush

@section('content')
<div class="space-y-8 pb-12">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Meus Favoritos</h1>
            <p class="text-gray-600 dark:text-gray-400">Sua coleção pessoal de sermões e estudos.</p>
        </div>
        <a href="{{ route('memberpanel.sermons.index') }}"
            class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 shadow-sm transition-all hover:-translate-y-0.5">
            <x-icon name="arrow-left" class="w-4 h-4 mr-2" />
            Voltar para Sermões
        </a>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="bg-linear-to-br from-red-500 to-pink-600 rounded-2xl p-6 text-white shadow-lg shadow-pink-500/20">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center">
                    <x-icon name="heart" class="w-6 h-6 text-white" />
                </div>
                <div>
                    <h3 class="text-3xl font-black">{{ $favorites->total() }}</h3>
                    <p class="text-pink-100 font-medium text-sm">Itens Favoritados</p>
                </div>
            </div>
        </div>
    </div>

    @if($favorites->count() > 0)
        <!-- Favorites Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($favorites as $favorite)
                @php $sermon = $favorite->sermon; @endphp
                <a href="{{ route('memberpanel.sermons.show', $sermon) }}" class="group bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden hover:shadow-xl hover:border-pink-500/30 dark:hover:border-pink-500/30 transition-all duration-300 flex flex-col h-full hover:-translate-y-1">
                    <!-- Card Header -->
                    <div class="p-6 pb-0 flex items-start justify-between">
                        @if($sermon->category)
                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold uppercase tracking-wide"
                                  style="background-color: {{ $sermon->category->color ?? '#3B82F6' }}15; color: {{ $sermon->category->color ?? '#3B82F6' }}">
                                {{ $sermon->category->name }}
                            </span>
                        @else
                            <span></span>
                        @endif

                        <div class="w-8 h-8 rounded-full bg-pink-50 dark:bg-pink-900/20 flex items-center justify-center text-pink-500 dark:text-pink-400">
                             <x-icon name="heart" class="w-4 h-4" />
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-6 flex-1 flex flex-col">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white group-hover:text-pink-600 dark:group-hover:text-pink-400 transition-colors mb-2 line-clamp-2">
                            {{ $sermon->title }}
                        </h3>

                        @if($sermon->subtitle)
                             <p class="text-sm text-gray-500 dark:text-gray-400 mb-4 line-clamp-2 leading-relaxed">
                                {{ $sermon->subtitle }}
                            </p>
                        @endif

                        @if($favorite->notes)
                            <div class="mt-auto mb-4 p-3 bg-yellow-50 dark:bg-yellow-900/10 rounded-xl border border-yellow-100 dark:border-yellow-800/30">
                                <div class="flex items-center gap-1.5 mb-1 text-xs font-bold text-yellow-700 dark:text-yellow-400 uppercase tracking-wide">
                                    <x-icon name="pencil-alt" class="w-3 h-3" /> Minhas Notas
                                </div>
                                <p class="text-xs text-yellow-800 dark:text-yellow-300 italic line-clamp-2">
                                    "{{ $favorite->notes }}"
                                </p>
                            </div>
                        @else
                            <div class="mt-auto"></div>
                        @endif

                        <div class="pt-4 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between text-xs font-bold text-gray-400 uppercase tracking-wider">
                            <div class="flex items-center gap-2">
                                 <div class="w-6 h-6 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-500 dark:text-gray-300 text-[10px]">
                                    {{ substr($sermon->user->name, 0, 1) }}
                                </div>
                                {{ Str::limit($sermon->user->name, 15) }}
                            </div>
                            <span>{{ $favorite->created_at->format('d/m') }}</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $favorites->links() }}
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 rounded-3xl p-16 text-center border border-gray-100 dark:border-gray-700 shadow-sm col-span-full">
            <div class="mx-auto w-24 h-24 bg-pink-50 dark:bg-pink-900/20 rounded-full flex items-center justify-center mb-6 animate-pulse">
                <x-icon name="heart" class="w-12 h-12 text-pink-400" />
            </div>
            <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-3">Lista de Favoritos Vazia</h3>
            <p class="text-gray-500 dark:text-gray-400 max-w-md mx-auto leading-relaxed mb-8">
                Você ainda não adicionou nenhum sermão aos favoritos. Explore nossa biblioteca e guarde o que mais lhe edificar.
            </p>
            <a href="{{ route('memberpanel.sermons.index') }}"
                class="inline-flex items-center px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg shadow-blue-600/20 transition-all hover:-translate-y-0.5">
                <x-icon name="search" class="w-5 h-5 mr-2" />
                Explorar Biblioteca
            </a>
        </div>
    @endif
</div>
@endsection

