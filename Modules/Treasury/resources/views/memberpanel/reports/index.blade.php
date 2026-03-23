@extends('memberpanel::components.layouts.master')

@section('page-title', 'Tesouraria - Relatórios')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-slate-950 transition-colors duration-200 pb-12">
        <div class="max-w-7xl mx-auto space-y-8 px-6 pt-8" data-tour="treasury-area">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Relatórios</h1>
                    <p class="text-gray-500 dark:text-slate-400 mt-1 max-w-md">Performance analítica e exportação por período.</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="px-4 py-2 bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-xl shadow-sm flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                        <span class="text-xs font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider">Tesouraria</span>
                    </div>
                    @if ($permission->canExportData())
                        <a href="{{ route('memberpanel.treasury.reports.export.pdf', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-bold border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-800">PDF</a>
                        <a href="{{ route('memberpanel.treasury.reports.export.excel', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-bold">Excel</a>
                    @endif
                </div>
            </div>

            <div class="relative overflow-hidden bg-white dark:bg-slate-900 rounded-3xl shadow-xl border border-gray-100 dark:border-slate-800">
                <div class="absolute inset-0 opacity-20 dark:opacity-40 pointer-events-none">
                    <div class="absolute -top-24 -left-20 w-96 h-96 bg-indigo-400 dark:bg-indigo-600 rounded-full blur-[100px]"></div>
                    <div class="absolute top-1/2 -right-20 w-80 h-80 bg-emerald-400 dark:bg-emerald-600 rounded-full blur-[100px]"></div>
                </div>
                <div class="relative px-8 py-10 z-10">
                    <p class="text-gray-600 dark:text-slate-300 font-medium">{{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }} ({{ $daysDiff }} {{ $daysDiff == 1 ? 'dia' : 'dias' }})</p>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-100 dark:border-slate-800 flex items-center gap-3 bg-gray-50/50 dark:bg-slate-900/50">
                    <div class="p-2 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-xl">
                        <x-icon name="filters" style="duotone" class="w-5 h-5" />
                    </div>
                    <h3 class="text-xs font-black uppercase tracking-widest text-gray-700 dark:text-slate-300">Filtro por período</h3>
                </div>
                <div class="p-8">
                <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-8">
                    <div class="space-y-6 flex-1">

                <div class="flex flex-wrap gap-2">
                    @foreach(['today' => 'Hoje', 'week' => 'Esta Semana', 'month' => 'Este Mês', 'year' => 'Este Ano'] as $key => $label)
                        <button type="button" onclick="setPeriod('{{ $key }}')"
                            class="px-5 py-2.5 text-[10px] font-black text-slate-600 dark:text-slate-400 bg-slate-50 dark:bg-slate-800 hover:bg-indigo-500 hover:text-white dark:hover:bg-indigo-600 dark:hover:text-white rounded-xl transition-all uppercase tracking-[0.15em] border border-slate-100 dark:border-slate-700/50">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>

            <form method="GET" action="{{ route('memberpanel.treasury.reports.index') }}" class="flex flex-col md:flex-row items-end gap-6">
                <div class="space-y-2 w-full md:w-48">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Data de Início</label>
                    <input type="date" name="start_date" id="start_date" value="{{ $startDate }}"
                        class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-slate-900 dark:text-white font-black text-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all">
                </div>
                <div class="space-y-2 w-full md:w-48">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Data de Término</label>
                    <input type="date" name="end_date" id="end_date" value="{{ $endDate }}"
                        class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-slate-900 dark:text-white font-black text-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all">
                </div>
                <div class="flex gap-3 w-full md:w-auto">
                    <button type="submit"
                        class="flex-1 md:flex-none px-10 py-4 bg-indigo-500 hover:bg-indigo-600 text-white font-black rounded-2xl shadow-lg shadow-indigo-500/20 transition-all hover:-translate-y-1 active:scale-95 text-sm uppercase tracking-widest">
                        Filtrar
                    </button>
                    <a href="{{ route('memberpanel.treasury.reports.index') }}"
                        class="p-4 bg-slate-100 dark:bg-slate-800 text-slate-400 hover:text-rose-500 rounded-2xl transition-colors">
                        <x-icon name="rotate-right" style="duotone" class="w-5 h-5" />
                    </a>
                </div>
            </form>
        </div>
                </div>
            </div>

            <!-- KPI Summary Section -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @php
            $kpis = [
                [
                    'label' => 'Total Receitas',
                    'value' => 'R$ ' . number_format($totalIncome, 2, ',', '.'),
                    'sub' => $totalIncomeEntries . ' entradas',
                    'icon' => 'money-bill-trend-up',
                    'color' => 'emerald',
                    'trend_icon' => 'arrow-up-to-line'
                ],
                [
                    'label' => 'Total Despesas',
                    'value' => 'R$ ' . number_format($totalExpense, 2, ',', '.'),
                    'sub' => $totalExpenseEntries . ' saídas',
                    'icon' => 'money-bill-transfer',
                    'color' => 'rose',
                    'trend_icon' => 'arrow-down-to-line'
                ],
                [
                    'label' => 'Saldo Período',
                    'value' => 'R$ ' . number_format($balance, 2, ',', '.'),
                    'sub' => 'Resultado operacional',
                    'icon' => 'scale-balanced',
                    'color' => $balance >= 0 ? 'indigo' : 'orange',
                    'trend_icon' => $balance >= 0 ? 'chart-line' : 'chart-line-down'
                ],
                [
                    'label' => 'Volume de Dados',
                    'value' => number_format($totalEntries, 0, ',', '.'),
                    'sub' => 'Registros processados',
                    'icon' => 'database',
                    'color' => 'slate',
                    'trend_icon' => 'list-check'
                ]
            ];
        @endphp

        @foreach($kpis as $kpi)
            <div class="group relative bg-white dark:bg-slate-900 rounded-3xl p-6 border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="absolute -top-12 -right-12 w-40 h-40 bg-{{ $kpi['color'] }}-500/5 rounded-full blur-3xl group-hover:bg-{{ $kpi['color'] }}-500/10 transition-colors"></div>

                <div class="relative flex flex-col items-center text-center space-y-4">
                    <div class="w-16 h-16 rounded-2xl bg-{{ $kpi['color'] }}-500/10 flex items-center justify-center text-{{ $kpi['color'] }}-500 ring-1 ring-{{ $kpi['color'] }}-500/20 group-hover:rotate-6 transition-transform">
                        <x-icon name="{{ $kpi['icon'] }}" style="duotone" class="w-8 h-8" />
                    </div>

                    <div class="space-y-1">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">{{ $kpi['label'] }}</p>
                        <h4 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">{{ $kpi['value'] }}</h4>
                    </div>

                    <div class="flex items-center gap-2 px-4 py-1.5 bg-slate-50 dark:bg-slate-800/50 rounded-full border border-slate-100 dark:border-slate-700/50">
                        <x-icon name="{{ $kpi['trend_icon'] }}" style="duotone" class="w-3.5 h-3.5 text-{{ $kpi['color'] }}-500" />
                        <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400">{{ $kpi['sub'] }}</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Distribution Analytics -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Income Breakdown -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-100 dark:border-slate-800 flex items-center gap-3 bg-gray-50/50 dark:bg-slate-900/50">
                <div class="p-2 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-xl">
                    <x-icon name="chart-pie" style="duotone" class="w-5 h-5" />
                </div>
                <h3 class="text-xs font-black uppercase tracking-widest text-gray-700 dark:text-slate-300">Receitas por categoria</h3>
            </div>

            <div class="p-8 md:p-12">
                @if ($incomeByCategory->count() > 0)
                    <div class="space-y-8">
                        @foreach ($incomeByCategory as $item)
                            <div class="group">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                                        <span class="text-sm font-black text-slate-700 dark:text-slate-300 capitalize tracking-wide">
                                            {{ str_replace('_', ' ', $item->category) }}
                                        </span>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-sm font-black text-slate-900 dark:text-white block">
                                            R$ {{ number_format($item->total, 2, ',', '.') }}
                                        </span>
                                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                            {{ number_format($item->percentage, 1) }}% do total
                                        </span>
                                    </div>
                                </div>
                                <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-3 overflow-hidden border border-slate-200/50 dark:border-slate-700/50">
                                    <div class="bg-linear-to-r from-emerald-500 to-teal-500 h-full rounded-full transition-all duration-1000 group-hover:brightness-110 shadow-lg shadow-emerald-500/20"
                                        style="width: {{ min(100, $item->percentage) }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center py-16 px-8 text-center bg-slate-50 dark:bg-slate-800/20 rounded-4xl border-2 border-dashed border-slate-200 dark:border-slate-700">
                        <x-icon name="empty-set" style="duotone" class="w-16 h-16 text-slate-300 mb-4" />
                        <p class="text-slate-500 dark:text-slate-400 font-bold tracking-wide">Sem dados de receita para este intervalo.</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-100 dark:border-slate-800 flex items-center gap-3 bg-gray-50/50 dark:bg-slate-900/50">
                <div class="p-2 bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 rounded-xl">
                    <x-icon name="chart-simple" style="duotone" class="w-5 h-5" />
                </div>
                <h3 class="text-xs font-black uppercase tracking-widest text-gray-700 dark:text-slate-300">Despesas por categoria</h3>
            </div>

            <div class="p-8 md:p-12">
                @if ($expenseByCategory->count() > 0)
                    <div class="space-y-8">
                        @foreach ($expenseByCategory as $item)
                            <div class="group">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-2 h-2 rounded-full bg-rose-500"></div>
                                        <span class="text-sm font-black text-slate-700 dark:text-slate-300 capitalize tracking-wide">
                                            {{ str_replace('_', ' ', $item->category) }}
                                        </span>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-sm font-black text-slate-900 dark:text-white block">
                                            R$ {{ number_format($item->total, 2, ',', '.') }}
                                        </span>
                                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                            {{ number_format($item->percentage, 1) }}% do total
                                        </span>
                                    </div>
                                </div>
                                <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-3 overflow-hidden border border-slate-200/50 dark:border-slate-700/50">
                                    <div class="bg-linear-to-r from-rose-500 to-orange-500 h-full rounded-full transition-all duration-1000 group-hover:brightness-110 shadow-lg shadow-rose-500/20"
                                        style="width: {{ min(100, $item->percentage) }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center py-16 px-8 text-center bg-slate-50 dark:bg-slate-800/20 rounded-4xl border-2 border-dashed border-slate-200 dark:border-slate-700">
                        <x-icon name="folder-open" style="duotone" class="w-16 h-16 text-slate-300 mb-4" />
                        <p class="text-slate-500 dark:text-slate-400 font-bold tracking-wide">Sem dados de despesa para este intervalo.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
        </div>
    </div>

<script>
    function setPeriod(period) {
        const startInput = document.getElementById('start_date');
        const endInput = document.getElementById('end_date');
        const today = new Date();
        let startDate, endDate;

        switch (period) {
            case 'today':
                startDate = endDate = today.toISOString().split('T')[0];
                break;
            case 'week':
                const dayOfWeek = today.getDay();
                const diff = today.getDate() - dayOfWeek + (dayOfWeek === 0 ? -6 : 1);
                startDate = new Date(today.setDate(diff)).toISOString().split('T')[0];
                endDate = new Date().toISOString().split('T')[0];
                break;
            case 'month':
                startDate = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().split('T')[0];
                endDate = new Date().toISOString().split('T')[0];
                break;
            case 'year':
                startDate = new Date(today.getFullYear(), 0, 1).toISOString().split('T')[0];
                endDate = new Date().toISOString().split('T')[0];
                break;
        }

        startInput.value = startDate;
        endInput.value = endDate;
        startInput.closest('form').submit();
    }
</script>
@endsection
