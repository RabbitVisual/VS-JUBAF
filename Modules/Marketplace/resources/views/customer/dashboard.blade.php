@extends('marketplace::customer.layout')

@section('customer-content')
    <div class="space-y-6">
        <div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-6 flex items-center justify-between gap-4">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('marketplace::messages.welcome_back') }}</p>
                <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $customer->name }}</p>
            </div>
            <div class="text-right">
                <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('marketplace::messages.total_orders') }}</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $orders->total() }}</p>
            </div>
        </div>

        @if($orders->isEmpty())
            <div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-10 text-center">
                <div class="w-16 h-16 mx-auto rounded-2xl bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center mb-4">
                    <x-icon name="bag-shopping" style="duotone" class="w-8 h-8 text-blue-600 dark:text-blue-400" />
                </div>
                <p class="text-gray-600 dark:text-gray-400 mb-4">
                    {{ __('marketplace::messages.no_orders_yet') }}
                </p>
                <a href="{{ route('marketplace.storefront.index') }}"
                   class="inline-flex items-center px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">
                    <x-icon name="store" class="w-4 h-4 mr-2" />
                    {{ __('marketplace::messages.go_to_store') }}
                </a>
            </div>
        @else
            <div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($orders as $order)
                    <div class="p-5 flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <p class="text-xs font-mono text-gray-500 dark:text-gray-400">#{{ \Illuminate\Support\Str::limit($order->uuid, 8) }}</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                R$ {{ number_format($order->total_amount, 2, ',', '.') }}
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $order->created_at->format('d/m/Y H:i') }}
                            </p>
                        </div>
                        <span class="px-3 py-1.5 rounded-full text-xs font-semibold
                            @if($order->status === \Modules\Marketplace\Models\Order::STATUS_PENDING) bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-200
                            @elseif(in_array($order->status, [\Modules\Marketplace\Models\Order::STATUS_PAID, \Modules\Marketplace\Models\Order::STATUS_PREPARING], true)) bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200
                            @elseif($order->status === \Modules\Marketplace\Models\Order::STATUS_SHIPPED_READY_FOR_PICKUP) bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-200
                            @elseif($order->status === \Modules\Marketplace\Models\Order::STATUS_COMPLETED) bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200
                            @else bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                            @endif">
                            {{ \Modules\Marketplace\Models\Order::statuses()[$order->status] ?? $order->status }}
                        </span>
                    </div>
                @endforeach
            </div>

            <div class="mt-4">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
@endsection

