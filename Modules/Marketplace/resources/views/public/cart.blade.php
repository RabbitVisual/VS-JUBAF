@extends('marketplace::layouts.storefront')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8 md:py-12">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-8" aria-label="Breadcrumb">
            <a href="{{ route('homepage.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Início</a>
            <x-icon name="chevron-right" class="w-4 h-4 flex-shrink-0" />
            <a href="{{ route('marketplace.storefront.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Loja</a>
            <x-icon name="chevron-right" class="w-4 h-4 flex-shrink-0" />
            <span class="text-gray-900 dark:text-white font-medium">Carrinho</span>
        </nav>

        <header class="mb-10">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white tracking-tight">Carrinho de compras</h1>
            <p class="mt-2 text-lg text-gray-600 dark:text-gray-400">Revise seus itens e finalize sua compra.</p>
        </header>

        @if(session('success'))
            <div class="mb-6 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 px-4 py-3 text-green-800 dark:text-green-200 text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if(empty($cartItems))
            <div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm p-12 md:p-16 text-center">
                <div class="w-24 h-24 mx-auto rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-6">
                    <x-icon name="cart-shopping" style="duotone" class="w-12 h-12 text-gray-400 dark:text-gray-500" />
                </div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Seu carrinho está vazio</h2>
                <p class="mt-3 text-gray-600 dark:text-gray-400 max-w-sm mx-auto">Adicione produtos da loja para continuar.</p>
                <a href="{{ route('marketplace.storefront.index') }}" class="inline-flex items-center mt-8 px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm">
                    <x-icon name="store" class="w-5 h-5 mr-2" /> Ir à loja
                </a>
            </div>
        @else
            <div class="space-y-8">
                <div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                    <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($cartItems as $row)
                            <li class="flex gap-4 md:gap-6 p-4 md:p-6">
                                <a href="{{ route('marketplace.storefront.show', $row['product_slug']) }}" class="flex-shrink-0 w-24 h-24 md:w-28 md:h-28 rounded-xl bg-gray-100 dark:bg-gray-700 overflow-hidden flex items-center justify-center">
                                    @if($row['thumb'])
                                        <img src="{{ asset($row['thumb']) }}" alt="" class="w-full h-full object-cover">
                                    @else
                                        <x-icon name="image" style="duotone" class="w-10 h-10 text-gray-400" />
                                    @endif
                                </a>
                                <div class="flex-1 min-w-0 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                                    <div>
                                        <a href="{{ route('marketplace.storefront.show', $row['product_slug']) }}" class="font-semibold text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 line-clamp-2">{{ $row['title'] }}</a>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">R$ {{ number_format($row['price'], 2, ',', '.') }} &times; {{ $row['quantity'] }}</p>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <p class="font-bold text-gray-900 dark:text-white">R$ {{ number_format($row['line_total'], 2, ',', '.') }}</p>
                                        @php $rp = ['product_id' => $row['product_id']]; if ($row['sku_id']) $rp['sku_id'] = $row['sku_id']; @endphp
                                        <a href="{{ route('marketplace.storefront.cart.remove', $rp) }}" class="text-sm text-red-600 dark:text-red-400 hover:underline">Remover</a>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm p-6" x-data="{ couponCode: '', applying: false, error: null }">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Cupom de desconto</h2>
                    @if($appliedCoupon)
                        <div class="flex items-center justify-between p-4 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800">
                            <span class="font-medium text-green-800 dark:text-green-200">{{ $appliedCoupon->code }} — R$ {{ number_format($discountAmount, 2, ',', '.') }} de desconto</span>
                            <form action="{{ route('marketplace.storefront.coupon.remove') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-sm text-red-600 dark:text-red-400 hover:underline">Remover</button>
                            </form>
                        </div>
                    @else
                        <div class="flex gap-3">
                            <input type="text" x-model="couponCode" placeholder="Digite o código do cupom" class="flex-1 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-3 uppercase" maxlength="50">
                            <button type="button"
                                @click="applying = true; error = null; fetch('{{ route('marketplace.storefront.coupon.apply') }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content }, body: JSON.stringify({ code: couponCode }) }).then(r => r.json()).then(d => { if (d.success) window.location.reload(); else { error = d.message; applying = false; } }).catch(() => { applying = false; error = 'Erro ao aplicar.'; })"
                                :disabled="!couponCode.trim() || applying"
                                class="px-5 py-3 rounded-xl bg-gray-800 dark:bg-gray-700 text-white font-semibold text-sm hover:bg-gray-700 dark:hover:bg-gray-600 disabled:opacity-50 transition-colors">
                                Aplicar
                            </button>
                        </div>
                        <p class="text-sm text-red-600 dark:text-red-400 mt-2" x-show="error" x-text="error" x-cloak style="display: none;"></p>
                    @endif
                </div>

                <div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm p-6">
                    <div class="space-y-2 text-right">
                        <p class="flex justify-between text-gray-700 dark:text-gray-300"><span>Subtotal</span><span>R$ {{ number_format($subtotal, 2, ',', '.') }}</span></p>
                        @if($discountAmount > 0)
                            <p class="flex justify-between text-green-600 dark:text-green-400"><span>Desconto</span><span>- R$ {{ number_format($discountAmount, 2, ',', '.') }}</span></p>
                        @endif
                        <p class="flex justify-between text-xl font-bold text-gray-900 dark:text-white pt-3 border-t border-gray-200 dark:border-gray-600 mt-3"><span>Total</span><span>R$ {{ number_format($total, 2, ',', '.') }}</span></p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-4 mt-6">
                        <a href="{{ route('marketplace.storefront.index') }}" class="flex-1 inline-flex items-center justify-center px-6 py-3 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-semibold hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <x-icon name="arrow-left" class="w-5 h-5 mr-2" /> Continuar comprando
                        </a>
                        <a href="{{ route('marketplace.storefront.checkout') }}" class="flex-1 inline-flex items-center justify-center px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold transition-colors">
                            Finalizar compra <x-icon name="chevron-right" class="w-5 h-5 ml-2" />
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
