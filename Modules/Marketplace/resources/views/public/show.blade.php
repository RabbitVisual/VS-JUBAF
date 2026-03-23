@php
    $primaryImageUrl = $product->images->isNotEmpty()
        ? $product->images->first()->url
        : ($product->image_url ?? null);
    $mediaItems = [];
    $images = $product->images->values()->all();
    foreach ($images as $i => $img) {
        $url = $img->url;
        $mediaItems[] = ['type' => 'image', 'url' => str_starts_with($url, 'http') ? $url : asset($url)];
        if ($i === 0 && $product->video_path) {
            $mediaItems[] = ['type' => 'video', 'url' => \Illuminate\Support\Facades\Storage::disk('public')->url($product->video_path)];
        }
    }
    if (empty($mediaItems) && $primaryImageUrl) {
        $mediaItems[] = ['type' => 'image', 'url' => str_starts_with($primaryImageUrl, 'http') ? $primaryImageUrl : asset($primaryImageUrl)];
    }
    $hasVariations = $product->skus->isNotEmpty();
    $optionsForAlpine = $product->options->map(fn ($o) => [
        'id' => $o->id,
        'name' => $o->name,
        'values' => $o->values->pluck('value', 'id')->toArray(),
    ])->values()->toArray();
    $skusForAlpine = $product->skus->map(fn ($s) => [
        'id' => $s->id,
        'attributes' => $s->attributes,
        'price_override' => $s->price_override,
        'stock' => $s->stock,
    ])->values()->toArray();
    $basePrice = (float) $product->price;
@endphp
@extends('marketplace::layouts.storefront')

@push('meta')
    <meta name="description" content="{{ $description ?? \Illuminate\Support\Str::limit(strip_tags($product->description ?? ''), 160) }}">
    <meta property="og:title" content="{{ $product->title }}">
    <meta property="og:description" content="{{ $description ?? \Illuminate\Support\Str::limit(strip_tags($product->description ?? ''), 160) }}">
    @if($primaryImageUrl)
    <meta property="og:image" content="{{ asset($primaryImageUrl) }}">
    @endif
    <meta property="og:url" content="{{ request()->url() }}">
    <meta property="og:type" content="product">
