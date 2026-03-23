@extends('marketplace::layouts.storefront')

@section('content')
<x-loading-overlay />
@php
    $subtotal = 0;
    foreach ($cart as $item) {
        $p = $products->get($item['product_id'] ?? 0);
        if (!$p) continue;
        $skuId = $item['sku_id'] ?? null;
        $price = $p->price;
        if ($skuId && $p->relationLoaded('skus')) {
            $sku = $p->skus->firstWhere('id', $skuId);
            if ($sku) $price = $sku->price_override ?? $p->price;
        }
        $subtotal += (float) $price * (int) ($item['quantity'] ?? 1);
    }
    $discountAmountVal = $discountAmount ?? 0;
@endphp
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8 md:py-12" x-data="marketplaceCheckout({{ $subtotal }}, {{ json_encode($campaignName) }}, {{ $freightWeightKg }}, {{ $freightLength }}, {{ $freightWidth }}, {{ $freightHeight }}}, {{ $discountAmountVal }})">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">{{ __('marketplace::messages.checkout') }}</h1>

        @if(empty($cart) || $products->isEmpty())
            <div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-12 text-center">
                <p class="text-gray-600 dark:text-gray-400">{{ __('marketplace::messages.no_items') }}</p>
                <a href="{{ route('marketplace.storefront.index') }}" class="inline-flex items-center mt-4 text-blue-600 dark:text-blue-400 font-semibold">
                    <x-icon name="store" style="duotone" class="w-4 h-4 mr-2" /> {{ __('marketplace::messages.store') }}
                </a>
            </div>
        @else
            @if($errors->any())
                <div class="mb-6 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 p-4">
                    <p class="text-red-700 dark:text-red-300 font-medium">{{ $errors->first() }}</p>
                </div>
            @endif

            <div class="lg:grid lg:grid-cols-12 lg:gap-8">
                {{-- Coluna esquerda: formulário --}}
                <div class="lg:col-span-7 space-y-6">
                    @if($campaignName)
                        <div class="rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 p-4 flex items-start gap-3">
                            <x-icon name="hand-holding-heart" style="duotone" class="w-6 h-6 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" />
                            <div>
                                <p class="font-semibold text-blue-900 dark:text-blue-100">Resumo missionário</p>
                                <p class="text-sm text-blue-800 dark:text-blue-200 mt-0.5">Sua compra destinará <strong x-text="'R$ ' + formatMoney(totalValue)"></strong> para a campanha <strong>{{ $campaignName }}</strong>.</p>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('marketplace.storefront.checkout.store') }}" method="post" class="space-y-6" @submit="window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: 'Processando pagamento...' } }))">
                        @csrf

                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Itens</h2>
                            <ul class="space-y-4">
                                @foreach($cart as $item)
                                    @php
                                        $p = $products->get($item['product_id'] ?? 0);
                                        if (!$p) continue;
                                        $qty = (int)($item['quantity'] ?? 1);
                                        $skuId = $item['sku_id'] ?? null;
                                        $price = (float) $p->price;
                                        $title = $p->title;
                                        if ($skuId && $p->relationLoaded('skus')) {
                                            $sku = $p->skus->firstWhere('id', $skuId);
                                            if ($sku) {
                                                $price = (float) ($sku->price_override ?? $p->price);
                                                $title = $p->title . ' - ' . $sku->display_name;
                                            }
                                        }
                                        $thumb = $p->images->isNotEmpty() ? $p->images->first()->url : ($p->image_url ?? null);
                                    @endphp
                                    <li class="flex gap-4">
                                        <div class="flex-shrink-0 w-16 h-16 rounded-lg bg-gray-100 dark:bg-gray-700 overflow-hidden flex items-center justify-center">
                                            @if($thumb)
                                                <img src="{{ asset($thumb) }}" alt="" class="w-full h-full object-cover">
                                            @else
                                                <x-icon name="image" style="duotone" class="w-8 h-8 text-gray-400" />
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-medium text-gray-900 dark:text-white text-sm">{{ $title }}</p>
                                            <p class="text-gray-500 dark:text-gray-400 text-sm">Qtd: {{ $qty }} · R$ {{ number_format($price * $qty, 2, ',', '.') }}</p>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                            <p class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600 font-bold text-gray-900 dark:text-white">Subtotal: R$ {{ number_format($subtotal, 2, ',', '.') }}</p>
                        </div>

                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6" x-data="{ couponCode: '', applying: false, error: null }">
                            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Cupom de desconto</h2>
                            @if($appliedCoupon ?? null)
                                <div class="flex items-center justify-between p-3 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800">
                                    <span class="font-medium text-green-800 dark:text-green-200">{{ $appliedCoupon->code }} — R$ {{ number_format($discountAmount, 2, ',', '.') }} de desconto</span>
                                    <form action="{{ route('marketplace.storefront.coupon.remove') }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-sm text-red-600 dark:text-red-400 hover:underline">Remover</button>
                                    </form>
                                </div>
                            @else
                                <div class="flex gap-2">
                                    <input type="text" x-model="couponCode" placeholder="Ex: MISSÕES10" class="flex-1 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 uppercase" maxlength="50">
                                    <button type="button" @click="applying = true; error = null; fetch('{{ route('marketplace.storefront.coupon.apply') }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content }, body: JSON.stringify({ code: couponCode }) }).then(r => r.json()).then(d => { if (d.success) window.location.reload(); else { error = d.message; applying = false; } }).catch(() => { applying = false; error = 'Erro ao aplicar.'; })" :disabled="!couponCode.trim() || applying" class="px-4 py-2 rounded-lg bg-gray-700 hover:bg-gray-800 text-white text-sm font-medium disabled:opacity-50">Aplicar</button>
                                </div>
                                <p class="text-sm text-red-600 dark:text-red-400 mt-1" x-show="error" x-text="error" x-cloak style="display: none;"></p>
                            @endif
                        </div>

                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">{{ __('marketplace::messages.delivery') }}</h2>
                            <div class="space-y-3">
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="radio" name="delivery_type" value="local_pickup" x-model="deliveryType" {{ old('delivery_type', 'local_pickup') === 'local_pickup' ? 'checked' : '' }}>
                                    <x-icon name="hand-holding-heart" style="duotone" class="w-5 h-5 text-gray-500" />
                                    <span>{{ __('marketplace::messages.delivery_local_pickup') }}</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="radio" name="delivery_type" value="shipping" x-model="deliveryType" {{ old('delivery_type') === 'shipping' ? 'checked' : '' }}>
                                    <x-icon name="truck-fast" style="duotone" class="w-5 h-5 text-gray-500" />
                                    <span>{{ __('marketplace::messages.delivery_shipping') }}</span>
                                </label>
                            </div>
                            <div class="mt-4" x-show="deliveryType === 'shipping'" x-cloak style="display: none;">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">CEP</label>
                                <div class="flex gap-2 mt-1">
                                    <input type="text" name="shipping_address[cep]" x-model="cep" @input="cep = $event.target.value.replace(/\D/g, '').slice(0,8)" value="{{ old('shipping_address.cep') }}" placeholder="00000000" maxlength="8" class="flex-1 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2">
                                    <button type="button" @click="calculateFreight()" :disabled="cep.length !== 8 || loadingFreight" class="px-4 py-2 rounded-lg bg-gray-700 hover:bg-gray-800 text-white text-sm font-medium disabled:opacity-50">Calcular</button>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-show="cep.length > 0 && cep.length !== 8">Informe 8 dígitos do CEP.</p>
                                <div class="mt-3 space-y-2" x-show="freightOptions.length > 0" x-cloak style="display: none;">
                                    <template x-for="opt in freightOptions" :key="opt.service">
                                        <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-600 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                            <input type="radio" name="freight_option" :value="opt.price" x-model="shippingAmount" :id="'opt-' + opt.service">
                                            <span class="font-medium text-gray-900 dark:text-white" x-text="opt.label"></span>
                                            <span class="ml-auto font-bold text-gray-900 dark:text-white" x-text="'R$ ' + formatMoney(opt.price)"></span>
                                        </label>
                                    </template>
                                </div>
                                <input type="hidden" name="shipping_amount" :value="deliveryType === 'shipping' ? shippingAmount : 0">
                            </div>
                            @if($pickupLocations->isNotEmpty())
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Local de retirada</label>
                                    <select name="pickup_location_id" class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2">
                                        <option value="">—</option>
                                        @foreach($pickupLocations as $loc)
                                            <option value="{{ $loc->id }}" {{ old('pickup_location_id') == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                        </div>

                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Dados para pagamento</h2>
                            <div class="grid sm:grid-cols-2 gap-4">
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nome *</label>
                                    <input type="text" name="payer_name" value="{{ old('payer_name', auth()->user()?->name) }}" required class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">E-mail *</label>
                                    <input type="email" name="payer_email" value="{{ old('payer_email', auth()->user()?->email) }}" required class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">CPF (opcional)</label>
                                    <input type="text" name="payer_document" value="{{ old('payer_document') }}" class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2">
                                </div>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Forma de pagamento</h2>
                            <div class="space-y-3">
                                @foreach($gateways as $gateway)
                                    <label class="flex items-center gap-3 cursor-pointer p-3 rounded-lg border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                        <input type="radio" name="payment_gateway_id" value="{{ $gateway->id }}" required {{ old('payment_gateway_id') == $gateway->id || ($gateways->first()->id == $gateway->id) ? 'checked' : '' }}>
                                        <x-icon name="credit-card" style="duotone" class="w-5 h-5 text-gray-500" />
                                        <span class="font-medium text-gray-900 dark:text-white">{{ $gateway->display_name ?? $gateway->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        @foreach($cart as $item)
                            @if($products->get($item['product_id'] ?? 0))
                                <input type="hidden" name="items[{{ $loop->index }}][product_id]" value="{{ $item['product_id'] }}">
                                <input type="hidden" name="items[{{ $loop->index }}][quantity]" value="{{ $item['quantity'] ?? 1 }}">
                                @if(!empty($item['sku_id']))
                                    <input type="hidden" name="items[{{ $loop->index }}][sku_id]" value="{{ $item['sku_id'] }}">
                                @endif
                            @endif
                        @endforeach

                        <div class="flex flex-wrap gap-4">
                            <button type="submit" class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow-md transition-colors">
                                <x-icon name="credit-card" style="duotone" class="w-5 h-5 mr-2" /> Finalizar e pagar
                            </button>
                            <a href="{{ route('marketplace.storefront.index') }}" class="inline-flex items-center px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700/50">{{ __('marketplace::messages.store') }}</a>
                        </div>
                    </form>
                </div>

                {{-- Coluna direita: resumo fixo --}}
                <div class="mt-8 lg:mt-0 lg:col-span-5">
                    <div class="lg:sticky lg:top-24 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Resumo do pedido</h2>
                        <ul class="space-y-3 mb-4 pb-4 border-b border-gray-200 dark:border-gray-600">
                            @foreach($cart as $item)
                                @php
                                    $p = $products->get($item['product_id'] ?? 0);
                                    if (!$p) continue;
                                    $qty = (int)($item['quantity'] ?? 1);
                                    $skuId = $item['sku_id'] ?? null;
                                    $price = (float) $p->price;
                                    $title = $p->title;
                                    if ($skuId && $p->relationLoaded('skus')) {
                                        $sku = $p->skus->firstWhere('id', $skuId);
                                        if ($sku) {
                                            $price = (float) ($sku->price_override ?? $p->price);
                                            $title = $p->title . ' - ' . $sku->display_name;
                                        }
                                    }
                                @endphp
                                <li class="flex justify-between text-sm text-gray-700 dark:text-gray-300">
                                    <span class="line-clamp-1 flex-1 mr-2">{{ $title }} &times; {{ $qty }}</span>
                                    <span class="flex-shrink-0">R$ {{ number_format($price * $qty, 2, ',', '.') }}</span>
                                </li>
                            @endforeach
                        </ul>
                        <div class="space-y-2 text-sm">
                            <p class="flex justify-between text-gray-700 dark:text-gray-300"><span>Subtotal</span><span>R$ {{ number_format($subtotal, 2, ',', '.') }}</span></p>
                            @if(($discountAmount ?? 0) > 0)
                                <p class="flex justify-between text-green-600 dark:text-green-400"><span>Desconto</span><span>- R$ {{ number_format($discountAmount, 2, ',', '.') }}</span></p>
                            @endif
                            <p class="flex justify-between text-gray-700 dark:text-gray-300" x-show="deliveryType === 'shipping'"><span>Frete</span><span x-text="'R$ ' + formatMoney(shippingAmount)"></span></p>
                            <p class="flex justify-between font-bold text-lg text-gray-900 dark:text-white pt-3 border-t border-gray-200 dark:border-gray-600 mt-3"><span>Total</span><span x-text="'R$ ' + formatMoney(totalValue)"></span></p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
function marketplaceCheckout(subtotal, campaignName, freightWeightKg, freightLength, freightWidth, freightHeight, discountAmount) {
    discountAmount = discountAmount || 0;
    return {
        subtotal: subtotal,
        discountAmount: discountAmount,
        campaignName: campaignName || '',
        deliveryType: '{{ old('delivery_type', 'local_pickup') }}',
        shippingAmount: 0,
        cep: '{{ old('shipping_address.cep') ?? '' }}'.replace(/\D/g, ''),
        freightOptions: [],
        loadingFreight: false,
        freightWeightKg: freightWeightKg || 0.5,
        freightLength: freightLength || 16,
        freightWidth: freightWidth || 11,
        freightHeight: freightHeight || 2,
        get totalValue() {
            const freight = this.deliveryType === 'shipping' ? parseFloat(this.shippingAmount) || 0 : 0;
            return Math.max(0, this.subtotal + freight - this.discountAmount);
        },
        formatMoney(n) {
            return Number(n).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },
        calculateFreight() {
            if (this.cep.length !== 8 || this.loadingFreight) return;
            this.loadingFreight = true;
            this.freightOptions = [];
            fetch('{{ url('/api/v1/marketplace/freight') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({
                    cep: this.cep,
                    weight_kg: this.freightWeightKg,
                    length_cm: this.freightLength,
                    width_cm: this.freightWidth,
                    height_cm: this.freightHeight
                })
            })
            .then(r => r.json())
            .then(data => {
                this.freightOptions = data.data || [];
                if (this.freightOptions.length > 0) this.shippingAmount = this.freightOptions[0].price;
            })
            .catch(() => { this.freightOptions = []; })
            .finally(() => { this.loadingFreight = false; });
        },
        init() {
            document.querySelectorAll('input[name="delivery_type"]').forEach(el => {
                el.addEventListener('change', (e) => { this.deliveryType = e.target.value; });
            });
        }
    };
}
</script>
@endsection
