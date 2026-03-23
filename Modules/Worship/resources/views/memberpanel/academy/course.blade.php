@extends('memberpanel::components.layouts.master')

@section('page-title', $course->title)

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
    <!-- Breadcrumbs -->
    <nav class="flex mb-8 text-xs font-bold uppercase tracking-widest text-gray-400">
        <a href="{{ route('worship.member.academy.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Academy</a>
        <span class="mx-2">/</span>
        <span class="text-gray-900 dark:text-white truncate">{{ $course->title }}</span>
    </nav>

    <!-- Header -->
    <div class="flex flex-col md:flex-row items-center md:items-start gap-8 mb-16 bg-white dark:bg-gray-950 p-6 md:p-10 rounded-[2.5rem] border border-gray-200 dark:border-white/5 shadow-sm transition-all duration-300 relative overflow-hidden">
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 bg-blue-600/5 dark:bg-blue-600/10 rounded-full blur-[100px]"></div>

        <div class="w-full md:w-1/3 aspect-video bg-gray-100 dark:bg-gray-900 rounded-4xl overflow-hidden border border-gray-200 dark:border-white/10 shadow-inner group relative z-10">
             @if($course->cover_image)
                <img src="{{ $course->cover_image }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110 opacity-90 group-hover:opacity-100">
            @else
                <div class="flex items-center justify-center h-full text-gray-300 dark:text-gray-700">
                    <x-icon name="music" class="w-16 h-16" />
                </div>
            @endif
        </div>
        <div class="flex-1 text-center md:text-left relative z-10">
            <div class="flex flex-wrap items-center justify-center md:justify-start gap-3 mb-6">
                <!-- Instrument Badge -->
                <div class="flex items-center gap-2 px-4 py-1.5 rounded-full bg-gray-50 dark:bg-white/5 border border-gray-100 dark:border-white/10">
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                    <span class="text-[10px] font-black uppercase tracking-widest text-gray-500 dark:text-gray-300">{{ $course->instrument->name ?? 'Geral' }}</span>
                </div>

                <!-- Level Badge -->
                <span class="px-4 py-1.5 bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20 rounded-full text-[10px] font-black uppercase tracking-widest">
                    {{ $course->level }}
                </span>

                <span class="text-gray-300 dark:text-gray-700 text-[10px]">|</span>
                <span class="text-gray-400 dark:text-gray-500 text-[10px] font-bold uppercase tracking-widest">{{ $course->lessons->count() }} Aulas</span>
            </div>

            <h1 class="text-3xl md:text-5xl font-black text-gray-900 dark:text-white mb-6 leading-tight tracking-tight">{{ $course->title }}</h1>
            <p class="text-lg text-gray-600 dark:text-gray-400 leading-relaxed max-w-2xl">{{ $course->description }}</p>
        </div>
    </div>

    <!-- Timeline / Lesson List -->
    <div class="max-w-4xl mx-auto">
        <h2 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tighter mb-10 flex items-center gap-4">
            <span class="w-10 h-10 rounded-2xl bg-gray-900 dark:bg-white flex items-center justify-center text-white dark:text-gray-900 shadow-lg shadow-gray-900/10 dark:shadow-white/5">
                <x-icon name="layer-group" class="w-5 h-5" />
            </span>
            Conteúdo do Curso
        </h2>

        <div class="relative border-l-2 border-gray-100 dark:border-white/5 ml-5 md:ml-5 space-y-4 pb-20">
            @foreach($course->lessons as $lesson)
            @php
                $isCompleted = $lesson->progress->isNotEmpty();
            @endphp
            <div class="relative pl-10 md:pl-12 group">
                <!-- Node -->
                <div class="absolute -left-[9px] top-7 w-5 h-5 rounded-full border-4 {{ $isCompleted ? 'bg-blue-600 border-white dark:border-gray-900 shadow-[0_0_15px_rgba(37,99,235,0.5)]' : 'bg-gray-100 dark:bg-gray-800 border-white dark:border-gray-900' }} transition-all group-hover:scale-110 z-10"></div>

                <a href="{{ route('worship.member.academy.classroom', [$course, $lesson]) }}"
                   class="block bg-white dark:bg-gray-900 border border-gray-100 dark:border-white/5 p-6 rounded-3xl transition-all duration-300 hover:border-blue-500/30 hover:shadow-xl hover:shadow-blue-500/5 hover:-translate-x-[-4px]">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-3 mb-2">
                                <span class="text-[10px] font-black uppercase tracking-widest {{ $isCompleted ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400' }}">Lição {{ str_pad($lesson->order, 2, '0', STR_PAD_LEFT) }}</span>
                                @if($isCompleted)
                                    <span class="px-2 py-0.5 rounded bg-green-100 dark:bg-green-500/10 text-green-600 dark:text-green-400 text-[9px] font-bold uppercase tracking-wider">Concluído</span>
                                @endif
                            </div>
                            <h3 class="text-xl font-bold {{ $isCompleted ? 'text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400' }} truncate group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">{{ $lesson->title }}</h3>
                        </div>
                        <div class="shrink-0">
                            @if($isCompleted)
                                <div class="w-12 h-12 rounded-2xl bg-blue-500/10 flex items-center justify-center border border-blue-500/20 text-blue-600 dark:text-blue-400">
                                    <x-icon name="check" class="w-5 h-5" />
                                </div>
                            @else
                                <div class="w-12 h-12 rounded-2xl bg-gray-50 dark:bg-gray-800 flex items-center justify-center border border-gray-200 dark:border-white/5 text-gray-400 group-hover:text-blue-500 group-hover:border-blue-500/30 group-hover:bg-blue-500/5 transition-all">
                                    <x-icon name="play" class="w-4 h-4 ml-0.5" />
                                </div>
                            @endif
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

