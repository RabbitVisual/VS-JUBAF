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
                        <span class="px-3 py-1 rounded-full bg-green-500/20 border border-green-400/30 text-green-300 text-xs font-bold uppercase tracking-wider">Dashboard</span>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-black tracking-tight mb-2">Patrimônio</h1>
                    <p class="text-gray-300 max-w-xl">Gestão de bens, localizações, movimentações e etiquetas.</p>
                </div>
                <div class="flex flex-shrink-0 flex-wrap items-center gap-3">
                    <a href="{{ route('assets.admin.movements.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 backdrop-blur-md border border-white/20 text-white font-bold hover:bg-white/20 transition-colors">
                        <x-icon name="arrow-right-arrow-left" class="w-5 h-5" />
                        Movimentar
                    </a>
                    <a href="{{ route('assets.admin.assets.create') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-white text-gray-900 font-bold hover:bg-gray-100 transition-all shadow-lg shadow-white/10">
                        <x-icon name="plus" class="w-5 h-5 text-blue-600" />
                        Novo Item
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-200 dark:border-gray-700 relative overflow-hidden group hover:border-blue-200 dark:hover:border-blue-800 transition-colors">
                <div class="absolute right-0 top-0 w-32 h-32 bg-blue-50 dark:bg-blue-900/20 rounded-bl-full -mr-8 -mt-8"></div>
                <div class="relative z-10">
                    <p class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Total de Itens</p>
                    <h3 class="text-3xl font-black text-gray-900 dark:text-white mt-2">{{ number_format($totalAssets) }}</h3>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-200 dark:border-gray-700 relative overflow-hidden group hover:border-green-200 dark:hover:border-green-800 transition-colors">
                <div class="absolute right-0 top-0 w-32 h-32 bg-green-50 dark:bg-green-900/20 rounded-bl-full -mr-8 -mt-8"></div>
                <div class="relative z-10">
                    <p class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Valor Total</p>
                    <h3 class="text-3xl font-black text-gray-900 dark:text-white mt-2">R$ {{ number_format($totalValue, 2, ',', '.') }}</h3>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-200 dark:border-gray-700 relative overflow-hidden group hover:border-amber-200 dark:hover:border-amber-800 transition-colors">
                <div class="absolute right-0 top-0 w-32 h-32 bg-amber-50 dark:bg-amber-900/20 rounded-bl-full -mr-8 -mt-8"></div>
                <div class="relative z-10">
                    <p class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Em Manutenção</p>
                    <h3 class="text-3xl font-black text-gray-900 dark:text-white mt-2">{{ number_format($assetsInMaintenance) }}</h3>
                </div>
            </div>
            <a href="{{ route('assets.admin.labels.select') }}" class="block bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-200 dark:border-gray-700 relative overflow-hidden group hover:border-purple-200 dark:hover:border-purple-800 transition-colors">
                <div class="absolute right-0 top-0 w-32 h-32 bg-purple-50 dark:bg-purple-900/20 rounded-bl-full -mr-8 -mt-8"></div>
                <div class="relative z-10 flex flex-col justify-center h-full">
                    <p class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Etiquetas</p>
                    <h3 class="text-xl font-bold text-blue-600 dark:text-blue-400 mt-2 flex items-center">Gerar Etiquetas <x-icon name="arrow-right" class="w-4 h-4 ml-1" /></h3>
                </div>
            </a>
        </div>

        <!-- Recent Movements -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden relative">
            <div class="absolute right-0 top-0 w-32 h-32 bg-gray-50 dark:bg-gray-900/30 rounded-bl-full -mr-8 -mt-8"></div>
            <div class="relative px-6 py-5 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 dark:bg-gray-800">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Movimentações Recentes</h3>
                <a href="{{ route('assets.admin.movements.history') }}"
                    class="text-sm font-bold text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors">
                    Ver histórico completo
                </a>
            </div>
            @if ($recentMovements->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-xs uppercase text-gray-500 dark:text-gray-400 border-b border-gray-100 dark:border-gray-700">
                                <th class="px-6 py-4 font-bold bg-gray-50 dark:bg-gray-900/20">Data</th>
                                <th class="px-6 py-4 font-bold bg-gray-50 dark:bg-gray-900/20">Item</th>
                                <th class="px-6 py-4 font-bold bg-gray-50 dark:bg-gray-900/20">Tipo</th>
                                <th class="px-6 py-4 font-bold bg-gray-50 dark:bg-gray-900/20">Usuário</th>
                                <th class="px-6 py-4 font-bold bg-gray-50 dark:bg-gray-900/20">Locations</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach ($recentMovements as $movement)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ $movement->date->format('d/m/Y H:i') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm font-bold text-gray-900 dark:text-white block">{{ $movement->asset->name }}</span>
                                        <span class="text-xs text-gray-500">{{ $movement->asset->code }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                            {{ ucfirst($movement->type) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $movement->user->name ?? 'System' }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                        {{ $movement->previousLocation->name ?? '-' }} <x-icon name="arrow-right" class="w-3 h-3 inline mx-1" /> {{ $movement->newLocation->name }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                    <p>Nenhuma movimentação recente.</p>
                </div>
            @endif
        </div>
    </div>
@endsection