@endpush

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8 md:py-12" x-data="{
    added: false,
    currentImageIndex: 0,
    zoomVisible: false,
    zoomX: 50,
    zoomY: 50,
    activeTab: 'descricao',
    selectedAttributes: {},
    options: @js($optionsForAlpine),
    skus: @js($skusForAlpine),
    basePrice: @js($basePrice),
    hasVariations: @js($hasVariations),
    noVariationStock: @js((int) $product->stock),
    mediaItems: @js($mediaItems),
    get currentMedia() {
        return this.mediaItems[this.currentImageIndex] || this.mediaItems[0] || null;
    },
    get primaryImageUrl() {
        const fallback = @js($primaryImageUrl);
        if (!this.currentMedia) return fallback;
        return this.currentMedia.type === 'image' ? this.currentMedia.url : fallback;
    },
    get selectedSku() {
        const keys = Object.keys(this.selectedAttributes);
        if (keys.length === 0 || keys.length !== this.options.length) return null;
        return this.skus.find(sku => {
            const att = sku.attributes || {};
            return keys.every(k => att[k] === this.selectedAttributes[k]);
        }) || null;
    },
    get displayPrice() {
        if (!this.selectedSku) return this.basePrice;
        return this.selectedSku.price_override != null ? parseFloat(this.selectedSku.price_override) : this.basePrice;
    },
    get displayStock() {
        if (!this.selectedSku) return this.hasVariations ? null : this.noVariationStock;
        return this.selectedSku.stock;
    },
    get canAddToCart() {
        if (!this.hasVariations) return this.noVariationStock >= 1;
        if (!this.selectedSku) return false;
        return this.selectedSku.stock >= 1;
    },
    get addToCartUrl() {
        const base = '{{ route('marketplace.storefront.checkout') }}?add={{ $product->id }}';
        if (this.selectedSku) return base + '&sku_id=' + this.selectedSku.id;
        return base;
    },
    get buyNowUrl() {
        return this.addToCartUrl + '&buy_now=1';
    }
}">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-6 flex-wrap" aria-label="Breadcrumb">
            <a href="{{ route('homepage.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Início</a>
            <x-icon name="chevron-right" class="w-4 h-4 flex-shrink-0" />
            <a href="{{ route('marketplace.storefront.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">{{ __('marketplace::messages.store') }}</a>
            <x-icon name="chevron-right" class="w-4 h-4 flex-shrink-0" />
            <span class="text-gray-900 dark:text-white font-medium line-clamp-1">{{ $product->title }}</span>
        </nav>

        <div class="lg:grid lg:grid-cols-12 lg:gap-8">
            {{-- Coluna esquerda: Galeria + Abas --}}
            <div class="lg:col-span-7 order-2 lg:order-1">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="block">
                {{-- Gallery --}}
                @php
                    $hasVariationsShow = $product->skus->isNotEmpty();
                    $inStockShow = $hasVariationsShow ? $product->skus->sum('stock') > 0 : $product->stock > 0;
                    $discountPctShow = $product->discount_percentage;
                    $isNewShow = $product->isNew();
                @endphp
                <div class="aspect-square bg-gray-100 dark:bg-gray-700 flex items-center justify-center min-h-[280px] relative overflow-hidden">
                    <div class="absolute top-2 left-2 right-2 z-10 flex flex-wrap gap-1.5">
                        @if($isNewShow)
                            <span class="px-2 py-0.5 rounded-md bg-green-600 text-white text-[10px] font-bold uppercase">Novo</span>
                        @endif
                        @if($discountPctShow !== null)
                            <span class="px-2 py-0.5 rounded-md bg-amber-500 text-white text-[10px] font-bold uppercase">Oferta -{{ $discountPctShow }}%</span>
                        @endif
                        @if(!$inStockShow)
                            <span class="px-2 py-0.5 rounded-md bg-gray-700 text-white text-[10px] font-bold uppercase">Esgotado</span>
                        @endif
                        @if($product->campaign)
                            <span class="px-2 py-0.5 rounded-md bg-blue-600 text-white text-[10px] font-bold uppercase">Apoia {{ $product->campaign->name }}</span>
                        @endif
                    </div>
                    @if(!empty($mediaItems) || $product->image_url)
                        <div class="gallery-main absolute inset-0 flex items-center justify-center">
                        <template x-if="currentMedia && currentMedia.type === 'video'">
                            <video :src="currentMedia.url" controls preload="metadata" class="w-full h-full object-contain bg-black"
                                   @click.stop></video>
                        </template>
                        <template x-if="!currentMedia || currentMedia.type === 'image'">
                            <img :src="primaryImageUrl" alt="{{ $product->title }}" class="w-full h-full object-cover transition-opacity duration-300"
                                 x-ref="mainImg">
                        </template>
                        </div>
                        {{-- Setas anterior / próxima (galeria) --}}
                        @if(count($mediaItems) > 1)
                            <button type="button" @click="currentImageIndex = currentImageIndex === 0 ? mediaItems.length - 1 : currentImageIndex - 1"
                                    class="absolute left-2 top-1/2 -translate-y-1/2 z-30 w-12 h-12 rounded-full bg-white/90 dark:bg-gray-800/90 shadow-lg border border-gray-200 dark:border-gray-600 flex items-center justify-center text-gray-700 dark:text-gray-200 hover:bg-white dark:hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                                    aria-label="Imagem anterior">
                                <x-icon name="chevron-left" class="w-6 h-6" />
                            </button>
                            <button type="button" @click="currentImageIndex = currentImageIndex === mediaItems.length - 1 ? 0 : currentImageIndex + 1"
                                    class="absolute right-2 top-1/2 -translate-y-1/2 z-30 w-12 h-12 rounded-full bg-white/90 dark:bg-gray-800/90 shadow-lg border border-gray-200 dark:border-gray-600 flex items-center justify-center text-gray-700 dark:text-gray-200 hover:bg-white dark:hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                                    aria-label="Próxima imagem">
                                <x-icon name="chevron-right" class="w-6 h-6" />
                            </button>
                            <div class="absolute bottom-2 left-0 right-0 flex justify-center gap-2 p-2 flex-wrap z-10">
                                @foreach($mediaItems as $idx => $m)
                                    <button type="button"
                                            @click="currentImageIndex = {{ $idx }}"
                                            class="w-12 h-12 rounded-lg overflow-hidden border-2 transition-all flex items-center justify-center {{ $idx === 0 ? 'border-blue-500 ring-2 ring-blue-200' : 'border-gray-300 dark:border-gray-600 hover:border-gray-400' }}"
                                            :class="currentImageIndex === {{ $idx }} ? 'border-blue-500 ring-2 ring-blue-200 dark:ring-blue-800' : ''">
                                        @if($m['type'] === 'video')
                                            <x-icon name="video" class="w-6 h-6 text-gray-500 dark:text-gray-400" />
                                        @else
                                            <img src="{{ $m['url'] }}" alt="" class="w-full h-full object-cover">
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    @else
                        <x-icon name="images" style="duotone" class="w-24 h-24 text-gray-400 dark:text-gray-500" />
                    @endif
                </div>
        </div>

            {{-- Tabs na coluna esquerda (abaixo da galeria) --}}
            <div class="mt-6 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-6">
                    <div class="flex gap-4 border-b border-gray-200 dark:border-gray-600 mb-4" role="tablist">
                        <button type="button" role="tab" @click="activeTab = 'descricao'"
                                class="pb-3 text-sm font-medium border-b-2 -mb-px transition-colors"
                                :class="activeTab === 'descricao' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'">
                            {{ __('marketplace::messages.description') }}
                        </button>
                        @if($product->specifications && count($product->specifications) > 0)
                            <button type="button" role="tab" @click="activeTab = 'detalhes'"
                                    class="pb-3 text-sm font-medium border-b-2 -mb-px transition-colors"
                                    :class="activeTab === 'detalhes' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'">
                                <x-icon name="utensils" style="duotone" class="w-4 h-4 inline mr-1" /> {{ __('marketplace::messages.specifications') }}
                            </button>
                        @endif
                        @if($product->category === \Modules\Marketplace\Models\Product::CATEGORY_LIVROS && $product->sample_url)
                            <button type="button" role="tab" @click="activeTab = 'amostra'"
                                    class="pb-3 text-sm font-medium border-b-2 -mb-px transition-colors"
                                    :class="activeTab === 'amostra' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'">
                                <x-icon name="book-open-reader" style="duotone" class="w-4 h-4 inline mr-1" /> {{ __('marketplace::messages.sample') }}
                            </button>
                        @endif
                    </div>
                    <div x-show="activeTab === 'descricao'" x-transition class="prose dark:prose-invert prose-sm max-w-none text-gray-600 dark:text-gray-400">
                        @if($product->description)
                            {!! nl2br(e($product->description)) !!}
                        @else
                            <p class="text-gray-500 dark:text-gray-500">{{ __('marketplace::messages.no_description') }}</p>
                        @endif
                    </div>
                    @if($product->specifications && count($product->specifications) > 0)
                        <div x-show="activeTab === 'detalhes'" x-transition class="text-gray-600 dark:text-gray-400" x-cloak>
                            @if(is_array($product->specifications))
                                <ul class="list-disc pl-5 space-y-1">
                                    @foreach($product->specifications as $key => $val)
                                        <li>
                                            @if(is_string($key) && !is_numeric($key))
                                                <strong>{{ $key }}:</strong> {{ is_array($val) ? implode(', ', $val) : $val }}
                                            @else
                                                {{ is_array($val) ? json_encode($val) : $val }}
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p>{{ $product->specifications }}</p>
                            @endif
                        </div>
                    @endif
                    @if($product->category === \Modules\Marketplace\Models\Product::CATEGORY_LIVROS && $product->sample_url)
                        <div x-show="activeTab === 'amostra'" x-transition class="text-gray-600 dark:text-gray-400" x-cloak>
                            <a href="{{ $product->sample_url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center text-blue-600 dark:text-blue-400 hover:underline">
                                <x-icon name="book-open-reader" style="duotone" class="w-5 h-5 mr-2" /> {{ __('marketplace::messages.view_sample') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            </div>

            {{-- Coluna direita: Resumo da compra (sticky / primeiro no mobile) --}}
            <div class="mt-8 lg:mt-0 lg:col-span-5 order-1 lg:order-2">
                <div class="lg:sticky lg:top-24 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="sr-only">Resumo da compra</h2>
                    @if($product->campaign)
                        <span class="inline-block px-3 py-1 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 text-sm font-bold mb-3">{{ $product->campaign->name }}</span>
                    @endif
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ \Modules\Marketplace\Models\Product::categories()[$product->category] ?? $product->category }}</span>
                    <h1 class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ $product->title }}</h1>
                    <p class="mt-4 text-2xl font-bold text-gray-900 dark:text-white">
                        <template x-if="skus.length > 0">
                            <span x-show="selectedSku" x-transition>R$ <span x-text="(displayPrice).toFixed(2).replace('.', ',')"></span></span>
                        </template>
                        <template x-if="skus.length > 0">
                            <span x-show="!selectedSku" x-transition class="text-lg text-gray-500">A partir de R$ {{ number_format($product->price, 2, ',', '.') }}</span>
                        </template>
                        <template x-if="skus.length === 0">
                            <span>R$ {{ number_format($product->price, 2, ',', '.') }}</span>
                        </template>
                    </p>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">em até 3x sem juros no cartão</p>

                    @if($hasVariations)
                        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-600">
                            <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-3">
                                <x-icon name="layer-group" style="duotone" class="w-4 h-4 inline mr-1" /> {{ __('marketplace::messages.options') }}
                            </h3>
                            @foreach($product->options as $option)
                                <div class="mb-4">
                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">{{ $option->name }}</p>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($option->values as $val)
                                            <button type="button"
                                                    @click="selectedAttributes['{{ $option->name }}'] = '{{ $val->value }}'; selectedAttributes = { ...selectedAttributes }"
                                                    class="px-4 py-2 rounded-lg border text-sm font-medium transition-all"
                                                    :class="selectedAttributes['{{ $option->name }}'] === '{{ $val->value }}'
                                                        ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300'
                                                        : 'border-gray-300 dark:border-gray-600 hover:border-gray-400 dark:hover:border-gray-500'">
                                                {{ $val->value }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400" x-show="selectedSku" x-transition>
                                {{ __('marketplace::messages.stock') }}: <span x-text="displayStock"></span>
                                <span x-show="displayStock <= 5 && displayStock > 0">(poucas unidades)</span>
                            </p>
                            <p class="mt-2 text-sm text-amber-600 dark:text-amber-400" x-show="selectedSku && selectedSku.stock === 0" x-transition>
                                {{ __('marketplace::messages.out_of_stock') }}
                            </p>
                        </div>
                    @endif

                    {{-- Delivery --}}
                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-600">
                        <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-3">{{ __('marketplace::messages.delivery') }}</h3>
                        <div class="space-y-3">
                            @if(in_array($product->delivery_type, ['local_pickup', 'both']))
                                <div class="flex items-start gap-3 p-3 rounded-xl bg-gray-50 dark:bg-gray-700/50">
                                    <x-icon name="hand-holding-heart" style="duotone" class="w-5 h-5 text-green-600 dark:text-green-400 flex-shrink-0 mt-0.5" />
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white">{{ __('marketplace::messages.delivery_local_pickup') }}</p>
                                        @if($product->pickupLocation)
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-0.5">{{ $product->pickupLocation->name }}@if($product->pickupLocation->address) – {{ $product->pickupLocation->address }}@endif</p>
                                        @else
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-0.5">Retire na igreja no local indicado no checkout.</p>
                                        @endif
                                    </div>
                                </div>
                            @endif
                            @if(in_array($product->delivery_type, ['shipping', 'both']))
                                <div class="flex items-start gap-3 p-3 rounded-xl bg-gray-50 dark:bg-gray-700/50">
                                    <x-icon name="truck-fast" style="duotone" class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" />
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white">{{ __('marketplace::messages.delivery_shipping') }}</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-0.5">Envio nacional. O frete será calculado no checkout conforme seu CEP.</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if(!$hasVariations)
                        <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ __('marketplace::messages.stock') }}: {{ $product->stock }} {{ $product->stock <= 5 && $product->stock > 0 ? '(poucas unidades)' : '' }}
                        </p>
                    @endif

                    @if($canPurchase ?? true)
                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-600 space-y-3">
                        <form :action="addToCartUrl" method="get" x-ref="addForm" class="block">
                            <button type="submit"
                                @click.prevent="if (canAddToCart) { added = true; $refs.addForm.submit(); }"
                                :disabled="!canAddToCart || added"
                                class="w-full inline-flex items-center justify-center px-6 py-3 rounded-xl font-semibold shadow-md transition-all disabled:opacity-60 disabled:cursor-not-allowed"
                                :class="(canAddToCart && !added) ? 'bg-blue-600 hover:bg-blue-700 text-white' : 'bg-gray-400 dark:bg-gray-600 text-white cursor-not-allowed'">
                                <template x-if="!added">
                                    <span class="flex items-center">
                                        <x-icon name="cart-shopping" style="duotone" class="w-5 h-5 mr-2" /> {{ __('marketplace::messages.add_to_cart') }}
                                    </span>
                                </template>
                                <template x-if="added">
                                    <span class="flex items-center"><x-icon name="check" style="duotone" class="w-5 h-5 mr-2" /> Adicionado!</span>
                                </template>
                            </button>
                        </form>
                        <a :href="buyNowUrl" class="block w-full text-center py-3 rounded-xl border-2 border-blue-600 dark:border-blue-500 text-blue-600 dark:text-blue-400 font-semibold hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors" x-show="canAddToCart">
                            Comprar agora
                        </a>
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400" x-show="canAddToCart">Você será redirecionado ao checkout.</p>
                        <p class="mt-2 text-xs text-amber-600 dark:text-amber-400" x-show="skus.length > 0 && !selectedSku" x-cloak>Selecione as opções para adicionar ao carrinho.</p>
                        <p class="mt-2 text-xs text-amber-600 dark:text-amber-400" x-show="selectedSku && selectedSku.stock === 0" x-cloak>Este item está esgotado.</p>
                    </div>
                    @else
                    <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-600 p-4 rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800">
                        <p class="text-sm text-blue-800 dark:text-blue-200 flex items-center gap-2">
                            <x-icon name="eye" style="duotone" class="w-5 h-5 flex-shrink-0" /> A loja está em modo vitrine. As compras estão temporariamente desativadas.
                        </p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($canPurchase ?? true)
    {{-- Sticky footer mobile: Adicionar ao Carrinho + Comprar Agora --}}
    <div class="md:hidden fixed bottom-0 left-0 right-0 z-40 p-3 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)] safe-area-pb">
        <div class="flex gap-3 max-w-5xl mx-auto">
            <a :href="addToCartUrl" class="flex-1 py-3.5 rounded-xl border-2 border-blue-600 dark:border-blue-500 text-blue-600 dark:text-blue-400 font-bold text-sm text-center flex items-center justify-center gap-2"
               x-show="canAddToCart">
                <x-icon name="cart-plus" style="duotone" class="w-5 h-5" /> Adicionar ao Carrinho
            </a>
            <a :href="buyNowUrl" class="flex-1 py-3.5 rounded-xl bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white font-bold text-sm text-center flex items-center justify-center gap-2"
               x-show="canAddToCart">
                Comprar Agora
            </a>
        </div>
        <p class="text-center text-xs text-gray-500 dark:text-gray-400 mt-2" x-show="!canAddToCart && (skus.length === 0 || selectedSku)" x-cloak>Selecione as opções acima.</p>
        <p class="text-center text-xs text-amber-600 dark:text-amber-400 mt-2" x-show="selectedSku && selectedSku.stock === 0" x-cloak>Item esgotado.</p>
    </div>
    <div class="md:hidden h-24"></div>
    @endif
</div>
@endsection
