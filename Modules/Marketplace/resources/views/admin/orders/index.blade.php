@extends('admin::components.layouts.master')

@section('title', __('marketplace::messages.orders'))

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('marketplace::messages.orders') }}</h1>
        <a href="{{ route('admin.marketplace.dashboard') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
            <x-icon name="chart-pie" style="duotone" class="w-4 h-4 mr-2" /> Dashboard
        </a>
    </div>

    <div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-4">
        <form method="GET" action="{{ route('admin.marketplace.orders.index') }}" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Status</label>
                <select name="status" class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm">
                    <option value="">Todos</option>
                    @foreach(\Modules\Marketplace\Models\Order::statuses() as $key => $label)
                        <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Campanha</label>
                <select name="campaign_id" class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm">
                    <option value="">Todas</option>
                    @foreach($campaigns as $c)
                        <option value="{{ $c->id }}" {{ request('campaign_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <button type="submit" class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium">Filtrar</button>
            </div>
        </form>
    </div>

    <div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
            <thead class="bg-gray-50 dark:bg-gray-700/50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Pedido</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Cliente</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Total</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Data</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                @forelse($orders as $order)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                    <td class="px-4 py-3 font-mono text-sm text-gray-900 dark:text-white">#{{ $order->uuid }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $order->payer_name ?? $order->user?->name ?? '—' }}<br><span class="text-xs text-gray-500">{{ $order->email }}</span></td>
                    <td class="px-4 py-3 text-sm text-right font-medium text-gray-900 dark:text-white">R$ {{ number_format($order->total_amount, 2, ',', '.') }}</td>
                    <td class="px-4 py-3 text-center">
                        @php $statuses = \Modules\Marketplace\Models\Order::statuses(); @endphp
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium
                            @if($order->status === \Modules\Marketplace\Models\Order::STATUS_PENDING) bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-200
                            @elseif($order->status === \Modules\Marketplace\Models\Order::STATUS_PAID || $order->status === \Modules\Marketplace\Models\Order::STATUS_PREPARING) bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200
                            @elseif($order->status === \Modules\Marketplace\Models\Order::STATUS_SHIPPED_READY_FOR_PICKUP) bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-200
                            @elseif($order->status === \Modules\Marketplace\Models\Order::STATUS_COMPLETED) bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200
                            @else bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                            @endif">{{ $statuses[$order->status] ?? $order->status }}</span>
                    </td>
                    <td class="px-4 py-3 text-sm text-right text-gray-500 dark:text-gray-400">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('admin.marketplace.orders.show', $order) }}" class="text-blue-600 dark:text-blue-400 hover:underline text-sm font-medium">Ver</a>
                        <a href="{{ route('admin.marketplace.orders.label', $order) }}" target="_blank" class="ml-2 text-gray-600 dark:text-gray-400 hover:underline text-sm font-medium">Etiqueta</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-12 text-center text-gray-500 dark:text-gray-400">Nenhum pedido encontrado.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-600">
            {{ $orders->links() }}
        </div>
    </div>
</div>
@endsection
