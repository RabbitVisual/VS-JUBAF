@extends('liderancapanel::components.layouts.master')

@section('title', 'Relatórios Financeiros')

@section('content')
    <div class="space-y-6">
        <div
            class="rounded-3xl bg-gradient-to-br from-slate-800 via-slate-900 to-slate-800 text-white shadow-xl border border-amber-900/30 overflow-hidden">
            <div class="p-6 md:p-8 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div>
                    <nav class="flex items-center gap-2 text-sm text-slate-400 font-medium mb-2">
                        <a href="{{ route('lideranca.tesouraria.dashboard') }}"
                            class="hover:text-white transition-colors">Tesouraria</a>
                        <x-icon name="chevron-right" class="w-3.5 h-3.5" />
                        <span class="text-white font-bold">Relatórios</span>
                    </nav>
                    <h1 class="text-2xl md:text-3xl font-bold tracking-tight mb-1">Inteligência Financeira</h1>
                    <p class="text-slate-300 text-sm max-w-xl">
                        De <strong>{{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}</strong>
                        até <strong>{{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</strong>
                        ({{ $daysDiff }} {{ $daysDiff == 1 ? 'dia' : 'dias' }})
                    </p>
                </div>
                @if ($permission->canExportData())
                    <div class="flex flex-wrap items-center gap-3">
                        <a href="{{ route('lideranca.tesouraria.reports.export.pdf', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 border border-white/20 text-white font-medium hover:bg-white/20 transition-colors text-sm"
                            title="Relatório completo em PDF">
                            <x-icon name="file-pdf" class="w-5 h-5" /> PDF
                        </a>
                        <a href="{{ route('lideranca.tesouraria.reports.export.excel', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-medium transition-colors text-sm">
                            <x-icon name="file-excel" class="w-5 h-5" /> Excel
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <form method="GET" action="{{ route('lideranca.tesouraria.reports.index') }}"
            class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-5">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div class="space-y-2">
                    <label for="start_date"
                        class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Data
                        inicial</label>
                    <input type="date" name="start_date" id="start_date" value="{{ $startDate }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                </div>
                <div class="space-y-2">
                    <label for="end_date"
                        class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Data
                        final</label>
                    <input type="date" name="end_date" id="end_date" value="{{ $endDate }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                </div>
                <div>
                    <button type="submit"
                        class="w-full px-4 py-2.5 bg-slate-800 dark:bg-amber-600 text-white text-sm font-bold rounded-xl hover:bg-slate-700 dark:hover:bg-amber-700 transition-all inline-flex items-center justify-center gap-2">
                        <x-icon name="magnifying-glass" class="w-4 h-4" /> Atualizar
                    </button>
                </div>
            </div>
        </form>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-6">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total receitas
                </p>
                <p class="mt-1 text-2xl font-bold text-green-600 dark:text-green-400 tabular-nums">R$
                    {{ number_format($totalIncome, 2, ',', '.') }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $totalIncomeEntries }} lançamento(s)</p>
            </div>
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-6">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total despesas
                </p>
                <p class="mt-1 text-2xl font-bold text-red-600 dark:text-red-400 tabular-nums">R$
                    {{ number_format($totalExpense, 2, ',', '.') }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $totalExpenseEntries }} lançamento(s)</p>
            </div>
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-6">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Saldo do período
                </p>
                <p
                    class="mt-1 text-2xl font-bold {{ $balance >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }} tabular-nums">
                    R$ {{ number_format($balance, 2, ',', '.') }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $totalEntries }} lançamento(s) no total</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div
                class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-700/50">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Receitas por
                        categoria</h3>
                </div>
                <div class="p-5">
                    @if ($incomeByCategory->count() > 0)
                        <div class="space-y-3">
                            @foreach ($incomeByCategory as $item)
                                <div class="flex items-center justify-between">
                                    <span
                                        class="text-sm font-medium text-gray-700 dark:text-gray-300 capitalize">{{ str_replace('_', ' ', $item->category) }}</span>
                                    <span class="text-sm font-bold text-gray-900 dark:text-white">R$
                                        {{ number_format($item->total, 2, ',', '.') }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400 py-4">Nenhuma receita no período.</p>
                    @endif
                </div>
            </div>
            <div
                class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-700/50">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Despesas por
                        categoria</h3>
                </div>
                <div class="p-5">
                    @if ($expenseByCategory->count() > 0)
                        <div class="space-y-3">
                            @foreach ($expenseByCategory as $item)
                                <div class="flex items-center justify-between">
                                    <span
                                        class="text-sm font-medium text-gray-700 dark:text-gray-300 capitalize">{{ str_replace('_', ' ', $item->category) }}</span>
                                    <span class="text-sm font-bold text-gray-900 dark:text-white">R$
                                        {{ number_format($item->total, 2, ',', '.') }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400 py-4">Nenhuma despesa no período.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
