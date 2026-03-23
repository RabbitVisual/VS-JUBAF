@extends('memberpanel::components.layouts.master')

@section('title', 'EBD Academy - Portal EAD')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-slate-950 transition-colors duration-200 pb-12">
    <div class="max-w-7xl mx-auto space-y-8 px-6 pt-8">

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Escola Bíblica Dominical</h1>
                <p class="text-gray-500 dark:text-slate-400 mt-1 max-w-md">Sua jornada de aprendizado e crescimento espiritual.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('memberpanel.ebd.arcade.index') }}" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl shadow-sm flex items-center gap-2 font-bold text-sm transition-all">
                    <x-icon name="gamepad-modern" class="w-4 h-4" />
                    Arcade Bíblico
                </a>
                <a href="{{ route('memberpanel.ebd.student.lessons') }}" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl shadow-sm flex items-center gap-2 font-bold text-sm transition-all">
                    <x-icon name="book-open-reader" class="w-4 h-4" />
                    Minhas Turmas
                </a>
            </div>
        </div>

        <!-- Hero XP Section (EAD Academy) -->
        <div class="relative overflow-hidden bg-white dark:bg-slate-900 rounded-3xl shadow-xl dark:shadow-2xl border border-gray-100 dark:border-slate-800 transition-colors duration-200" data-tour="ebd-student-hero">
            <div class="absolute inset-0 opacity-20 dark:opacity-40 pointer-events-none">
                <div class="absolute -top-24 -left-20 w-96 h-96 bg-purple-400 dark:bg-purple-600 rounded-full blur-[100px]"></div>
                <div class="absolute top-1/2 -right-20 w-80 h-80 bg-amber-400 dark:bg-orange-600 rounded-full blur-[100px]"></div>
            </div>

            <div class="relative px-6 sm:px-8 py-8 sm:py-10 flex flex-col md:flex-row md:items-start items-center gap-8 md:gap-10 z-10">
                <!-- User Avatar & Core Level (wrapper com espaço para o badge) -->
                <div class="flex flex-col items-center shrink-0 pt-0 pb-6 md:pb-0 order-1">
                    <div class="relative group">
                        <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-full p-[3px] bg-gradient-to-br from-amber-500 via-orange-500 to-amber-600 shadow-xl shadow-amber-500/20">
                            <div class="w-full h-full rounded-full overflow-hidden border-4 border-white dark:border-slate-900 bg-gray-100 dark:bg-slate-800">
                                <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500">
                            </div>
                        </div>
                        @php
                            $tierClass = match($levelTier ?? 'bronze') {
                                'silver' => 'bg-slate-400 dark:bg-slate-500 text-white',
                                'gold' => 'bg-amber-500 dark:bg-amber-500 text-white',
                                'platinum' => 'bg-slate-200 dark:bg-slate-300 text-slate-900',
                                default => 'bg-amber-600 dark:bg-amber-600 text-white',
                            };
                        @endphp
                        <div class="absolute top-full left-1/2 -translate-x-1/2 mt-2 px-3 py-1.5 {{ $tierClass }} text-[10px] font-black uppercase tracking-wider rounded-full border-2 border-white dark:border-slate-900 shadow-lg whitespace-nowrap">
                            {{ $stats['level_name'] }}
                        </div>
                    </div>
                </div>

                <!-- XP & Welcome Info -->
                <div class="flex-1 min-w-0 text-center md:text-left space-y-4 order-2">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-50 dark:bg-purple-900/30 border border-purple-100 dark:border-purple-800">
                        <x-icon name="graduation-cap" class="w-3 h-3 text-purple-600 dark:text-purple-400" />
                        <span class="text-[10px] font-black uppercase tracking-widest text-purple-600 dark:text-purple-400">Aluno Ativo - Nível {{ $stats['level'] }}</span>
                    </div>
                    <h2 class="text-2xl md:text-3xl font-black text-gray-900 dark:text-white tracking-tight">
                        A Paz, <span class="text-amber-600 dark:text-amber-400">{{ explode(' ', auth()->user()->name)[0] }}</span>!
                    </h2>
                    <p class="text-gray-500 dark:text-slate-300 font-medium max-w-xl text-sm leading-relaxed">
                        A Palavra de Deus é viva e eficaz. Acompanhe suas lições, responda aos quizes e ganhe XP no Arcade.
                    </p>

                    <!-- XP Details Bar -->
                    @php
                        $progressRatio = min(100, max(0, $stats['progress_to_next'] ?? 0));
                    @endphp
                    <div class="max-w-lg">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-bold text-gray-900 dark:text-white">{{ number_format($stats['xp'] ?? 0) }} XP Total</span>
                            <span class="text-xs font-bold text-amber-600 dark:text-amber-400">{{ $progressRatio }}% pro Próximo Nível</span>
                        </div>
                        <div class="relative h-3 w-full bg-gray-100 dark:bg-slate-800 rounded-full overflow-hidden">
                            <div class="absolute h-full bg-gradient-to-r from-amber-500 to-orange-500 rounded-full transition-all duration-1000 shadow-[0_0_10px_rgba(245,158,11,0.5)]"
                                 style="width: {{ $progressRatio }}%"></div>
                        </div>
                        <p class="text-xs text-gray-400 dark:text-slate-500 mt-2 flex justify-between">
                            <span>Sua evolução reflete seu envolvimento na EBD.</span>
                        </p>
                    </div>

                    @if(isset($courseProgressName) && $courseProgressName)
                    <div class="max-w-lg">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-bold text-gray-900 dark:text-white">Progresso do curso</span>
                            <span class="text-xs font-bold text-blue-600 dark:text-blue-400">{{ $courseProgressPercent ?? 0 }}% concluído</span>
                        </div>
                        <div class="relative h-2.5 w-full bg-gray-100 dark:bg-slate-800 rounded-full overflow-hidden">
                            <div class="absolute h-full bg-blue-500 dark:bg-blue-600 rounded-full transition-all duration-500" style="width: {{ $courseProgressPercent ?? 0 }}%"></div>
                        </div>
                        <p class="text-xs text-gray-400 dark:text-slate-500 mt-1">{{ $courseProgressName }}</p>
                    </div>
                    @endif
                </div>

                <!-- Quick Overall Stats Card -->
                <div class="shrink-0 grid grid-cols-2 gap-4 order-3 w-full md:w-auto">
                    <div class="bg-gray-50 dark:bg-slate-800 rounded-2xl p-5 text-center border border-gray-100 dark:border-slate-700">
                        <x-icon name="book-bible" style="duotone" class="w-6 h-6 text-purple-500 mx-auto mb-2" />
                        <span class="block text-2xl font-black text-gray-900 dark:text-white">{{ $stats['total_classes'] }}</span>
                        <span class="block text-[9px] font-bold text-gray-400 dark:text-slate-500 uppercase tracking-wider">Turmas</span>
                    </div>
                    <div class="bg-gray-50 dark:bg-slate-800 rounded-2xl p-5 text-center border border-gray-100 dark:border-slate-700">
                        <x-icon name="medal" style="duotone" class="w-6 h-6 text-amber-500 mx-auto mb-2" />
                        <span class="block text-2xl font-black text-gray-900 dark:text-white">{{ count($userAchievementIds ?? []) }}</span>
                        <span class="block text-[9px] font-bold text-gray-400 dark:text-slate-500 uppercase tracking-wider">Badges</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Workspace Grid Layout -->
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">

            <!-- Left Column: Classes, LMS & Lessons -->
            <div class="xl:col-span-2 space-y-8">

                <!-- Continue Learning (LMS Banner) -->
                @if(isset($lastLesson) && $lastLesson)
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden border-l-4 border-l-purple-500">
                    <div class="px-6 py-5 border-b border-gray-100 dark:border-slate-800 flex items-center justify-between bg-gray-50/50 dark:bg-slate-900/50">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 rounded-xl">
                                <x-icon name="presentation-screen" style="duotone" class="w-5 h-5" />
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 dark:text-white">Player EAD</h3>
                                <p class="text-xs text-gray-500 dark:text-slate-400">Continue a assistir de onde parou</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center gap-6">
                            <div class="relative w-24 h-16 bg-gray-200 dark:bg-slate-800 rounded-xl overflow-hidden flex items-center justify-center shadow-inner shrink-0">
                                @if($lastLesson->media->first()?->thumbnail_url)
                                    <img src="{{ $lastLesson->media->first()->thumbnail_url }}" class="w-full h-full object-cover opacity-60">
                                @else
                                    <div class="absolute inset-0 bg-gradient-to-br from-purple-100 to-indigo-100 dark:from-purple-900 dark:to-indigo-900 opacity-30 w-full h-full"></div>
                                @endif
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div class="w-8 h-8 rounded-full bg-purple-600/90 text-white flex items-center justify-center shadow shadow-black/30">
                                        <x-icon name="play" class="w-3 h-3 ml-1" />
                                    </div>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <span class="text-[10px] font-bold text-purple-600 dark:text-purple-400 uppercase tracking-wider">{{ $lastLesson->ebdClass->name ?? 'Aula EBD' }}</span>
                                <h4 class="font-bold text-gray-900 dark:text-white truncate mt-1">{{ $lastLesson->title }}</h4>
                                <p class="text-sm text-gray-500 dark:text-slate-400 line-clamp-1 mt-0.5">{{ Str::limit($lastLesson->description, 60) }}</p>
                            </div>
                            <a href="{{ route('memberpanel.ebd.student.classroom.player', $lastLesson) }}" class="shrink-0 px-5 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-xl font-bold text-sm transition-all shadow-lg shadow-purple-600/20 active:scale-[0.98] flex items-center gap-2">
                                Assistir Aula
                            </a>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Upcoming Lessons (Traditional) -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden">
                    <div class="px-8 py-6 border-b border-gray-100 dark:border-slate-800 flex items-center justify-between bg-gray-50/50 dark:bg-slate-900/50">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-xl">
                                <x-icon name="calendar-days" style="duotone" class="w-5 h-5" />
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 dark:text-white">Próximos Encontros</h3>
                                <p class="text-xs text-gray-500 dark:text-slate-400">Suas turmas e cultos agendados</p>
                            </div>
                        </div>
                        <a href="{{ route('memberpanel.ebd.student.lessons') }}" class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline">Revistas Completas</a>
                    </div>
                    <div class="p-6 space-y-4">
                        @forelse($upcomingLessons ?? [] as $lesson)
                            <a href="{{ route('memberpanel.ebd.student.lessons.show', $lesson) }}" class="group flex items-center gap-6 p-4 bg-gray-50 dark:bg-slate-800 rounded-2xl border border-transparent hover:border-indigo-200 dark:hover:border-indigo-800 hover:bg-white dark:hover:bg-slate-800/80 transition-all">
                                <div class="w-14 h-14 bg-white dark:bg-slate-900 rounded-2xl flex flex-col items-center justify-center shadow-sm border border-gray-100 dark:border-slate-700 group-hover:border-indigo-300 dark:group-hover:border-indigo-700 transition-colors">
                                    <span class="text-[9px] font-black uppercase text-indigo-600 dark:text-indigo-400 tracking-widest">{{ $lesson->lesson_date->format('M') }}</span>
                                    <span class="text-lg font-black text-gray-900 dark:text-white leading-none">{{ $lesson->lesson_date->format('d') }}</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-bold text-gray-900 dark:text-white truncate group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">{{ $lesson->title }}</h4>
                                    <div class="flex items-center gap-3 mt-1 text-xs text-gray-500 dark:text-slate-400">
                                        <span class="flex items-center gap-1">
                                            <x-icon name="users" class="w-3 h-3" />
                                            {{ optional($lesson->ebdClass)->name }}
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <x-icon name="clock" class="w-3 h-3" />
                                            {{ \Carbon\Carbon::parse($lesson->lesson_time)->format('H:i') }}
                                        </span>
                                    </div>
                                </div>
                                @if($lesson->materials->where('is_public', true)->where('type', 'video')->count() > 0)
                                    <span class="hidden sm:flex items-center gap-1 px-3 py-1.5 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-lg text-[10px] font-bold uppercase tracking-wider">
                                        <x-icon name="video" class="w-3 h-3" />
                                        EAD
                                    </span>
                                @endif
                                <x-icon name="chevron-right" class="w-4 h-4 text-gray-300 dark:text-slate-600 group-hover:text-indigo-500 group-hover:translate-x-1 transition-all" />
                            </a>
                        @empty
                            <div class="text-center py-10">
                                <x-icon name="calendar-xmark" style="duotone" class="w-12 h-12 mx-auto text-gray-200 dark:text-slate-700 mb-3" />
                                <p class="text-sm font-medium text-gray-400 dark:text-slate-500">Nenhuma lição ou encontro agendado presencialmente no calendário.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Fast Arcade Portal -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 dark:border-slate-800 flex items-center justify-between bg-gray-50/50 dark:bg-slate-900/50">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-xl">
                                <x-icon name="joystick" style="duotone" class="w-5 h-5" />
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 dark:text-white">Arcade Bíblico</h3>
                                <p class="text-xs text-gray-500 dark:text-slate-400">Diversão e aprendizado</p>
                            </div>
                        </div>
                        <a href="{{ route('memberpanel.ebd.arcade.index') }}" class="text-xs font-bold text-amber-600 dark:text-amber-400 hover:underline">Entrar no Arcade</a>
                    </div>
                    <div class="p-6 grid grid-cols-2 lg:grid-cols-4 gap-4">
                        <a href="{{ route('memberpanel.ebd.arcade.versemaster') }}" class="group aspect-video relative rounded-2xl overflow-hidden shadow bg-slate-900 flex flex-col justify-end p-4 border border-slate-700 hover:border-indigo-500 transition-all">
                            <div class="absolute inset-0 bg-indigo-600/20 group-hover:bg-indigo-600/40 transition-colors z-0"></div>
                            <x-icon name="puzzle-piece" style="duotone" class="absolute top-3 right-3 w-5 h-5 text-indigo-400 z-10" />
                            <h4 class="font-bold text-white z-10 text-sm">Verse Master</h4>
                            <p class="text-[9px] text-indigo-200 z-10 uppercase font-black">+XP Garantido</p>
                        </a>
                        <a href="{{ route('memberpanel.ebd.arcade.quiz') }}" class="group aspect-video relative rounded-2xl overflow-hidden shadow bg-slate-900 flex flex-col justify-end p-4 border border-slate-700 hover:border-emerald-500 transition-all">
                            <div class="absolute inset-0 bg-emerald-600/20 group-hover:bg-emerald-600/40 transition-colors z-0"></div>
                            <x-icon name="circle-question" style="duotone" class="absolute top-3 right-3 w-5 h-5 text-emerald-400 z-10" />
                            <h4 class="font-bold text-white z-10 text-sm">Quiz Bíblico</h4>
                            <p class="text-[9px] text-emerald-200 z-10 uppercase font-black">Teste Rápido</p>
                        </a>
                        <a href="{{ route('memberpanel.ebd.arcade.memory') }}" class="group aspect-video relative rounded-2xl overflow-hidden shadow bg-slate-900 flex flex-col justify-end p-4 border border-slate-700 hover:border-pink-500 transition-all">
                            <div class="absolute inset-0 bg-pink-600/20 group-hover:bg-pink-600/40 transition-colors z-0"></div>
                            <x-icon name="clone" style="duotone" class="absolute top-3 right-3 w-5 h-5 text-pink-400 z-10" />
                            <h4 class="font-bold text-white z-10 text-sm">Memória</h4>
                            <p class="text-[9px] text-pink-200 z-10 uppercase font-black">Concentração</p>
                        </a>
                        <a href="{{ route('memberpanel.ebd.arcade.whosaidit') }}" class="group aspect-video relative rounded-2xl overflow-hidden shadow bg-slate-900 flex flex-col justify-end p-4 border border-slate-700 hover:border-blue-500 transition-all">
                            <div class="absolute inset-0 bg-blue-600/20 group-hover:bg-blue-600/40 transition-colors z-0"></div>
                            <x-icon name="quote-left" style="duotone" class="absolute top-3 right-3 w-5 h-5 text-blue-400 z-10" />
                            <h4 class="font-bold text-white z-10 text-sm break-words">Quem disse?</h4>
                            <p class="text-[9px] text-blue-200 z-10 uppercase font-black">Frases Famosas</p>
                        </a>
                    </div>
                </div>

            </div>

            <!-- Right Column: Badges, Ranking, Attendance -->
            <div class="space-y-8">

                <!-- Conquistas Block -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 dark:border-slate-800 flex items-center justify-between bg-gray-50/50 dark:bg-slate-900/50">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-xl">
                                <x-icon name="medal" style="duotone" class="w-5 h-5" />
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 dark:text-white">Badges & Conquistas</h3>
                                <p class="text-xs text-slate-500">Sua galeria de troféus</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-4 sm:grid-cols-3 gap-3 gap-y-5 justify-items-center">
                            @foreach(($achievements ?? collect())->take(6) as $achievement)
                                @php
                                    $hasAchievement = in_array($achievement->id, $userAchievementIds ?? []);
                                    $tierBg = $hasAchievement ? match($achievement->tier ?? 'bronze') {
                                        'silver' => 'bg-slate-400 text-white shadow-slate-400/20',
                                        'gold' => 'bg-amber-500 text-white shadow-amber-500/20',
                                        'platinum' => 'bg-slate-200 text-slate-900 shadow-slate-300/20 border border-slate-300',
                                        default => 'bg-amber-600 text-white shadow-amber-500/20',
                                    } : 'bg-gray-100 dark:bg-slate-800 text-gray-400 dark:text-slate-600 border border-transparent';
                                @endphp
                                <div class="flex flex-col items-center group cursor-pointer" x-data="{ tooltip: false }">
                                    <div class="relative w-12 h-12 rounded-2xl flex items-center justify-center transition-all duration-300 {{ $tierBg }} group-hover:-translate-y-1"
                                         @mouseenter="tooltip = true" @mouseleave="tooltip = false">
                                        <x-icon name="{{ $achievement->icon_fa_name ?? 'medal' }}" style="duotone" class="w-6 h-6 {{ !$hasAchievement ? 'opacity-30' : '' }}" />
                                        @if($hasAchievement)
                                            <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-500 rounded-full border-2 border-white dark:border-slate-900 flex items-center justify-center">
                                                <x-icon name="check" class="w-2 h-2 text-white" />
                                            </div>
                                        @endif
                                    </div>
                                    <!-- Tooltip -->
                                    <div x-show="tooltip" x-transition class="absolute bottom-full mb-2 w-40 bg-gray-900 dark:bg-slate-800 border border-slate-700 text-white p-3 rounded-xl z-50 pointer-events-none shadow-xl text-center">
                                        <p class="text-[10px] font-bold text-amber-400">{{ $achievement->name }}</p>
                                        <p class="text-[9px] text-gray-300 mt-1">{{ $achievement->description }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Recent Attendance Tracker -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 dark:border-slate-800 flex items-center justify-between bg-gray-50/50 dark:bg-slate-900/50">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-xl">
                                <x-icon name="clipboard-check" style="duotone" class="w-5 h-5" />
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 dark:text-white">Presenças (Físicas/Lives)</h3>
                                <p class="text-xs text-gray-500 dark:text-slate-400">Suas idas à EBD e cultos</p>
                            </div>
                        </div>
                        <a href="{{ route('memberpanel.ebd.student.my-progress') }}" class="text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:underline">Ver Histórico</a>
                    </div>
                    <div class="p-6 space-y-4">
                        @forelse($recentAttendance ?? [] as $attendance)
                            <div class="relative pl-6 pb-4 border-l-2 border-gray-100 dark:border-slate-800 last:border-0 last:pb-0">
                                <div class="absolute -left-[5px] top-0 w-2 h-2 rounded-full
                                    @if($attendance->status === 'present') bg-emerald-500
                                    @elseif($attendance->status === 'late') bg-amber-500
                                    @else bg-rose-500 @endif"></div>
                                <h4 class="text-xs font-bold text-gray-900 dark:text-white leading-tight mt-[-2px]">{{ Str::limit($attendance->lesson->title, 35) }}</h4>
                                <p class="text-[9px] font-medium text-gray-400 dark:text-slate-500 mt-1 uppercase tracking-wider">{{ $attendance->lesson->lesson_date->format('d/m/Y') }}</p>
                            </div>
                        @empty
                            <div class="text-center py-6">
                                <p class="text-sm font-medium text-gray-400 dark:text-slate-500">Histórico limpo. Vá nos próximos encontros para marcar presença.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Inspiration Card (Maintained for Christian flavor) -->
                <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-3xl shadow-xl shadow-blue-500/20 p-8 text-white relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-40 h-40 bg-white/10 rounded-full blur-3xl -mr-10 -mt-10 pointer-events-none group-hover:scale-150 transition-transform duration-700"></div>
                    <x-icon name="quote-right" class="absolute -right-2 -bottom-2 w-24 h-24 text-white/10" />

                    <div class="relative z-10">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="p-2 bg-white/20 rounded-xl backdrop-blur-sm shadow-inner">
                                <x-icon name="sparkles" style="duotone" class="w-5 h-5 text-white" />
                            </div>
                            <h3 class="font-black text-sm uppercase tracking-widest text-indigo-100">Inspiração Diária</h3>
                        </div>
                        <p class="text-lg font-bold leading-relaxed italic mb-4">
                            "Lâmpada para os meus pés é tua palavra, e luz para o meu caminho."
                        </p>
                        <span class="text-xs font-black text-amber-300 uppercase tracking-widest">Salmos 119:105</span>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
