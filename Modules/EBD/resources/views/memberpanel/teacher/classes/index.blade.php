@extends('memberpanel::components.layouts.master')

@section('title', 'Minhas Turmas - EBD')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-slate-950 transition-colors duration-200 pb-12">
    <div class="max-w-7xl mx-auto space-y-8 px-4 sm:px-6 pt-6 sm:pt-8">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-xs text-gray-500 dark:text-slate-400 mb-2">
                    <a href="{{ route('memberpanel.ebd.teacher.index') }}" class="hover:text-purple-600 dark:hover:text-purple-400 transition-colors">Portal do Professor</a>
                    <x-icon name="chevron-right" class="w-3 h-3" />
                    <span class="text-gray-900 dark:text-white font-medium">Minhas Turmas</span>
                </nav>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Minhas Turmas</h1>
                <p class="text-gray-500 dark:text-slate-400 mt-1 text-sm sm:text-base">Acompanhe os grupos de estudo sob sua responsabilidade.</p>
            </div>
            <a href="{{ route('memberpanel.ebd.teacher.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-xl shadow-sm font-bold text-sm text-gray-700 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-800 transition-all">
                <x-icon name="arrow-left" class="w-4 h-4" />
                <span class="hidden sm:inline">Painel</span>
            </a>
        </div>

        <!-- Hero -->
        <div class="relative overflow-hidden bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl shadow-xl dark:shadow-2xl border border-gray-100 dark:border-slate-800">
            <div class="absolute inset-0 opacity-20 dark:opacity-40 pointer-events-none">
                <div class="absolute -top-24 -left-20 w-72 sm:w-96 h-72 sm:h-96 bg-blue-400 dark:bg-blue-600 rounded-full blur-[100px]"></div>
                <div class="absolute top-1/2 -right-20 w-64 sm:w-80 h-64 sm:h-80 bg-indigo-400 dark:bg-indigo-600 rounded-full blur-[100px]"></div>
            </div>
            <div class="relative px-5 sm:px-8 py-8 sm:py-10 z-10 flex flex-col sm:flex-row items-center gap-6">
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-xl shadow-blue-500/20 shrink-0">
                    <x-icon name="screen-users" style="duotone" class="w-8 h-8 sm:w-10 sm:h-10 text-white" />
                </div>
                <div class="flex-1 text-center sm:text-left">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 dark:bg-blue-900/30 border border-blue-100 dark:border-blue-800 mb-2">
                        <x-icon name="users-class" class="w-3 h-3 text-blue-600 dark:text-blue-400" />
                        <span class="text-[10px] font-black uppercase tracking-widest text-blue-600 dark:text-blue-400">Gestão de Turmas</span>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-black text-gray-900 dark:text-white tracking-tight">Suas turmas EBD</h2>
                    <p class="text-gray-500 dark:text-slate-300 text-sm mt-1">Visualize e gerencie cada grupo de estudo.</p>
                </div>
            </div>
        </div>

        @if($teacherClasses->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                @foreach($teacherClasses as $teacherClass)
                    @php
                        $class = $teacherClass->ebdClass;
                        $studentCount = $class->activeStudents->count();
                    @endphp
                    <div class="group bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden flex flex-col">
                        <div class="p-5 sm:p-6 flex-1">
                            <div class="flex items-start justify-between gap-3 mb-4">
                                <div class="p-2 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-xl">
                                    <x-icon name="users-class" style="duotone" class="w-5 h-5" />
                                </div>
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold {{ $class->is_active ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-800' : 'bg-gray-100 dark:bg-slate-800 text-gray-500 dark:text-slate-400' }}">
                                    {{ $class->is_active ? 'Ativa' : 'Inativa' }}
                                </span>
                            </div>
                            <h3 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white tracking-tight mb-2">{{ $class->name }}</h3>
                            <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-slate-400 mb-4">
                                <span class="font-medium">{{ $class->age_group_display }}</span>
                                <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-slate-600"></span>
                                <span class="text-blue-600 dark:text-blue-400 font-bold">{{ $teacherClass->role_display }}</span>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="bg-gray-50 dark:bg-slate-800 rounded-xl p-3 border border-gray-100 dark:border-slate-700">
                                    <span class="block text-[9px] font-bold uppercase text-gray-400 dark:text-slate-500 mb-0.5">Alunos</span>
                                    <span class="text-sm font-black text-gray-900 dark:text-white tabular-nums flex items-center gap-1">
                                        <x-icon name="user-group" class="w-3.5 h-3.5 text-blue-500" />
                                        {{ $studentCount }}
                                    </span>
                                </div>
                                <div class="bg-gray-50 dark:bg-slate-800 rounded-xl p-3 border border-gray-100 dark:border-slate-700">
                                    <span class="block text-[9px] font-bold uppercase text-gray-400 dark:text-slate-500 mb-0.5">Horário</span>
                                    <span class="text-sm font-black text-gray-900 dark:text-white tabular-nums flex items-center gap-1">
                                        <x-icon name="clock" class="w-3.5 h-3.5 text-purple-500" />
                                        {{ $class->schedule_time ? \Carbon\Carbon::parse($class->schedule_time)->format('H:i') : '—' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="p-4 sm:p-5 border-t border-gray-100 dark:border-slate-800">
                            <a href="{{ route('memberpanel.ebd.teacher.classes.show', $class) }}"
                               class="w-full inline-flex justify-center items-center gap-2 px-4 py-3 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-xl font-bold text-sm transition-all hover:bg-blue-600 dark:hover:bg-blue-500 active:scale-[0.98]">
                                Gerenciar Turma
                                <x-icon name="chevron-right" class="w-4 h-4" />
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm p-12 sm:p-16 text-center">
                <div class="w-16 h-16 sm:w-20 sm:h-20 mx-auto mb-4 rounded-2xl bg-gray-100 dark:bg-slate-800 flex items-center justify-center">
                    <x-icon name="users-slash" style="duotone" class="w-8 h-8 sm:w-10 sm:h-10 text-gray-400 dark:text-slate-500" />
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Nenhuma turma encontrada</h3>
                <p class="text-sm text-gray-500 dark:text-slate-400 max-w-sm mx-auto">Você ainda não possui turmas sob sua supervisão.</p>
            </div>
        @endif
    </div>
</div>
@endsection
