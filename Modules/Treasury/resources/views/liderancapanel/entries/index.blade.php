@extends('liderancapanel::components.layouts.master')

@section('title', 'Lançamentos Financeiros')

@section('content')
    <div class="space-y-6">
        @if (session('success'))
            <div
                class="rounded-2xl border border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-900/20 px-4 py-3 text-sm font-medium text-green-800 dark:text-green-200 flex items-center gap-2">
                <x-icon name="check-circle" class="w-5 h-5 flex-shrink-0" /> {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div
                class="rounded-2xl border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/20 px-4 py-3 text-sm font-medium text-red-800 dark:text-red-200 flex items-center gap-2">
                <x-icon name="exclamation-circle" class="w-5 h-5 flex-shrink-0" /> {{ session('error') }}
            </div>
        @endif

        <div
            class="rounded-3xl bg-gradient-to-br from-slate-800 via-slate-900 to-slate-800 text-white shadow-xl border border-amber-900/30 overflow-hidden">
            <div class="p-6 md:p-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <nav class="flex items-center gap-2 text-sm text-slate-400 font-medium mb-2">
                        <a href="{{ route('pastor.tesouraria.dashboard') }}"
                            class="hover:text-white transition-colors">Tesouraria</a>
                        <x-icon name="chevron-right" class="w-3.5 h-3.5" />
                        <span class="text-white font-bold">Lançamentos</span>
                    </nav>
                    <h1 class="text-2xl md:text-3xl font-bold tracking-tight mb-1">Entradas e Saídas</h1>
                    <p class="text-slate-300 text-sm max-w-xl">Controle detalhado de receitas e despesas. Filtre por tipo,
                        categoria e período.</p>
                </div>
                @if ($permission->canCreateEntries())
                    <a href="{{ route('pastor.tesouraria.entries.create') }}"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-medium transition-colors">
                        <x-icon name="plus" class="w-5 h-5" /> Nova Entrada
                    </a>
                @endif
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 overflow-hidden">
            <div
                class="px-5 py-3 border-b border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-700/50 flex items-center gap-2">
                <x-icon name="filter" class="w-5 h-5 text-gray-500 dark:text-gray-400" />
                <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Filtros</h3>
            </div>
            <div class="p-5">
                <form method="GET" action="{{ route('pastor.tesouraria.entries.index') }}"
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo</label>
                        <select name="type"
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                            <option value="">Todos</option>
                            <option value="income" {{ request('type') === 'income' ? 'selected' : '' }}>Entradas</option>
                            <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>Saídas</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Categoria</label>
                        <select name="category"
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                            <option value="">Todas</option>
                            <option value="tithe" {{ request('category') === 'tithe' ? 'selected' : '' }}>Dízimo</option>
                            <option value="offering" {{ request('category') === 'offering' ? 'selected' : '' }}>Oferta
                            </option>
                            <option value="donation" {{ request('category') === 'donation' ? 'selected' : '' }}>Doação
                            </option>
                            <option value="campaign" {{ request('category') === 'campaign' ? 'selected' : '' }}>Campanha
                            </option>
                            <option value="maintenance" {{ request('category') === 'maintenance' ? 'selected' : '' }}>
                                Manutenção</option>
                            <option value="utilities" {{ request('category') === 'utilities' ? 'selected' : '' }}>Contas
                            </option>
                            <option value="salary" {{ request('category') === 'salary' ? 'selected' : '' }}>Salários
                            </option>
                            <option value="other" {{ request('category') === 'other' ? 'selected' : '' }}>Outros</option>
                        </select>
                    </div>
                    @if (isset($financial_funds) && $financial_funds->isNotEmpty())
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fundo</label>
                            <select name="fund_id"
                                class="w-full px-4 py-2.5 border border-gray-300 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                                <option value="">Todos</option>
                                @foreach ($financial_funds as $fund)
                                    <option value="{{ $fund->id }}"
                                        {{ request('fund_id') == $fund->id ? 'selected' : '' }}>{{ $fund->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">De</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}"
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Até</label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}"
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                    </div>
                    <div class="flex items-end">
                        <button type="submit"
                            class="w-full px-4 py-2.5 bg-slate-800 dark:bg-amber-600 text-white text-sm font-bold rounded-xl hover:bg-slate-700 dark:hover:bg-amber-700 transition-all inline-flex items-center justify-center gap-2">
                            <x-icon name="magnifying-glass" class="w-4 h-4" /> Filtrar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                    <thead class="bg-gray-50 dark:bg-slate-700/50">
                        <tr>
                            <th
                                class="px-5 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Data</th>
                            <th
                                class="px-5 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Tipo / Categoria</th>
                            <th
                                class="px-5 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Descrição / Registro</th>
                            <th
                                class="px-5 py-4 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Valor</th>
                            <th
                                class="px-5 py-4 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-slate-700">
                        @forelse($entries as $entry)
                            <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors group">
                                <td
                                    class="px-5 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300 font-medium">
                                    {{ $entry->entry_date->format('d/m/Y') }}</td>
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <div class="flex flex-col gap-1">
                                        <span
                                            class="text-xs font-bold {{ $entry->type === 'income' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                            {{ $entry->type === 'income' ? 'Entrada' : 'Saída' }}
                                        </span>
                                        <span
                                            class="text-sm font-medium text-gray-900 dark:text-white capitalize">{{ $entry->financialCategory?->name ?? str_replace('_', ' ', $entry->category) }}</span>
                                        @if ($entry->type === 'expense' && $entry->expense_status)
                                            @php
                                                $statusConfig = [
                                                    'pending' => [
                                                        'label' => 'Pendente',
                                                        'class' =>
                                                            'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300',
                                                    ],
                                                    'approved' => [
                                                        'label' => 'Aprovado',
                                                        'class' =>
                                                            'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300',
                                                    ],
                                                    'paid' => [
                                                        'label' => 'Pago',
                                                        'class' =>
                                                            'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300',
                                                    ],
                                                ];
                                                $sc = $statusConfig[$entry->expense_status] ?? [
                                                    'label' => $entry->expense_status,
                                                    'class' => 'bg-gray-100 text-gray-800',
                                                ];
                                            @endphp
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ $sc['class'] }}">{{ $sc['label'] }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex flex-col">
                                        <span
                                            class="text-sm font-bold text-gray-900 dark:text-white">{{ $entry->title }}</span>
                                        @if ($entry->user)
                                            <span class="text-xs text-gray-500 dark:text-gray-400">Por:
                                                {{ $entry->user->name }}</span>
                                        @endif
                                        @if ($entry->reference_number)
                                            <span class="text-xs text-gray-500 dark:text-gray-400">Ref:
                                                {{ $entry->reference_number }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-right">
                                    <span
                                        class="text-sm font-bold {{ $entry->type === 'income' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                        {{ $entry->type === 'income' ? '+' : '-' }} R$
                                        {{ number_format($entry->amount, 2, ',', '.') }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-right">
                                    <div
                                        class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        @if ($permission->canCreateEntries() && !$entry->reversal_of_id && !isset($reversedEntryIds[$entry->id]))
                                            <form action="{{ route('pastor.tesouraria.entries.reverse', $entry) }}"
                                                method="POST" class="inline"
                                                onsubmit="if(confirm('Confirmar estorno? Será criada uma entrada inversa vinculada.')) { window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: 'Estornando...' } })); return true; } return false;">
                                                @csrf
                                                <button type="submit"
                                                    class="p-2 text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/30 rounded-xl transition-colors"
                                                    title="Estornar">
                                                    <x-icon name="arrow-rotate-left" class="w-4 h-4" />
                                                </button>
                                            </form>
                                        @endif
                                        @if ($permission->canEditEntries())
                                            <a href="{{ route('pastor.tesouraria.entries.edit', $entry) }}"
                                                class="p-2 text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/30 rounded-xl transition-colors"
                                                title="Editar">
                                                <x-icon name="pencil" class="w-4 h-4" />
                                            </a>
                                        @endif
                                        @if ($permission->canDeleteEntries())
                                            <form action="{{ route('pastor.tesouraria.entries.destroy', $entry) }}"
                                                method="POST" class="inline"
                                                onsubmit="if(confirm('Excluir esta entrada?')) { window.dispatchEvent(new CustomEvent('loading-overlay:show')); return true; } return false;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-xl transition-colors"
                                                    title="Excluir">
                                                    <x-icon name="trash" class="w-4 h-4" />
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div
                                            class="w-16 h-16 rounded-full bg-gray-100 dark:bg-slate-700 flex items-center justify-center mb-4">
                                            <x-icon name="inbox" class="w-8 h-8 text-gray-400 dark:text-gray-500" />
                                        </div>
                                        <p class="text-lg font-bold text-gray-900 dark:text-white">Nenhuma entrada
                                            encontrada</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Ajuste os filtros ou crie
                                            uma nova entrada.</p>
                                        @if ($permission->canCreateEntries())
                                            <a href="{{ route('pastor.tesouraria.entries.create') }}"
                                                class="mt-4 inline-flex items-center gap-2 px-5 py-2.5 bg-amber-500 text-white font-bold rounded-xl hover:bg-amber-600 transition-all text-sm">
                                                <x-icon name="plus" class="w-4 h-4" /> Nova Entrada
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($entries->hasPages())
                <div class="px-5 py-4 border-t border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-700/30">
                    {{ $entries->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
