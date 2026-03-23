@extends('liderancapanel::components.layouts.master')

@section('title', 'Tesouraria')

@section('content')
    <div class="space-y-6">
        @if (session('success'))
            <div
                class="rounded-2xl border border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-900/20 px-4 py-3 text-sm font-medium text-green-800 dark:text-green-200 flex items-center gap-2">
                <x-icon name="check-circle" class="w-5 h-5 flex-shrink-0" /> {{ session('success') }}
            </div>
        @endif

        <div
            class="rounded-3xl bg-gradient-to-br from-slate-800 via-slate-900 to-slate-800 text-white shadow-xl border border-amber-900/30 overflow-hidden">
            <div class="p-6 md:p-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <nav class="flex items-center gap-2 text-sm text-slate-400 font-medium mb-2">
                        <a href="{{ route('pastor.transparencia.index') }}"
                            class="hover:text-white transition-colors">Transparência</a>
                        <x-icon name="chevron-right" class="w-3.5 h-3.5" />
                        <span class="text-white font-bold">Tesouraria</span>
                    </nav>
                    <span
                        class="inline-flex items-center px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300 text-xs font-bold uppercase tracking-wider mb-2">Gestão
                        Financeira</span>
                    <h1 class="text-2xl md:text-3xl font-bold tracking-tight mb-1">Tesouraria</h1>
                    <p class="text-slate-300 text-sm max-w-xl">Controle de entradas e saídas, campanhas, metas e relatórios.
                    </p>
                </div>
                <div class="flex flex-shrink-0 flex-wrap items-center gap-3">
                    <a href="{{ route('pastor.tesouraria.reports.index') }}"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 border border-white/20 text-white font-medium hover:bg-white/20 transition-colors">
                        <x-icon name="chart-bar" class="w-5 h-5" /> Relatórios
                    </a>
                    @if ($permission->canCreateEntries())
                        <a href="{{ route('pastor.tesouraria.entries.create') }}"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-medium transition-colors">
                            <x-icon name="plus" class="w-5 h-5" /> Nova Entrada
                        </a>
                    @endif
                    <a href="{{ route('pastor.tesouraria.entries.index') }}"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 border border-white/20 text-white font-medium hover:bg-white/20 transition-colors">
                        <x-icon name="list" class="w-5 h-5" /> Lançamentos
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div
                class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-6 flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Receita do
                        Mês</p>
                    <p class="mt-1 text-xl font-bold text-gray-900 dark:text-white">R$
                        {{ number_format($monthlyIncome, 2, ',', '.') }}</p>
                </div>
                <div class="p-3 rounded-xl bg-green-500/20 text-green-600 dark:text-green-400">
                    <x-icon name="arrow-down" class="w-6 h-6" />
                </div>
            </div>
            <div
                class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-6 flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Despesa do
                        Mês</p>
                    <p class="mt-1 text-xl font-bold text-gray-900 dark:text-white">R$
                        {{ number_format($monthlyExpense, 2, ',', '.') }}</p>
                </div>
                <div class="p-3 rounded-xl bg-red-500/20 text-red-600 dark:text-red-400">
                    <x-icon name="arrow-up" class="w-6 h-6" />
                </div>
            </div>
            <div
                class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-6 flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Saldo do Mês
                    </p>
                    <p
                        class="mt-1 text-xl font-bold {{ $monthlyBalance >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                        R$ {{ number_format($monthlyBalance, 2, ',', '.') }}
                    </p>
                </div>
                <div class="p-3 rounded-xl bg-amber-500/20 text-amber-600 dark:text-amber-400">
                    <x-icon name="currency-dollar" class="w-6 h-6" />
                </div>
            </div>
            <div
                class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-6 flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Saldo do Ano
                    </p>
                    <p
                        class="mt-1 text-xl font-bold {{ $yearlyBalance >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                        R$ {{ number_format($yearlyBalance, 2, ',', '.') }}
                    </p>
                </div>
                <div class="p-3 rounded-xl bg-slate-500/20 text-slate-400">
                    <x-icon name="chart-line" class="w-6 h-6" />
                </div>
            </div>
        </div>

        @if (!empty($planoCooperativo))
            <div class="bg-amber-50 dark:bg-amber-900/20 rounded-2xl border border-amber-200 dark:border-amber-800 p-5">
                <div class="flex items-center gap-3 mb-2">
                    <div class="p-2 rounded-lg bg-amber-100 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400">
                        <x-icon name="building" class="w-5 h-5" />
                    </div>
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Plano Cooperativo
                        (Convenção)</h3>
                </div>
                <p class="text-xl font-bold text-amber-700 dark:text-amber-300">
                    {{ number_format($planoCooperativo['percent'] ?? 10, 1) }}%: R$
                    {{ number_format($planoCooperativo['suggested_amount'] ?? 0, 2, ',', '.') }}
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Base do mês: R$
                    {{ number_format($planoCooperativo['base_amount'] ?? 0, 2, ',', '.') }}</p>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div
                class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-700/50">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Receitas por
                        Categoria</h3>
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
                        <p class="text-sm text-gray-500 dark:text-gray-400 py-4">Nenhuma receita este mês.</p>
                    @endif
                </div>
            </div>
            <div
                class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-700/50">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Despesas por
                        Categoria</h3>
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
                        <p class="text-sm text-gray-500 dark:text-gray-400 py-4">Nenhuma despesa este mês.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 overflow-hidden">
            <div
                class="px-5 py-3 border-b border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-700/50 flex items-center justify-between">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Últimas Entradas</h3>
                <a href="{{ route('pastor.tesouraria.entries.index') }}"
                    class="text-xs font-bold text-amber-600 dark:text-amber-400 hover:underline">Ver todas</a>
            </div>
            @if ($recentEntries->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                        <thead class="bg-gray-50 dark:bg-slate-700/50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Data</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tipo</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Descrição</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Valor</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-slate-700">
                            @foreach ($recentEntries as $entry)
                                <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50">
                                    <td class="px-5 py-3 text-sm text-gray-700 dark:text-gray-300">
                                        {{ $entry->entry_date->format('d/m/Y') }}</td>
                                    <td class="px-5 py-3">
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $entry->type === 'income' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' }}">
                                            {{ $entry->type === 'income' ? 'Entrada' : 'Saída' }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $entry->title }}</td>
                                    <td
                                        class="px-5 py-3 text-right text-sm font-bold {{ $entry->type === 'income' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                        {{ $entry->type === 'income' ? '+' : '-' }} R$
                                        {{ number_format($entry->amount, 2, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="py-12 text-center text-gray-500 dark:text-gray-400">
                    <x-icon name="inbox" class="w-12 h-12 mx-auto mb-2 opacity-50" />
                    <p>Nenhuma entrada recente.</p>
                </div>
            @endif
        </div>

        @if ($activeCampaigns->count() > 0)
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Campanhas Ativas
                    </h3>
                    <a href="{{ route('pastor.tesouraria.campaigns.index') }}"
                        class="text-xs font-bold text-amber-600 dark:text-amber-400 hover:underline">Ver todas</a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach ($activeCampaigns as $campaign)
                        <div
                            class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-5">
                            <div class="flex items-start justify-between mb-3">
                                <h4 class="font-bold text-gray-900 dark:text-white">{{ $campaign->name }}</h4>
                                <span
                                    class="text-xs font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 px-2 py-1 rounded">{{ number_format($campaign->progress_percentage ?? 0, 1) }}%</span>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mb-2">
                                <span>R$ {{ number_format($campaign->current_amount ?? 0, 2, ',', '.') }}</span>
                                @if ($campaign->target_amount)
                                    <span>Meta: R$ {{ number_format($campaign->target_amount, 2, ',', '.') }}</span>
                                @endif
                            </div>
                            @if ($campaign->target_amount)
                                <div class="w-full bg-gray-100 dark:bg-slate-700 rounded-full h-2 overflow-hidden">
                                    <div class="bg-amber-500 h-full rounded-full transition-all"
                                        style="width: {{ min(100, $campaign->progress_percentage ?? 0) }}%"></div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endsection
