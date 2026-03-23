@extends('admin::components.layouts.master')

@section('title', 'Pedido #' . $order->uuid)

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Pedido #{{ $order->uuid }}</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">{{ $order->created_at->format('d/m/Y H:i') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.marketplace.orders.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                <x-icon name="arrow-left" style="duotone" class="w-4 h-4 mr-2" /> Voltar
            </a>
            <a href="{{ route('admin.marketplace.orders.label', $order) }}" target="_blank" class="inline-flex items-center px-4 py-2 rounded-lg bg-gray-700 hover:bg-gray-800 text-white text-sm font-medium">
                <x-icon name="print" style="duotone" class="w-4 h-4 mr-2" /> Imprimir etiqueta
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-lg bg-green-50 dark:bg-green-900/20 text-green-800 dark:text-green-200 px-4 py-3">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Cliente</h2>
            <p class="font-medium text-gray-900 dark:text-white">{{ $order->payer_name ?? $order->user?->name ?? '—' }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $order->email }}</p>
            @if($order->delivery_type === 'shipping' && $order->shipping_address)
                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Endereço de entrega</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ $order->shipping_address['street'] ?? '' }}, {{ $order->shipping_address['number'] ?? '' }}<br>
                        {{ $order->shipping_address['neighborhood'] ?? '' }} {{ $order->shipping_address['complement'] ?? '' }}<br>
                        {{ $order->shipping_address['city'] ?? '' }} - {{ $order->shipping_address['state'] ?? '' }}<br>
                        CEP {{ $order->shipping_address['zipcode'] ?? '' }}
                    </p>
                </div>
            @endif
            @if($order->delivery_type === 'local_pickup' && $order->pickupLocation)
                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Retirada</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $order->pickupLocation->name }}<br>{{ $order->pickupLocation->address }}</p>
                </div>
            @endif
        </div>
        <div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Status e valor</h2>
            <p class="flex items-center gap-2">
                @php $statuses = \Modules\Marketplace\Models\Order::statuses(); @endphp
                <span class="px-3 py-1 rounded-full text-sm font-medium
                    @if($order->status === \Modules\Marketplace\Models\Order::STATUS_PENDING) bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-200
                    @elseif($order->status === \Modules\Marketplace\Models\Order::STATUS_PAID || $order->status === \Modules\Marketplace\Models\Order::STATUS_PREPARING) bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200
                    @elseif($order->status === \Modules\Marketplace\Models\Order::STATUS_SHIPPED_READY_FOR_PICKUP) bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-200
                    @elseif($order->status === \Modules\Marketplace\Models\Order::STATUS_COMPLETED) bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200
                    @else bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                    @endif">{{ $statuses[$order->status] ?? $order->status }}</span>
            </p>
            <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">R$ {{ number_format($order->total_amount, 2, ',', '.') }}</p>
            @if($order->tracking_code)
                <p class="mt-2 text-sm">
                    <a href="{{ $order->tracking_url }}" target="_blank" class="text-blue-600 dark:text-blue-400 hover:underline">Rastreio: {{ $order->tracking_code }}</a>
                </p>
            @endif

            @if(!in_array($order->status, [\Modules\Marketplace\Models\Order::STATUS_COMPLETED, \Modules\Marketplace\Models\Order::STATUS_CANCELLED]))
            <form action="{{ route('admin.marketplace.orders.status', $order) }}" method="POST" class="mt-6 space-y-4" onsubmit="window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: 'Atualizando status...' } }))">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Alterar status</label>
                    <select name="status" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2">
                        <option value="preparing" {{ $order->status === \Modules\Marketplace\Models\Order::STATUS_PREPARING ? 'selected' : '' }}>{{ $statuses[\Modules\Marketplace\Models\Order::STATUS_PREPARING] }}</option>
                        <option value="shipped_ready_for_pickup" {{ $order->status === \Modules\Marketplace\Models\Order::STATUS_SHIPPED_READY_FOR_PICKUP ? 'selected' : '' }}>{{ $statuses[\Modules\Marketplace\Models\Order::STATUS_SHIPPED_READY_FOR_PICKUP] }}</option>
                        <option value="completed" {{ $order->status === \Modules\Marketplace\Models\Order::STATUS_COMPLETED ? 'selected' : '' }}>{{ $statuses[\Modules\Marketplace\Models\Order::STATUS_COMPLETED] }}</option>
                        <option value="cancelled">{{ $statuses[\Modules\Marketplace\Models\Order::STATUS_CANCELLED] }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Código de rastreio (se enviado)</label>
                    <input type="text" name="tracking_code" value="{{ old('tracking_code', $order->tracking_code) }}" placeholder="Ex: BR123456789BR" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2">
                </div>
                <button type="submit" class="inline-flex items-center px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium">Atualizar status</button>
            </form>
            @endif
        </div>
    </div>

    <div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Itens</h2>
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
            <thead>
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Produto</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Qtd</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Preço</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                @foreach($order->items as $item)
                <tr>
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $item->title }}</td>
                    <td class="px-4 py-3 text-sm text-right text-gray-600 dark:text-gray-300">{{ $item->quantity }}</td>
                    <td class="px-4 py-3 text-sm text-right text-gray-600 dark:text-gray-300">R$ {{ number_format($item->price, 2, ',', '.') }}</td>
                    <td class="px-4 py-3 text-sm text-right font-medium text-gray-900 dark:text-white">R$ {{ number_format($item->price * $item->quantity, 2, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if($order->shipping_amount > 0)
            <p class="mt-4 text-right text-sm text-gray-600 dark:text-gray-400">Frete: R$ {{ number_format($order->shipping_amount, 2, ',', '.') }}</p>
        @endif
        <p class="mt-2 text-right text-lg font-bold text-gray-900 dark:text-white">Total: R$ {{ number_format($order->total_amount, 2, ',', '.') }}</p>
    </div>
</div>
@endsection
