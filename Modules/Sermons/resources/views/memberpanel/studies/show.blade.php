@extends('memberpanel::components.layouts.master')

@section('title', $study->title)

@push('styles')
    @vite(['Modules/Sermons/resources/assets/sass/app.scss'])
    <style>
        .study-content {
            font-family: 'Merriweather', 'Georgia', serif;
            line-height: 1.9;
            font-size: 1.125rem;
        }
        .study-content h2 {
            font-family: 'Inter', sans-serif;
            margin-top: 2.5em;
            margin-bottom: 1em;
            font-weight: 800;
            letter-spacing: -0.025em;
        }
        .study-content h3 {
             font-family: 'Inter', sans-serif;
            margin-top: 2em;
            margin-bottom: 0.75em;
            font-weight: 700;
             letter-spacing: -0.025em;
        }
        .study-content p { margin-bottom: 1.5em; }
        .study-content ul { list-style-type: disc; padding-left: 1.5em; margin-bottom: 1.5em; }
        .study-content blockquote {
            border-left: 4px solid #10B981;
            padding-left: 1.5rem;
            font-style: italic;
            color: #4B5563;
            margin: 2em 0;
            background: #F0FDF4;
            padding: 1.5rem;
            border-radius: 0 0.5rem 0.5rem 0;
        }
        .dark .study-content blockquote {
            background: rgba(16, 185, 129, 0.1);
            color: #D1D5DB;
        }
    </style>
@endpush

