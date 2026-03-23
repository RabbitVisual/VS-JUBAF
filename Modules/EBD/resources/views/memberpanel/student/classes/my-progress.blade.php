@extends('memberpanel::components.layouts.master')

@section('title', 'Meu Progresso EBD')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-slate-950 transition-colors duration-200 pb-12">
    <div class="max-w-7xl mx-auto space-y-8 px-6 pt-8">

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-sm mb-2">
                    <a href="{{ route('memberpanel.ebd.student.index') }}" class="text-gray-400 dark:text-slate-500 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">
                        <x-icon name="house" class="w-4 h-4" />
                    </a>
                    <x-icon name="chevron-right" class="w-3 h-3 text-gray-300 dark:text-slate-600" />
                    <span class="text-gray-600 dark:text-slate-300 font-medium">Meu Progresso</span>
                </nav>
                <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Meu Progresso</h1>
                <p class="text-gray-500 dark:text-slate-400 mt-1 max-w-md">Acompanhe sua jornada de crescimento espiritual e acadêmico.</p>
            </div>
            <a href="{{ route('memberpanel.ebd.student.index') }}" class="inline-flex items-center px-5 py-3 bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-xl font-bold text-sm text-gray-700 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-800 transition-all shadow-sm">
                <x-icon name="arrow-left" class="w-4 h-4 mr-2" />
                Voltar ao Painel
            </a>
        </div>

        <!-- Hero Stats Card -->
        <div class="relative overflow-hidden bg-white dark:bg-slate-900 rounded-3xl shadow-xl dark:shadow-2xl border border-gray-100 dark:border-slate-800">
            <div class="absolute inset-0 opacity-20 dark:opacity-40 pointer-events-none">
                <div class="absolute -top-24 -left-20 w-96 h-96 bg-emerald-400 dark:bg-emerald-600 rounded-full blur-[100px]"></div>
                <div class="absolute bottom-0 right-0 w-80 h-80 bg-teal-400 dark:bg-teal-600 rounded-full blur-[100px] opacity-50"></div>
            </div>

            <div class="relative px-8 py-10 flex flex-col md:flex-row items-center gap-8 z-10">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-xl shadow-emerald-500/20">
                    <x-icon name="chart-line-up" style="duotone" class="w-8 h-8 text-white" />
                </div>
                <div class="flex-1 text-center md:text-left">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-100 dark:border-emerald-800 mb-2">
                        <x-icon name="trophy" class="w-3 h-3 text-emerald-600 dark:text-emerald-400" />
                        <span class="text-[10px] font-black uppercase tracking-widest text-emerald-600 dark:text-emerald-400">Desempenho Acadêmico</span>
                    </div>
                    <h2 class="text-xl md:text-2xl font-black text-gray-900 dark:text-white tracking-tight">
                        Cada passo é uma <span class="text-emerald-600 dark:text-emerald-400">vitória</span>
                    </h2>
                    <p class="text-gray-500 dark:text-slate-300 font-medium max-w-xl text-sm mt-1">
                        Veja sua evolução na Escola Bíblica Dominical.
                    </p>
                </div>
            </div>
        </div>

        <!-- Statistics Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Total Lessons -->
            <div class="group relative bg-white dark:bg-slate-900 rounded-3xl p-6 border border-gray-100 dark:border-slate-800 shadow-sm hover:shadow-xl hover:shadow-blue-500/5 transition-all duration-300">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-slate-500 mb-1">Total de Lições</p>
                        <h3 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">{{ $stats['total_lessons'] }}</h3>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-blue-600 dark:text-blue-400 group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                        <x-icon name="book-open-reader" style="duotone" class="w-6 h-6" />
                    </div>
                </div>
                <p class="text-xs font-bold text-gray-500 dark:text-slate-400 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                    Aulas disponíveis
                </p>
            </div>

            <!-- Presences -->
            <div class="group relative bg-white dark:bg-slate-900 rounded-3xl p-6 border border-gray-100 dark:border-slate-800 shadow-sm hover:shadow-xl hover:shadow-emerald-500/5 transition-all duration-300">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-slate-500 mb-1">Presenças</p>
                        <h3 class="text-3xl font-black text-emerald-600 dark:text-emerald-400 tracking-tight">{{ $stats['present_count'] }}</h3>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center text-emerald-600 dark:text-emerald-400 group-hover:scale-110 group-hover:-rotate-3 transition-transform duration-300">
                        <x-icon name="calendar-check" style="duotone" class="w-6 h-6" />
                    </div>
                </div>
                <p class="text-xs font-bold text-gray-500 dark:text-slate-400 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    Frequência confirmada
                </p>
            </div>

            <!-- Attendance Rate -->
            <div class="group relative bg-white dark:bg-slate-900 rounded-3xl p-6 border border-gray-100 dark:border-slate-800 shadow-sm hover:shadow-xl hover:shadow-amber-500/5 transition-all duration-300">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-slate-500 mb-1">Taxa de Frequência</p>
                        <h3 class="text-3xl font-black text-amber-600 dark:text-amber-400 tracking-tight">{{ $stats['attendance_rate'] }}%</h3>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-900/20 flex items-center justify-center text-amber-600 dark:text-amber-400 group-hover:scale-110 transition-transform duration-300">
                        <x-icon name="chart-simple" style="duotone" class="w-6 h-6" />
                    </div>
                </div>
                <div class="relative h-2 w-full bg-gray-100 dark:bg-slate-800 rounded-full overflow-hidden">
                    <div class="absolute h-full bg-gradient-to-r from-amber-500 to-orange-500 rounded-full transition-all duration-1000" style="width: {{ $stats['attendance_rate'] }}%"></div>
                </div>
            </div>

            <!-- Average Score -->
            <div class="group relative bg-white dark:bg-slate-900 rounded-3xl p-6 border border-gray-100 dark:border-slate-800 shadow-sm hover:shadow-xl hover:shadow-purple-500/5 transition-all duration-300">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-slate-500 mb-1">Média de Notas</p>
                        <h3 class="text-3xl font-black text-purple-600 dark:text-purple-400 tracking-tight">
                            {{ $stats['average_score'] > 0 ? number_format($stats['average_score'], 1) : '-' }}
                        </h3>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-purple-50 dark:bg-purple-900/20 flex items-center justify-center text-purple-600 dark:text-purple-400 group-hover:scale-110 transition-transform duration-300">
                        <x-icon name="graduation-cap" style="duotone" class="w-6 h-6" />
                    </div>
                </div>
                <p class="text-xs font-bold text-gray-500 dark:text-slate-400 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span>
                    Desempenho geral
                </p>
            </div>
        </div>

        <!-- Visual Summary -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm p-8">
            <div class="flex flex-col sm:flex-row items-center justify-around gap-8">
                <div class="text-center">
                    <div class="w-20 h-20 bg-emerald-500 rounded-2xl flex items-center justify-center mx-auto shadow-lg shadow-emerald-500/20 mb-3 hover:scale-105 transition-transform">
                        <span class="text-2xl font-black text-white">{{ $stats['present_count'] }}</span>
                    </div>
                    <p class="text-xs font-bold text-gray-400 dark:text-slate-500 uppercase tracking-wider">Presente</p>
                </div>
                <div class="text-center">
                    <div class="w-20 h-20 bg-rose-500 rounded-2xl flex items-center justify-center mx-auto shadow-lg shadow-rose-500/20 mb-3 hover:scale-105 transition-transform">
                        <span class="text-2xl font-black text-white">{{ $stats['absent_count'] }}</span>
                    </div>
                    <p class="text-xs font-bold text-gray-400 dark:text-slate-500 uppercase tracking-wider">Ausente</p>
                </div>
                <div class="text-center">
                    <div class="w-20 h-20 bg-amber-500 rounded-2xl flex items-center justify-center mx-auto shadow-lg shadow-amber-500/20 mb-3 hover:scale-105 transition-transform">
                        <span class="text-2xl font-black text-white">{{ $stats['late_count'] }}</span>
                    </div>
                    <p class="text-xs font-bold text-gray-400 dark:text-slate-500 uppercase tracking-wider">Atrasado</p>
                </div>
                <div class="text-center">
                    <div class="w-20 h-20 bg-blue-500 rounded-2xl flex items-center justify-center mx-auto shadow-lg shadow-blue-500/20 mb-3 hover:scale-105 transition-transform">
                        <span class="text-2xl font-black text-white">{{ $stats['completed_evaluations'] }}</span>
                    </div>
                    <p class="text-xs font-bold text-gray-400 dark:text-slate-500 uppercase tracking-wider">Provas</p>
                </div>
            </div>
        </div>

        <!-- Data Tables -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Attendance History -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden flex flex-col">
                <div class="px-6 py-5 border-b border-gray-100 dark:border-slate-800 flex items-center justify-between bg-gray-50/50 dark:bg-slate-900/50">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-xl">
                            <x-icon name="clipboard-check" class="w-5 h-5" />
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 dark:text-white">Histórico de Frequência</h3>
                            <p class="text-xs text-gray-500 dark:text-slate-400">Suas presenças registradas</p>
                        </div>
                    </div>
                </div>
                <div class="flex-1 overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-slate-800/50">
                            <tr>
                                <th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 dark:text-slate-500 uppercase tracking-wider">Data</th>
                                <th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 dark:text-slate-500 uppercase tracking-wider">Lição</th>
                                <th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 dark:text-slate-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                            @forelse($attendance as $record)
                            <tr class="group hover:bg-emerald-50/50 dark:hover:bg-emerald-900/10 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white">
                                    {{ $record->lesson->lesson_date->format('d/m') }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-bold text-sm text-gray-900 dark:text-white block group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">{{ Str::limit($record->lesson->title, 30) }}</span>
                                    <span class="text-[10px] font-medium text-gray-400 dark:text-slate-500">{{ $record->lesson->ebdClass->name }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider rounded-lg border
                                        @if($record->status === 'present') bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800
                                        @elseif($record->status === 'late') bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 border-amber-200 dark:border-amber-800
                                        @elseif($record->status === 'excused') bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 border-blue-200 dark:border-blue-800
                                        @else bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400 border-rose-200 dark:border-rose-800 @endif">
                                        {{ $record->status_display }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-6 py-12 text-center">
                                    <x-icon name="clipboard-question" style="duotone" class="w-10 h-10 text-gray-200 dark:text-slate-700 mx-auto mb-3" />
                                    <p class="text-sm font-medium text-gray-400 dark:text-slate-500">Nenhum registro de frequência encontrado</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Evaluations History -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden flex flex-col">
                <div class="px-6 py-5 border-b border-gray-100 dark:border-slate-800 flex items-center justify-between bg-gray-50/50 dark:bg-slate-900/50">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 rounded-xl">
                            <x-icon name="file-check" class="w-5 h-5" />
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 dark:text-white">Avaliações</h3>
                            <p class="text-xs text-gray-500 dark:text-slate-400">Suas notas e resultados</p>
                        </div>
                    </div>
                </div>
                <div class="flex-1 overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-slate-800/50">
                            <tr>
                                <th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 dark:text-slate-500 uppercase tracking-wider">Lição</th>
                                <th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 dark:text-slate-500 uppercase tracking-wider">Nota</th>
                                <th class="px-6 py-4 text-right text-[10px] font-bold text-gray-400 dark:text-slate-500 uppercase tracking-wider">Ação</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                            @forelse($evaluations as $evaluation)
                            <tr class="group hover:bg-purple-50/50 dark:hover:bg-purple-900/10 transition-colors">
                                <td class="px-6 py-4">
                                    <span class="font-bold text-sm text-gray-900 dark:text-white block group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">{{ Str::limit($evaluation->lesson->title, 30) }}</span>
                                    <span class="text-[10px] font-medium text-gray-400 dark:text-slate-500">
                                        {{ $evaluation->completed_at ? $evaluation->completed_at->format('d/m/Y') : 'Em andamento' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($evaluation->score)
                                        <div class="flex items-center gap-2">
                                            <div class="w-1 h-6 bg-purple-500 rounded-full"></div>
                                            <span class="text-xl font-black text-purple-600 dark:text-purple-400">{{ $evaluation->score }}</span>
                                        </div>
                                    @else
                                        <span class="text-gray-300 dark:text-slate-600 font-bold">---</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('memberpanel.ebd.student.lessons.show', $evaluation->lesson) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 dark:bg-white text-white dark:text-gray-900 text-xs font-bold rounded-xl hover:bg-purple-600 hover:text-white dark:hover:bg-purple-600 dark:hover:text-white transition-all active:scale-95">
                                        Revisar <x-icon name="arrow-right" class="w-3 h-3" />
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-6 py-12 text-center">
                                    <x-icon name="pen-to-square" style="duotone" class="w-10 h-10 text-gray-200 dark:text-slate-700 mx-auto mb-3" />
                                    <p class="text-sm font-medium text-gray-400 dark:text-slate-500">Nenhuma avaliação encontrada</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
