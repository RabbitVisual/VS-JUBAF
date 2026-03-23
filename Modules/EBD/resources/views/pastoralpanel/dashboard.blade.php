@extends('pastoralpanel::components.layouts.master')

@section('title', 'EBD - Dashboard')

@section('content')
    <div class="space-y-6">
        <div class="rounded-3xl bg-gradient-to-br from-slate-800 via-slate-900 to-slate-800 text-white shadow-xl border border-amber-900/30 overflow-hidden">
            <div class="p-6 md:p-8">
                <h1 class="text-2xl md:text-3xl font-bold tracking-tight mb-1">Escola Bíblica Dominical</h1>
                <p class="text-slate-300 text-sm md:text-base">Visão pastoral da EBD: turmas, lições, presença e avaliações.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-5">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Turmas</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['total_classes'] }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $stats['active_classes'] }} ativas</p>
            </div>
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-5">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Alunos</p>
                <p class="text-2xl font-bold text-amber-600 dark:text-amber-400 mt-1">{{ $stats['total_students'] }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Matrículas ativas</p>
            </div>
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-5">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Assiduidade</p>
                <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">{{ $attendanceRate }}%</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Últimos 30 dias</p>
            </div>
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-5">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Avaliações</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['graded_evaluations'] }}/{{ $stats['total_evaluations'] }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Média {{ $stats['avg_score'] ?? 0 }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Próximas lições</h3>
                    <a href="{{ route('pastor.ebd.lessons.index') }}" class="text-xs font-medium text-amber-600 dark:text-amber-400 hover:underline">Ver todas</a>
                </div>
                <div class="p-6 space-y-3">
                    @forelse($upcomingLessons as $lesson)
                        <a href="{{ route('pastor.ebd.lessons.show', $lesson) }}" class="flex items-center justify-between p-3 rounded-xl bg-gray-50 dark:bg-slate-700/50 hover:bg-amber-50 dark:hover:bg-amber-900/20 border border-transparent hover:border-amber-500/30 transition-all">
                            <div class="flex items-center gap-3">
                                <div class="w-12 flex-shrink-0 text-center">
                                    <span class="text-[10px] font-bold text-amber-600 dark:text-amber-400 block">{{ $lesson->lesson_date->translatedFormat('M') }}</span>
                                    <span class="text-lg font-bold text-gray-900 dark:text-white">{{ $lesson->lesson_date->format('d') }}</span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $lesson->title }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $lesson->ebdClass->name ?? '—' }} · {{ $lesson->lesson_date->format('H:i') }}</p>
                                </div>
                            </div>
                            <span class="text-xs font-medium px-2 py-0.5 rounded {{ $lesson->status === 'scheduled' ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300' : 'bg-gray-100 text-gray-600 dark:bg-slate-600 dark:text-gray-300' }}">{{ $lesson->status === 'scheduled' ? 'Agendada' : ($lesson->status ?? '—') }}</span>
                        </a>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">Nenhuma lição agendada.</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Desempenho por turma (30 dias)</h3>
                </div>
                <div class="p-6 space-y-4">
                    @forelse($classPerformance as $perf)
                        <div class="flex items-center justify-between gap-4">
                            <span class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $perf['name'] }}</span>
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <div class="w-24 h-2 bg-gray-200 dark:bg-slate-600 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full {{ $perf['rate'] >= 70 ? 'bg-green-500' : ($perf['rate'] >= 40 ? 'bg-amber-500' : 'bg-red-500') }}" style="width: {{ min($perf['rate'], 100) }}%"></div>
                                </div>
                                <span class="text-xs font-medium text-gray-600 dark:text-gray-400 w-10">{{ $perf['rate'] }}%</span>
                            </div>
                            <span class="text-xs text-gray-500 w-12 text-right">{{ $perf['total_students'] }} al.</span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">Sem dados de desempenho.</p>
                    @endforelse
                </div>
            </div>
        </div>

        @if($studentsWithAbsences->isNotEmpty())
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-amber-200 dark:border-amber-800/50 overflow-hidden">
                <div class="px-6 py-4 bg-amber-50 dark:bg-amber-900/20 border-b border-amber-200 dark:border-amber-800/50">
                    <h3 class="text-sm font-bold text-amber-800 dark:text-amber-300">Alunos com faltas (2+ nos últimos 30 dias)</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                        <thead class="bg-gray-50 dark:bg-slate-900/50">
                            <tr>
                                <th class="px-6 py-2 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Aluno</th>
                                <th class="px-6 py-2 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Turma</th>
                                <th class="px-6 py-2 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Faltas</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                            @foreach($studentsWithAbsences->take(10) as $item)
                                <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/30">
                                    <td class="px-6 py-3 text-sm font-medium text-gray-900 dark:text-white">{{ $item['student']->user->name ?? '—' }}</td>
                                    <td class="px-6 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $item['student']->ebdClass->name ?? '—' }}</td>
                                    <td class="px-6 py-3 text-sm text-right font-medium text-amber-600 dark:text-amber-400">{{ $item['absences'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <div class="flex flex-wrap gap-3">
            <a href="{{ route('pastor.ebd.classes.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-medium text-sm transition-colors">
                <x-icon name="screen-users" class="w-5 h-5" /> Turmas
            </a>
            <a href="{{ route('pastor.ebd.lessons.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-700 dark:text-gray-300 font-medium text-sm hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                <x-icon name="book-open-reader" class="w-5 h-5" /> Lições
            </a>
            <a href="{{ route('pastor.ebd.attendance.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-700 dark:text-gray-300 font-medium text-sm hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                <x-icon name="clipboard-user" class="w-5 h-5" /> Presença
            </a>
            <a href="{{ route('pastor.ebd.evaluations.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-700 dark:text-gray-300 font-medium text-sm hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                <x-icon name="clipboard-check" class="w-5 h-5" /> Avaliações
            </a>
        </div>
    </div>
@endsection
