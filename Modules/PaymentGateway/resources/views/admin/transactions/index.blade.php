@extends('admin::components.layouts.master')

@php
    $pageTitle = 'Transações';
@endphp

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Transações</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Gerencie o histórico completo de pagamentos e doações.</p>
        </div>
        <div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                Total: {{ $transactions->total() }} registros
            </span>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
        <form action="{{ route('admin.transactions.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-4">
            <div class="md:col-span-5">
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Buscar</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <x-icon name="search" class="w-5 h-5 text-gray-400" />
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="ID, Nome ou Email..."
                        class="pl-10 w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>
            </div>
            <div class="md:col-span-5">
                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                <select name="status" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    <option value="">Todos os Status</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Confirmado</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pendente</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Falhou</option>
                </select>
            </div>
            <div class="md:col-span-2 flex items-end">
                <button type="submit" class="w-full px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-medium transition-colors shadow-sm flex items-center justify-center">
                    <x-icon name="filter" class="w-5 h-5 mr-2" />
                    Filtrar
                </button>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">ID / Data</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Doador</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Metódo</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Valor</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($transactions as $t)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-gray-900 dark:text-white font-mono">#{{ $t->transaction_id }}</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400 flex items-center mt-1">
                                    <x-icon name="calendar" class="w-3 h-3 mr-1" />
                                    {{ $t->created_at->format('d/m/Y H:i') }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mr-3">
                                    <span class="text-xs font-bold text-gray-600 dark:text-gray-300">{{ substr($t->payer_name ?? 'A', 0, 1) }}</span>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $t->payer_name ?? 'Anônimo' }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $t->payer_email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600">
                                @if($t->gateway && $t->gateway->icon)
                                    <x-icon name="{{ $t->gateway->icon }}" class="w-3 h-3 mr-1" />
                                @endif
                                {{ $t->gateway->display_name ?? 'Sistema' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-bold text-gray-900 dark:text-white">R$ {{ number_format($t->amount, 2, ',', '.') }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($t->status === 'completed')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5"></span>
                                    Confirmado
                                </span>
                            @elseif($t->status === 'pending')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">
                                    <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 mr-1.5 animate-pulse"></span>
                                    Pendente
                                </span>
                            @elseif($t->status === 'cancelled')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-500 mr-1.5"></span>
                                    Cancelado
                                </span>
                             @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 mr-1.5"></span>
                                    Falhou
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('admin.transactions.show', $t) }}" class="p-2 text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded-lg transition-colors" title="Ver detalhes e auditoria">
                                    <x-icon name="eye" class="w-5 h-5" />
                                </a>
                                <a href="{{ route('admin.transactions.receipt', $t) }}" target="_blank" class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-lg transition-colors" title="Comprovante para impressão">
                                    <x-icon name="print" class="w-5 h-5" />
                                </a>
                                @if($t->status === 'pending')
                                    <form action="{{ route('admin.transactions.cancel', $t->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Tem certeza que deseja cancelar este pagamento?');">
                                        @csrf
                                        <button type="submit" class="p-2 text-yellow-600 hover:text-yellow-900 dark:text-yellow-500 dark:hover:text-yellow-400 hover:bg-yellow-50 dark:hover:bg-yellow-900/20 rounded-lg transition-colors" title="Cancelar Pendência">
                                            <x-icon name="ban" class="w-5 h-5" />
                                        </button>
                                    </form>
                                @endif
                                <form action="{{ route('admin.transactions.destroy', $t->id) }}" method="POST" class="inline-block" onsubmit="return confirm('ATENÇÃO: Deseja realmente excluir este registro? Isso não pode ser desfeito.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors" title="Excluir Registro">
                                        <x-icon name="trash" class="w-5 h-5" />
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                                    <x-icon name="search" class="w-8 h-8 text-gray-400 dark:text-gray-500" />
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Nenhuma transação encontrada</h3>
                                <p class="text-gray-500 dark:text-gray-400 mt-1">Tente ajustar seus filtros de busca.</p>
                                <a href="{{ route('admin.transactions.index') }}" class="mt-4 text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 font-medium">Limpar Filtros</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
            {{ $transactions->links() }}
        </div>
    </div>
</div>
@endsection

