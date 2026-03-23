@extends('memberpanel::components.layouts.master')

@section('page-title', 'Portal de Transparência - Tesouraria')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-slate-950 transition-colors duration-200 pb-12">
        <div class="max-w-7xl mx-auto space-y-8 px-6 pt-8">

            <!-- Header (padrão MemberPanel) -->
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Portal de Transparência</h1>
                    <p class="text-gray-500 dark:text-slate-400 mt-1 max-w-md">Prestação de contas: receitas e despesas por período e categoria.</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="px-4 py-2 bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-xl shadow-sm flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                        <span class="text-xs font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider">Tesouraria</span>
                    </div>
                </div>
            </div>

            <!-- Hero Card (mesmo padrão do dashboard: white/slate-900, mesh suave) -->
            <div class="relative overflow-hidden bg-white dark:bg-slate-900 rounded-3xl shadow-xl dark:shadow-2xl border border-gray-100 dark:border-slate-800 transition-colors duration-200" data-tour="treasury-transparency">
                <div class="absolute inset-0 opacity-20 dark:opacity-40 pointer-events-none">
                    <div class="absolute -top-24 -left-20 w-96 h-96 bg-cyan-400 dark:bg-cyan-600 rounded-full blur-[100px]"></div>
                    <div class="absolute top-1/2 -right-20 w-80 h-80 bg-emerald-400 dark:bg-emerald-600 rounded-full blur-[100px]"></div>
                </div>
                <div class="relative px-8 py-10 flex flex-col md:flex-row md:items-center justify-between gap-8 z-10">
                    <div class="flex-1">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-50 dark:bg-cyan-900/30 border border-cyan-100 dark:border-cyan-800 mb-4">
                            <x-icon name="eye" style="duotone" class="w-3 h-3 text-cyan-600 dark:text-cyan-400" />
                            <span class="text-[10px] font-black uppercase tracking-widest text-cyan-600 dark:text-cyan-400">Prestação de contas</span>
                        </div>
                        <p class="text-gray-500 dark:text-slate-300 font-medium max-w-xl text-lg leading-relaxed">
                            Visão consolidada das receitas e despesas da igreja por período e categoria, em conformidade com a prestação de contas e boas práticas de gestão.
                        </p>
                    </div>
                    <form method="get" action="{{ route('memberpanel.treasury.transparency') }}" class="flex flex-wrap items-center gap-3 shrink-0">
                        <label for="ano" class="text-sm font-bold text-gray-700 dark:text-slate-300">Ano:</label>
                        <select name="ano" id="ano" class="rounded-xl border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-gray-900 dark:text-white px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all">
                            @foreach(range(now()->year, now()->year - 5) as $y)
                                <option value="{{ $y }}" {{ (string)$y === (string)$year ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="inline-flex items-center justify-center px-5 py-2.5 bg-cyan-600 hover:bg-cyan-700 text-white rounded-xl text-sm font-bold transition-all shadow-lg shadow-cyan-500/20">
                            <x-icon name="magnifying-glass" style="duotone" class="w-4 h-4 mr-2" />
                            Atualizar
                        </button>
                    </form>
                </div>
            </div>

            <!-- Stats Grid (mesmo padrão do dashboard: rounded-3xl, border-gray-100 dark:border-slate-800) -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="group relative bg-white dark:bg-slate-900 rounded-3xl p-6 border border-gray-100 dark:border-slate-800 shadow-sm hover:shadow-xl hover:shadow-emerald-500/5 transition-all duration-300">
                    <div class="flex items-start justify-between mb-2">
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-slate-500">Receitas {{ $year }}</p>
                        <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center text-emerald-600 dark:text-emerald-400 group-hover:scale-110 transition-transform duration-300">
                            <x-icon name="money-bills" style="duotone" class="w-6 h-6" />
                        </div>
                    </div>
                    <h3 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight tabular-nums">R$ {{ number_format($yearly_income, 2, ',', '.') }}</h3>
                </div>
                <div class="group relative bg-white dark:bg-slate-900 rounded-3xl p-6 border border-gray-100 dark:border-slate-800 shadow-sm hover:shadow-xl hover:shadow-rose-500/5 transition-all duration-300">
                    <div class="flex items-start justify-between mb-2">
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-slate-500">Despesas {{ $year }}</p>
                        <div class="w-12 h-12 rounded-2xl bg-rose-50 dark:bg-rose-900/20 flex items-center justify-center text-rose-600 dark:text-rose-400 group-hover:scale-110 transition-transform duration-300">
                            <x-icon name="credit-card-blank" style="duotone" class="w-6 h-6" />
                        </div>
                    </div>
                    <h3 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight tabular-nums">R$ {{ number_format($yearly_expense, 2, ',', '.') }}</h3>
                </div>
                <div class="group relative bg-white dark:bg-slate-900 rounded-3xl p-6 border border-gray-100 dark:border-slate-800 shadow-sm hover:shadow-xl transition-all duration-300 {{ $yearly_balance >= 0 ? 'hover:shadow-cyan-500/5' : 'hover:shadow-amber-500/5' }}">
                    <div class="flex items-start justify-between mb-2">
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-slate-500">Saldo {{ $year }}</p>
                        <div class="w-12 h-12 rounded-2xl {{ $yearly_balance >= 0 ? 'bg-cyan-50 dark:bg-cyan-900/20 text-cyan-600 dark:text-cyan-400' : 'bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400' }} flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                            <x-icon name="scale-balanced" style="duotone" class="w-6 h-6" />
                        </div>
                    </div>
                    <h3 class="text-2xl font-black tracking-tight tabular-nums {{ $yearly_balance >= 0 ? 'text-cyan-600 dark:text-cyan-400' : 'text-amber-600 dark:text-amber-400' }}">R$ {{ number_format($yearly_balance, 2, ',', '.') }}</h3>
                </div>
            </div>

            <!-- Grid 2 colunas: Receitas e Despesas por categoria -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden">
                    <div class="px-8 py-6 border-b border-gray-100 dark:border-slate-800 flex items-center gap-3 bg-gray-50/50 dark:bg-slate-900/50">
                        <div class="p-2 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-xl">
                            <x-icon name="chart-pie" style="duotone" class="w-5 h-5" />
                        </div>
                        <h3 class="text-xs font-black uppercase tracking-widest text-gray-700 dark:text-slate-300">Receitas por categoria ({{ $year }})</h3>
                    </div>
                    <div class="p-8">
                        @if($income_by_category->count() > 0)
                            <div class="space-y-6">
                                @foreach($income_by_category as $item)
                                    @php $pct = $yearly_income > 0 ? ($item->total / $yearly_income) * 100 : 0; @endphp
                                    <div>
                                        <div class="flex justify-between mb-2">
                                            <span class="text-sm font-bold text-gray-700 dark:text-slate-300 capitalize">{{ str_replace('_', ' ', $item->category) }}</span>
                                            <span class="text-sm font-black text-gray-900 dark:text-white">R$ {{ number_format($item->total, 2, ',', '.') }}</span>
                                        </div>
                                        <div class="relative h-2 w-full bg-gray-100 dark:bg-slate-800 rounded-full overflow-hidden">
                                            <div class="absolute h-full bg-linear-to-r from-emerald-500 to-green-500 rounded-full transition-all duration-1000" style="width: {{ $pct }}%"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 dark:text-slate-400 text-center py-8 font-medium">Nenhuma receita registrada neste ano.</p>
                        @endif
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden">
                    <div class="px-8 py-6 border-b border-gray-100 dark:border-slate-800 flex items-center gap-3 bg-gray-50/50 dark:bg-slate-900/50">
                        <div class="p-2 bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 rounded-xl">
                            <x-icon name="chart-simple" style="duotone" class="w-5 h-5" />
                        </div>
                        <h3 class="text-xs font-black uppercase tracking-widest text-gray-700 dark:text-slate-300">Despesas por categoria ({{ $year }})</h3>
                    </div>
                    <div class="p-8">
                        @if($expense_by_category->count() > 0)
                            <div class="space-y-6">
                                @foreach($expense_by_category as $item)
                                    @php $pct = $yearly_expense > 0 ? ($item->total / $yearly_expense) * 100 : 0; @endphp
                                    <div>
                                        <div class="flex justify-between mb-2">
                                            <span class="text-sm font-bold text-gray-700 dark:text-slate-300 capitalize">{{ str_replace('_', ' ', $item->category) }}</span>
                                            <span class="text-sm font-black text-gray-900 dark:text-white">R$ {{ number_format($item->total, 2, ',', '.') }}</span>
                                        </div>
                                        <div class="relative h-2 w-full bg-gray-100 dark:bg-slate-800 rounded-full overflow-hidden">
                                            <div class="absolute h-full bg-linear-to-r from-rose-500 to-red-500 rounded-full transition-all duration-1000" style="width: {{ $pct }}%"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 dark:text-slate-400 text-center py-8 font-medium">Nenhuma despesa registrada neste ano.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Movimento mensal -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-100 dark:border-slate-800 flex items-center gap-3 bg-gray-50/50 dark:bg-slate-900/50">
                    <div class="p-2 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-xl">
                        <x-icon name="chart-line" style="duotone" class="w-5 h-5" />
                    </div>
                    <h3 class="text-xs font-black uppercase tracking-widest text-gray-700 dark:text-slate-300">Receitas e despesas por mês ({{ $year }})</h3>
                </div>
                <div class="p-8 overflow-x-auto">
                    <div class="min-w-[600px] space-y-4">
                        @foreach($monthly_chart as $m)
                            <div class="flex items-center gap-4">
                                <span class="w-24 text-sm font-bold text-gray-600 dark:text-slate-400 shrink-0">{{ $m['label'] }}</span>
                                <div class="flex-1 flex gap-2 items-center">
                                    <span class="text-xs font-medium text-emerald-600 dark:text-emerald-400 w-20 text-right">R$ {{ number_format($m['income'], 2, ',', '.') }}</span>
                                    <div class="flex-1 h-6 bg-gray-100 dark:bg-slate-800 rounded-full overflow-hidden flex">
                                        @php
                                            $maxVal = max(1, collect($monthly_chart)->max('income') + collect($monthly_chart)->max('expense'));
                                            $wIncome = ($m['income'] / $maxVal) * 100;
                                            $wExpense = ($m['expense'] / $maxVal) * 100;
                                        @endphp
                                        <div class="h-full bg-emerald-500 rounded-l-full" style="width: {{ $wIncome }}%"></div>
                                        <div class="h-full bg-rose-500 rounded-r-full" style="width: {{ $wExpense }}%"></div>
                                    </div>
                                    <span class="text-xs font-medium text-rose-600 dark:text-rose-400 w-20">R$ {{ number_format($m['expense'], 2, ',', '.') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="flex gap-8 mt-6 pt-6 border-t border-gray-100 dark:border-slate-800">
                        <span class="flex items-center gap-2 text-sm font-medium text-gray-500 dark:text-slate-400">
                            <span class="w-3 h-3 rounded-full bg-emerald-500"></span> Receita
                        </span>
                        <span class="flex items-center gap-2 text-sm font-medium text-gray-500 dark:text-slate-400">
                            <span class="w-3 h-3 rounded-full bg-rose-500"></span> Despesa
                        </span>
                    </div>
                </div>
            </div>

            <!-- Campanhas (resumo) -->
            @if($campaigns_summary->count() > 0)
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-100 dark:border-slate-800 flex items-center gap-3 bg-gray-50/50 dark:bg-slate-900/50">
                    <div class="p-2 bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 rounded-xl">
                        <x-icon name="bullhorn" style="duotone" class="w-5 h-5" />
                    </div>
                    <h3 class="text-xs font-black uppercase tracking-widest text-gray-700 dark:text-slate-300">Campanhas (visão consolidada)</h3>
                </div>
                <div class="p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($campaigns_summary as $c)
                            @php $pct = $c->target_amount > 0 ? min(100, ($c->current_amount / $c->target_amount) * 100) : 0; @endphp
                            <div class="rounded-2xl border border-gray-200 dark:border-slate-700 p-6 bg-gray-50/50 dark:bg-slate-800/30">
                                <p class="font-bold text-gray-900 dark:text-white mb-2">{{ $c->name }}</p>
                                <p class="text-sm text-gray-500 dark:text-slate-400 mb-4">
                                    Meta: R$ {{ number_format($c->target_amount, 2, ',', '.') }} · Arrecadado: R$ {{ number_format($c->current_amount, 2, ',', '.') }}
                                </p>
                                <div class="relative h-2 w-full bg-gray-100 dark:bg-slate-800 rounded-full overflow-hidden">
                                    <div class="absolute h-full bg-purple-500 rounded-full transition-all duration-1000" style="width: {{ $pct }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
@endsection
