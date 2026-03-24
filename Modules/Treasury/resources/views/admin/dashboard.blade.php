@extends('admin::components.layouts.master')

@section('content')
    <div class="space-y-8">
        <!-- Hero Header -->
        <div class="relative overflow-hidden rounded-3xl bg-linear-to-br from-gray-900 to-gray-800 text-white shadow-xl border border-gray-700/50">
            <div class="absolute inset-0 dash-pattern opacity-10"></div>
            <div class="absolute right-0 top-0 h-full w-1/2 bg-linear-to-l from-blue-600/20 to-transparent"></div>
            <div class="relative p-8 md:p-10 flex flex-col gap-6">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <span class="px-3 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300 text-xs font-bold uppercase tracking-wider">Financeiro</span>
                            <span class="px-3 py-1 rounded-full bg-green-500/20 border border-green-400/30 text-green-300 text-xs font-bold uppercase tracking-wider">Dashboard</span>
                        </div>
                        <h1 class="text-3xl md:text-4xl font-black tracking-tight mb-2">Tesouraria</h1>
                        <p class="text-gray-300 max-w-xl">Controle e gestão de entradas e saídas. Relatórios e prestação de contas.</p>
                    </div>
                    <div class="flex flex-shrink-0 flex-wrap items-center gap-3">
                        <a href="{{ route('treasury.reports.index') }}" class="px-4 py-2.5 rounded-xl bg-white/10 border border-white/20 text-white font-bold hover:bg-white/20 inline-flex items-center gap-2">
                            <x-icon name="chart-bar" style="duotone" class="w-5 h-5" /> Relatórios
                        </a>
                        @if ($permission->canCreateEntries())
                            <a href="{{ route('treasury.entries.create') }}" class="px-6 py-3 rounded-xl bg-white text-gray-900 font-bold hover:bg-gray-100 shadow-lg shadow-white/10 inline-flex items-center gap-2">
                                <x-icon name="plus" style="duotone" class="w-5 h-5 text-blue-600" /> Nova Entrada
                            </a>
                        @endif
                    </div>
                </div>
                @include('treasury::admin.partials.nav', ['breadcrumb' => ['Dashboard' => null], 'hideQuickLinks' => false])
            </div>
        </div>

        <!-- Statistics Cards (Nubank Style) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Conta (Saldo Atual) -->
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 flex flex-col justify-between relative overflow-hidden group hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <x-icon name="wallet" style="duotone" class="w-6 h-6 text-gray-900 dark:text-gray-100" />
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">Conta Institucional</h2>
                    </div>
                </div>
                <div class="mt-2">
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-1">Saldo em Caixa</p>
                    <p class="text-4xl font-black text-gray-900 dark:text-white">R$ {{ number_format($saldoAtual, 2, ',', '.') }}</p>
                </div>
            </div>

            <!-- Receitas do Mês -->
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 flex flex-col justify-between relative overflow-hidden group hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-full">
                            <x-icon name="arrow-trend-up" style="solid" class="w-5 h-5 text-green-600 dark:text-green-400" />
                        </div>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">Entradas</h2>
                    </div>
                </div>
                <div class="mt-2">
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-1">Recebido este mês</p>
                    <p class="text-3xl font-bold text-green-600 dark:text-green-500">R$ {{ number_format($receitasMes, 2, ',', '.') }}</p>
                </div>
            </div>

            <!-- Despesas do Mês -->
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 flex flex-col justify-between relative overflow-hidden group hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-red-100 dark:bg-red-900/30 rounded-full">
                            <x-icon name="arrow-trend-down" style="solid" class="w-5 h-5 text-red-600 dark:text-red-400" />
                        </div>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">Saídas</h2>
                    </div>
                </div>
                <div class="mt-2">
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-1">Gasto este mês</p>
                    <p class="text-3xl font-bold text-red-600 dark:text-red-500">R$ {{ number_format($despesasMes, 2, ',', '.') }}</p>
                </div>
            </div>
        </div>

        @if(!empty($planoCooperativo))
        <!-- Plano Cooperativo (CBAV2026) -->
        <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-3xl shadow-sm border-2 border-indigo-200 dark:border-indigo-800 p-6">
            <div class="flex items-center gap-3 mb-3">
                <div class="p-2 rounded-lg bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400">
                    <x-icon name="building" style="duotone" class="w-5 h-5" />
                </div>
                <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Sugestão de Repasse para Convenção (Plano Cooperativo)</h3>
            </div>
            <p class="text-2xl font-black text-indigo-600 dark:text-indigo-400">
                {{ number_format($planoCooperativo['percent'] ?? 10, 1) }}%: R$ {{ number_format($planoCooperativo['suggested_amount'] ?? 0, 2, ',', '.') }}
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Base do mês: R$ {{ number_format($planoCooperativo['base_amount'] ?? 0, 2, ',', '.') }}</p>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Income by Category Card -->
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Receitas por Categoria</h3>
                </div>
                <div class="p-6">
                    @if ($incomeByCategory->count() > 0)
                        <div class="space-y-4">
                            @foreach ($incomeByCategory as $item)
                                <div class="flex items-center justify-between group">
                                    <div class="flex items-center">
                                        <div class="w-2 h-2 rounded-full bg-green-500 mr-3"></div>
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300 capitalize group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                            {{ str_replace('_', ' ', $item->category) }}
                                        </span>
                                    </div>
                                    <span class="text-sm font-bold text-gray-900 dark:text-white">
                                        R$ {{ number_format($item->total, 2, ',', '.') }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center py-6 text-center">
                            <x-icon name="information-circle" class="w-10 h-10 text-gray-300 dark:text-gray-600 mb-2" />
                            <p class="text-sm text-gray-500 dark:text-gray-400">Nenhuma receita registrada este mês.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Expense by Category Card -->
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Despesas por Categoria</h3>
                </div>
                <div class="p-6">
                    @if ($expenseByCategory->count() > 0)
                        <div class="space-y-4">
                            @foreach ($expenseByCategory as $item)
                                <div class="flex items-center justify-between group">
                                    <div class="flex items-center">
                                        <div class="w-2 h-2 rounded-full bg-red-500 mr-3"></div>
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300 capitalize group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                            {{ str_replace('_', ' ', $item->category) }}
                                        </span>
                                    </div>
                                    <span class="text-sm font-bold text-gray-900 dark:text-white">
                                        R$ {{ number_format($item->total, 2, ',', '.') }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center py-6 text-center">
                            <x-icon name="information-circle" class="w-10 h-10 text-gray-300 dark:text-gray-600 mb-2" />
                            <p class="text-sm text-gray-500 dark:text-gray-400">Nenhuma despesa registrada este mês.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Entries Table -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="hidden sm:flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-500">
                        <x-icon name="list" style="duotone" class="w-4 h-4" />
                    </div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white tracking-tight">Últimos Movimentos</h3>
                </div>
                <a href="{{ route('treasury.entries.index') }}" class="inline-flex items-center text-sm font-bold text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors">
                    Ver extrato completo <x-icon name="angle-right" style="solid" class="w-4 h-4 ml-1" />
                </a>
            </div>
            @if ($ultimosLancamentos->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach ($ultimosLancamentos as $entry)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 rounded-full flex items-center justify-center {{ $entry->type === 'income' ? 'bg-green-100 dark:bg-green-900/30' : 'bg-red-100 dark:bg-red-900/30' }}">
                                                <x-icon name="{{ $entry->type === 'income' ? 'arrow-trend-up' : 'arrow-trend-down' }}" style="solid" class="w-5 h-5 {{ $entry->type === 'income' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}" />
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $entry->title }}</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $entry->entry_date->format('d/m/Y') }} &bull; {{ str_replace('_', ' ', Str::title($entry->category)) }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="text-sm font-black {{ $entry->type === 'income' ? 'text-green-600 dark:text-green-400' : 'text-gray-900 dark:text-white' }}">
                                            {{ $entry->type === 'income' ? '+' : '-' }} R$ {{ number_format($entry->amount, 2, ',', '.') }}
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="flex flex-col items-center justify-center py-12 text-center">
                    <x-icon name="receipt" class="w-12 h-12 text-gray-200 dark:text-gray-700 mb-4" />
                    <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">Você ainda não possui movimentações.</p>
                </div>
            @endif
        </div>

        <!-- Active Campaigns Section -->
        @if ($activeCampaigns->count() > 0)
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white tracking-tight uppercase tracking-wider text-sm">Campanhas Ativas</h3>
                    <a href="{{ route('treasury.campaigns.index') }}" class="text-xs font-bold text-blue-600 dark:text-blue-400 hover:text-blue-700 uppercase tracking-widest transition-colors">Ver todas</a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach ($activeCampaigns as $campaign)
                        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 group hover:shadow-md transition-all duration-200">
                            <div class="flex items-start justify-between mb-4">
                                <h4 class="font-bold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">{{ $campaign->name }}</h4>
                                <span class="text-xs font-bold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20 px-2 py-1 rounded">
                                    {{ number_format($campaign->progress_percentage, 1) }}%
                                </span>
                            </div>
                            <div class="space-y-3">
                                <div class="flex justify-between text-xs font-medium text-gray-500 dark:text-gray-400">
                                    <span>Arrecadado: R$ {{ number_format($campaign->current_amount, 2, ',', '.') }}</span>
                                    @if ($campaign->target_amount)
                                        <span>Meta: R$ {{ number_format($campaign->target_amount, 2, ',', '.') }}</span>
                                    @endif
                                </div>
                                @if ($campaign->target_amount)
                                    <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2 overflow-hidden">
                                        <div class="bg-linear-to-r from-blue-500 to-blue-600 h-full rounded-full transition-all duration-500"
                                            style="width: {{ min(100, $campaign->progress_percentage) }}%"></div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endsection

