@extends('memberpanel::components.layouts.master')

@section('title', __('marketplace::messages.order') . ' #' . $order->uuid)

@php
    $status = $order->status;
    $step1 = true;
    $step2 = $status !== \Modules\Marketplace\Models\Order::STATUS_PENDING;
    $step3 = in_array($status, [\Modules\Marketplace\Models\Order::STATUS_PREPARING, \Modules\Marketplace\Models\Order::STATUS_SHIPPED_READY_FOR_PICKUP, \Modules\Marketplace\Models\Order::STATUS_COMPLETED], true);
    $step4 = in_array($status, [\Modules\Marketplace\Models\Order::STATUS_SHIPPED_READY_FOR_PICKUP, \Modules\Marketplace\Models\Order::STATUS_COMPLETED], true);
    $step5 = $status === \Modules\Marketplace\Models\Order::STATUS_COMPLETED;
    $canPickup = $order->delivery_type === 'local_pickup' && $step4;
@endphp
@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <a href="{{ route('memberpanel.marketplace.orders.index') }}" class="inline-flex items-center text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 font-medium">
            <x-icon name="arrow-left" class="w-4 h-4 mr-2" /> {{ __('marketplace::messages.my_orders') }}
        </a>
        <a href="{{ route('marketplace.storefront.index') }}" class="inline-flex items-center px-4 py-2 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium text-sm hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
            <x-icon name="store" class="w-4 h-4 mr-2" /> Continuar comprando
        </a>
    </div>

    {{-- Timeline (5 etapas) --}}
    <div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden p-6">
        <h2 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">Andamento do pedido</h2>
        <ul class="flex flex-col sm:flex-row gap-4 sm:gap-0 sm:justify-between">
            <li class="flex items-center gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full {{ $step1 ? 'bg-green-600 text-white' : 'bg-gray-200 dark:bg-gray-600 text-gray-500' }}">
                    @if($step1)<x-icon name="check" class="w-5 h-5" />@else<span class="text-sm font-bold">1</span>@endif
                </span>
                <span class="text-sm font-medium {{ $step1 ? 'text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400' }}">Pedido recebido</span>
            </li>
            <li class="flex items-center gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full {{ $step2 ? 'bg-green-600 text-white' : 'bg-gray-200 dark:bg-gray-600 text-gray-500' }}">
                    @if($step2)<x-icon name="check" class="w-5 h-5" />@else<span class="text-sm font-bold">2</span>@endif
                </span>
                <span class="text-sm font-medium {{ $step2 ? 'text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400' }}">Pagamento confirmado</span>
            </li>
            <li class="flex items-center gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full {{ $step3 ? 'bg-green-600 text-white' : 'bg-gray-200 dark:bg-gray-600 text-gray-500' }}">
                    @if($step3)<x-icon name="check" class="w-5 h-5" />@else<span class="text-sm font-bold">3</span>@endif
                </span>
                <span class="text-sm font-medium {{ $step3 ? 'text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400' }}">Em preparação</span>
            </li>
            <li class="flex items-center gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full {{ $step4 ? 'bg-green-600 text-white' : 'bg-gray-200 dark:bg-gray-600 text-gray-500' }}">
                    @if($step4)<x-icon name="check" class="w-5 h-5" />@else<span class="text-sm font-bold">4</span>@endif
                </span>
                <div>
                    <span class="text-sm font-medium {{ $step4 ? 'text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400' }}">
                        {{ $order->delivery_type === 'local_pickup' ? 'Pronto para retirada' : 'Enviado' }}
                    </span>
                    @if($step4 && $order->tracking_code && $order->delivery_type !== 'local_pickup')
                        <br><a href="{{ $order->tracking_url }}" target="_blank" rel="noopener" class="text-xs text-blue-600 dark:text-blue-400 font-medium mt-0.5 inline-flex items-center">Rastrear entrega <x-icon name="arrow-up-right-from-square" class="w-3 h-3 ml-0.5" /></a>
                    @endif
                </div>
            </li>
            <li class="flex items-center gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full {{ $step5 ? 'bg-green-600 text-white' : 'bg-gray-200 dark:bg-gray-600 text-gray-500' }}">
                    @if($step5)<x-icon name="check" class="w-5 h-5" />@else<span class="text-sm font-bold">5</span>@endif
                </span>
                <span class="text-sm font-medium {{ $step5 ? 'text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400' }}">Entregue / Finalizado</span>
            </li>
        </ul>
    </div>

    <div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-gray-900 dark:text-white">Pedido #{{ $order->uuid }}</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $order->created_at->format('d/m/Y H:i') }}</p>
            </div>
            <span class="px-4 py-2 rounded-xl text-sm font-medium
                @if($order->status === \Modules\Marketplace\Models\Order::STATUS_PENDING) bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-200
                @elseif($order->status === \Modules\Marketplace\Models\Order::STATUS_PAID || $order->status === \Modules\Marketplace\Models\Order::STATUS_PREPARING) bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200
                @elseif($order->status === \Modules\Marketplace\Models\Order::STATUS_SHIPPED_READY_FOR_PICKUP) bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-200
                @elseif($order->status === \Modules\Marketplace\Models\Order::STATUS_COMPLETED) bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200
                @else bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                @endif">
                {{ \Modules\Marketplace\Models\Order::statuses()[$order->status] ?? $order->status }}
            </span>
        </div>

        <div class="p-6 space-y-6">
            <div>
                <h2 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Itens</h2>
                <ul class="space-y-2">
                    @foreach($order->items as $item)
                        <li class="flex justify-between text-gray-700 dark:text-gray-300">
                            <span>{{ $item->title }} &times; {{ $item->quantity }}</span>
                            <span>R$ {{ number_format($item->price * $item->quantity, 2, ',', '.') }}</span>
                        </li>
                    @endforeach
                </ul>
                @if($order->shipping_amount > 0)
                    <p class="mt-2 flex justify-between text-gray-600 dark:text-gray-400">
                        <span>Frete</span>
                        <span>R$ {{ number_format($order->shipping_amount, 2, ',', '.') }}</span>
                    </p>
                @endif
                <p class="mt-2 flex justify-between font-bold text-gray-900 dark:text-white">
                    <span>Total</span>
                    <span>R$ {{ number_format($order->total_amount, 2, ',', '.') }}</span>
                </p>
            </div>

            @if($order->delivery_type === 'local_pickup')
                <div>
                    <h2 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Retirada</h2>
                    @if($canPickup && $order->pickupLocation)
                        <div class="p-4 rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 mb-4">
                            <p class="font-medium text-blue-900 dark:text-blue-100 flex items-center gap-2">
                                <x-icon name="location-dot" style="duotone" class="w-5 h-5 shrink-0" /> Onde e quando retirar
                            </p>
                            <p class="font-semibold text-gray-900 dark:text-white mt-2">{{ $order->pickupLocation->name }}</p>
                            @if($order->pickupLocation->address)
                                <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">{{ $order->pickupLocation->address }}</p>
                            @endif
                            @if($order->pickupLocation->availability)
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ is_array($order->pickupLocation->availability) ? implode(', ', $order->pickupLocation->availability) : $order->pickupLocation->availability }}</p>
                            @endif
                            @if($order->pickupLocation->instructions)
                                <p class="text-sm text-gray-700 dark:text-gray-300 mt-2 whitespace-pre-line">{{ $order->pickupLocation->instructions }}</p>
                            @endif
                        </div>
                    @endif
                    @if($order->pickupLocation && !$canPickup)
                        <p class="font-medium text-gray-900 dark:text-white">{{ $order->pickupLocation->name }}</p>
                        @if($order->pickupLocation->address)
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $order->pickupLocation->address }}</p>
                        @endif
                        @if($order->pickupLocation->instructions)
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $order->pickupLocation->instructions }}</p>
                        @endif
                    @endif
                    <div class="mt-4 p-4 rounded-xl bg-gray-50 dark:bg-gray-700/50 inline-block">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">Apresente este QR Code na retirada</p>
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode('ORDER-' . $order->uuid) }}" alt="QR Code pedido {{ $order->uuid }}" class="w-48 h-48 object-contain rounded-lg border border-gray-200 dark:border-gray-600">
                        <p class="mt-2 text-xs font-mono text-gray-500 dark:text-gray-400">#{{ $order->uuid }}</p>
                    </div>
                </div>
            @endif

            @if($order->tracking_code)
                <div>
                    <h2 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">{{ __('marketplace::messages.tracking') }}</h2>
                    <p class="font-mono text-gray-900 dark:text-white">{{ $order->tracking_code }}</p>
                    <a href="{{ $order->tracking_url }}" target="_blank" rel="noopener" class="inline-flex items-center mt-2 text-blue-600 dark:text-blue-400 font-medium text-sm">
                        Rastrear entrega <x-icon name="arrow-up-right-from-square" class="w-4 h-4 ml-1" />
                    </a>
                </div>
            @endif
        </div>
    </div>

    <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
        <a href="{{ route('marketplace.storefront.index') }}" class="inline-flex items-center text-blue-600 dark:text-blue-400 font-medium hover:underline">
            <x-icon name="store" class="w-4 h-4 mr-2" /> Continuar comprando na loja
        </a>
    </div>
</div>
@endsection
