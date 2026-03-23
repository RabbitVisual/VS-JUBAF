@extends('admin::components.layouts.master')

@section('title', __('marketplace::messages.products'))

@php
    $categories = \Modules\Marketplace\Models\Product::categories();
@endphp

@section('content')
<div class="space-y-8">
    {{-- Hero --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 text-white shadow-xl border border-gray-700/50">
        <div class="absolute inset-0 dash-pattern opacity-10"></div>
        <div class="absolute right-0 top-0 h-full w-1/2 bg-gradient-to-l from-blue-600/20 to-transparent"></div>
        <div class="relative p-6 md:p-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="px-3 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300 text-xs font-bold uppercase tracking-wider">{{ __('marketplace::messages.name') }}</span>
                </div>
                <h1 class="text-2xl md:text-3xl font-black tracking-tight">{{ __('marketplace::messages.products') }}</h1>
                <p class="text-gray-300 text-sm mt-1">Gerencie os produtos da loja missionária. Visualize fotos, estoque e campanhas.</p>
            </div>
            <a href="{{ route('admin.marketplace.products.create') }}" class="flex-shrink-0 inline-flex items-center px-5 py-3 rounded-xl bg-white text-gray-900 font-bold text-sm hover:bg-gray-100 transition-colors shadow-lg shadow-white/10">
                <x-icon name="plus" style="duotone" class="w-5 h-5 mr-2" /> Novo {{ __('marketplace::messages.product') }}
            </a>
        </div>
    </div>

    @if(session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif

    {{-- Filtros --}}
    <form method="GET" action="{{ route('admin.marketplace.products.index') }}" class="flex flex-wrap items-end gap-4">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Buscar</label>
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Nome do produto..." class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2.5 text-sm">
        </div>
        <div class="w-48">
            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Categoria</label>
            <select name="category" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2.5 text-sm">
                <option value="">Todas</option>
                @foreach($categories as $key => $label)
                    <option value="{{ $key }}" {{ request('category') === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-4 py-2.5 rounded-xl bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-white font-medium text-sm hover:bg-gray-300 dark:hover:bg-gray-500">
            <x-icon name="magnifying-glass" style="duotone" class="w-4 h-4 mr-2 inline" /> Filtrar
        </button>
        @if(request()->hasAny(['q', 'category']))
            <a href="{{ route('admin.marketplace.products.index') }}" class="px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400 text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700">Limpar</a>
        @endif
    </form>

    {{-- Grid de cards --}}
    @if($products->isEmpty())
        <div class="rounded-3xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-12 md:p-16 text-center">
            <div class="w-20 h-20 mx-auto rounded-2xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-6">
                <x-icon name="box-open" style="duotone" class="w-10 h-10 text-gray-400 dark:text-gray-500" />
            </div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Nenhum produto cadastrado</h2>
            <p class="mt-2 text-gray-500 dark:text-gray-400 max-w-sm mx-auto">Comece criando o primeiro produto da sua loja missionária.</p>
            <a href="{{ route('admin.marketplace.products.create') }}" class="inline-flex items-center mt-6 px-5 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm">
                <x-icon name="plus" style="duotone" class="w-5 h-5 mr-2" /> Novo {{ __('marketplace::messages.product') }}
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($products as $product)
                @php
                    $thumbUrl = $product->images->isNotEmpty()
                        ? $product->images->first()->url
                        : ($product->image_url ?? null);
                    $hasSkus = $product->skus->isNotEmpty();
                    if ($hasSkus) {
                        $totalStock = $product->skus->sum('stock');
                        $prices = $product->skus->map(fn ($s) => (float) ($s->price_override ?? $product->price));
                        $minPrice = $prices->min();
                        $maxPrice = $prices->max();
                        $priceLabel = $minPrice == $maxPrice
                            ? 'R$ ' . number_format($minPrice, 2, ',', '.')
                            : 'R$ ' . number_format($minPrice, 2, ',', '.') . ' – R$ ' . number_format($maxPrice, 2, ',', '.');
                    } else {
                        $totalStock = (int) $product->stock;
                    }
                @endphp
                <div class="group rounded-3xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm hover:shadow-lg hover:border-gray-300 dark:hover:border-gray-600 transition-all duration-200 flex flex-col">
                    {{-- Imagem --}}
                    <a href="{{ route('admin.marketplace.products.edit', $product) }}" class="relative block aspect-[4/3] bg-gray-100 dark:bg-gray-700/50 overflow-hidden">
                        @if($thumbUrl)
                            <img src="{{ $thumbUrl }}" alt="{{ $product->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-300 dark:text-gray-500">
                                <x-icon name="image" style="duotone" class="w-16 h-16" />
                            </div>
                        @endif
                        @if(!$product->is_active)
                            <div class="absolute top-3 left-3 px-2 py-1 rounded-lg bg-gray-800/80 text-white text-xs font-medium">Inativo</div>
                        @endif
                        @if($totalStock <= 0)
                            <div class="absolute top-3 right-3 px-2 py-1 rounded-lg bg-red-600/90 text-white text-xs font-medium">{{ $hasSkus ? 'Sem estoque' : 'Sem estoque' }}</div>
                        @elseif($totalStock <= $lowStockThreshold)
                            <div class="absolute top-3 right-3 px-2 py-1 rounded-lg bg-amber-500/90 text-white text-xs font-medium">Estoque baixo</div>
                        @endif
                    </a>

                    {{-- Conteúdo --}}
                    <div class="p-5 flex-1 flex flex-col">
                        <div class="flex items-start justify-between gap-2 mb-1">
                            <a href="{{ route('admin.marketplace.products.edit', $product) }}" class="font-bold text-gray-900 dark:text-white line-clamp-2 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                {{ $product->title }}
                            </a>
                        </div>
                        <div class="flex flex-wrap gap-1.5 mb-3">
                            <span class="px-2 py-0.5 rounded-lg text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">
                                {{ $categories[$product->category] ?? $product->category }}
                            </span>
                            @if($product->campaign)
                                <span class="px-2 py-0.5 rounded-lg text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">
                                    {{ $product->campaign->name }}
                                </span>
                            @endif
                        </div>
                        <div class="mt-auto flex items-center justify-between gap-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                            <div>
                                @if($hasSkus)
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $minPrice == $maxPrice ? 'R$ ' . number_format($minPrice, 2, ',', '.') : 'A partir de R$ ' . number_format($minPrice, 2, ',', '.') }}</p>
                                    @if($minPrice != $maxPrice)
                                        <p class="text-xs text-gray-400 dark:text-gray-500">até R$ {{ number_format($maxPrice, 2, ',', '.') }}</p>
                                    @endif
                                @else
                                    <p class="text-lg font-black text-gray-900 dark:text-white">R$ {{ number_format($product->price, 2, ',', '.') }}</p>
                                @endif
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                    @if($hasSkus)
                                        Estoque (por tamanho): 
                                        @if($totalStock <= 0)
                                            <span class="text-red-600 dark:text-red-400 font-medium">0</span>
                                        @elseif($totalStock <= $lowStockThreshold)
                                            <span class="text-amber-600 dark:text-amber-400 font-medium">{{ $totalStock }} un.</span>
                                        @else
                                            <span class="text-gray-700 dark:text-gray-300">{{ $totalStock }} un.</span>
                                        @endif
                                    @else
                                        Estoque:
                                        @if($totalStock <= 0)
                                            <span class="text-red-600 dark:text-red-400 font-medium">0</span>
                                        @elseif($totalStock <= $lowStockThreshold)
                                            <span class="text-amber-600 dark:text-amber-400 font-medium">{{ $totalStock }}</span>
                                        @else
                                            <span class="text-gray-700 dark:text-gray-300">{{ $totalStock }}</span>
                                        @endif
                                    @endif
                                </p>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.marketplace.products.edit', $product) }}" class="p-2.5 rounded-xl bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 hover:bg-blue-200 dark:hover:bg-blue-900/50 transition-colors" title="Editar">
                                    <x-icon name="pen" style="duotone" class="w-4 h-4" />
                                </a>
                                <a href="{{ route('marketplace.storefront.show', $product->slug ?? $product->uuid) }}" target="_blank" class="p-2.5 rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors" title="Ver na loja">
                                    <x-icon name="eye" style="duotone" class="w-4 h-4" />
                                </a>
                                <form action="{{ route('admin.marketplace.products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Remover este produto?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2.5 rounded-xl text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors" title="Excluir">
                                        <x-icon name="trash" style="duotone" class="w-4 h-4" />
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Paginação --}}
        <div class="flex justify-center pt-4">
            {{ $products->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
