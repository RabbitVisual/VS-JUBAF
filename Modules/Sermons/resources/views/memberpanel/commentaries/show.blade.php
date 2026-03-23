@extends('memberpanel::components.layouts.master')

@section('title', $commentary->title ?? $commentary->reference)

@push('styles')
    @vite(['Modules/Sermons/resources/assets/sass/app.scss'])
    <style>
        .commentary-content {
            font-family: 'Merriweather', 'Georgia', serif;
            line-height: 1.9;
            font-size: 1.125rem;
        }
        .commentary-content h2 { margin-top: 2em; margin-bottom: 1em; font-weight: 700; font-family: 'Inter', sans-serif; letter-spacing: -0.025em; }
        .commentary-content h3 { margin-top: 1.5em; margin-bottom: 0.75em; font-weight: 600; font-family: 'Inter', sans-serif; letter-spacing: -0.025em; }
        .commentary-content p { margin-bottom: 1.5em; }
        .commentary-content blockquote {
            border-left: 4px solid #06B6D4;
            padding-left: 1.5rem;
            font-style: italic;
            color: #4B5563;
            margin: 2em 0;
            background: #ECFEFF;
            padding: 1.5rem;
            border-radius: 0 0.5rem 0.5rem 0;
        }
        .dark .commentary-content blockquote {
            background: rgba(6, 182, 212, 0.1);
            color: #D1D5DB;
        }
    </style>
@endpush

@section('content')
<div class="max-w-4xl mx-auto space-y-8 pb-12">
    <!-- Breadcrumb -->
    <nav class="flex text-sm font-medium" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2">
            <li><a href="{{ route('memberpanel.commentaries.index') }}" class="text-gray-500 hover:text-cyan-600 dark:text-gray-400 dark:hover:text-cyan-400 transition-colors">Comentários</a></li>
            <li class="text-gray-300 dark:text-gray-600">/</li>
            <li class="text-gray-900 dark:text-gray-200" aria-current="page">{{ $commentary->reference }}</li>
        </ol>
    </nav>

    <article class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl shadow-gray-200/50 dark:shadow-none border border-gray-100 dark:border-gray-700 overflow-hidden">
        <!-- Header -->
        <header class="border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 relative overflow-hidden">
             <!-- Cover Image Hero -->
            @if($commentary->cover_image)
                <div class="h-64 md:h-80 w-full relative">
                    <img src="{{ asset('storage/' . $commentary->cover_image) }}" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-linear-to-t from-gray-50 dark:from-gray-900 via-transparent to-transparent"></div>
                </div>
            @endif

            <div class="p-8 md:p-12 relative z-10 {{ $commentary->cover_image ? '-mt-16' : '' }}">
                <div class="inline-block px-4 py-1.5 mb-6 text-sm font-black rounded-lg bg-cyan-100 text-cyan-800 dark:bg-cyan-900/30 dark:text-cyan-300 uppercase tracking-widest">
                    {{ $commentary->reference }}
                </div>

            @if($commentary->title)
                <h1 class="text-3xl md:text-5xl font-black text-gray-900 dark:text-white mb-6 leading-tight tracking-tight">
                    {{ $commentary->title }}
                </h1>
            @else
                <h1 class="text-3xl md:text-5xl font-black text-gray-900 dark:text-white mb-6 leading-tight tracking-tight">
                    Comentário Bíblico
                </h1>
            @endif

            <div class="flex flex-wrap items-center gap-8 mt-8 pt-8 border-t border-gray-200 dark:border-gray-700/50">
                <div class="flex items-center gap-3">
                    <img src="{{ $commentary->user->avatar_url }}" alt="{{ $commentary->user->name }}" class="w-12 h-12 rounded-full object-cover shadow-sm">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Autor</p>
                        <p class="font-bold text-gray-900 dark:text-white">{{ $commentary->user->name }}</p>
                    </div>
                </div>

                @if($commentary->is_official)
                    <div class="flex items-center gap-2 px-4 py-2 bg-green-50 dark:bg-green-900/20 rounded-xl border border-green-100 dark:border-green-900/30">
                        <x-icon name="check-circle" class="w-5 h-5 text-green-600 dark:text-green-400" />
                        <span class="text-sm font-bold text-green-700 dark:text-green-300 uppercase tracking-wide">Conteúdo Oficial</span>
                    </div>
                @endif
            </div>
        </header>

        <!-- Audio Player -->
        @if($commentary->audio_path || $commentary->audio_url)
            <div class="p-6 bg-cyan-50 dark:bg-cyan-900/10 border-b border-cyan-100 dark:border-cyan-900/20">
                <div class="flex items-center gap-4 max-w-3xl mx-auto">
                    <div class="w-12 h-12 bg-cyan-500 rounded-full flex items-center justify-center flex-shrink-0 text-white shadow-lg shadow-cyan-500/20 animate-pulse">
                        <x-icon name="volume-up" class="w-6 h-6" />
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-bold text-cyan-700 dark:text-cyan-300 uppercase tracking-wider mb-2">Versão em Áudio</p>
                        <audio controls class="w-full h-10 rounded-lg" controlsList="nodownload">
                            <source src="{{ $commentary->audio_source }}" type="audio/mpeg">
                            <source src="{{ $commentary->audio_source }}" type="audio/mp4">
                            Seu navegador não suporta o elemento de áudio.
                        </audio>
                        @if($commentary->audio_path)
                            <p class="mt-2 text-[10px] font-bold text-gray-400 uppercase tracking-wider flex items-center gap-1">
                                <x-icon name="server" class="w-3 h-3" /> Hospedagem Local
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Content Body -->
        <div class="p-8 md:p-16 commentary-content text-gray-800 dark:text-gray-200">
            {!! nl2br(e($commentary->content)) !!}
        </div>

        <!-- Footer -->
        <div class="px-8 md:px-16 pb-12 pt-8 border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/30">
            <a href="{{ route('memberpanel.commentaries.index') }}"
               class="inline-flex items-center px-6 py-3 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl font-bold text-gray-700 dark:text-white shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all">
                <x-icon name="arrow-left" class="w-4 h-4 mr-2" />
                Voltar aos Comentários
            </a>
        </div>
    </article>
</div>
@endsection

