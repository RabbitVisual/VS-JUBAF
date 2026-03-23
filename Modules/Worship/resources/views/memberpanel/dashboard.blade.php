@extends('memberpanel::components.layouts.master')

@section('page-title', 'Portal do Adorador')

@section('content')
<div class="max-w-7xl mx-auto space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
    <!-- Hero Section -->
    <div class="relative overflow-hidden rounded-[2.5rem] bg-gray-900 border border-white/10 p-8 sm:p-12 shadow-2xl">
        <div class="absolute inset-0 bg-gradient-to-br from-blue-600/30 to-purple-600/30 mix-blend-multiply flex items-center justify-center">
            <div class="w-[40rem] h-[40rem] bg-blue-500/20 rounded-full blur-3xl opacity-50 mix-blend-screen -ml-[20rem] -mt-[10rem]"></div>
            <div class="w-[30rem] h-[30rem] bg-purple-500/20 rounded-full blur-3xl opacity-50 mix-blend-screen ml-[20rem] mt-[10rem]"></div>
        </div>

        <div class="relative z-10 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <nav class="flex items-center gap-2 text-[10px] font-black text-blue-400 uppercase tracking-[0.2em] mb-4">
                    <span>Ministério de Louvor</span>
                </nav>
                <h1 class="text-4xl sm:text-6xl font-black text-white tracking-tight leading-[1.1] mb-4">
                    Portal do <br><span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-purple-400">Adorador</span>
                </h1>
                <p class="text-gray-300 text-lg max-w-md leading-relaxed mb-8">
                    Seu espaço central para visualizar escalas, ensaiar repertórios e aprimorar seu dom na Academia de Louvor.
                </p>

                <div class="flex flex-wrap items-center gap-4">
                    <a href="{{ route('worship.member.rosters.index') }}" class="px-6 py-3 rounded-2xl bg-white text-gray-900 font-bold hover:bg-gray-100 transition-all shadow-lg flex items-center gap-2">
                        <x-icon name="calendar-days" class="w-5 h-5 text-blue-600" /> Minhas Escalas
                    </a>
                    <a href="{{ route('worship.member.academy.index') }}" class="px-6 py-3 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 text-white font-bold hover:bg-white/20 transition-all flex items-center gap-2">
                        <x-icon name="graduation-cap" class="w-5 h-5 text-purple-400" /> Academia EAD
                    </a>
                </div>
            </div>

            <!-- Próxima Escala Highlight -->
            @if($nextRoster)
                <div class="bg-black/40 backdrop-blur-xl rounded-3xl p-8 border border-white/10 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-blue-500/10 rounded-full blur-[50px] group-hover:bg-blue-500/20 transition-all duration-700"></div>
                    <div class="relative">
                        <div class="flex items-center justify-between mb-6">
                            <span class="px-3 py-1 rounded-full bg-blue-500/20 text-blue-400 text-[10px] font-black uppercase tracking-widest border border-blue-500/30 flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-400 animate-pulse"></span>
                                Próxima Ministração
                            </span>
                            @if($nextRoster->status === 'pending')
                                <span class="text-xs font-bold text-yellow-400">Escala Pendente!</span>
                            @endif
                        </div>
                        <h3 class="text-3xl font-black text-white text-balance mb-2 leading-tight">{{ $nextRoster->setlist->title }}</h3>
                        <p class="text-gray-400 text-sm mb-6 flex items-center gap-2">
                            <x-icon name="clock" class="w-4 h-4" />
                            {{ $nextRoster->setlist->scheduled_at->translatedFormat('d \d\e F \a\s H:i') }}
                        </p>

                        <div class="flex items-center gap-4 p-4 rounded-2xl bg-white/5 border border-white/10">
                            <div class="w-12 h-12 rounded-xl bg-purple-500/20 flex items-center justify-center text-purple-400">
                                <x-icon name="music" class="w-6 h-6" />
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">Seu Papel</p>
                                <p class="text-white font-bold">{{ $nextRoster->instrument->name }}</p>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <a href="{{ route('worship.member.rosters.index') }}" class="text-sm font-bold text-white hover:text-blue-400 transition-colors flex items-center gap-2">
                                Ver detalhes da escala <x-icon name="arrow-right" class="w-4 h-4" />
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-black/20 backdrop-blur-md rounded-3xl p-8 border border-white/5 border-dashed flex flex-col items-center justify-center text-center h-full min-h-[300px]">
                    <div class="w-16 h-16 rounded-full bg-white/5 flex items-center justify-center text-gray-500 mb-4">
                        <x-icon name="calendar-check" class="w-8 h-8" />
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">Sem ministrações próximas</h3>
                    <p class="text-gray-400 text-sm max-w-xs">Você não está escalado para os próximos dias. Aproveite para ensaiar ou avançar nos módulos da Academia.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Academia de Louvor -->
    <div>
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-black text-gray-900 dark:text-white flex items-center gap-3">
                <x-icon name="graduation-cap" class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                Seu Progresso EAD
            </h2>
            <a href="{{ route('worship.member.academy.index') }}" class="text-sm font-bold text-blue-600 dark:text-blue-400 hover:underline">Ver todos os cursos</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($recentProgress as $progress)
                @php
                    $course = $progress->lesson->module->course;
                    $lesson = $progress->lesson;
                @endphp
                <a href="{{ route('worship.member.academy.classroom', $course->id) }}" class="group block bg-white dark:bg-gray-900 rounded-3xl overflow-hidden border border-gray-200 dark:border-gray-800 shadow-sm hover:shadow-xl hover:border-blue-500/30 transition-all duration-300">
                    <div class="aspect-video w-full relative overflow-hidden bg-gray-100 dark:bg-gray-800">
                        @if($course->cover_image)
                            <img src="{{ $course->cover_image }}" alt="{{ $course->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-300 dark:text-gray-700">
                                <x-icon name="play-circle" class="w-16 h-16 opacity-50" />
                            </div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-gray-900/90 via-gray-900/40 to-transparent"></div>
                        <div class="absolute bottom-4 left-4 right-4">
                            <span class="px-2 py-0.5 rounded-md bg-blue-600/80 backdrop-blur text-white text-[10px] font-black uppercase tracking-widest mb-2 inline-block">Continuar</span>
                            <h4 class="text-white font-bold text-lg leading-tight line-clamp-2">{{ $course->title }}</h4>
                        </div>
                    </div>
                    <div class="p-6">
                        <p class="text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-1">Última Aula Concluída</p>
                        <p class="text-sm text-gray-900 dark:text-white font-medium truncate mb-4">{{ $lesson->title }}</p>

                        <div class="w-full bg-gray-100 dark:bg-gray-800 rounded-full h-1.5 mb-2 overflow-hidden">
                            <div class="bg-gradient-to-r from-blue-500 to-purple-500 h-1.5 rounded-full w-full" style="width: 100%"></div>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full bg-white dark:bg-gray-900 rounded-3xl p-8 border border-gray-200 dark:border-gray-800 flex flex-col items-center justify-center text-center">
                    <div class="w-16 h-16 rounded-2xl bg-gray-50 dark:bg-gray-800 text-gray-400 dark:text-gray-500 flex items-center justify-center mb-4">
                        <x-icon name="book-open" class="w-8 h-8" />
                    </div>
                    <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Nenhum curso iniciado</h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400 max-w-sm mx-auto mb-6">Acesse a Academia EAD para começar a aprender teoria, prática instrumental e liderança.</p>
                    <a href="{{ route('worship.member.academy.index') }}" class="px-5 py-2.5 rounded-xl bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-bold text-sm hover:scale-[1.02] transition-transform">Explorar Catálogo</a>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
