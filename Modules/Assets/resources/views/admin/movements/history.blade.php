@extends('admin::components.layouts.master')

@section('content')
    <div class="space-y-8">
        <!-- Hero -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 text-white shadow-xl border border-gray-700/50">
            <div class="absolute inset-0 dash-pattern opacity-10"></div>
            <div class="absolute right-0 top-0 h-full w-1/2 bg-gradient-to-l from-blue-600/20 to-transparent"></div>
            <div class="relative p-8 md:p-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <div class="flex items-center gap-3 mb-2 flex-wrap">
                        <span class="px-3 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300 text-xs font-bold uppercase tracking-wider">Patrimônio</span>
                        <span class="px-3 py-1 rounded-full bg-green-500/20 border border-green-400/30 text-green-300 text-xs font-bold uppercase tracking-wider">Histórico</span>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-black tracking-tight mb-2">Histórico de Movimentações</h1>
                    <p class="text-gray-300 max-w-xl">Transferências, empréstimos e devoluções registrados.</p>
                </div>
                <a href="{{ route('assets.admin.movements.create') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-white text-gray-900 font-bold hover:bg-gray-100 transition-all shadow-lg shadow-white/10">
                    <x-icon name="arrow-right-arrow-left" class="w-5 h-5 text-blue-600" />
                    Nova Movimentação
                </a>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 relative overflow-hidden">
            <div class="absolute right-0 top-0 w-32 h-32 bg-blue-50 dark:bg-blue-900/20 rounded-bl-full -mr-8 -mt-8"></div>
            <form action="{{ route('assets.admin.movements.history') }}" method="GET" class="relative grid grid-cols-1 md:grid-cols-5 gap-4">
                <div class="relative md:col-span-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nome ou código do item..."
                           class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <x-icon name="search" class="h-5 w-5 text-gray-400" />
                    </div>
                </div>
                <div>
                    <select name="type" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500" onchange="this.form.submit()">
                        <option value="">Todos os Tipos</option>
                        <option value="transfer" {{ request('type') == 'transfer' ? 'selected' : '' }}>Transferência</option>
                        <option value="loan" {{ request('type') == 'loan' ? 'selected' : '' }}>Empréstimo</option>
                        <option value="return" {{ request('type') == 'return' ? 'selected' : '' }}>Devolução</option>
                        <option value="maintenance_out" {{ request('type') == 'maintenance_out' ? 'selected' : '' }}>Saída Manutenção</option>
                        <option value="maintenance_return" {{ request('type') == 'maintenance_return' ? 'selected' : '' }}>Retorno Manutenção</option>
                        <option value="disposal" {{ request('type') == 'disposal' ? 'selected' : '' }}>Descarte</option>
                    </select>
                </div>
                <div>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 text-sm" placeholder="Data Inicial" onchange="this.form.submit()">
                </div>
                <div>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 text-sm" placeholder="Data Final" onchange="this.form.submit()">
                </div>
            </form>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden relative">
            <div class="absolute right-0 top-0 w-32 h-32 bg-gray-50 dark:bg-gray-900/30 rounded-bl-full -mr-8 -mt-8"></div>
            <div class="relative overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                        <tr>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Data</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Item</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tipo</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Usuário</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Origem > Destino</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Responsável</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach ($movements as $movement)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-300">
                                    {{ $movement->date->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $movement->asset->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $movement->asset->code }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                                        {{ ucfirst($movement->type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                    {{ $movement->user->name ?? 'Sistema' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                    <div class="flex items-center">
                                        <span class="text-red-500">{{ $movement->previousLocation->name ?? '-' }}</span>
                                        <x-icon name="arrow-narrow-right" class="w-4 h-4 mx-2 text-gray-400" />
                                        <span class="text-green-500">{{ $movement->newLocation->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                    {{ $movement->responsible->name ?? '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="relative p-4 border-t border-gray-100 dark:border-gray-700">
                {{ $movements->links() }}
            </div>
        </div>
    </div>
@endsection

