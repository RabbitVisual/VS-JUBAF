@extends('admin::components.layouts.master')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Gerenciar Notificações</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Crie e gerencie notificações para os membros.</p>
            </div>
            <div class="flex items-center gap-3">
                <form action="{{ route('admin.notifications.clear-my-inbox') }}" method="POST" class="inline" onsubmit="return confirm('Excluir todas as notificações da sua caixa?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600">
                        <x-icon name="trash" class="-ml-0.5 mr-1.5 h-4 w-4" />
                        Limpar minha caixa
                    </button>
                </form>
                <a href="{{ route('admin.notifications.control.dashboard') }}"
                    class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600">
                    <x-icon name="chart-line" class="-ml-0.5 mr-1.5 h-4 w-4" />
                    Control Room
                </a>
                <a href="{{ route('admin.notifications.create') }}"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-500 dark:hover:bg-blue-600">
                    <x-icon name="plus" class="-ml-1 mr-2 h-5 w-5" />
                    Nova Notificação
                </a>
            </div>
        </div>

        <!-- Filtros -->
        <form method="GET" action="{{ route('admin.notifications.index') }}" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex flex-wrap items-end gap-4">
                <div>
                    <label for="type" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Tipo</label>
                    <select name="type" id="type" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                        <option value="">Todos</option>
                        <option value="info" {{ request('type') === 'info' ? 'selected' : '' }}>Informação</option>
                        <option value="success" {{ request('type') === 'success' ? 'selected' : '' }}>Sucesso</option>
                        <option value="warning" {{ request('type') === 'warning' ? 'selected' : '' }}>Aviso</option>
                        <option value="error" {{ request('type') === 'error' ? 'selected' : '' }}>Erro</option>
                        <option value="achievement" {{ request('type') === 'achievement' ? 'selected' : '' }}>Conquista</option>
                    </select>
                </div>
                <div>
                    <label for="priority" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Prioridade</label>
                    <select name="priority" id="priority" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                        <option value="">Todas</option>
                        <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Baixa</option>
                        <option value="normal" {{ request('priority') === 'normal' ? 'selected' : '' }}>Normal</option>
                        <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>Alta</option>
                        <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgente</option>
                    </select>
                </div>
                <div>
                    <label for="date_from" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">De</label>
                    <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div>
                    <label for="date_to" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Até</label>
                    <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <button type="submit" class="px-3 py-2 text-sm font-medium text-white bg-gray-600 dark:bg-gray-600 rounded-md hover:bg-gray-700 dark:hover:bg-gray-500">Filtrar</button>
                @if(request()->hasAny(['type','priority','date_from','date_to']))
                <a href="{{ route('admin.notifications.index') }}" class="px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">Limpar</a>
                @endif
            </div>
        </form>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Título</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tipo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Prioridade</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Criada em</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($notifications as $notification)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $notification->title }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400 truncate max-w-xs">{{ Str::limit($notification->message, 50) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $typeColors = [
                                            'info' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                            'success' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                            'warning' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                            'error' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                            'achievement' => 'bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200',
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $typeColors[$notification->type] ?? $typeColors['info'] }}">
                                        {{ ucfirst($notification->type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-900 dark:text-white">{{ ucfirst($notification->priority) }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $notification->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="{{ route('admin.notifications.show', $notification) }}"
                                            class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                            Ver
                                        </a>
                                        <form action="{{ route('admin.notifications.destroy', $notification) }}" method="POST" class="inline"
                                            onsubmit="return confirm('Tem certeza que deseja remover esta notificação?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                                Remover
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                    Nenhuma notificação encontrada.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($notifications->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