@section('content')
<div class="max-w-4xl mx-auto space-y-8 pb-12">
    <!-- Breadcrumb -->
    <nav class="flex text-sm font-medium" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2">
            <li>
                <a href="{{ route('memberpanel.studies.index') }}" class="text-gray-500 hover:text-emerald-600 dark:text-gray-400 dark:hover:text-emerald-400 transition-colors">Estudos</a>
            </li>
            <li class="text-gray-300 dark:text-gray-600">/</li>
            @if($study->series)
                <li>
                    <a href="{{ route('memberpanel.series.show', $study->series) }}" class="text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300">
                        {{ $study->series->title }}
                    </a>
                </li>
                <li class="text-gray-300 dark:text-gray-600">/</li>
            @endif
             <li class="text-gray-900 dark:text-gray-200 truncate max-w-xs" aria-current="page">{{ $study->title }}</li>
        </ol>
    </nav>

    <article class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl shadow-gray-200/50 dark:shadow-none border border-gray-100 dark:border-gray-700 overflow-hidden">
        <!-- Header -->
        <header class="border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 relative overflow-hidden">
             <!-- Cover Image Hero -->
            @if($study->cover_image)
                <div class="h-64 md:h-80 w-full relative">
                    <img src="{{ asset('storage/' . $study->cover_image) }}" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-linear-to-t from-gray-50 dark:from-gray-900 via-transparent to-transparent"></div>
                </div>
            @endif

            <div class="p-8 md:p-16 relative z-10 {{ $study->cover_image ? '-mt-24' : '' }}">

            <div class="relative z-10">
                <div class="flex flex-wrap items-center gap-3 mb-6">
                    @if($study->category)
                        <span class="inline-flex px-3 py-1 rounded-lg text-xs font-black uppercase tracking-widest"
                            style="background-color: {{ $study->category->color ?? '#10B981' }}20; color: {{ $study->category->color ?? '#10B981' }}">
                            {{ $study->category->name }}
                        </span>
                    @endif
                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-black uppercase tracking-widest bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                         {{ ceil(str_word_count(strip_tags($study->content)) / 200) }} min de leitura
                    </span>
                </div>

                <h1 class="text-4xl md:text-6xl font-black text-gray-900 dark:text-white mb-6 leading-tight tracking-tight">
                    {{ $study->title }}
                </h1>

                @if($study->subtitle)
                    <p class="text-xl md:text-2xl text-gray-500 dark:text-gray-400 font-medium leading-relaxed max-w-3xl">
                        {{ $study->subtitle }}
                    </p>
                @endif

                <div class="flex flex-wrap items-center gap-8 mt-8 pt-8 border-t border-gray-200 dark:border-gray-700/50">
                    <div class="flex items-center gap-3">
                        <img src="{{ $study->user->avatar_url }}" alt="{{ $study->user->name }}" class="w-12 h-12 rounded-full object-cover shadow-sm">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Autor</p>
                            <p class="font-bold text-gray-900 dark:text-white">{{ $study->user->name }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                         <div class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-400">
                            <x-icon name="calendar" class="w-5 h-5" />
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Publicado</p>
                            <p class="font-bold text-gray-900 dark:text-white">{{ $study->published_at?->format('d M, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Media Section -->
        @if($study->video_url || $study->audio_file || $study->audio_url)
        <div class="bg-gray-900 border-b border-gray-800">
             @if($study->video_url)
            <div class="aspect-w-16 aspect-h-9 mx-auto max-w-5xl">
                @if(str_contains($study->video_url, 'youtube.com') || str_contains($study->video_url, 'youtu.be'))
                    @php
                        $videoId = '';
                        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $study->video_url, $matches)) {
                            $videoId = $matches[1];
                        }
                    @endphp
                    <iframe src="https://www.youtube.com/embed/{{ $videoId }}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen class="w-full h-full"></iframe>
                @else
                    <div class="flex items-center justify-center h-full text-white bg-gray-800">
                        <a href="{{ $study->video_url }}" target="_blank" class="flex flex-col items-center gap-4 hover:text-emerald-400 transition-colors group p-8 text-center">
                            <div class="w-16 h-16 rounded-full bg-white/10 flex items-center justify-center group-hover:scale-110 transition-transform">
                                 <x-icon name="play" class="w-8 h-8" />
                            </div>
                            <span class="font-bold text-lg">Assistir Vídeo Externo</span>
                        </a>
                    </div>
                @endif
            </div>
            @endif

            @if($study->audio_file || $study->audio_url)
                <div class="p-6 bg-gray-800/50 backdrop-blur-sm border-t border-gray-700">
                    <div class="flex items-center gap-4 max-w-3xl mx-auto">
                        <div class="w-12 h-12 bg-emerald-500 rounded-full flex items-center justify-center flex-shrink-0 text-white shadow-lg shadow-emerald-500/20 animate-pulse">
                            <x-icon name="volume-up" class="w-6 h-6" />
                        </div>
                        <div class="flex-1">
                             <p class="text-xs font-bold text-emerald-400 uppercase tracking-wider mb-2">Versão em Áudio</p>
                            <audio controls class="w-full h-10 rounded-lg" controlsList="nodownload">
                                <source src="{{ $study->audio_source }}" type="audio/mpeg">
                                <source src="{{ $study->audio_source }}" type="audio/mp4">
                                Seu navegador não suporta o elemento de áudio.
                            </audio>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        @endif

        <!-- Content Body -->
        <div class="p-8 md:p-16 study-content text-gray-800 dark:text-gray-200">
            {!! nl2br(e($study->content)) !!}
        </div>

        <!-- Footer -->
        <div class="px-8 md:px-16 pb-12 pt-8 border-t border-gray-100 dark:border-gray-700 flex flex-col md:flex-row justify-between items-center gap-6 bg-gray-50/30 dark:bg-gray-800">
            <a href="{{ route('memberpanel.studies.index') }}"
               class="inline-flex items-center px-6 py-3 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl font-bold text-gray-700 dark:text-white shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all">
                <x-icon name="arrow-left" class="w-4 h-4 mr-2" />
                Voltar aos Estudos
            </a>

             @if($study->series)
                <a href="{{ route('memberpanel.series.show', $study->series) }}"
                   class="inline-flex items-center px-6 py-3 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800 rounded-xl font-bold text-emerald-700 dark:text-emerald-300 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all">
                    Mais da série "{{ Str::limit($study->series->title, 20) }}"
                    <x-icon name="arrow-right" class="w-4 h-4 ml-2" />
                </a>
            @endif
        </div>
    </article>
</div>
@endsection

