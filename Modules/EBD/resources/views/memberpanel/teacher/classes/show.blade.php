@extends('memberpanel::components.layouts.master')

@section('title', $class->name . ' - Turma')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-slate-950 transition-colors duration-200 pb-12">
    <div class="max-w-7xl mx-auto space-y-8 px-4 sm:px-6 pt-6 sm:pt-8">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-xs text-gray-500 dark:text-slate-400 mb-2">
                    <a href="{{ route('memberpanel.ebd.teacher.index') }}" class="hover:text-purple-600 dark:hover:text-purple-400">Portal</a>
                    <x-icon name="chevron-right" class="w-3 h-3" />
                    <a href="{{ route('memberpanel.ebd.teacher.my-classes') }}" class="hover:text-purple-600 dark:hover:text-purple-400">Turmas</a>
                    <x-icon name="chevron-right" class="w-3 h-3" />
                    <span class="text-gray-900 dark:text-white font-medium truncate max-w-[180px] sm:max-w-none">{{ $class->name }}</span>
                </nav>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight truncate">{{ $class->name }}</h1>
                <p class="text-gray-500 dark:text-slate-400 mt-1 text-sm">{{ $class->age_group_display }} • {{ $class->activeStudents->count() }} alunos ativos</p>
            </div>
            <a href="{{ route('memberpanel.ebd.teacher.my-classes') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-xl shadow-sm font-bold text-sm text-gray-700 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-800 transition-all">
                <x-icon name="arrow-left" class="w-4 h-4" />
                Voltar
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
                    <x-icon name="users-rays" style="duotone" class="w-8 h-8 sm:w-10 sm:h-10 text-white" />
                </div>
                <div class="flex-1 text-center sm:text-left">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 dark:bg-blue-900/30 border border-blue-100 dark:border-blue-800 mb-2">
                        <x-icon name="users-class" class="w-3 h-3 text-blue-600 dark:text-blue-400" />
                        <span class="text-[10px] font-black uppercase tracking-widest text-blue-600 dark:text-blue-400">Visão da Turma</span>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-black text-gray-900 dark:text-white tracking-tight">Corpo discente</h2>
                    <p class="text-gray-500 dark:text-slate-300 text-sm mt-1">Alunos matriculados e desempenho.</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-8">
            <!-- Main: Students List -->
            <div class="lg:col-span-8">
                <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden">
                    <div class="px-5 sm:px-6 py-4 sm:py-5 border-b border-gray-100 dark:border-slate-800 flex items-center gap-3 bg-gray-50/50 dark:bg-slate-900/50">
                        <div class="p-2 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-xl">
                            <x-icon name="user-group" style="duotone" class="w-5 h-5" />
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 dark:text-white">Lista de Alunos</h3>
                            <p class="text-xs text-gray-500 dark:text-slate-400">Matrícula, presença e desempenho</p>
                        </div>
                    </div>

                    @if($class->activeStudents->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-left min-w-[600px]">
                                <thead>
                                    <tr class="border-b border-gray-100 dark:border-slate-800">
                                        <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-wider text-gray-500 dark:text-slate-400">Aluno</th>
                                        <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-wider text-gray-500 dark:text-slate-400">Matrícula</th>
                                        <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-wider text-gray-500 dark:text-slate-400">Presença</th>
                                        <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-wider text-gray-500 dark:text-slate-400">Desempenho</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                                    @foreach($class->activeStudents as $student)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/50 transition-colors">
                                            <td class="px-5 py-4">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-10 h-10 rounded-xl overflow-hidden border border-gray-200 dark:border-slate-700 shrink-0">
                                                        <img src="{{ $student->user->avatar_url }}" alt="{{ $student->user->name }}" class="w-full h-full object-cover">
                                                    </div>
                                                    <div class="min-w-0">
                                                        <p class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ $student->user->name }}</p>
                                                        <p class="text-[10px] text-gray-500 dark:text-slate-400 truncate">{{ $student->user->email }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-5 py-4">
                                                <span class="text-xs font-bold text-gray-600 dark:text-slate-300 tabular-nums">{{ $student->enrollment_date->format('d/m/Y') }}</span>
                                            </td>
                                            <td class="px-5 py-4">
                                                @php
                                                    $totalAttendance = $student->attendance->count();
                                                    $presentCount = $student->attendance->where('status', 'present')->count();
                                                    $attendanceRate = $totalAttendance > 0 ? round(($presentCount / $totalAttendance) * 100) : 0;
                                                @endphp
                                                <div class="flex flex-col gap-1">
                                                    <span class="text-xs font-bold text-gray-900 dark:text-white">{{ $attendanceRate }}%</span>
                                                    <div class="w-20 h-1.5 bg-gray-100 dark:bg-slate-800 rounded-full overflow-hidden">
                                                        <div class="h-full bg-blue-500 rounded-full transition-all" style="width: {{ $attendanceRate }}%"></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-5 py-4">
                                                @php
                                                    $gradedEvals = $student->evaluations->where('status', 'graded');
                                                    $avgScore = $gradedEvals->count() > 0 ? round($gradedEvals->avg('score'), 1) : 0;
                                                @endphp
                                                @if($gradedEvals->count() > 0)
                                                    <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-bold {{ $avgScore >= 70 ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400' : ($avgScore >= 50 ? 'bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400' : 'bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400') }}">
                                                        {{ $avgScore }} média
                                                    </span>
                                                @else
                                                    <span class="text-[10px] font-medium text-gray-400 dark:text-slate-500">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="py-16 text-center">
                            <div class="w-14 h-14 mx-auto mb-4 rounded-2xl bg-gray-100 dark:bg-slate-800 flex items-center justify-center">
                                <x-icon name="user-group-slash" style="duotone" class="w-7 h-7 text-gray-400 dark:text-slate-500" />
                            </div>
                            <p class="text-sm text-gray-500 dark:text-slate-400">Nenhum aluno matriculado nesta turma.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-4 space-y-6">
                <!-- Class Info -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm p-5 sm:p-6">
                    <div class="flex items-center gap-2 mb-5">
                        <div class="w-1 h-5 bg-indigo-500 rounded-full"></div>
                        <h3 class="text-sm font-bold uppercase tracking-wider text-gray-900 dark:text-white">Info da Turma</h3>
                    </div>
                    <div class="space-y-4">
                        <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-slate-800 rounded-xl">
                            <x-icon name="clock" class="w-5 h-5 text-blue-500" />
                            <div>
                                <span class="block text-[10px] font-bold text-gray-500 dark:text-slate-400 uppercase">Horário</span>
                                <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $class->schedule ?? 'Não definido' }}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-slate-800 rounded-xl">
                            <x-icon name="map-marker-alt" class="w-5 h-5 text-emerald-500" />
                            <div>
                                <span class="block text-[10px] font-bold text-gray-500 dark:text-slate-400 uppercase">Local</span>
                                <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $class->location ?? 'Sala Principal' }}</span>
                            </div>
                        </div>
                    </div>
                    @if($class->description)
                        <div class="mt-4 p-4 bg-gray-50 dark:bg-slate-800 rounded-xl border border-gray-100 dark:border-slate-700">
                            <span class="block text-[10px] font-bold text-gray-500 dark:text-slate-400 uppercase mb-2">Meta pedagógica</span>
                            <p class="text-xs text-gray-600 dark:text-slate-300 leading-relaxed">{{ $class->description }}</p>
                        </div>
                    @endif
                </div>

                <!-- Teachers -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm p-5 sm:p-6">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-gray-900 dark:text-white mb-4">Preceptoria</h3>
                    <div class="space-y-3">
                        @foreach($class->teachers as $teacher)
                            <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-slate-800 rounded-xl border border-gray-100 dark:border-slate-700">
                                <div class="w-10 h-10 rounded-xl overflow-hidden border border-gray-200 dark:border-slate-600 shrink-0">
                                    <img src="{{ $teacher->user->avatar_url }}" alt="{{ $teacher->user->name }}" class="w-full h-full object-cover">
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ $teacher->user->name }}</p>
                                    <span class="inline-block mt-1 px-2 py-0.5 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-lg text-[9px] font-bold uppercase">
                                        {{ $teacher->role_display }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
