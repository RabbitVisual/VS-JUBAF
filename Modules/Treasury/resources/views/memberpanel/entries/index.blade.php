@extends('memberpanel::components.layouts.master')

@section('page-title', 'Tesouraria - Entradas Financeiras')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-slate-950 transition-colors duration-200 pb-12">
        <div class="max-w-7xl mx-auto space-y-8 px-6 pt-8">

            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Entradas financeiras</h1>
                    <p class="text-gray-500 dark:text-slate-400 mt-1 max-w-md">Histórico de movimentações, dízimos, ofertas e despesas.</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="px-4 py-2 bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-xl shadow-sm flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                        <span class="text-xs font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider">Tesouraria</span>
                    </div>
                </div>
            </div>

            <div class="relative overflow-hidden bg-white dark:bg-slate-900 rounded-3xl shadow-xl dark:shadow-2xl border border-gray-100 dark:border-slate-800 transition-colors duration-200" data-tour="treasury-area">
                <div class="absolute inset-0 opacity-20 dark:opacity-40 pointer-events-none">
                    <div class="absolute -top-24 -left-20 w-96 h-96 bg-emerald-400 dark:bg-emerald-600 rounded-full blur-[100px]"></div>
                    <div class="absolute top-1/2 -right-20 w-80 h-80 bg-teal-400 dark:bg-teal-600 rounded-full blur-[100px]"></div>
                </div>
                <div class="relative px-8 py-10 flex flex-col md:flex-row md:items-center justify-between gap-8 z-10">
                    <div class="flex-1">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-100 dark:border-emerald-800 mb-4">
                            <x-icon name="arrow-right-arrow-left" style="duotone" class="w-3 h-3 text-emerald-600 dark:text-emerald-400" />
                            <span class="text-[10px] font-black uppercase tracking-widest text-emerald-600 dark:text-emerald-400">Fluxo de auditoria</span>
                        </div>
                        <p class="text-gray-500 dark:text-slate-300 font-medium max-w-xl text-lg leading-relaxed">
                            Histórico detalhado de todas as movimentações, dízimos, ofertas e despesas da instituição.
                        </p>
                    </div>
                    @if($permission->canCreateEntries())
                    <a href="{{ route('memberpanel.treasury.entries.create') }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-bold transition-all shadow-lg shadow-emerald-500/20 shrink-0">
                        <x-icon name="plus" style="duotone" class="w-4 h-4 mr-2" />
                        Registrar entrada
                    </a>
                    @endif
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-100 dark:border-slate-800 flex items-center gap-3 bg-gray-50/50 dark:bg-slate-900/50">
                    <div class="p-2 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-xl">
                        <x-icon name="filter-list" style="duotone" class="w-5 h-5" />
                    </div>
                    <h3 class="text-xs font-black uppercase tracking-widest text-gray-700 dark:text-slate-300">Filtros</h3>
                </div>
                <div class="p-8">
                <form method="GET" action="{{ route('memberpanel.treasury.entries.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
                    <div class="space-y-2">
                        <label class="block text-[10px] font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Tipo</label>
                        <select name="type" class="w-full px-4 py-2.5 border border-gray-300 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                            <option value="">Todos os fluxos</option>
                            <option value="income" {{ request('type') === 'income' ? 'selected' : '' }}>Entradas (+)</option>
                            <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>Saídas (-)</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-[10px] font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Categoria</label>
                        <select name="category" class="w-full px-4 py-2.5 border border-gray-300 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                            <option value="">Todas categorias</option>
                            <option value="tithe" {{ request('category') === 'tithe' ? 'selected' : '' }}>Dízimos</option>
                            <option value="offering" {{ request('category') === 'offering' ? 'selected' : '' }}>Ofertas</option>
                            <option value="donation" {{ request('category') === 'donation' ? 'selected' : '' }}>Doações</option>
                            <option value="campaign" {{ request('category') === 'campaign' ? 'selected' : '' }}>Campanhas</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[10px] font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Início</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}"
                               class="w-full px-4 py-2.5 border border-gray-300 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[10px] font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Fim</label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}"
                               class="w-full px-4 py-2.5 border border-gray-300 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                    </div>

                    <div class="flex items-end">
                        <button type="submit" class="w-full py-3 bg-gray-900 dark:bg-indigo-600 hover:bg-gray-800 dark:hover:bg-indigo-700 text-white rounded-xl font-bold text-sm transition-all flex items-center justify-center gap-2">
                            <x-icon name="magnifying-glass" style="duotone" class="w-4 h-4" />
                            Filtrar
                        </button>
                    </div>
                </form>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-100 dark:border-slate-800 flex items-center gap-3 bg-gray-50/50 dark:bg-slate-900/50">
                    <div class="p-2 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-xl">
                        <x-icon name="list" style="duotone" class="w-5 h-5" />
                    </div>
                    <h3 class="text-xs font-black uppercase tracking-widest text-gray-700 dark:text-slate-300">Lista de entradas</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[800px]">
                        <thead>
                            <tr class="bg-gray-50/50 dark:bg-slate-800/50 border-b border-gray-100 dark:border-slate-800">
                                <th class="px-8 py-5 text-left text-[10px] font-black text-gray-500 dark:text-slate-400 uppercase tracking-widest">Data</th>
                                <th class="px-8 py-5 text-left text-[10px] font-black text-gray-500 dark:text-slate-400 uppercase tracking-widest">Classificação</th>
                                <th class="px-8 py-5 text-left text-[10px] font-black text-gray-500 dark:text-slate-400 uppercase tracking-widest">Descrição / Origem</th>
                                <th class="px-8 py-5 text-left text-[10px] font-black text-gray-500 dark:text-slate-400 uppercase tracking-widest">Montante</th>
                                <th class="px-8 py-5 text-right text-[10px] font-black text-gray-500 dark:text-slate-400 uppercase tracking-widest">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                            @forelse($entries as $entry)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-800/30 transition-all group">
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-slate-800 flex items-center justify-center text-gray-500 dark:text-slate-400">
                                            <x-icon name="calendar-day" style="duotone" class="w-5 h-5" />
                                        </div>
                                        <span class="text-sm font-bold text-gray-900 dark:text-white">
                                            {{ $entry->entry_date ? $entry->entry_date->format('d/m/Y') : '-' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="flex flex-col gap-1.5">
                                        <span class="px-3 py-1 inline-flex text-[10px] font-bold rounded-lg border uppercase tracking-widest w-fit
                                            {{ $entry->type === 'income' ? 'bg-emerald-500/10 text-emerald-600 border-emerald-500/20' : 'bg-rose-500/10 text-rose-600 border-rose-500/20' }}">
                                            {{ $entry->type === 'income' ? 'Entrada' : 'Saída' }}
                                        </span>
                                        <span class="text-[10px] text-gray-500 dark:text-slate-400 font-bold uppercase tracking-widest pl-1">
                                            {{ str_replace('_', ' ', $entry->category) }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <p class="text-sm font-bold text-gray-800 dark:text-slate-200 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                        {{ $entry->title }}
                                    </p>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <span class="text-lg font-bold tabular-nums {{ $entry->type === 'income' ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                        {{ $entry->type === 'income' ? '+' : '-' }} R$ {{ number_format($entry->amount, 2, ',', '.') }}
                                    </span>
                                    @if($entry->payment_id)
                                        <span class="block text-[10px] text-gray-500 dark:text-slate-400 mt-0.5" title="Valor líquido já descontadas as taxas do gateway de pagamento (ex.: Mercado Pago, Stripe).">Valor líquido (gateway)</span>
                                    @endif
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end gap-3 opacity-0 group-hover:opacity-100 transition-all">
                                        @if($permission->canEditEntries())
                                        <a href="{{ route('memberpanel.treasury.entries.edit', $entry) }}"
                                           class="inline-flex items-center justify-center w-10 h-10 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-800/50 rounded-xl transition-all" title="Editar">
                                            <x-icon name="pen-to-square" style="duotone" class="w-4 h-4" />
                                        </a>
                                        @endif
                                        @if($permission->canDeleteEntries())
                                        <form action="{{ route('memberpanel.treasury.entries.destroy', $entry) }}" method="POST" class="inline"
                                              onsubmit="return confirm('Tem certeza que deseja excluir esta movimentação? Esta ação é irreversível.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center justify-center w-10 h-10 bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-800/50 rounded-xl transition-all" title="Excluir">
                                                <x-icon name="trash-can" style="duotone" class="w-4 h-4" />
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-8 py-32 text-center">
                                    <div class="flex flex-col items-center justify-center space-y-4">
                                        <div class="w-24 h-24 bg-gray-50 dark:bg-slate-800 rounded-3xl flex items-center justify-center mb-4">
                                            <x-icon name="file-invoice-dollar" style="duotone" class="w-10 h-10 text-gray-400 dark:text-slate-500" />
                                        </div>
                                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Nenhum registro</h3>
                                        <p class="text-gray-500 dark:text-slate-400 text-sm max-w-sm mx-auto">
                                            Não há entradas com os filtros aplicados. Ajuste os filtros ou registre uma nova entrada.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($entries->hasPages())
                <div class="px-8 py-6 bg-gray-50/50 dark:bg-slate-800/20 border-t border-gray-100 dark:border-slate-800">
                    {{ $entries->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection
