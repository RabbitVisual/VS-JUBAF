@extends('memberpanel::components.layouts.master')

@section('title', $series->title)

@push('styles')
    @vite(['Modules/Sermons/resources/assets/sass/app.scss'])
@endpush

@section('content')
<div class="space-y-8 pb-12">
    <!-- Header Section -->
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl border border-gray-100 dark:border-gray-700 overflow-hidden relative">
        <div class="relative h-72 md:h-96">
            @if($series->image)
                <img src="{{ asset('storage/' . $series->image) }}" alt="{{ $series->title }}" class="absolute inset-0 w-full h-full object-cover">
                <div class="absolute inset-0 bg-linear-to-t from-gray-900 via-gray-900/60 to-transparent"></div>
            @else
                <div class="absolute inset-0 bg-linear-to-br from-indigo-900 to-purple-900"></div>
                 <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20"></div>
            @endif

            <div class="absolute inset-0 flex flex-col justify-end p-8 md:p-12 z-10">
                 <div class="flex items-center gap-3 mb-4">
                     <a href="{{ route('memberpanel.series.index') }}"
                        class="inline-flex items-center px-3 py-1.5 bg-white/10 hover:bg-white/20 text-white backdrop-blur-md rounded-lg text-xs font-bold uppercase tracking-wider transition-all">
                        <x-icon name="arrow-left" class="w-3 h-3 mr-1" /> Voltar
                    </a>
                    @if($series->is_featured)
                        <span class="inline-flex items-center px-3 py-1.5 bg-yellow-500/20 text-yellow-300 backdrop-blur-md rounded-lg text-xs font-bold uppercase tracking-wider border border-yellow-500/30">
                            <x-icon name="star" class="w-3 h-3 mr-1" /> Destaque
                        </span>
                    @endif
                </div>

                <h1 class="text-4xl md:text-6xl font-black text-white mb-6 leading-tight tracking-tight drop-shadow-lg">
                    {{ $series->title }}
                </h1>

                <div class="flex flex-wrap items-center gap-6 text-sm font-medium text-gray-200">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-white/10 backdrop-blur-md flex items-center justify-center">
                            <x-icon name="clock" class="w-4 h-4" />
                        </div>
                        <span>Atualizado {{ $series->updated_at->diffForHumans() }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-white/10 backdrop-blur-md flex items-center justify-center">
                            <x-icon name="collection" class="w-4 h-4" />
                        </div>
                        <span>{{ $series->sermons_count + $series->studies_count }} Episódios</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-8 md:p-12 bg-white dark:bg-gray-800">
             <div class="prose dark:prose-invert max-w-none text-lg leading-relaxed text-gray-600 dark:text-gray-300 font-medium">
                {{ $series->description }}
            </div>
        </div>
    </div>

    <!-- Content List -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content Column -->
        <div class="lg:col-span-2 space-y-10">
            @if($series->sermons->count() > 0)
                <div>
                    <h2 class="text-2xl font-black text-gray-900 dark:text-white mb-6 flex items-center gap-3">
                         <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400 shadow-sm">
                            <x-icon name="microphone" class="w-5 h-5" />
                        </div>
                        Sermões
                    </h2>
                    <div class="space-y-4">
                        @foreach($series->sermons as $sermon)
                            <a href="{{ route('memberpanel.sermons.show', $sermon) }}"
                               class="group block bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-xl hover:border-blue-500/30 dark:hover:border-blue-500/30 transition-all duration-300 hover:-translate-y-1">
                                <div class="flex gap-6 items-start">
                                    <div class="flex-shrink-0 flex items-center justify-center w-12 h-12 rounded-2xl bg-gray-50 dark:bg-gray-700 text-gray-400 dark:text-gray-500 font-black text-lg group-hover:bg-blue-600 group-hover:text-white dark:group-hover:bg-blue-600 dark:group-hover:text-white transition-colors shadow-inner">
                                        {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors mb-1">
                                            {{ $sermon->title }}
                                        </h3>
                                        @if($sermon->subtitle)
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-3 font-medium">{{ $sermon->subtitle }}</p>
                                        @endif

                                        <div class="flex items-center gap-4 text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wide">
                                            <span class="flex items-center gap-1.5">
                                                <x-icon name="calendar" class="w-3.5 h-3.5" />
                                                {{ $sermon->published_at?->format('d M, Y') }}
                                            </span>
                                            <span class="flex items-center gap-1.5">
                                                <x-icon name="eye" class="w-3.5 h-3.5" />
                                                {{ number_format($sermon->views) }}
                                            </span>
                                            <span class="flex items-center gap-1.5 ml-auto text-blue-600 dark:text-blue-400 opacity-0 group-hover:opacity-100 transition-opacity">
                                                Ouvir agora <x-icon name="arrow-right" class="w-3.5 h-3.5" />
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($series->studies->count() > 0)
                <div>
                    <h2 class="text-2xl font-black text-gray-900 dark:text-white mb-6 flex items-center gap-3">
                         <div class="w-10 h-10 rounded-xl bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center text-purple-600 dark:text-purple-400 shadow-sm">
                            <x-icon name="book-open" class="w-5 h-5" />
                        </div>
                        Estudos Relacionados
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($series->studies as $study)
                            <a href="{{ route('memberpanel.studies.show', $study) }}"
                               class="group bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-lg hover:border-purple-500/30 dark:hover:border-purple-500/30 transition-all duration-300 hover:-translate-y-1 flex flex-col h-full">
                                <div class="mb-4">
                                     <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-purple-50 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300 uppercase tracking-wide">
                                        Estudo
                                    </span>
                                </div>

                                <h3 class="text-lg font-bold text-gray-900 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors mb-2 line-clamp-2">
                                    {{ $study->title }}
                                </h3>

                                <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2 mb-4 leading-relaxed flex-1">
                                    {{ $study->subtitle ?? Str::limit(strip_tags($study->content), 80) }}
                                </p>

                                <div class="mt-auto flex items-center justify-between pt-4 border-t border-gray-50 dark:border-gray-700/50">
                                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">{{ $study->published_at?->format('d/m') }}</span>
                                    <span class="text-purple-600 dark:text-purple-400 text-xs font-bold uppercase tracking-wider flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                                        Ler <x-icon name="arrow-right" class="w-3 h-3" />
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-3xl p-8 shadow-sm border border-gray-100 dark:border-gray-700 sticky top-6">
                <h3 class="text-lg font-black text-gray-900 dark:text-white mb-6 uppercase tracking-wide flex items-center gap-2">
                    <x-icon name="information-circle" class="w-5 h-5 text-gray-400" />
                    Sobre a Série
                </h3>
                <div class="space-y-5">
                    <div class="flex items-start gap-4 p-4 bg-gray-50 dark:bg-gray-700/30 rounded-2xl">
                         <div class="w-10 h-10 rounded-full bg-white dark:bg-gray-600 flex items-center justify-center text-gray-400 shadow-sm flex-shrink-0">
                            <x-icon name="users" class="w-5 h-5" />
                        </div>
                        <div>
                             <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Participação</p>
                            <p class="font-bold text-gray-900 dark:text-white text-sm">Diversos Autores</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 p-4 bg-gray-50 dark:bg-gray-700/30 rounded-2xl">
                        <div class="w-10 h-10 rounded-full bg-white dark:bg-gray-600 flex items-center justify-center text-gray-400 shadow-sm flex-shrink-0">
                             <x-icon name="calendar" class="w-5 h-5" />
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Início</p>
                            <p class="font-bold text-gray-900 dark:text-white text-sm">{{ $series->created_at->format('F \d\e Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

