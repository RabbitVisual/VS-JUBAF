@extends('memberpanel::components.layouts.master')

@section('title', 'Séries Bíblicas')

@push('styles')
    @vite(['Modules/Sermons/resources/assets/sass/app.scss'])
@endpush

@section('content')
<div class="space-y-8 pb-12">
    <!-- Hero Section -->
    <div class="relative overflow-hidden bg-slate-900 rounded-3xl shadow-2xl border border-slate-800">
         <div class="absolute inset-0 opacity-40 pointer-events-none">
            <div class="absolute -top-24 -left-20 w-96 h-96 bg-indigo-600 rounded-full blur-[100px]"></div>
             <div class="absolute top-1/2 right-20 w-80 h-80 bg-pink-600 rounded-full blur-[100px]"></div>
        </div>

        <div class="relative px-8 py-10 flex flex-col md:flex-row items-center justify-between gap-8 z-10">
            <div class="flex-1 space-y-2">
                 <p class="text-indigo-200/80 font-bold uppercase tracking-widest text-xs">Jornadas Temáticas</p>
                <h1 class="text-3xl font-black text-white tracking-tight">
                    Séries Bíblicas
                </h1>
                <p class="text-slate-300 font-medium max-w-xl">
                    Acompanhe nossas sequências de mensagens e estudos aprofundados.
                </p>
            </div>
        </div>
    </div>

    <!-- Series Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($series as $s)
            <a href="{{ route('memberpanel.series.show', $s) }}" class="group flex flex-col bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-2xl hover:shadow-purple-500/10 transition-all duration-300 hover:-translate-y-1 overflow-hidden h-full">
                <!-- Image -->
                <div class="aspect-video w-full bg-gray-100 dark:bg-gray-700 relative overflow-hidden">
                    @if($s->image)
                        <img src="{{ asset('storage/' . $s->image) }}" alt="{{ $s->title }}" class="object-cover w-full h-full group-hover:scale-105 transition-transform duration-700">
                    @else
                        <div class="flex flex-col items-center justify-center h-full text-gray-400 dark:text-gray-500 bg-gray-50 dark:bg-gray-800 group-hover:bg-gray-100 dark:group-hover:bg-gray-700/50 transition-colors">
                            <x-icon name="collection" class="w-16 h-16 opacity-30 mb-2" />
                            <span class="text-xs font-bold uppercase tracking-widest opacity-50">Sem Capa</span>
                        </div>
                    @endif

                    <!-- Overlay Gradient -->
                    <div class="absolute inset-0 bg-linear-to-t from-black/80 via-transparent to-transparent opacity-60 group-hover:opacity-40 transition-opacity"></div>

                    <!-- Floating Badge -->
                    <div class="absolute bottom-4 left-4 right-4 flex justify-between items-end">
                        <div class="space-x-1">
                             <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-white/20 backdrop-blur-md text-white border border-white/10 shadow-lg">
                                {{ $s->sermons_count }} sermões
                            </span>
                             @if($s->studies_count > 0)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-purple-500/80 backdrop-blur-md text-white shadow-lg">
                                {{ $s->studies_count }} estudos
                            </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div class="p-8 flex-1 flex flex-col">
                    <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-3 group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors line-clamp-2 leading-tight">
                        {{ $s->title }}
                    </h3>

                    <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed mb-8 line-clamp-3 font-medium">
                        {{ $s->description }}
                    </p>

                    <div class="mt-auto pt-6 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
                        <span class="text-sm font-bold text-purple-600 dark:text-purple-400 group-hover:translate-x-1 transition-transform flex items-center gap-1">
                            Acessar Série <x-icon name="arrow-narrow-right" class="w-4 h-4" />
                        </span>
                    </div>
                </div>
            </a>
        @empty
            <div class="col-span-full">
                <div class="bg-white dark:bg-gray-800 rounded-3xl p-16 text-center border border-gray-100 dark:border-gray-700 shadow-sm">
                    <div class="mx-auto w-20 h-20 bg-gray-50 dark:bg-gray-700 rounded-full flex items-center justify-center mb-6 animate-pulse">
                        <x-icon name="collection" class="w-10 h-10 text-gray-300 dark:text-gray-500" />
                    </div>
                    <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-3">Nenhuma série encontrada</h3>
                    <p class="text-gray-500 dark:text-gray-400 max-w-md mx-auto leading-relaxed">
                        Nossas séries de mensagens estarão disponíveis aqui em breve.
                    </p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="pt-8">
        {{ $series->links() }}
    </div>
</div>
@endsection

