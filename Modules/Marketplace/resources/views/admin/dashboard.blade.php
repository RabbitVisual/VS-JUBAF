@extends('admin::components.layouts.master')

@section('title', __('marketplace::messages.name') . ' - Dashboard')

@section('content')
<div class="space-y-8">
    @if(session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif
    @if(session('info'))
        <x-alert type="info" :message="session('info')" />
    @endif

    {{-- Hero no padrão Admin --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 text-white shadow-xl border border-gray-700/50">
        <div class="absolute inset-0 dash-pattern opacity-10"></div>
        <div class="absolute right-0 top-0 h-full w-1/2 bg-gradient-to-l from-blue-600/20 to-transparent"></div>
        <div class="relative p-6 md:p-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="px-3 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300 text-xs font-bold uppercase tracking-wider">{{ __('marketplace::messages.name') }}</span>
                </div>
                <h1 class="text-2xl md:text-3xl font-black tracking-tight">{{ __('marketplace::messages.name') }}</h1>
                <p class="text-gray-300 text-sm mt-1">Resumo da loja missionária e atalhos.</p>
            </div>
            <a href="{{ route('admin.marketplace.products.index') }}" class="flex-shrink-0 inline-flex items-center px-5 py-3 rounded-xl bg-white text-gray-900 font-bold text-sm hover:bg-gray-100 transition-colors shadow-lg shadow-white/10">
                <x-icon name="plus" style="duotone" class="w-5 h-5 mr-2" /> Novo {{ __('marketplace::messages.product') }}
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="rounded-3xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-6 shadow-sm relative overflow-hidden group">
            <p class="text-sm text-gray-500 dark:text-gray-400">Receita total</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">R$ {{ number_format($totalRevenue, 2, ',', '.') }}</p>
        </div>
        <div class="rounded-3xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-6 shadow-sm relative overflow-hidden group">
            <p class="text-sm text-gray-500 dark:text-gray-400">Pedidos pendentes</p>
            <p class="text-2xl font-bold text-amber-600 dark:text-amber-400 mt-1">{{ $pendingOrders }}</p>
        </div>
        <div class="rounded-3xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-6 shadow-sm relative overflow-hidden group">
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('marketplace::messages.products') }}</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $productsCount }}</p>
        </div>
        <div class="rounded-3xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-6 shadow-sm relative overflow-hidden group">
            <p class="text-sm text-gray-500 dark:text-gray-400">Estoque baixo</p>
            <p class="text-2xl font-bold {{ $lowStockCount > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-gray-900 dark:text-white' }} mt-1">{{ $lowStockCount }}</p>
        </div>
    </div>

    @if($pendingShipmentOrders->isNotEmpty())
    <div class="rounded-3xl bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 p-6 shadow-sm">
        <div class="flex items-start gap-4">
            <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-purple-100 dark:bg-purple-900/40 flex items-center justify-center">
                <x-icon name="truck-ramp-box" style="duotone" class="w-6 h-6 text-purple-600 dark:text-purple-400" />
            </div>
            <div class="flex-1 min-w-0">
                <h2 class="text-lg font-semibold text-purple-900 dark:text-purple-100">Elias – Pedidos pendentes de envio</h2>
                <p class="mt-1 text-sm text-purple-800 dark:text-purple-200">{{ $pendingShipmentOrders->count() }} pedido(s) pago(s) ou em preparação há mais de 48h.</p>
                <a href="{{ route('admin.marketplace.orders.index', ['status' => 'paid']) }}" class="inline-flex items-center mt-4 px-4 py-2 rounded-lg bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium">
                    <x-icon name="tags" style="duotone" class="w-4 h-4 mr-2" /> Ver pedidos / Gerar etiquetas
                </a>
            </div>
        </div>
    </div>
    @endif

    @if($lowStockSuggestions->isNotEmpty())
    <div class="rounded-3xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 p-6 shadow-sm">
        <div class="flex items-start gap-4">
            <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-amber-100 dark:bg-amber-900/40 flex items-center justify-center">
                <x-icon name="robot" style="duotone" class="w-6 h-6 text-amber-600 dark:text-amber-400" />
            </div>
            <div class="flex-1 min-w-0">
                <h2 class="text-lg font-semibold text-amber-900 dark:text-amber-100">Elias – Estoque baixo</h2>
                <p class="mt-1 text-sm text-amber-800 dark:text-amber-200">Pastor, os seguintes produtos estão com estoque baixo:</p>
                <ul class="mt-2 space-y-1 text-sm text-amber-800 dark:text-amber-200">
                    @foreach($lowStockSuggestions as $p)
                        <li><strong>{{ $p->title }}</strong> – {{ $p->stock }} un.</li>
                    @endforeach
                </ul>
                <p class="mt-3 text-sm text-amber-700 dark:text-amber-300">Deseja notificar a igreja?</p>
                <form action="{{ route('admin.marketplace.elias.notify-low-stock') }}" method="POST" class="mt-4">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 rounded-lg bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium">
                        <x-icon name="bell" style="duotone" class="w-4 h-4 mr-2" /> Notificar igreja
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endif

    @if($chartData->isNotEmpty())
    <div class="rounded-3xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Vendas por campanha</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                <thead>
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Campanha</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Pedidos</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                    @foreach($chartData as $row)
                    <tr>
                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $row['campaign'] }}</td>
                        <td class="px-4 py-3 text-sm text-right text-gray-600 dark:text-gray-300">{{ $row['count'] }}</td>
                        <td class="px-4 py-3 text-sm text-right font-medium text-gray-900 dark:text-white">R$ {{ number_format($row['total'], 2, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <div class="flex flex-wrap gap-3">
        <a href="{{ route('admin.marketplace.orders.index') }}" class="inline-flex items-center px-5 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 font-medium text-sm">
            <x-icon name="cart-shopping" style="duotone" class="w-5 h-5 mr-2" /> {{ __('marketplace::messages.orders') }}
        </a>
        <a href="{{ route('marketplace.storefront.index') }}" target="_blank" class="inline-flex items-center px-5 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 font-medium text-sm">
            <x-icon name="store" style="duotone" class="w-5 h-5 mr-2" /> Ver loja
        </a>
    </div>
</div>
@endsection
