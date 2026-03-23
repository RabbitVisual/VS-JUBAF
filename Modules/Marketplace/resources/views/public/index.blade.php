@extends('marketplace::layouts.storefront')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900" x-data="{ filtersOpen: false, searchQ: '{{ request('q', '') }}', searchTimeout: null }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-8" aria-label="Breadcrumb">
            <a href="{{ route('homepage.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400">Início</a>
            <x-icon name="chevron-right" class="w-4 h-4 flex-shrink-0" />
            <a href="{{ route('marketplace.storefront.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400">Loja</a>
        </nav>

        {{-- Header --}}
        <header class="mb-10 md:mb-12">
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6">
                <div>
                    <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white tracking-tight">{{ __('marketplace::messages.name') }}</h1>
                    <p class="mt-2 text-lg text-gray-600 dark:text-gray-400">{{ __('marketplace::messages.store') }}</p>
                </div>
                @if($canPurchase ?? true)
                    <a href="{{ route('marketplace.storefront.cart') }}" class="inline-flex items-center justify-center px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold shadow-lg hover:shadow-xl transition-all shrink-0 w-full md:w-auto">
                        <x-icon name="cart-shopping" style="duotone" class="w-5 h-5 mr-2" /> Ver carrinho
                    </a>
                @endif
            </div>
            {{-- Busca --}}
            <form method="GET" action="{{ route('marketplace.storefront.index') }}" class="mt-8 max-w-2xl" x-ref="searchForm">
                <input type="hidden" name="category" value="{{ request('category') }}">
                <input type="hidden" name="campaign_id" value="{{ request('campaign_id') }}">
                <input type="hidden" name="availability" value="{{ request('availability') }}">
                <input type="hidden" name="min_price" value="{{ request('min_price') }}">
                <input type="hidden" name="max_price" value="{{ request('max_price') }}">
                <label for="store-search" class="sr-only">Buscar produtos</label>
                <div class="relative">
                    <input id="store-search" type="search" name="q" x-model="searchQ" @input="clearTimeout(searchTimeout); searchTimeout = setTimeout(() => $refs.searchForm.submit(), 450)" placeholder="Buscar produtos..." class="w-full rounded-2xl border-2 border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white pl-5 pr-12 py-4 text-base placeholder-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-colors">
                    <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 p-2 rounded-xl text-gray-500 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <x-icon name="magnifying-glass" class="w-5 h-5" />
                    </button>
                </div>
            </form>
        </header>

        @if(isset($canPurchase) && !$canPurchase)
            <div class="mb-8 rounded-2xl bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 p-5 flex items-center gap-4">
                <x-icon name="eye" style="duotone" class="w-7 h-7 text-blue-600 dark:text-blue-400 flex-shrink-0" />
                <p class="text-blue-800 dark:text-blue-200">Modo vitrine. Você pode navegar; as compras estão temporariamente desativadas.</p>
            </div>
        @endif

        <div class="flex flex-col lg:flex-row gap-10 lg:gap-12">
            {{-- Filtros --}}
            <aside class="lg:w-80 flex-shrink-0">
                <button type="button" @click="filtersOpen = !filtersOpen" class="lg:hidden w-full flex items-center justify-between px-5 py-4 rounded-2xl bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 text-left font-semibold text-gray-900 dark:text-white">
                    <span>Filtros</span>
                    <x-icon name="chevron-down" class="w-5 h-5 transition-transform" x-bind:class="filtersOpen ? 'rotate-180' : ''" />
                </button>
                <div class="mt-4 lg:mt-0 hidden lg:block" :class="{ '!block': filtersOpen }">
                    <div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm p-6 lg:sticky lg:top-24 space-y-6">
                        <h2 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Filtrar</h2>
                        <form method="GET" action="{{ route('marketplace.storefront.index') }}" x-ref="filterForm">
                            <div class="space-y-5">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Busca</label>
                                    <input type="search" name="q" x-model="searchQ" @input="clearTimeout(searchTimeout); searchTimeout = setTimeout(() => $refs.filterForm.submit(), 350)" placeholder="Nome..." class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Preço (R$)</label>
                                    <div class="flex gap-3">
                                        <input type="number" name="min_price" value="{{ request('min_price') }}" step="0.01" min="0" placeholder="Mín" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-sm">
                                        <input type="number" name="max_price" value="{{ request('max_price') }}" step="0.01" min="0" placeholder="Máx" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-sm">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Disponibilidade</label>
                                    <select name="availability" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-sm">
                                        <option value="">Todos</option>
                                        <option value="in_stock" {{ request('availability') === 'in_stock' ? 'selected' : '' }}>Em estoque</option>
                                        <option value="out_of_stock" {{ request('availability') === 'out_of_stock' ? 'selected' : '' }}>Sob encomenda</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Categoria</label>
                                    <select name="category" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-sm">
                                        <option value="">Todas</option>
                                        @foreach($categories as $key => $label)
                                            <option value="{{ $key }}" {{ request('category') === $key ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Campanha</label>
                                    <select name="campaign_id" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-sm">
                                        <option value="">Todas</option>
                                        @foreach($campaigns as $c)
                                            <option value="{{ $c->id }}" {{ request('campaign_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="flex gap-3 pt-2">
                                    <button type="submit" class="flex-1 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm">Filtrar</button>
                                    <a href="{{ route('marketplace.storefront.index') }}" class="py-2.5 px-4 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium text-sm hover:bg-gray-50 dark:hover:bg-gray-700/50">Limpar</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </aside>

            <main class="flex-1 min-w-0">
                @if($products->isEmpty())
                    <div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm p-16 md:p-24 text-center">
                        <div class="w-24 h-24 mx-auto rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-8">
                            <x-icon name="hand-holding-heart" style="duotone" class="w-12 h-12 text-gray-400 dark:text-gray-500" />
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Em breve</h2>
                        <p class="mt-4 text-gray-600 dark:text-gray-400 max-w-md mx-auto text-lg">Nossas campanhas e produtos estarão disponíveis em breve. Acompanhe!</p>
                        <a href="{{ route('homepage.index') }}" class="inline-flex items-center mt-8 px-6 py-3 rounded-xl bg-gray-800 dark:bg-gray-700 text-white font-semibold hover:bg-gray-700 dark:hover:bg-gray-600">
                            <x-icon name="arrow-left" class="w-5 h-5 mr-2" /> Voltar
                        </a>
                    </div>
                @else
                    <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-6 md:gap-8">
                        @foreach($products as $product)
                            @php
                                $thumbUrl = $product->images->isNotEmpty() ? $product->images->first()->url : ($product->image_url ?? null);
                                if ($thumbUrl && !str_starts_with($thumbUrl, 'http')) {
                                    $thumbUrl = asset($thumbUrl);
                                }
                                $hasVariations = $product->skus->isNotEmpty();
                                $displayPrice = (float) $product->price;
                                if ($hasVariations) {
                                    $inStockSkus = $product->skus->filter(fn ($s) => $s->stock > 0);
                                    $displayPrice = $inStockSkus->isEmpty()
                                        ? (float) $product->skus->min(fn ($s) => $s->price_override ?? $product->price)
                                        : (float) $inStockSkus->min(fn ($s) => $s->price_override ?? $product->price);
                                }
                                $inStock = $hasVariations ? $product->skus->sum('stock') > 0 : $product->stock > 0;
                                $discountPct = $product->discount_percentage;
                                $isNew = $product->isNew();
                            @endphp
                            <a href="{{ route('marketplace.storefront.show', $product->slug ?: $product->uuid) }}" class="group block bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm hover:shadow-xl hover:border-blue-200 dark:hover:border-blue-800 transition-all duration-300">
                                <div class="aspect-[4/3] bg-gray-100 dark:bg-gray-700 flex items-center justify-center relative overflow-hidden">
                                    @if($thumbUrl)
                                        <img src="{{ $thumbUrl }}" alt="{{ $product->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                    @else
                                        <x-icon name="images" style="duotone" class="w-20 h-20 text-gray-400 dark:text-gray-500" />
                                    @endif
                                    <div class="absolute top-3 left-3 right-3 flex flex-wrap gap-2">
                                        @if($isNew)
                                            <span class="px-2.5 py-1 rounded-lg bg-green-600 text-white text-xs font-bold uppercase">Novo</span>
                                        @endif
                                        @if($discountPct !== null)
                                            <span class="px-2.5 py-1 rounded-lg bg-amber-500 text-white text-xs font-bold uppercase">-{{ $discountPct }}%</span>
                                        @endif
                                        @if(!$inStock)
                                            <span class="px-2.5 py-1 rounded-lg bg-gray-700 text-white text-xs font-bold uppercase">Esgotado</span>
                                        @endif
                                        @if($product->campaign)
                                            <span class="px-2.5 py-1 rounded-lg bg-blue-600 text-white text-xs font-bold truncate max-w-[140px]" title="{{ $product->campaign->name }}">Apoia {{ Str::limit($product->campaign->name, 14) }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="p-6">
                                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ $categories[$product->category] ?? $product->category }}</span>
                                    <h2 class="mt-2 text-xl font-bold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors line-clamp-2 leading-snug">{{ $product->title }}</h2>
                                    @if($product->description)
                                        <p class="mt-3 text-sm text-gray-600 dark:text-gray-400 line-clamp-2 leading-relaxed">{{ Str::limit(strip_tags($product->description), 90) }}</p>
                                    @endif
                                    <p class="mt-5 text-2xl font-bold text-blue-600 dark:text-blue-400">
                                        @if($hasVariations)
                                            A partir de R$ {{ number_format($displayPrice, 2, ',', '.') }}
                                        @else
                                            R$ {{ number_format($displayPrice, 2, ',', '.') }}
                                        @endif
                                    </p>
                                    @if(!$inStock)
                                        <p class="mt-2 text-sm text-amber-600 dark:text-amber-400 font-medium">Esgotado</p>
                                    @endif
                                    <span class="inline-flex items-center mt-4 text-sm font-semibold text-blue-600 dark:text-blue-400 group-hover:underline">
                                        Ver produto <x-icon name="chevron-right" class="w-4 h-4 ml-1" />
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                    <div class="mt-12 flex justify-center">
                        {{ $products->withQueryString()->links() }}
                    </div>
                @endif
            </main>
        </div>
    </div>
</div>
@endsection
