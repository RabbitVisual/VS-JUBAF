@extends('admin::components.layouts.master')

@section('content')
    <div class="space-y-8">
        <!-- Hero -->
        <div class="relative overflow-hidden rounded-3xl bg-linear-to-br from-gray-900 to-gray-800 text-white shadow-xl border border-gray-700/50">
            <div class="absolute inset-0 dash-pattern opacity-10"></div>
            <div class="absolute right-0 top-0 h-full w-1/2 bg-linear-to-l from-blue-600/20 to-transparent"></div>
            <div class="relative p-8 md:p-10 flex flex-col gap-6">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <span class="px-3 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300 text-xs font-bold uppercase tracking-wider">Relatórios</span>
                            <span class="px-3 py-1 rounded-full bg-green-500/20 border border-green-400/30 text-green-300 text-xs font-bold uppercase tracking-wider">{{ $daysDiff }} {{ $daysDiff == 1 ? 'dia' : 'dias' }}</span>
                        </div>
                        <h1 class="text-3xl md:text-4xl font-black tracking-tight mb-2">Inteligência Financeira</h1>
                        <p class="text-gray-300 max-w-xl">
                            De <strong>{{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}</strong>
                            até <strong>{{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</strong>
                        </p>
                        @include('treasury::admin.partials.nav', ['breadcrumb' => ['Relatórios' => null]])
                    </div>
                    @if ($permission->canExportData())
                        <div class="flex flex-wrap items-center gap-3">
                            <a href="{{ route('treasury.reports.export.pdf', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
                                class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl bg-white/10 border border-white/20 text-white font-bold hover:bg-white/20 transition-all text-sm"
                                title="Relatório completo em PDF">
                                <x-icon name="file-pdf" style="duotone" class="w-5 h-5 mr-2" /> PDF Geral
                            </a>
                            <a href="{{ route('treasury.reports.export.tithes.pdf', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
                                class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl bg-white/10 border border-white/20 text-white font-bold hover:bg-white/20 transition-all text-sm">
                                <x-icon name="heart" style="duotone" class="w-5 h-5 mr-2" /> Dízimos & Ofertas
                            </a>
                            <a href="{{ route('treasury.reports.export.excel', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
                                class="inline-flex items-center justify-center px-6 py-3 rounded-xl bg-white text-gray-900 font-bold hover:bg-gray-100 shadow-lg shadow-white/10 transition-all text-sm">
                                <x-icon name="file-excel" style="duotone" class="w-5 h-5 mr-2 text-green-600" /> Excel
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        @if(isset($monthlyClosing) && $monthlyClosing)
            <div class="mt-4 bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-amber-300/70 dark:border-amber-500/60 px-5 py-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <div class="p-1.5 rounded-lg bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300">
                            <x-icon name="badge-check" class="w-4 h-4" />
                        </div>
                        <span class="text-[10px] font-black uppercase tracking-widest text-amber-700 dark:text-amber-300">
                            Parecer Fiscal do Conselho
                        </span>
                    </div>
                    <p class="text-xs text-gray-700 dark:text-gray-300">
                        Balancete de
                        <span class="font-semibold">
                            {{ \Carbon\Carbon::create($monthlyClosing->year, $monthlyClosing->month, 1)->translatedFormat('F/Y') }}
                        </span>
                        –
                        @if($monthlyClosing->ready_for_assembly)
                            <span class="text-emerald-600 dark:text-emerald-400 font-semibold">
                                Aprovado para assembleia
                            </span>
                            @if($monthlyClosing->council_approved_at)
                                em {{ $monthlyClosing->council_approved_at->format('d/m/Y H:i') }}
                            @endif
                        @else
                            <span class="text-amber-600 dark:text-amber-400 font-semibold">
                                Aguardando parecer do conselho
                            </span>
                        @endif
                    </p>
                </div>

                @if($canCouncilApprove && ! $monthlyClosing->ready_for_assembly)
                    <div class="flex items-center gap-2">
                        <button
                            type="button"
                            id="btn-approve-closing"
                            data-approve-url="{{ route('treasury.reports.closing.approve-for-assembly', $monthlyClosing) }}"
                            class="inline-flex items-center justify-center px-4 py-2 text-[11px] font-black uppercase tracking-widest rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 shadow-sm hover:shadow-md transition-all"
                        >
                            <x-icon name="check-circle" class="w-4 h-4 mr-1.5" />
                            <span>Aprovar para Assembleia</span>
                        </button>
                    </div>
                @endif
            </div>
        @endif

        <!-- Filter Control Card -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/30 dark:bg-gray-700/30 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></div>
                    <span class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-widest">Parâmetros de Análise</span>
                </div>

                <div class="flex flex-wrap gap-2">
                    @php
                        $periods = [
                            'today' => 'Hoje',
                            'week' => 'Esta Semana',
                            'month' => 'Este Mês',
                            'year' => 'Este Ano'
                        ];
                    @endphp
                    @foreach($periods as $key => $label)
                        <button type="button" onclick="setPeriod('{{ $key }}')"
                            class="px-3 py-1.5 text-[10px] font-black uppercase tracking-wider text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg transition-all duration-200 hover:border-blue-300 dark:hover:border-blue-800">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>

            <form method="GET" action="{{ route('treasury.reports.index') }}" class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-end">
                    <div class="md:col-span-3 space-y-2">
                        <label for="start_date" class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Início</label>
                        <input type="date" name="start_date" id="start_date" value="{{ $startDate }}"
                                class="block w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all">
                    </div>

                    <div class="md:col-span-3 space-y-2">
                        <label for="end_date" class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Fim</label>
                        <input type="date" name="end_date" id="end_date" value="{{ $endDate }}"
                                class="block w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all">
                    </div>

                    <div class="md:col-span-3 space-y-2">
                        <label for="category" class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Categoria Principal</label>
                        <select name="category" id="category" class="block w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all">
                            <option value="">Todas as Categorias</option>
                            <option value="tithe" {{ request('category') === 'tithe' ? 'selected' : '' }}>Dízimo</option>
                            <option value="offering" {{ request('category') === 'offering' ? 'selected' : '' }}>Oferta</option>
                            <option value="donation" {{ request('category') === 'donation' ? 'selected' : '' }}>Doação</option>
                            <option value="campaign" {{ request('category') === 'campaign' ? 'selected' : '' }}>Campanha</option>
                        </select>
                    </div>

                    <div class="md:col-span-3 flex gap-3">
                        <button type="submit"
                            class="flex-1 px-6 py-3 bg-blue-600 text-white font-bold text-xs uppercase tracking-widest rounded-xl hover:bg-blue-700 shadow-lg shadow-blue-600/20 transition-all active:scale-95">
                            FILTRAR
                        </button>
                        <a href="{{ route('treasury.reports.index') }}"
                            class="px-4 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 rounded-xl transition-all"
                            title="Limpar">
                            <x-icon name="refresh" class="w-5 h-5" />
                        </a>
                    </div>
                </div>
            </form>
        </div>

        @if(isset($planoCooperativo))
        <!-- Widget destacado: Plano Cooperativo -->
        <div class="bg-indigo-600 dark:bg-indigo-800 rounded-3xl shadow-xl border border-indigo-500/30 p-6 text-white">
            <div class="flex items-center gap-3 mb-2">
                <div class="p-2 rounded-xl bg-white/20">
                    <x-icon name="building" class="w-6 h-6" />
                </div>
                <span class="text-xs font-black uppercase tracking-widest opacity-90">Plano Cooperativo (Contribuição Denominacional)</span>
            </div>
            <p class="text-3xl font-black">
                Sugestão de Repasse para Convenção ({{ number_format($planoCooperativo['percent'], 1) }}%): R$ {{ number_format($planoCooperativo['suggested_amount'], 2, ',', '.') }}
            </p>
            <p class="text-sm opacity-90 mt-1">Base do período: R$ {{ number_format($planoCooperativo['base_amount'], 2, ',', '.') }}</p>
        </div>
        @endif

        <!-- Intelligence Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Income -->
            <div class="relative group bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden transition-all duration-300 hover:shadow-lg">
                <div class="absolute top-0 right-0 p-3 opacity-10 group-hover:scale-110 transition-transform duration-300">
                    <x-icon name="trending-up" class="w-16 h-16 text-green-600" />
                </div>
                <div class="relative z-10 flex flex-col h-full justify-between">
                    <div>
                        <div class="flex items-center gap-2 mb-4">
                            <div class="p-2 rounded-lg bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400">
                                <x-icon name="currency-dollar" class="w-5 h-5" />
                            </div>
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total Receitas</span>
                        </div>
                        <h3 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">
                            <span class="text-sm font-medium text-gray-400 mr-1">R$</span>{{ number_format($totalIncome, 2, ',', '.') }}
                        </h3>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-50 dark:border-gray-700/50 flex items-center justify-between text-[10px] font-bold">
                        <span class="text-green-600 dark:text-green-400 px-2 py-0.5 rounded bg-green-50 dark:bg-green-900/20">{{ $totalIncomeEntries }} transações</span>
                        @if ($daysDiff > 0)
                            <span class="text-gray-400">Média R$ {{ number_format($avgDailyIncome, 2) }}/dia</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Total Expenses -->
            <div class="relative group bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden transition-all duration-300 hover:shadow-lg">
                <div class="absolute top-0 right-0 p-3 opacity-10 group-hover:scale-110 transition-transform duration-300">
                    <x-icon name="trending-down" class="w-16 h-16 text-red-600" />
                </div>
                <div class="relative z-10 flex flex-col h-full justify-between">
                    <div>
                        <div class="flex items-center gap-2 mb-4">
                            <div class="p-2 rounded-lg bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400">
                                <x-icon name="credit-card" class="w-5 h-5" />
                            </div>
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total Despesas</span>
                        </div>
                        <h3 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">
                            <span class="text-sm font-medium text-gray-400 mr-1">R$</span>{{ number_format($totalExpense, 2, ',', '.') }}
                        </h3>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-50 dark:border-gray-700/50 flex items-center justify-between text-[10px] font-bold">
                        <span class="text-red-600 dark:text-red-400 px-2 py-0.5 rounded bg-red-50 dark:bg-red-900/20">{{ $totalExpenseEntries }} transações</span>
                        @if ($daysDiff > 0)
                            <span class="text-gray-400">Média R$ {{ number_format($avgDailyExpense, 2) }}/dia</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Net Balance -->
            <div class="relative group bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden transition-all duration-300 hover:shadow-lg">
                <div class="absolute top-0 right-0 p-3 opacity-10 group-hover:scale-110 transition-transform duration-300">
                    <x-icon name="scale" class="w-16 h-16 {{ $balance >= 0 ? 'text-blue-600' : 'text-orange-600' }}" />
                </div>
                <div class="relative z-10 flex flex-col h-full justify-between">
                    <div>
                        <div class="flex items-center gap-2 mb-4">
                            <div class="p-2 rounded-lg {{ $balance >= 0 ? 'bg-blue-50 text-blue-600' : 'bg-orange-50 text-orange-600' }} dark:{{ $balance >= 0 ? 'bg-blue-900/30 text-blue-400' : 'bg-orange-900/30 text-orange-400' }}">
                                <x-icon name="cash" class="w-5 h-5" />
                            </div>
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Saldo do Período</span>
                        </div>
                        <h3 class="text-3xl font-black {{ $balance >= 0 ? 'text-blue-600 dark:text-blue-400' : 'text-orange-600 dark:text-orange-400' }} tracking-tight">
                            <span class="text-sm font-medium text-gray-400 mr-1">R$</span>{{ number_format($balance, 2, ',', '.') }}
                        </h3>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-50 dark:border-gray-700/50 flex items-center justify-between text-[10px] font-bold">
                        <span class="px-2 py-0.5 rounded {{ $balance >= 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }} uppercase tracking-widest">{{ $balance >= 0 ? 'Superávit' : 'Déficit' }}</span>
                        @if ($totalIncome > 0)
                            <span class="text-gray-400">Margem: {{ number_format(($balance / $totalIncome) * 100, 1) }}%</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Plano Cooperativo (CBAV2026) -->
            @if(isset($planoCooperativo))
            <div class="relative group bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden transition-all duration-300 hover:shadow-lg md:col-span-2 lg:col-span-4">
                <div class="flex items-center gap-2 mb-4">
                    <div class="p-2 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400">
                        <x-icon name="building" class="w-5 h-5" />
                    </div>
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Plano Cooperativo (Contribuição Denominacional)</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500 dark:text-gray-400 block text-xs">Percentual configurado</span>
                        <span class="font-bold text-gray-900 dark:text-white">{{ number_format($planoCooperativo['percent'], 1) }}%</span>
                    </div>
                    <div>
                        <span class="text-gray-500 dark:text-gray-400 block text-xs">Base do período</span>
                        <span class="font-bold text-gray-900 dark:text-white">R$ {{ number_format($planoCooperativo['base_amount'], 2, ',', '.') }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 dark:text-gray-400 block text-xs">Valor sugerido a repassar</span>
                        <span class="font-bold text-indigo-600 dark:text-indigo-400">R$ {{ number_format($planoCooperativo['suggested_amount'], 2, ',', '.') }}</span>
                    </div>
                </div>
            </div>
            @endif

            <!-- Operation Volume -->
            <div class="relative group bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden transition-all duration-300 hover:shadow-lg">
                <div class="absolute top-0 right-0 p-3 opacity-10 group-hover:scale-110 transition-transform duration-300">
                    <x-icon name="document-text" class="w-16 h-16 text-purple-600" />
                </div>
                <div class="relative z-10 flex flex-col h-full justify-between">
                    <div>
                        <div class="flex items-center gap-2 mb-4">
                            <div class="p-2 rounded-lg bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400">
                                <x-icon name="collection" class="w-5 h-5" />
                            </div>
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Volume de Atividade</span>
                        </div>
                        <h3 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">
                            {{ number_format($totalEntries, 0, ',', '.') }}
                        </h3>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-50 dark:border-gray-700/50">
                        <p class="text-[10px] font-bold text-gray-400 leading-tight">Registros processados no período selecionado.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Analytical Insights -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Major Income -->
            @if ($largestIncome)
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 flex items-center gap-6">
                    <div class="w-16 h-16 rounded-3xl bg-green-50 dark:bg-green-900/30 flex items-center justify-center text-green-600 dark:text-green-400 border border-green-100 dark:border-green-800/50">
                        <x-icon name="trending-up" class="w-8 h-8" />
                    </div>
                    <div class="flex-1">
                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest block mb-1">Maior Receita Registrada</span>
                        <h4 class="text-xl font-black text-gray-900 dark:text-white leading-tight mb-1 truncate">{{ $largestIncome->title }}</h4>
                        <div class="flex items-center gap-2">
                            <span class="text-lg font-bold text-green-600 dark:text-green-400">R$ {{ number_format($largestIncome->amount, 2, ',', '.') }}</span>
                            <span class="text-xs text-gray-400">•</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $largestIncome->entry_date->format('d/m/Y') }}</span>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Major Expense -->
            @if ($largestExpense)
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 flex items-center gap-6">
                    <div class="w-16 h-16 rounded-3xl bg-red-50 dark:bg-red-900/30 flex items-center justify-center text-red-600 dark:text-red-400 border border-red-100 dark:border-red-800/50">
                        <x-icon name="trending-down" class="w-8 h-8" />
                    </div>
                    <div class="flex-1">
                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest block mb-1">Maior Despesa Registrada</span>
                        <h4 class="text-xl font-black text-gray-900 dark:text-white leading-tight mb-1 truncate">{{ $largestExpense->title }}</h4>
                        <div class="flex items-center gap-2">
                            <span class="text-lg font-bold text-red-600 dark:text-red-400">R$ {{ number_format($largestExpense->amount, 2, ',', '.') }}</span>
                            <span class="text-xs text-gray-400">•</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $largestExpense->entry_date->format('d/m/Y') }}</span>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Categorical Breakdown -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Income by Category -->
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <x-icon name="chart-pie" class="w-4 h-4 text-green-600" />
                        <span class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-widest">Receitas por Categoria</span>
                    </div>
                </div>
                <div class="p-6 space-y-5">
                    @forelse ($incomeByCategory as $item)
                        <div class="group">
                            <div class="flex items-center justify-between text-xs mb-2">
                                <span class="font-bold text-gray-700 dark:text-gray-300 capitalize group-hover:text-blue-600 transition-colors">{{ str_replace('_', ' ', $item->category) }}</span>
                                <div class="flex gap-2">
                                    <span class="font-black text-gray-900 dark:text-white">R$ {{ number_format($item->total, 2, ',', '.') }}</span>
                                    <span class="px-1.5 py-0.5 rounded bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 font-bold">{{ number_format($item->percentage, 1) }}%</span>
                                </div>
                            </div>
                            <div class="w-full bg-gray-100 dark:bg-gray-700 h-1.5 rounded-full overflow-hidden">
                                <div class="bg-green-500 h-full rounded-full transition-all duration-700 ease-out" style="width: {{ $item->percentage }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="py-12 flex flex-col items-center justify-center opacity-40">
                            <x-icon name="chart-bar" class="w-12 h-12 mb-2" />
                            <p class="text-xs font-bold uppercase tracking-widest">Sem dados no período</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Expense by Category -->
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <x-icon name="chart-pie" class="w-4 h-4 text-red-600" />
                        <span class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-widest">Despesas por Categoria</span>
                    </div>
                </div>
                <div class="p-6 space-y-5">
                    @forelse ($expenseByCategory as $item)
                        <div class="group">
                            <div class="flex items-center justify-between text-xs mb-2">
                                <span class="font-bold text-gray-700 dark:text-gray-300 capitalize group-hover:text-blue-600 transition-colors">{{ str_replace('_', ' ', $item->category) }}</span>
                                <div class="flex gap-2">
                                    <span class="font-black text-gray-900 dark:text-white">R$ {{ number_format($item->total, 2, ',', '.') }}</span>
                                    <span class="px-1.5 py-0.5 rounded bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 font-bold">{{ number_format($item->percentage, 1) }}%</span>
                                </div>
                            </div>
                            <div class="w-full bg-gray-100 dark:bg-gray-700 h-1.5 rounded-full overflow-hidden">
                                <div class="bg-red-500 h-full rounded-full transition-all duration-700 ease-out" style="width: {{ $item->percentage }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="py-12 flex flex-col items-center justify-center opacity-40">
                            <x-icon name="chart-bar" class="w-12 h-12 mb-2" />
                            <p class="text-xs font-bold uppercase tracking-widest">Sem dados no período</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Operational Dynamics -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Income by Payment Method -->
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between bg-gray-50/50 dark:bg-gray-700/30">
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Receitas por Método</span>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @forelse ($incomeByPaymentMethod as $item)
                            <div class="p-3 rounded-xl border border-gray-100 dark:border-gray-700 flex items-center justify-between">
                                <span class="text-xs font-bold text-gray-700 dark:text-gray-300 capitalize">{{ str_replace('_', ' ', $item->payment_method) }}</span>
                                <div class="text-right">
                                    <p class="text-sm font-black text-gray-900 dark:text-white">R$ {{ number_format($item->total, 2, ',', '.') }}</p>
                                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter">{{ $item->count }} ops</p>
                                </div>
                            </div>
                        @empty
                            <p class="col-span-full text-center py-6 text-xs text-gray-400 font-bold uppercase tracking-widest">Sem movimentações</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Expense by Payment Method -->
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between bg-gray-50/50 dark:bg-gray-700/30">
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Despesas por Método</span>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @forelse ($expenseByPaymentMethod as $item)
                            <div class="p-3 rounded-xl border border-gray-100 dark:border-gray-700 flex items-center justify-between">
                                <span class="text-xs font-bold text-gray-700 dark:text-gray-300 capitalize">{{ str_replace('_', ' ', $item->payment_method) }}</span>
                                <div class="text-right">
                                    <p class="text-sm font-black text-gray-900 dark:text-white">R$ {{ number_format($item->total, 2, ',', '.') }}</p>
                                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter">{{ $item->count }} ops</p>
                                </div>
                            </div>
                        @empty
                            <p class="col-span-full text-center py-6 text-xs text-gray-400 font-bold uppercase tracking-widest">Sem movimentações</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Timeline of Daily Cash Flow -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30">
                <h3 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-[0.2em]">Fluxo de Caixa Diário</h3>
            </div>

            @php
                $allDates = collect($incomeByDay->pluck('date'))->merge($expenseByDay->pluck('date'))->unique()->sortDesc();
                $maxAmount = max($incomeByDay->max('total') ?? 0, $expenseByDay->max('total') ?? 0);
            @endphp

            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse ($allDates as $date)
                    @php
                        $income = $incomeByDay->firstWhere('date', $date);
                        $expense = $expenseByDay->firstWhere('date', $date);
                        $incomeAmount = $income->total ?? 0;
                        $expenseAmount = $expense->total ?? 0;
                        $dayBalance = $incomeAmount - $expenseAmount;
                        $carbonDate = \Carbon\Carbon::parse($date);
                    @endphp
                    <div class="p-6 flex flex-col md:flex-row md:items-center gap-6 group hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors">
                        <!-- Date Badge -->
                        <div class="flex items-center gap-4 w-48">
                            <div class="w-12 h-12 rounded-xl bg-gray-100 dark:bg-gray-700 flex flex-col items-center justify-center border border-gray-200 dark:border-gray-600">
                                <span class="text-xs font-black text-blue-600 dark:text-blue-400 -mb-1">{{ $carbonDate->format('d') }}</span>
                                <span class="text-[8px] font-black text-gray-400 uppercase">{{ $carbonDate->locale('pt_BR')->shortMonthName }}</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-tighter">{{ $carbonDate->locale('pt_BR')->dayName }}</span>
                                <span class="text-[9px] font-black {{ $dayBalance >= 0 ? 'text-green-600' : 'text-red-600' }} uppercase transform scale-90 -translate-x-1 origin-left">
                                    {{ $dayBalance >= 0 ? '+' : '' }} R$ {{ number_format($dayBalance, 2, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        <!-- Mini Charts -->
                        <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Income Bar -->
                            <div class="space-y-1">
                                <div class="flex items-center justify-between">
                                    <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Receitas</span>
                                    <span class="text-[10px] font-bold text-green-600">R$ {{ number_format($incomeAmount, 2, ',', '.') }}</span>
                                </div>
                                <div class="w-full bg-gray-100 dark:bg-gray-700 h-1.5 rounded-full overflow-hidden">
                                    <div class="bg-green-500 h-full rounded-full transition-all duration-700" style="width: {{ $maxAmount > 0 ? ($incomeAmount / $maxAmount) * 100 : 0 }}%"></div>
                                </div>
                            </div>
                            <!-- Expense Bar -->
                            <div class="space-y-1">
                                <div class="flex items-center justify-between">
                                    <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Despesas</span>
                                    <span class="text-[10px] font-bold text-red-600">R$ {{ number_format($expenseAmount, 2, ',', '.') }}</span>
                                </div>
                                <div class="w-full bg-gray-100 dark:bg-gray-700 h-1.5 rounded-full overflow-hidden">
                                    <div class="bg-red-500 h-full rounded-full transition-all duration-700" style="width: {{ $maxAmount > 0 ? ($expenseAmount / $maxAmount) * 100 : 0 }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-20 flex flex-col items-center justify-center opacity-30">
                        <x-icon name="calendar" class="w-16 h-16 mb-4" />
                        <h4 class="text-sm font-black uppercase tracking-[0.2em]">Sem movimentação registrada</h4>
                    </div>
                @endforelse
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
                    if (new Date(startDate) > new Date()) {
                        startDate = new Date(new Date().setDate(new Date().getDate() - 6)).toISOString().split('T')[0];
                    }
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

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const btn = document.getElementById('btn-approve-closing');
            if (!btn) return;

            btn.addEventListener('click', async function () {
                if (!confirm('Confirmar parecer fiscal do conselho e marcar este balancete como pronto para assembleia?')) {
                    return;
                }

                const url = btn.getAttribute('data-approve-url');
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: 'Registrando parecer fiscal...' } }));

                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json',
                        },
                        body: new FormData(),
                    });

                    const data = await response.json();
                    if (!response.ok || data.success === false) {
                        window.dispatchEvent(new CustomEvent('loading-overlay:hide'));
                        alert(data.message || 'Não foi possível registrar o parecer fiscal.');
                        return;
                    }

                    window.location.reload();
                } catch (e) {
                    window.dispatchEvent(new CustomEvent('loading-overlay:hide'));
                    alert('Erro ao comunicar com o servidor. Tente novamente.');
                }
            });
        });
    </script>
@endpush

