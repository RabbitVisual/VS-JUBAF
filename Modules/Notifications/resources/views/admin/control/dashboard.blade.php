@extends('admin::components.layouts.master')

@section('content')
    <div class="space-y-6">
        {{-- Alerta contingência: modo polling ativo --}}
        <div id="notification-polling-alert" class="hidden rounded-xl border border-amber-200 dark:border-amber-800 bg-amber-50 dark:bg-amber-950/30 px-4 py-3 flex items-center gap-3" role="status">
            <x-icon name="triangle-exclamation" class="w-5 h-5 text-amber-600 dark:text-amber-400 shrink-0" />
            <p class="text-sm font-medium text-amber-800 dark:text-amber-200">Sistema operando em modo de Polling Otimizado (Contingência ativa).</p>
        </div>
        <script>
            (function() {
                function showPollingAlert() {
                    var el = document.getElementById('notification-polling-alert');
                    if (!el) return;
                    if ((window.NotificationSystem && window.NotificationSystem.mode === 'polling') || document.body.getAttribute('data-notification-mode') === 'polling') {
                        el.classList.remove('hidden');
                    }
                }
                document.addEventListener('DOMContentLoaded', function() { setTimeout(showPollingAlert, 600); });
                document.addEventListener('notification:mode-changed', function(e) {
                    if (e.detail && e.detail.mode === 'polling') document.getElementById('notification-polling-alert')?.classList.remove('hidden');
                });
            })();
        </script>

        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Central de Notificações (Control Room)</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Estatísticas de entrega e canais.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.notifications.index') }}" class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600">
                    <x-icon name="list" class="w-4 h-4 mr-1.5" /> Listar notificações
                </a>
                <a href="{{ route('admin.notifications.dlq.index') }}" class="inline-flex items-center px-3 py-2 text-sm font-medium text-red-700 dark:text-red-300 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-md hover:bg-red-100 dark:hover:bg-red-900/30">
                    <x-icon name="inbox" class="w-4 h-4 mr-1.5" /> DLQ
                </a>
                <a href="{{ route('admin.notifications.broadcast.create') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">
                    <x-icon name="broadcast-tower" class="w-4 h-4 mr-1.5" /> Broadcast
                </a>
                <a href="{{ route('admin.notifications.templates.index') }}" class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600">
                    <x-icon name="code" class="w-4 h-4 mr-1.5" /> Templates
                </a>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.notifications.control.dashboard') }}" class="flex items-center gap-2">
            <label for="days" class="text-sm font-medium text-gray-700 dark:text-gray-300">Período:</label>
            <select name="days" id="days" onchange="this.form.submit()" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                <option value="7" {{ $days == 7 ? 'selected' : '' }}>Últimos 7 dias</option>
                <option value="30" {{ $days == 30 ? 'selected' : '' }}>Últimos 30 dias</option>
                <option value="90" {{ $days == 90 ? 'selected' : '' }}>Últimos 90 dias</option>
            </select>
        </form>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 shadow-sm" title="Total de notificações enviadas no período">
                <p class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Enviadas</p>
                <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">{{ number_format($sent) }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 shadow-sm" title="Notificações que falharam após retentativas automáticas">
                <p class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Falhas</p>
                <p class="text-2xl font-bold text-red-600 dark:text-red-400 mt-1">{{ number_format($failed) }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 shadow-sm" title="Taxa de Sucesso: Indica quantas notificações chegaram efetivamente ao destino final após as retentativas automáticas.">
                <p class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Taxa de sucesso</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $successRate }}%</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 shadow-sm" title="Percentual de notificações abertas (marcadas como lidas) pelos destinatários">
                <p class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Abertas</p>
                <p class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-1">{{ $openRate }}%</p>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 shadow-sm">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Por canal</h2>
            <div class="flex flex-wrap gap-4">
                @forelse($byChannel as $channel => $total)
                    <div class="px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-700">
                        <span class="font-medium text-gray-900 dark:text-white">{{ $channel }}</span>
                        <span class="text-gray-600 dark:text-gray-300 ml-1">({{ number_format($total) }})</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400">Nenhum dado no período.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
