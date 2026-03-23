@extends('memberpanel::components.layouts.master')

@section('page-title', 'Tesouraria - Dashboard')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-slate-950 transition-colors duration-200 pb-12">
        <div class="max-w-7xl mx-auto space-y-8 px-6 pt-8">

            <!-- Header (padrão MemberPanel) -->
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Tesouraria</h1>
                    <p class="text-gray-500 dark:text-slate-400 mt-1 max-w-md">Visão geral financeira, campanhas e transparência.</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="px-4 py-2 bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-xl shadow-sm flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                        <span class="text-xs font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider">Tesouraria</span>
                    </div>
                </div>
            </div>

            <!-- Hero Card (mesmo padrão transparência/dashboard) -->
            <div class="relative overflow-hidden bg-white dark:bg-slate-900 rounded-3xl shadow-xl dark:shadow-2xl border border-gray-100 dark:border-slate-800 transition-colors duration-200" data-tour="treasury-area">
                <div class="absolute inset-0 opacity-20 dark:opacity-40 pointer-events-none">
                    <div class="absolute -top-24 -left-20 w-96 h-96 bg-indigo-400 dark:bg-indigo-600 rounded-full blur-[100px]"></div>
                    <div class="absolute top-1/2 -right-20 w-80 h-80 bg-emerald-400 dark:bg-emerald-600 rounded-full blur-[100px]"></div>
                </div>
                <div class="relative px-8 py-10 flex flex-col md:flex-row md:items-center justify-between gap-8 z-10">
                    <div class="flex-1">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-100 dark:border-indigo-800 mb-4">
                            <x-icon name="chart-mixed" style="duotone" class="w-3 h-3 text-indigo-600 dark:text-indigo-400" />
                            <span class="text-[10px] font-black uppercase tracking-widest text-indigo-600 dark:text-indigo-400">Visão geral</span>
                        </div>
                        <p class="text-gray-500 dark:text-slate-300 font-medium max-w-xl text-lg leading-relaxed">
                            Monitoramento financeiro, gestão de campanhas e transparência ministerial em tempo real.
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center gap-3 shrink-0">
                        <a href="{{ route('memberpanel.treasury.transparency') }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-gray-100 dark:bg-slate-800 hover:bg-gray-200 dark:hover:bg-slate-700 text-gray-900 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl text-sm font-bold transition-all">
                            <x-icon name="eye" style="duotone" class="w-4 h-4 mr-2" />
                            Transparência
                        </a>
                        <a href="{{ route('treasury.reports.contribution-receipt', ['member_id' => auth()->id(), 'year' => now()->year]) }}" target="_blank" class="inline-flex items-center justify-center px-5 py-2.5 bg-white dark:bg-slate-800 hover:bg-gray-50 dark:hover:bg-slate-700 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800 rounded-xl text-sm font-bold transition-all">
                            <x-icon name="document-arrow-down" style="duotone" class="w-4 h-4 mr-2" />
                            Baixar Recibo {{ now()->year }}
                        </a>
                        @if($permission->canViewReports())
                        <a href="{{ route('memberpanel.treasury.reports.index') }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-gray-100 dark:bg-slate-800 hover:bg-gray-200 dark:hover:bg-slate-700 text-gray-900 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl text-sm font-bold transition-all">
                            <x-icon name="file-chart-pie" style="duotone" class="w-4 h-4 mr-2" />
                            Relatórios
                        </a>
                        @endif
                        @if($permission->canCreateEntries())
                        <a href="{{ route('memberpanel.treasury.entries.create') }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-bold transition-all shadow-lg shadow-indigo-500/20">
                            <x-icon name="plus" style="duotone" class="w-4 h-4 mr-2" />
                            Novo lançamento
                        </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Statistics Grid (padrão transparência) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        @php
            $stats = [
                [
                    'label' => 'Receita do Mês',
                    'value' => 'R$ ' . number_format($monthlyIncome, 2, ',', '.'),
                    'sub' => 'Entrada Mensal',
                    'icon' => 'money-bills',
                    'trend_icon' => 'arrow-up-right-dots',
                    'color' => 'emerald'
                ],
                [
                    'label' => 'Despesa do Mês',
                    'value' => 'R$ ' . number_format($monthlyExpense, 2, ',', '.'),
                    'sub' => 'Saída Mensal',
                    'icon' => 'credit-card-blank',
                    'trend_icon' => 'arrow-down-right-dots',
                    'color' => 'rose'
                ],
                [
                    'label' => 'Saldo Operacional',
                    'value' => 'R$ ' . number_format($monthlyBalance, 2, ',', '.'),
                    'sub' => 'Equilíbrio Mensal',
                    'icon' => 'wallet',
                    'trend_icon' => 'scale-balanced',
                    'color' => $monthlyBalance >= 0 ? 'indigo' : 'orange'
                ],
                [
                    'label' => 'Acumulado Anual',
                    'value' => 'R$ ' . number_format($yearlyBalance, 2, ',', '.'),
                    'sub' => 'Saldo Consolidado',
                    'icon' => 'vault',
                    'trend_icon' => 'chart-user',
                    'color' => $yearlyBalance >= 0 ? 'purple' : 'rose'
                ],
            ];
        @endphp

        @foreach($stats as $stat)
            <div class="group relative bg-white dark:bg-slate-900 rounded-3xl p-6 border border-gray-100 dark:border-slate-800 shadow-sm hover:shadow-xl transition-all duration-300">
                <div class="flex items-start justify-between mb-2">
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-slate-500">{{ $stat['label'] }}</p>
                    <div class="w-12 h-12 rounded-2xl bg-{{ $stat['color'] }}-50 dark:bg-{{ $stat['color'] }}-900/20 flex items-center justify-center text-{{ $stat['color'] }}-600 dark:text-{{ $stat['color'] }}-400 group-hover:scale-110 transition-transform duration-300">
                        <x-icon name="{{ $stat['icon'] }}" style="duotone" class="w-6 h-6" />
                    </div>
                </div>
                <h3 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight tabular-nums">{{ $stat['value'] }}</h3>
                <p class="text-xs font-bold text-gray-500 dark:text-slate-400 mt-1 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-{{ $stat['color'] }}-500"></span>
                    {{ $stat['sub'] }}
                </p>
            </div>
        @endforeach
            </div>

            <!-- Minhas Contribuições (CBAV2026) -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-100 dark:border-slate-800 flex items-center justify-between bg-gray-50/50 dark:bg-slate-900/50">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-xl">
                            <x-icon name="receipt" style="duotone" class="w-5 h-5" />
                        </div>
                        <h3 class="text-xs font-black uppercase tracking-widest text-gray-700 dark:text-slate-300">Minhas Contribuições</h3>
                    </div>
                </div>
                <div class="p-8">
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">Baixe seu comprovante anual de contribuição (dízimos e ofertas) para fins de declaração.</p>
                    <div class="flex flex-wrap gap-3">
                        @foreach([now()->year, now()->year - 1, now()->year - 2] as $y)
                            <a href="{{ route('treasury.reports.contribution-receipt', ['member_id' => auth()->id(), 'year' => $y]) }}" target="_blank"
                                class="inline-flex items-center justify-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-bold transition-all">
                                <x-icon name="document-arrow-down" style="duotone" class="w-4 h-4 mr-2" />
                                Baixar Recibo {{ $y }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Charts and Tables -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Income by Category -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden">
                    <div class="px-8 py-6 border-b border-gray-100 dark:border-slate-800 flex items-center gap-3 bg-gray-50/50 dark:bg-slate-900/50">
                        <div class="p-2 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-xl">
                            <x-icon name="chart-pie" style="duotone" class="w-5 h-5" />
                        </div>
                        <h3 class="text-xs font-black uppercase tracking-widest text-gray-700 dark:text-slate-300">Receitas por categoria</h3>
                    </div>

            <div class="p-8 md:p-12">
                @if($incomeByCategory->count() > 0)
                    <div class="space-y-8">
                        @foreach($incomeByCategory as $item)
                        @php $percentage = $monthlyIncome > 0 ? ($item->total / $monthlyIncome) * 100 : 0; @endphp
                        <div class="group">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-sm font-black text-slate-700 dark:text-slate-300 capitalize tracking-wide">{{ str_replace('_', ' ', $item->category) }}</span>
                                <div class="text-right">
                                    <span class="text-sm font-black text-slate-900 dark:text-white block">R$ {{ number_format($item->total, 2, ',', '.') }}</span>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ number_format($percentage, 1) }}%</span>
                                </div>
                            </div>
                            <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-3 overflow-hidden border border-slate-200/50 dark:border-slate-700/50">
                                <div class="h-full rounded-full bg-linear-to-r from-emerald-500 to-teal-500 transition-all duration-1000 group-hover:brightness-110 shadow-lg shadow-emerald-500/20"
                                     style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center py-16 px-8 text-center bg-slate-50 dark:bg-slate-800/20 rounded-4xl border-2 border-dashed border-slate-200 dark:border-slate-700">
                        <x-icon name="empty-set" style="duotone" class="w-16 h-16 text-slate-300 mb-4" />
                        <p class="text-slate-500 dark:text-slate-400 font-bold tracking-wide">Nenhuma receita registrada este mês.</p>
                    </div>
                @endif
            </div>
        </div>

                <!-- Expense by Category -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden">
                    <div class="px-8 py-6 border-b border-gray-100 dark:border-slate-800 flex items-center gap-3 bg-gray-50/50 dark:bg-slate-900/50">
                        <div class="p-2 bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 rounded-xl">
                            <x-icon name="chart-simple" style="duotone" class="w-5 h-5" />
                        </div>
                        <h3 class="text-xs font-black uppercase tracking-widest text-gray-700 dark:text-slate-300">Despesas por categoria</h3>
                    </div>

            <div class="p-8 md:p-12">
                @if($expenseByCategory->count() > 0)
                    <div class="space-y-8">
                        @foreach($expenseByCategory as $item)
                        @php $percentage = $monthlyExpense > 0 ? ($item->total / $monthlyExpense) * 100 : 0; @endphp
                        <div class="group">
                             <div class="flex items-center justify-between mb-3">
                                <span class="text-sm font-black text-slate-700 dark:text-slate-300 capitalize tracking-wide">{{ str_replace('_', ' ', $item->category) }}</span>
                                <div class="text-right">
                                    <span class="text-sm font-black text-slate-900 dark:text-white block">R$ {{ number_format($item->total, 2, ',', '.') }}</span>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ number_format($percentage, 1) }}%</span>
                                </div>
                            </div>
                             <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-3 overflow-hidden border border-slate-200/50 dark:border-slate-700/50">
                                <div class="h-full rounded-full bg-linear-to-r from-red-500 to-rose-500 transition-all duration-1000 group-hover:brightness-110 shadow-lg shadow-rose-500/20"
                                     style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center py-16 px-8 text-center bg-slate-50 dark:bg-slate-800/20 rounded-4xl border-2 border-dashed border-slate-200 dark:border-slate-700">
                        <x-icon name="receipt" style="duotone" class="w-16 h-16 text-slate-300 mb-4" />
                        <p class="text-slate-500 dark:text-slate-400 font-bold tracking-wide">Nenhuma despesa registrada este mês.</p>
                    </div>
                @endif
            </div>
                </div>
            </div>

            <!-- Active Campaigns -->
            @if($activeCampaigns->count() > 0)
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-100 dark:border-slate-800 flex items-center justify-between bg-gray-50/50 dark:bg-slate-900/50">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 rounded-xl">
                            <x-icon name="bullhorn" style="duotone" class="w-5 h-5" />
                        </div>
                        <h3 class="text-xs font-black uppercase tracking-widest text-gray-700 dark:text-slate-300">Campanhas ativas</h3>
                    </div>
                    <a href="{{ route('memberpanel.treasury.campaigns.index') }}" class="text-sm font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                        Ver todas
                    </a>
                </div>

        <div class="p-8 md:p-12">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                @foreach($activeCampaigns as $campaign)
                <div class="bg-slate-50 dark:bg-slate-800/40 rounded-4xl p-8 border border-slate-100 dark:border-slate-700 hover:border-indigo-200 dark:hover:border-indigo-800 transition-all group">
                    <div class="flex justify-between items-start mb-6">
                        <h4 class="font-black text-slate-900 dark:text-white text-xl">{{ $campaign->name }}</h4>
                        <span class="px-3 py-1 text-[10px] font-black rounded-lg bg-emerald-500/10 text-emerald-600 border border-emerald-500/20 uppercase tracking-widest">
                            Ativa
                        </span>
                    </div>

                    <div class="space-y-6">
                         <div class="flex justify-between items-end">
                            <div>
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Arrecadado</span>
                                <span class="font-black text-emerald-600 dark:text-emerald-400 text-2xl tabular-nums">R$ {{ number_format($campaign->current_amount, 2, ',', '.') }}</span>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Progresso</span>
                                <span class="font-black text-indigo-500 text-lg">{{ number_format($campaign->progress_percentage, 1) }}%</span>
                            </div>
                        </div>

                        @if($campaign->target_amount)
                        <div class="space-y-2">
                             <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-3 overflow-hidden border border-slate-300/30 dark:border-slate-600/30">
                                <div class="bg-linear-to-r from-indigo-500 via-purple-500 to-fuchsia-500 h-full rounded-full transition-all duration-[1.5s] ease-out shadow-lg shadow-indigo-500/20"
                                     style="width: {{ min(100, $campaign->progress_percentage) }}%"></div>
                            </div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest text-center italic">Meta: R$ {{ number_format($campaign->target_amount, 2, ',', '.') }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

            <!-- Active Goals -->
            @if($activeGoals->count() > 0)
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-100 dark:border-slate-800 flex items-center justify-between bg-gray-50/50 dark:bg-slate-900/50">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-xl">
                            <x-icon name="bullseye-arrow" style="duotone" class="w-5 h-5" />
                        </div>
                        <h3 class="text-xs font-black uppercase tracking-widest text-gray-700 dark:text-slate-300">Metas</h3>
                    </div>
                    <a href="{{ route('memberpanel.treasury.goals.index') }}" class="text-sm font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                        Ver todas
                    </a>
                </div>

        <div class="p-8 md:p-12">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($activeGoals as $goal)
                @php
                    $gColor = $goal->color ?? 'indigo';
                    $gIcon = $goal->icon ?? 'flag-checkered';
                @endphp
                <a href="{{ route('memberpanel.treasury.goals.show', $goal) }}" class="group bg-slate-50 dark:bg-slate-800/40 rounded-4xl p-8 border border-slate-100 dark:border-slate-700 hover:border-{{ $gColor }}-200 dark:hover:border-{{ $gColor }}-800 transition-all">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-12 h-12 rounded-2xl bg-{{ $gColor }}-500/10 flex items-center justify-center text-{{ $gColor }}-500 ring-1 ring-{{ $gColor }}-500/20 group-hover:scale-110 transition-transform">
                            <x-icon name="{{ $gIcon }}" style="duotone" class="w-6 h-6" />
                        </div>
                        <div class="flex-1">
                            <h4 class="font-black text-slate-800 dark:text-white text-lg group-hover:text-{{ $gColor }}-600 transition-colors line-clamp-1">{{ $goal->name }}</h4>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Até {{ $goal->end_date->format('d/m/Y') }}</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                         <div class="flex justify-between items-end">
                            <div class="text-2xl font-black text-slate-900 dark:text-white tabular-nums">
                                {{ number_format($goal->progress_percentage, 1) }}<span class="text-xs font-bold text-slate-400 ml-0.5">%</span>
                            </div>
                            <div class="text-right">
                                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-0.5">Saldo Atual</span>
                                <span class="font-black text-{{ $gColor }}-600 dark:text-{{ $gColor }}-400 text-sm tabular-nums">R$ {{ number_format($goal->current_amount, 2, ',', '.') }}</span>
                            </div>
                        </div>

                        <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2 overflow-hidden border border-slate-300/30 dark:border-slate-600/30">
                            <div class="bg-linear-to-r from-{{ $gColor }}-500 to-{{ $gColor }}-400 h-full rounded-full transition-all duration-[1s] ease-out shadow-lg shadow-{{ $gColor }}-500/20"
                                    style="width: {{ min(100, $goal->progress_percentage) }}%"></div>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </div>
    @endif
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-100 dark:border-slate-800 flex items-center justify-between bg-gray-50/50 dark:bg-slate-900/50">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-xl">
                            <x-icon name="arrow-right-arrow-left" style="duotone" class="w-5 h-5" />
                        </div>
                        <h3 class="text-xs font-black uppercase tracking-widest text-gray-700 dark:text-slate-300">Últimas transações</h3>
                    </div>
                    <a href="{{ route('memberpanel.treasury.entries.index') }}" class="text-sm font-bold text-blue-600 dark:text-blue-400 hover:underline">
                        Fluxo completo
                    </a>
                </div>

        @if($recentEntries->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50/80 dark:bg-slate-800/50">
                        <tr>
                            <th class="px-8 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Data</th>
                            <th class="px-8 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Fluxo</th>
                            <th class="px-8 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Descrição / Origem</th>
                            <th class="px-8 py-5 text-right text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Montante</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                        @foreach($recentEntries as $entry)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-all group">
                            <td class="px-8 py-6 whitespace-nowrap text-sm font-bold text-slate-500 dark:text-slate-400">
                                {{ $entry->entry_date->format('d/m/Y') }}
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap">
                                <span class="px-3 py-1.5 inline-flex text-[10px] font-black rounded-lg border uppercase tracking-widest
                                    {{ $entry->type === 'income' ? 'bg-emerald-500/10 text-emerald-600 border-emerald-500/20' : 'bg-rose-500/10 text-rose-600 border-rose-500/20' }}">
                                    <x-icon name="{{ $entry->type === 'income' ? 'circle-arrow-up' : 'circle-arrow-down' }}" style="duotone" class="w-3.5 h-3.5 mr-1.5" />
                                    {{ $entry->type === 'income' ? 'Entrada' : 'Saída' }}
                                </span>
                            </td>
                            <td class="px-8 py-6">
                                <p class="text-sm font-black text-slate-800 dark:text-slate-200 group-hover:text-indigo-500 transition-colors">{{ $entry->title }}</p>
                                <p class="text-[11px] text-slate-400 font-bold mt-0.5 capitalize tracking-wide">{{ str_replace('_', ' ', $entry->category) }}</p>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap text-right">
                                <span class="text-xl font-black tabular-nums {{ $entry->type === 'income' ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                    {{ $entry->type === 'income' ? '+' : '-' }} R$ {{ number_format($entry->amount, 2, ',', '.') }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-20 px-8 text-center bg-slate-50/30 dark:bg-slate-800/10">
                <div class="w-20 h-20 bg-slate-50 dark:bg-slate-800 rounded-4xl flex items-center justify-center mx-auto mb-6 shadow-inner">
                    <x-icon name="list-tree" style="duotone" class="w-10 h-10 text-slate-300" />
                </div>
                <h4 class="text-slate-900 dark:text-white font-black text-xl mb-2">Sem movimentações recentes</h4>
                <p class="text-slate-500 dark:text-slate-400 font-medium max-w-xs mx-auto">Tudo está em ordem. Novas transações aparecerão aqui assim que forem registradas.</p>
            </div>
        @endif
            </div>
        </div>
    </div>
@endsection
