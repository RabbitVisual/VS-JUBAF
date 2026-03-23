@extends('admin::components.layouts.master')

@section('title', 'Dashboard | EBD Academy')

@section('content')
<div class="space-y-8 animate-in fade-in duration-500">
    <!-- Premium Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-6 border-b border-gray-100 dark:border-gray-800">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="px-2 py-0.5 text-[10px] font-black uppercase tracking-widest bg-blue-600 text-white rounded">Admin</span>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Escola Bíblica Dominical</span>
            </div>
            <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight">Painel de <span class="text-blue-600">Gestão</span></h1>
            <p class="text-gray-500 dark:text-gray-400 mt-2 max-w-2xl font-medium">Gestão inteligente e acompanhamento pedagógico da Escola Bíblica.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.ebd.attendance.create') }}"
                class="inline-flex items-center px-6 py-3 rounded-xl bg-gray-950 dark:bg-white text-white dark:text-gray-950 text-sm font-bold transition-all hover:scale-105 active:scale-95 shadow-xl shadow-gray-950/20">
                <x-icon name="clipboard-user" style="duotone" class="mr-2 h-4 w-4" />
                Registrar Presença
            </a>
        </div>
    </div>

    <!-- Quick Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm transition-all hover:shadow-md">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                    <x-icon name="graduation-cap" style="duotone" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                </div>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Classes</span>
            </div>
            <div class="text-3xl font-black text-gray-900 dark:text-white">{{ $stats['total_classes'] }}</div>
            <div class="text-[10px] font-bold text-gray-400 mt-1 uppercase tracking-tighter">{{ $stats['active_classes'] }} em atividade</div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm transition-all hover:shadow-md">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-green-50 dark:bg-green-900/20 rounded-lg">
                    <x-icon name="users" style="duotone" class="w-5 h-5 text-green-600 dark:text-green-400" />
                </div>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Alunos</span>
            </div>
            <div class="text-3xl font-black text-gray-900 dark:text-white">{{ $stats['total_students'] }}</div>
            <div class="text-[10px] font-bold text-gray-400 mt-1 uppercase tracking-tighter">Matrículas ativas</div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm transition-all hover:shadow-md">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                    <x-icon name="chart-pie" style="duotone" class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                </div>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Assiduidade</span>
            </div>
            <div class="text-3xl font-black text-gray-900 dark:text-white">{{ $attendanceRate }}%</div>
            <div class="text-[10px] font-bold text-gray-400 mt-1 uppercase tracking-tighter">Presença média global</div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm transition-all hover:shadow-md">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-amber-50 dark:bg-amber-900/20 rounded-lg">
                    <x-icon name="clipboard-check" style="duotone" class="w-5 h-5 text-amber-600 dark:text-amber-400" />
                </div>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Avaliações</span>
            </div>
            <div class="text-3xl font-black text-gray-900 dark:text-white">{{ $stats['total_evaluations'] }}</div>
            <div class="text-[10px] font-bold text-gray-400 mt-1 uppercase tracking-tighter">{{ $stats['graded_evaluations'] }} corrigidas · Média {{ $stats['avg_score'] ?? 0 }}</div>
        </div>
    </div>

    <!-- Secondary Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-gradient-to-br from-indigo-600 to-blue-700 p-6 rounded-2xl shadow-lg shadow-indigo-500/20">
            <div class="flex items-center justify-between mb-3">
                <x-icon name="trophy" style="duotone" class="w-5 h-5 text-white/80" />
                <span class="text-[10px] font-bold text-white/60 uppercase tracking-widest">XP Total</span>
            </div>
            <div class="text-3xl font-black text-white">{{ number_format($stats['total_xp_distributed']) }}</div>
            <div class="text-[10px] font-bold text-white/50 mt-1 uppercase tracking-tighter">
                {{ $gamificationStats['active_players'] }} jogadores ativos este mês
            </div>
        </div>

        <div class="bg-gradient-to-br from-emerald-600 to-green-700 p-6 rounded-2xl shadow-lg shadow-emerald-500/20">
            <div class="flex items-center justify-between mb-3">
                <x-icon name="book-open-cover" style="duotone" class="w-5 h-5 text-white/80" />
                <span class="text-[10px] font-bold text-white/60 uppercase tracking-widest">Lições</span>
            </div>
            <div class="text-3xl font-black text-white">{{ $stats['completed_lessons'] }}/{{ $stats['total_lessons'] }}</div>
            <div class="text-[10px] font-bold text-white/50 mt-1 uppercase tracking-tighter">
                {{ $lessonCompletionRate }}% taxa de conclusão
            </div>
        </div>

        <div class="bg-gradient-to-br from-amber-500 to-orange-600 p-6 rounded-2xl shadow-lg shadow-amber-500/20">
            <div class="flex items-center justify-between mb-3">
                <x-icon name="chalkboard-user" style="duotone" class="w-5 h-5 text-white/80" />
                <span class="text-[10px] font-bold text-white/60 uppercase tracking-widest">Professores</span>
            </div>
            <div class="text-3xl font-black text-white">{{ $stats['total_teachers'] }}</div>
            <div class="text-[10px] font-bold text-white/50 mt-1 uppercase tracking-tighter">
                {{ $stats['upcoming_lessons'] }} lições agendadas
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-xl p-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest">Tendência de Presença</h3>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">Histórico de assiduidade (6 Meses)</p>
                </div>
                <div class="p-2 bg-blue-50 dark:bg-blue-900/20 rounded-xl">
                    <x-icon name="chart-line-up" style="duotone" class="w-4 h-4 text-blue-600" />
                </div>
            </div>
            <div id="attendanceTrendChart" class="min-h-[300px]"></div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-xl p-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest">Desempenho por Classe</h3>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">Frequência percentual por turma</p>
                </div>
                <div class="p-2 bg-green-50 dark:bg-green-900/20 rounded-xl">
                    <x-icon name="chart-user" style="duotone" class="w-4 h-4 text-green-600" />
                </div>
            </div>
            <div id="classPerformanceChart" class="min-h-[300px]"></div>
        </div>
    </div>

    <!-- Detailed Insights -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Upcoming Lessons -->
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-xl overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-50 dark:border-gray-800 flex items-center justify-between bg-gray-50/50 dark:bg-gray-800/30">
                <div class="flex items-center gap-3">
                    <x-icon name="calendar-clock" style="duotone" class="w-5 h-5 text-blue-600" />
                    <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest">Próximas Lições</h3>
                </div>
                <a href="{{ route('admin.ebd.lessons.index') }}" class="text-[10px] font-black text-blue-600 uppercase tracking-widest hover:underline">Ver Agenda Completa</a>
            </div>
            <div class="p-6 space-y-4">
                @forelse($upcomingLessons as $lesson)
                <div class="flex items-center justify-between p-4 bg-gray-50/50 dark:bg-gray-800/30 rounded-2xl border border-transparent hover:border-blue-500/20 transition-all hover:scale-[1.01] group">
                    <div class="flex items-center gap-4">
                        <div class="p-2 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col items-center justify-center min-w-[50px]">
                            <span class="text-[8px] font-black text-blue-600 uppercase">{{ $lesson->lesson_date->translatedFormat('M') }}</span>
                            <span class="text-base font-black text-gray-900 dark:text-white">{{ $lesson->lesson_date->format('d') }}</span>
                        </div>
                        <div>
                            <h4 class="text-sm font-black text-gray-900 dark:text-white tracking-tight uppercase group-hover:text-blue-600 transition-colors">{{ $lesson->title }}</h4>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="text-[10px] font-bold text-gray-400 uppercase">{{ $lesson->ebdClass->name }}</span>
                                <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">{{ $lesson->lesson_date->format('H:i') }}</span>
                            </div>
                        </div>
                    </div>
                    <div>
                        @if($lesson->status === 'scheduled')
                            <span class="px-2.5 py-1 bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400 rounded-lg text-[9px] font-black uppercase tracking-widest border border-blue-100 dark:border-blue-800">
                                Agendada
                            </span>
                        @else
                            <span class="px-2.5 py-1 bg-green-50 text-green-700 dark:bg-green-900/20 dark:text-green-400 rounded-lg text-[9px] font-black uppercase tracking-widest border border-green-100 dark:border-green-800">
                                {{ $lesson->status_display }}
                            </span>
                        @endif
                    </div>
                </div>
                @empty
                <div class="text-center py-12">
                    <x-icon name="calendar-xmark" style="duotone" class="w-10 h-10 text-gray-200 mx-auto mb-4" />
                    <p class="text-xs font-black text-gray-400 uppercase tracking-widest">Nenhuma lição agendada para os próximos dias.</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Demographics & Records -->
        <div class="space-y-6">
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-xl p-8">
                <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest mb-6">Mix de Idades</h3>
                <div class="space-y-5">
                    @php
                        $ageGroups = [
                            'children' => ['name' => 'Primários/Crianças', 'color' => 'bg-amber-500'],
                            'teen' => ['name' => 'Juniores/Adolescentes', 'color' => 'bg-purple-500'],
                            'youth' => ['name' => 'Jovens/Adultos', 'color' => 'bg-green-500'],
                            'adult' => ['name' => 'Casais/Geral', 'color' => 'bg-blue-500']
                        ];
                    @endphp
                    @foreach($ageGroups as $key => $info)
                    <div class="relative pt-1">
                        <div class="flex mb-2 items-center justify-between">
                            <div>
                                <span class="text-[10px] font-black uppercase tracking-widest inline-block py-1 text-gray-500 dark:text-gray-400">
                                    {{ $info['name'] }}
                                </span>
                            </div>
                            <div class="text-right">
                                <span class="text-xs font-black inline-block text-gray-900 dark:text-white">
                                    {{ $classesByAgeGroup[$key] ?? 0 }}
                                </span>
                            </div>
                        </div>
                        <div class="overflow-hidden h-1 text-xs flex rounded-full bg-gray-100 dark:bg-gray-800">
                            <div style="width:{{ $stats['total_classes'] > 0 ? (($classesByAgeGroup[$key] ?? 0) / $stats['total_classes']) * 100 : 0 }}%"
                                class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center {{ $info['color'] }}"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-xl p-8">
                <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest mb-6">Top Engajamento</h3>
                <div class="space-y-4">
                    @foreach($topClasses as $class)
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800/30 rounded-xl">
                        <div class="flex-1 min-w-0">
                            <span class="text-xs font-black text-gray-700 dark:text-gray-300 uppercase tracking-tight truncate block">{{ $class->name }}</span>
                            <span class="text-[9px] font-bold text-gray-400">{{ $class->active_students_count }} alunos · {{ $class->lessons_count }} lições</span>
                        </div>
                        <div class="flex items-center gap-2">
                             <span class="text-[10px] font-black text-green-600">{{ $class->recent_attendance_count }}</span>
                             <x-icon name="user-check" style="duotone" class="w-3 h-3 text-green-500" />
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-xl overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800 bg-rose-50/30 dark:bg-rose-900/10">
                    <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest flex items-center gap-2">
                        <x-icon name="user-xmark" style="duotone" class="w-4 h-4 text-rose-500" />
                        Quem está faltando?
                    </h3>
                    <p class="text-[10px] font-bold text-gray-500 dark:text-slate-400 mt-1">2+ faltas nos últimos 30 dias</p>
                </div>
                <div class="p-4 max-h-64 overflow-y-auto space-y-2">
                    @forelse($studentsWithAbsences ?? [] as $item)
                    <div class="flex items-center justify-between py-2 px-3 rounded-xl bg-gray-50 dark:bg-gray-800/50">
                        <div class="min-w-0">
                            <span class="text-xs font-bold text-gray-900 dark:text-white truncate block">{{ $item['student']->user->name ?? '—' }}</span>
                            <span class="text-[9px] text-gray-500 dark:text-slate-400">{{ $item['student']->ebdClass->name ?? '—' }}</span>
                        </div>
                        <span class="text-[10px] font-black text-rose-600 dark:text-rose-400 shrink-0">{{ $item['absences'] }} faltas</span>
                    </div>
                    @empty
                    <p class="text-xs text-gray-500 dark:text-slate-400 py-4 text-center">Nenhum aluno com 2+ faltas no período.</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-xl overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800 bg-amber-50/30 dark:bg-amber-900/10">
                    <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest flex items-center gap-2">
                        <x-icon name="chart-simple" style="duotone" class="w-4 h-4 text-amber-500" />
                        Média de notas por turma
                    </h3>
                </div>
                <div class="p-4 space-y-2">
                    @forelse($classAverageScores ?? [] as $row)
                    <div class="flex items-center justify-between py-2 px-3 rounded-xl bg-gray-50 dark:bg-gray-800/50">
                        <span class="text-xs font-bold text-gray-900 dark:text-white truncate">{{ $row['class_name'] }}</span>
                        <span class="text-[10px] font-black text-amber-600 dark:text-amber-400 shrink-0">{{ $row['avg_score'] }} ({{ $row['count'] }})</span>
                    </div>
                    @empty
                    <p class="text-xs text-gray-500 dark:text-slate-400 py-4 text-center">Nenhuma avaliação corrigida ainda.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Attendance Trend Chart
        const trendData = @json($monthlyAttendanceTrend);
        const optionsTrend = {
            series: [{
                name: 'Presença (%)',
                data: trendData.map(d => d.rate)
            }],
            chart: {
                type: 'area',
                height: 300,
                toolbar: { show: false },
                animations: { enabled: true, easing: 'easeinout', speed: 800 },
                fontFamily: 'Inter, sans-serif'
            },
            stroke: { curve: 'smooth', width: 4, colors: ['#2563eb'] },
            fill: {
                type: 'gradient',
                gradient: { shadeIntensity: 1, opacityFrom: 0.3, opacityTo: 0.05, stops: [0, 90, 100], colorStops: [
                    { offset: 0, color: '#2563eb', opacity: 0.3 },
                    { offset: 100, color: '#2563eb', opacity: 0 }
                ]}
            },
            colors: ['#2563eb'],
            xaxis: {
                categories: trendData.map(d => d.month),
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: { style: { colors: '#94a3b8', fontSize: '10px', fontWeight: 700 } }
            },
            yaxis: {
                labels: { style: { colors: '#94a3b8', fontSize: '10px', fontWeight: 700 } }
            },
            grid: { borderColor: 'rgba(148, 163, 184, 0.1)', strokeDashArray: 4 },
            dataLabels: { enabled: false },
            tooltip: { theme: 'dark' }
        };
        new ApexCharts(document.querySelector("#attendanceTrendChart"), optionsTrend).render();

        // Class Performance Chart
        const performanceData = @json($classPerformance);
        const optionsPerf = {
            series: [{
                name: 'Frequência (%)',
                data: performanceData.map(d => d.rate)
            }],
            chart: {
                type: 'bar',
                height: 300,
                toolbar: { show: false },
                fontFamily: 'Inter, sans-serif'
            },
            plotOptions: {
                bar: {
                    borderRadius: 12,
                    columnWidth: '45%',
                    distributed: true,
                    dataLabels: { position: 'top' }
                }
            },
            colors: ['#2563eb', '#10b981', '#f59e0b', '#8b5cf6', '#ef4444', '#0ea5e9'],
            xaxis: {
                categories: performanceData.map(d => d.name),
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: { show: false }
            },
            grid: { show: false },
            legend: { show: false },
            dataLabels: {
                enabled: true,
                formatter: function (val) { return val + "%" },
                offsetY: -20,
                style: { fontSize: '10px', fontWeight: '900', colors: ['#94a3b8'] }
            },
            tooltip: { theme: 'dark' }
        };
        new ApexCharts(document.querySelector("#classPerformanceChart"), optionsPerf).render();
    });
</script>
@endsection

