@extends('liderancapanel::components.layouts.master')

@section('title', 'Transparência')

@section('content')
    <div class="space-y-8">
        <div
            class="relative overflow-hidden rounded-3xl bg-slate-800 dark:bg-slate-900 border border-amber-900/30 text-white shadow-xl">
            <div class="absolute right-0 top-0 h-full w-1/2 bg-gradient-to-l from-amber-600/20 to-transparent"></div>
            <div class="relative p-8 md:p-10">
                <div class="flex items-center gap-3 mb-2">
                    <span
                        class="px-3 py-1.5 rounded-full bg-amber-500/20 border border-amber-400/30 text-amber-200 text-xs font-bold uppercase tracking-wider">Transparência</span>
                    <span
                        class="px-3 py-1.5 rounded-full bg-emerald-500/20 border border-emerald-400/30 text-emerald-200 text-xs font-bold uppercase tracking-wider">Somente
                        leitura</span>
                </div>
                <h1 class="text-3xl md:text-4xl font-black tracking-tight mb-2">Saúde Financeira</h1>
                <p class="text-slate-300 max-w-xl">Visão de receitas, despesas e balancetes. Lançamentos e configurações são
                    feitos no painel técnico.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div
                class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-200 dark:border-slate-700 p-6 flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Receita do
                        Mês</p>
                    <p class="mt-1 text-xl font-bold text-gray-900 dark:text-white">R$
                        {{ number_format($monthlyIncome ?? 0, 2, ',', '.') }}</p>
                </div>
                <div class="p-3 rounded-xl bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400">
                    <x-icon name="arrow-down" class="w-6 h-6" />
                </div>
            </div>
            <div
                class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-200 dark:border-slate-700 p-6 flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Despesa do
                        Mês</p>
                    <p class="mt-1 text-xl font-bold text-gray-900 dark:text-white">R$
                        {{ number_format($monthlyExpense ?? 0, 2, ',', '.') }}</p>
                </div>
                <div class="p-3 rounded-xl bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400">
                    <x-icon name="arrow-up" class="w-6 h-6" />
                </div>
            </div>
            <div
                class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-200 dark:border-slate-700 p-6 flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Saldo do Mês
                    </p>
                    <p
                        class="mt-1 text-xl font-bold {{ ($monthlyBalance ?? 0) >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                        R$ {{ number_format($monthlyBalance ?? 0, 2, ',', '.') }}
                    </p>
                </div>
                <div class="p-3 rounded-xl bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                    <x-icon name="currency-dollar" class="w-6 h-6" />
                </div>
            </div>
            <div
                class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-200 dark:border-slate-700 p-6 flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Saldo do Ano
                    </p>
                    <p
                        class="mt-1 text-xl font-bold {{ ($yearlyBalance ?? 0) >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                        R$ {{ number_format($yearlyBalance ?? 0, 2, ',', '.') }}
                    </p>
                </div>
                <div class="p-3 rounded-xl bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400">
                    <x-icon name="chart-line" class="w-6 h-6" />
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div
                class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-700/50">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Receitas por
                        Categoria</h3>
                </div>
                <div class="p-6">
                    @if (($incomeByCategory ?? collect())->count() > 0)
                        <div class="space-y-3">
                            @foreach ($incomeByCategory as $item)
                                <div class="flex justify-between text-sm">
                                    <span
                                        class="text-gray-700 dark:text-gray-300 capitalize">{{ str_replace('_', ' ', $item->category) }}</span>
                                    <span class="font-bold text-gray-900 dark:text-white">R$
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
                class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-700/50">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Despesas por
                        Categoria</h3>
                </div>
                <div class="p-6">
                    @if (($expenseByCategory ?? collect())->count() > 0)
                        <div class="space-y-3">
                            @foreach ($expenseByCategory as $item)
                                <div class="flex justify-between text-sm">
                                    <span
                                        class="text-gray-700 dark:text-gray-300 capitalize">{{ str_replace('_', ' ', $item->category) }}</span>
                                    <span class="font-bold text-gray-900 dark:text-white">R$
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

        <div
            class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-700/50">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Últimas Entradas</h3>
            </div>
            @if (($recentEntries ?? collect())->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                        <thead class="bg-gray-50 dark:bg-slate-700/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Data</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tipo</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Descrição</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Valor</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-slate-700">
                            @foreach ($recentEntries as $entry)
                                <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/30">
                                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                        {{ $entry->entry_date->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $entry->type === 'income' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' }}">
                                            {{ $entry->type === 'income' ? 'Entrada' : 'Saída' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $entry->title }}</td>
                                    <td
                                        class="px-6 py-4 text-right text-sm font-bold {{ $entry->type === 'income' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                        {{ $entry->type === 'income' ? '+' : '-' }} R$
                                        {{ number_format($entry->amount, 2, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                    <x-icon name="document-text" class="w-12 h-12 mx-auto mb-2 opacity-50" />
                    <p>Nenhuma entrada recente.</p>
                </div>
            @endif
        </div>

        @if (($activeCampaigns ?? collect())->count() > 0)
            <div class="space-y-4">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Campanhas Ativas</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach ($activeCampaigns as $campaign)
                        <div
                            class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-5">
                            <h4 class="font-bold text-gray-900 dark:text-white">{{ $campaign->name }}</h4>
                            <div class="mt-2 flex justify-between text-sm text-gray-500 dark:text-gray-400">
                                <span>Arrecadado: R$
                                    {{ number_format($campaign->current_amount ?? 0, 2, ',', '.') }}</span>
                                @if (!empty($campaign->target_amount))
                                    <span>Meta: R$ {{ number_format($campaign->target_amount, 2, ',', '.') }}</span>
                                @endif
                            </div>
                            @if (!empty($campaign->target_amount) && isset($campaign->progress_percentage))
                                <div class="mt-2 w-full bg-gray-100 dark:bg-slate-700 rounded-full h-2">
                                    <div class="bg-amber-500 h-2 rounded-full transition-all"
                                        style="width: {{ min(100, $campaign->progress_percentage) }}%"></div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endsection
