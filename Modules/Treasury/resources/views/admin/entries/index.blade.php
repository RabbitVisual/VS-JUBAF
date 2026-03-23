@extends('admin::components.layouts.master')

@section('content')
    <div class="space-y-8">
        <!-- Hero -->
        <div class="relative overflow-hidden rounded-3xl bg-linear-to-br from-gray-900 to-gray-800 text-white shadow-xl border border-gray-700/50">
            <div class="absolute inset-0 dash-pattern opacity-10"></div>
            <div class="absolute right-0 top-0 h-full w-1/2 bg-linear-to-l from-blue-600/20 to-transparent"></div>
            <div class="relative p-8 md:p-10 flex flex-col gap-6">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <span class="px-3 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300 text-xs font-bold uppercase tracking-wider">Movimentações</span>
                        </div>
                        <h1 class="text-3xl md:text-4xl font-black tracking-tight mb-2">Entradas Financeiras</h1>
                        <p class="text-gray-300 max-w-xl">Controle total das movimentações de receitas e despesas da congregação.</p>
                    </div>
                    @if ($permission->canCreateEntries())
                        <a href="{{ route('treasury.entries.create') }}"
                            class="inline-flex items-center justify-center px-6 py-3 rounded-xl bg-white text-gray-900 font-bold hover:bg-gray-100 shadow-lg shadow-white/10 transition-all">
                            <x-icon name="plus" style="duotone" class="w-5 h-5 text-blue-600 mr-2" />
                            Nova Entrada
                        </a>
                    @endif
                </div>
                @include('treasury::admin.partials.nav', ['breadcrumb' => ['Entradas' => null]])
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden relative">
            <div class="absolute right-0 top-0 w-40 h-40 bg-blue-50 dark:bg-blue-900/20 rounded-bl-full -mr-12 -mt-12"></div>
            <div class="relative px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 flex items-center gap-2">
                <x-icon name="filter" style="duotone" class="w-5 h-5 text-gray-500 dark:text-gray-400" />
                <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Filtros</h3>
            </div>
            <div class="p-6">
                <form method="GET" action="{{ route('treasury.entries.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo</label>
                        <select name="type" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all">
                            <option value="">Todos</option>
                            <option value="income" {{ request('type') === 'income' ? 'selected' : '' }}>Entradas</option>
                            <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>Saídas</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Categoria</label>
                        <select name="category" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all">
                            <option value="">Todas</option>
                            <option value="tithe" {{ request('category') === 'tithe' ? 'selected' : '' }}>Dízimo</option>
                            <option value="offering" {{ request('category') === 'offering' ? 'selected' : '' }}>Oferta</option>
                            <option value="donation" {{ request('category') === 'donation' ? 'selected' : '' }}>Doação</option>
                            <option value="campaign" {{ request('category') === 'campaign' ? 'selected' : '' }}>Campanha</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fundo</label>
                        <select name="fund_id" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all">
                            <option value="">Todos</option>
                            @foreach($financial_funds as $fund)
                                <option value="{{ $fund->id }}" {{ request('fund_id') == $fund->id ? 'selected' : '' }}>{{ $fund->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">De</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}"
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Até</label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}"
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full px-4 py-2.5 bg-gray-900 dark:bg-blue-600 text-white text-sm font-bold rounded-xl hover:bg-gray-800 dark:hover:bg-blue-700 transition-all shadow-sm inline-flex items-center justify-center gap-2">
                            <x-icon name="magnifying-glass" style="duotone" class="w-4 h-4" /> Filtrar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Data</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tipo / Categoria</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Descrição / Registro</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Valor</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($entries as $entry)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300 font-medium">
                                    {{ $entry->entry_date->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col gap-1">
                                        <span class="inline-flex items-center text-xs font-bold {{ $entry->type === 'income' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                            {{ $entry->type === 'income' ? 'Entrada' : 'Saída' }}
                                        </span>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white capitalize">
                                            {{ $entry->financialCategory?->name ?? str_replace('_', ' ', $entry->category) }}
                                        </span>
                                        @if($entry->type === 'expense' && $entry->expense_status)
                                            @php
                                                $statusConfig = [
                                                    'pending' => ['label' => 'Pendente', 'class' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300'],
                                                    'approved' => ['label' => 'Aprovado', 'class' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300'],
                                                    'paid' => ['label' => 'Pago', 'class' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300'],
                                                ];
                                                $sc = $statusConfig[$entry->expense_status] ?? ['label' => $entry->expense_status, 'class' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'];
                                            @endphp
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ $sc['class'] }}">{{ $sc['label'] }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                            {{ $entry->title }}
                                        </span>
                                        @if($entry->user)
                                            <span class="text-xs text-gray-500 dark:text-gray-400">Por: {{ $entry->user->name }}</span>
                                        @endif
                                        @if($entry->reference_number)
                                            <span class="text-xs text-gray-500 dark:text-gray-400">Ref: {{ $entry->reference_number }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <span class="text-sm font-bold {{ $entry->type === 'income' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                        {{ $entry->type === 'income' ? '+' : '-' }} R$ {{ number_format($entry->amount, 2, ',', '.') }}
                                    </span>
                                    @if($entry->payment_id)
                                        <span class="block text-xs text-gray-500 dark:text-gray-400 mt-0.5">Valor líquido</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        @if($permission->canCreateEntries() && !$entry->reversal_of_id && !isset($reversedEntryIds[$entry->id]))
                                            <form action="{{ route('treasury.entries.reverse', $entry) }}" method="POST" class="inline"
                                                onsubmit="if(confirm('Confirmar estorno? Será criada uma entrada inversa vinculada.')) { window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: 'Estornando...' } })); return true; } return false;">
                                                @csrf
                                                <button type="submit" class="p-2 text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/30 rounded-xl transition-colors" title="Estornar">
                                                    <x-icon name="arrow-rotate-left" style="duotone" class="w-4 h-4" />
                                                </button>
                                            </form>
                                        @endif
                                        @if($permission->canEditEntries())
                                            <a href="{{ route('treasury.entries.edit', $entry) }}"
                                                class="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-xl transition-colors" title="Editar">
                                                <x-icon name="pencil" style="duotone" class="w-4 h-4" />
                                            </a>
                                        @endif
                                        @if($permission->canDeleteEntries())
                                            <form action="{{ route('treasury.entries.destroy', $entry) }}" method="POST" class="inline"
                                                onsubmit="if(confirm('Excluir esta entrada?')) { window.dispatchEvent(new CustomEvent('loading-overlay:show')); return true; } return false;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-xl transition-colors" title="Excluir">
                                                    <x-icon name="trash" style="duotone" class="w-4 h-4" />
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-4">
                                            <x-icon name="inbox" style="duotone" class="w-8 h-8 text-gray-400 dark:text-gray-500" />
                                        </div>
                                        <p class="text-lg font-bold text-gray-900 dark:text-white">Nenhuma entrada encontrada</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Ajuste os filtros ou crie uma nova entrada.</p>
                                        @if ($permission->canCreateEntries())
                                            <a href="{{ route('treasury.entries.create') }}" class="mt-4 inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition-all text-sm">
                                                <x-icon name="plus" style="duotone" class="w-4 h-4" /> Nova Entrada
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
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30">
                    {{ $entries->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
