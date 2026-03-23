@extends('memberpanel::components.layouts.master')

@section('title', 'EBD - Painel do Professor')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-slate-950 transition-colors duration-200 pb-12">
    <div class="max-w-7xl mx-auto space-y-8 px-4 sm:px-6 pt-6 sm:pt-8">

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Portal do Professor</h1>
                <p class="text-gray-500 dark:text-slate-400 mt-1 max-w-md">Gerencie suas turmas, lições e avaliações.</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="px-4 py-2 bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-xl shadow-sm flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-purple-500 animate-pulse"></div>
                    <span class="text-xs font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider">Professor Ativo</span>
                </div>
            </div>
        </div>

        <!-- Hero Section -->
        <div class="relative overflow-hidden bg-white dark:bg-slate-900 rounded-3xl shadow-xl dark:shadow-2xl border border-gray-100 dark:border-slate-800 transition-colors duration-200" data-tour="ebd-teacher-hero">
            <div class="absolute inset-0 opacity-20 dark:opacity-40 pointer-events-none">
                <div class="absolute -top-24 -left-20 w-96 h-96 bg-purple-400 dark:bg-purple-600 rounded-full blur-[100px]"></div>
                <div class="absolute top-1/2 -right-20 w-80 h-80 bg-indigo-400 dark:bg-indigo-600 rounded-full blur-[100px]"></div>
            </div>

            <div class="relative px-8 py-10 flex flex-col md:flex-row items-center gap-10 z-10">
                <div class="relative group shrink-0">
                    <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center shadow-xl shadow-purple-500/20">
                        <x-icon name="chalkboard-user" style="duotone" class="w-10 h-10 text-white" />
                    </div>
                </div>

                <div class="flex-1 text-center md:text-left space-y-3">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-50 dark:bg-purple-900/30 border border-purple-100 dark:border-purple-800">
                        <x-icon name="user-tie" class="w-3 h-3 text-purple-600 dark:text-purple-400" />
                        <span class="text-[10px] font-black uppercase tracking-widest text-purple-600 dark:text-purple-400">Portal Master EBD</span>
                    </div>
                    <h2 class="text-2xl md:text-3xl font-black text-gray-900 dark:text-white tracking-tight">
                        Olá, Professor {{ explode(' ', auth()->user()->name)[0] }}!
                    </h2>
                    <p class="text-gray-500 dark:text-slate-300 font-medium max-w-xl text-sm leading-relaxed">
                        Capacitando mentores para formar discípulos. Gerencie seu conteúdo e acompanhe seus alunos.
                    </p>
                </div>

                <div class="shrink-0 flex items-center gap-4 bg-gray-50 dark:bg-slate-800 rounded-2xl px-6 py-4 border border-gray-100 dark:border-slate-700">
                    <div class="w-12 h-12 rounded-full overflow-hidden border-2 border-purple-500/50">
                        <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400 dark:text-slate-500">Suas Turmas</p>
                        <p class="text-2xl font-black text-gray-900 dark:text-white">{{ $stats['total_classes'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4" data-tour="ebd-teacher-stats">
            <!-- Turmas -->
            <div class="group relative bg-white dark:bg-slate-900 rounded-3xl p-6 border border-gray-100 dark:border-slate-800 shadow-sm hover:shadow-xl hover:shadow-blue-500/5 transition-all duration-300">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-slate-500 mb-1">Turmas Ativas</p>
                        <h3 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">{{ $stats['total_classes'] }}</h3>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-blue-600 dark:text-blue-400 group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                        <x-icon name="screen-users" style="duotone" class="w-6 h-6" />
                    </div>
                </div>
                <p class="text-xs font-bold text-gray-500 dark:text-slate-400 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                    Classes sob sua responsabilidade
                </p>
            </div>

            <!-- Alunos -->
            <div class="group relative bg-white dark:bg-slate-900 rounded-3xl p-6 border border-gray-100 dark:border-slate-800 shadow-sm hover:shadow-xl hover:shadow-emerald-500/5 transition-all duration-300">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-slate-500 mb-1">Total Alunos</p>
                        <h3 class="text-3xl font-black text-emerald-600 dark:text-emerald-400 tracking-tight">{{ $stats['total_students'] }}</h3>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center text-emerald-600 dark:text-emerald-400 group-hover:scale-110 group-hover:-rotate-3 transition-transform duration-300">
                        <x-icon name="users" style="duotone" class="w-6 h-6" />
                    </div>
                </div>
                <p class="text-xs font-bold text-gray-500 dark:text-slate-400 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    Alunos matriculados
                </p>
            </div>

            <!-- Aulas Pendentes -->
            <div class="group relative bg-white dark:bg-slate-900 rounded-3xl p-6 border border-gray-100 dark:border-slate-800 shadow-sm hover:shadow-xl hover:shadow-purple-500/5 transition-all duration-300">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-slate-500 mb-1">Aulas Pendentes</p>
                        <h3 class="text-3xl font-black text-purple-600 dark:text-purple-400 tracking-tight">{{ $stats['upcoming_lessons'] }}</h3>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-purple-50 dark:bg-purple-900/20 flex items-center justify-center text-purple-600 dark:text-purple-400 group-hover:scale-110 transition-transform duration-300">
                        <x-icon name="book-open" style="duotone" class="w-6 h-6" />
                    </div>
                </div>
                <p class="text-xs font-bold text-gray-500 dark:text-slate-400 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span>
                    Lições a ministrar
                </p>
            </div>

            <!-- Avaliações -->
            <div class="group relative bg-white dark:bg-slate-900 rounded-3xl p-6 border border-gray-100 dark:border-slate-800 shadow-sm hover:shadow-xl hover:shadow-amber-500/5 transition-all duration-300">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-slate-500 mb-1">Para Corrigir</p>
                        <h3 class="text-3xl font-black text-amber-600 dark:text-amber-400 tracking-tight">{{ $stats['pending_evaluations'] }}</h3>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-900/20 flex items-center justify-center text-amber-600 dark:text-amber-400 group-hover:scale-110 transition-transform duration-300">
                        <x-icon name="file-check" style="duotone" class="w-6 h-6" />
                    </div>
                </div>
                <p class="text-xs font-bold text-gray-500 dark:text-slate-400 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                    Avaliações pendentes
                </p>
            </div>
        </div>

        <!-- Main Grid Layout -->
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">

            <!-- Left Column: Quick Actions & Agenda -->
            <div class="xl:col-span-2 space-y-8">

                <!-- Quick Actions -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 dark:border-slate-800 flex items-center gap-3">
                        <x-icon name="bolt" class="w-4 h-4 text-gray-400 dark:text-slate-500" />
                        <h3 class="text-xs font-black uppercase tracking-widest text-gray-500 dark:text-slate-400">Central de Comando</h3>
                    </div>
                    <div class="p-4 grid grid-cols-3 gap-3">
                        <a href="{{ route('memberpanel.ebd.teacher.my-classes') }}" class="flex flex-col items-center justify-center gap-3 p-5 rounded-2xl bg-blue-50 dark:bg-blue-900/10 hover:bg-blue-100 dark:hover:bg-blue-900/20 border border-blue-100 dark:border-blue-900/20 transition-all group text-center">
                            <div class="w-10 h-10 rounded-full bg-white dark:bg-blue-900/30 flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform">
                                <x-icon name="users-gear" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                            </div>
                            <span class="text-xs font-bold text-gray-900 dark:text-white">Turmas</span>
                        </a>
                        <a href="{{ route('memberpanel.ebd.teacher.lessons') }}" class="flex flex-col items-center justify-center gap-3 p-5 rounded-2xl bg-emerald-50 dark:bg-emerald-900/10 hover:bg-emerald-100 dark:hover:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-900/20 transition-all group text-center">
                            <div class="w-10 h-10 rounded-full bg-white dark:bg-emerald-900/30 flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform">
                                <x-icon name="book-open-reader" class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                            </div>
                            <span class="text-xs font-bold text-gray-900 dark:text-white">Lições</span>
                        </a>
                        <a href="{{ route('memberpanel.ebd.teacher.evaluations') }}" class="flex flex-col items-center justify-center gap-3 p-5 rounded-2xl bg-amber-50 dark:bg-amber-900/10 hover:bg-amber-100 dark:hover:bg-amber-900/20 border border-amber-100 dark:border-amber-900/20 transition-all group text-center">
                            <div class="w-10 h-10 rounded-full bg-white dark:bg-amber-900/30 flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform">
                                <x-icon name="graduation-cap" class="w-5 h-5 text-amber-600 dark:text-amber-400" />
                            </div>
                            <span class="text-xs font-bold text-gray-900 dark:text-white">Avaliações</span>
                        </a>
                    </div>
                </div>

                <!-- Upcoming Lessons -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden">
                    <div class="px-8 py-6 border-b border-gray-100 dark:border-slate-800 flex items-center justify-between bg-gray-50/50 dark:bg-slate-900/50">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 rounded-xl">
                                <x-icon name="calendar-days" class="w-5 h-5" />
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 dark:text-white">Agenda Master</h3>
                                <p class="text-xs text-gray-500 dark:text-slate-400">Próximas aulas a ministrar</p>
                            </div>
                        </div>
                        <a href="{{ route('memberpanel.ebd.teacher.lessons') }}" class="text-xs font-bold text-purple-600 dark:text-purple-400 hover:underline">Ver Todas</a>
                    </div>
                    <div class="p-6 space-y-4">
                        @forelse($upcomingLessons as $lesson)
                            <a href="{{ route('memberpanel.ebd.teacher.lessons.show', $lesson) }}" class="group flex items-center gap-6 p-5 bg-gray-50 dark:bg-slate-800 rounded-2xl border border-transparent hover:border-purple-200 dark:hover:border-purple-800 hover:bg-white dark:hover:bg-slate-800/80 transition-all">
                                <div class="w-14 h-14 bg-white dark:bg-slate-900 rounded-2xl flex flex-col items-center justify-center shadow-sm border border-gray-100 dark:border-slate-700 group-hover:border-purple-300 dark:group-hover:border-purple-700 transition-colors">
                                    <span class="text-[9px] font-black uppercase text-purple-600 dark:text-purple-400 tracking-widest">{{ $lesson->lesson_date->format('M') }}</span>
                                    <span class="text-lg font-black text-gray-900 dark:text-white leading-none">{{ $lesson->lesson_date->format('d') }}</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-bold text-gray-900 dark:text-white truncate group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">{{ $lesson->title }}</h4>
                                    <div class="flex items-center gap-3 mt-1 text-xs text-gray-500 dark:text-slate-400">
                                        <span class="flex items-center gap-1">
                                            <x-icon name="users" class="w-3 h-3" />
                                            {{ $lesson->ebdClass->name }}
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <x-icon name="clock" class="w-3 h-3" />
                                            {{ \Carbon\Carbon::parse($lesson->lesson_time)->format('H:i') }}
                                        </span>
                                    </div>
                                </div>
                                <span class="hidden sm:flex items-center gap-1 px-3 py-1.5 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-lg text-[10px] font-bold uppercase tracking-wider">
                                    {{ $lesson->status_display }}
                                </span>
                            </a>
                        @empty
                            <div class="text-center py-10">
                                <x-icon name="calendar-plus" style="duotone" class="w-12 h-12 mx-auto text-gray-200 dark:text-slate-700 mb-3" />
                                <p class="text-sm font-medium text-gray-400 dark:text-slate-500">Nenhuma aula agendada</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Right Column: Evaluations & Tips -->
            <div class="space-y-8">

                <!-- Pending Evaluations -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 dark:border-slate-800 flex items-center gap-3 bg-gray-50/50 dark:bg-slate-900/50">
                        <div class="p-2 bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-xl">
                            <x-icon name="file-pen" class="w-5 h-5" />
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 dark:text-white">Correções Pendentes</h3>
                            <p class="text-xs text-gray-500 dark:text-slate-400">Avaliações para corrigir</p>
                        </div>
                    </div>
                    <div class="p-6 space-y-4">
                        @forelse($pendingEvaluations->where('status', 'completed')->take(5) as $evaluation)
                            <div class="flex items-center gap-4 p-4 bg-gray-50 dark:bg-slate-800 rounded-xl border border-transparent hover:border-amber-200 dark:hover:border-amber-800 transition-all">
                                <div class="w-10 h-10 rounded-full overflow-hidden bg-gray-200 dark:bg-slate-700">
                                    <img src="{{ $evaluation->student->user->avatar_url }}" class="w-full h-full object-cover" alt="">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ $evaluation->student->user->name }}</h4>
                                    <p class="text-[10px] text-gray-400 dark:text-slate-500 truncate">{{ Str::limit($evaluation->lesson->title, 25) }}</p>
                                </div>
                                <a href="{{ route('memberpanel.ebd.teacher.evaluations.grade', $evaluation) }}" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-[10px] font-bold uppercase tracking-wider rounded-lg transition-all active:scale-95">
                                    Corrigir
                                </a>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <x-icon name="circle-check" style="duotone" class="w-10 h-10 mx-auto text-emerald-300 dark:text-emerald-700 mb-3" />
                                <p class="text-sm font-medium text-gray-400 dark:text-slate-500">Tudo em dia!</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Tip Card -->
                <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-3xl shadow-xl shadow-indigo-500/20 p-8 text-white relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-40 h-40 bg-white/10 rounded-full blur-3xl -mr-10 -mt-10 pointer-events-none group-hover:scale-150 transition-transform duration-700"></div>
                    <x-icon name="lightbulb" class="absolute -right-2 -bottom-2 w-24 h-24 text-white/10" />

                    <div class="relative z-10">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="p-2 bg-white/20 rounded-xl backdrop-blur-sm shadow-inner">
                                <x-icon name="lightbulb" class="w-5 h-5 text-white" />
                            </div>
                            <h3 class="font-black text-sm uppercase tracking-widest text-indigo-200">Dica do Mestre</h3>
                        </div>
                        <p class="text-lg font-bold leading-relaxed italic mb-4">
                            "Procure apresentar-te a Deus aprovado, como obreiro que não tem de que se envergonhar..."
                        </p>
                        <span class="text-xs font-black text-amber-300 uppercase tracking-widest">2 Timóteo 2:15</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
