@extends('memberpanel::components.layouts.master')

@section('title', __('marketplace::messages.my_orders'))

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('marketplace::messages.my_orders') }}</h1>
        <div class="flex items-center gap-3">
            <a href="{{ route('marketplace.storefront.index') }}" class="inline-flex items-center px-4 py-2 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium text-sm hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                <x-icon name="store" class="w-4 h-4 mr-2" /> Continuar comprando
            </a>
            <a href="{{ route('marketplace.storefront.index') }}" class="inline-flex items-center px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm shadow-sm">
                <x-icon name="bag-shopping" class="w-4 h-4 mr-2" /> {{ __('marketplace::messages.store') }}
            </a>
        </div>
    </div>

    @if($orders->isEmpty())
        <div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm p-12 text-center">
            <div class="w-20 h-20 mx-auto rounded-2xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center mb-6">
                <x-icon name="bag-shopping" style="duotone" class="w-10 h-10 text-blue-600 dark:text-blue-400" />
            </div>
            <p class="text-gray-600 dark:text-gray-400">Você ainda não fez pedidos na loja.</p>
            <a href="{{ route('marketplace.storefront.index') }}" class="inline-flex items-center mt-6 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm">
                <x-icon name="store" class="w-4 h-4 mr-2" /> Ir à loja
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($orders as $order)
                <a href="{{ route('memberpanel.marketplace.orders.show', $order->uuid) }}" class="block rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-lg p-6 transition-all hover:border-blue-200 dark:hover:border-blue-800">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <span class="text-sm font-mono text-gray-500 dark:text-gray-400">#{{ Str::limit($order->uuid, 8) }}</span>
                            <p class="mt-1 text-xl font-bold text-gray-900 dark:text-white">R$ {{ number_format($order->total_amount, 2, ',', '.') }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <span class="px-3 py-1.5 rounded-full text-sm font-semibold
                            @if($order->status === \Modules\Marketplace\Models\Order::STATUS_PENDING) bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-200
                            @elseif($order->status === \Modules\Marketplace\Models\Order::STATUS_PAID || $order->status === \Modules\Marketplace\Models\Order::STATUS_PREPARING) bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200
                            @elseif($order->status === \Modules\Marketplace\Models\Order::STATUS_SHIPPED_READY_FOR_PICKUP) bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-200
                            @elseif($order->status === \Modules\Marketplace\Models\Order::STATUS_COMPLETED) bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200
                            @else bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                            @endif">
                            {{ \Modules\Marketplace\Models\Order::statuses()[$order->status] ?? $order->status }}
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
        <div class="mt-6">
            {{ $orders->links() }}
        </div>
    @endif
</div>
@endsection
