<?php

namespace Modules\Marketplace\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Marketplace\Models\CartItem;
use Modules\Marketplace\Models\Coupon;
use Modules\Marketplace\Models\PickupLocation;
use Modules\Marketplace\Models\Product;
use Modules\Marketplace\Services\MarketplaceCheckoutService;
use Modules\PaymentGateway\App\Models\PaymentGateway;
use Modules\Treasury\App\Models\Campaign;

class CheckoutController extends Controller
{
    public function __construct(
        protected MarketplaceCheckoutService $checkoutService,
        protected \Modules\PaymentGateway\App\Services\PaymentService $paymentService
    ) {}

    public function cart(Request $request)
    {
        if (! (bool) Settings::get('homepage_show_marketplace', false)) {
            return response()->view('marketplace::public.closed', [], 503);
        }
        if ((bool) Settings::get('marketplace_maintenance', false)) {
            return redirect()->route('marketplace.storefront.index')->with('info', 'A loja está em manutenção.');
        }
        if ((bool) Settings::get('marketplace_showcase_only', false)) {
            return redirect()->route('marketplace.storefront.index')->with('info', 'A loja está em modo vitrine.');
        }

        $cart = session('marketplace_cart', []);
        $productIds = array_unique(array_column($cart, 'product_id'));
        $products = Product::with(['skus', 'images' => fn ($q) => $q->orderBy('sort_order')->limit(1)])
            ->whereIn('id', $productIds)->get()->keyBy('id');

        $subtotal = 0;
        $cartItems = [];
        foreach ($cart as $item) {
            $p = $products->get($item['product_id'] ?? 0);
            if (! $p) {
                continue;
            }
            $skuId = isset($item['sku_id']) && $item['sku_id'] ? (int) $item['sku_id'] : null;
            $qty = (int) ($item['quantity'] ?? 1);
            $price = (float) $p->price;
            $title = $p->title;
            $thumb = $p->images->isNotEmpty() ? $p->images->first()->url : ($p->image_url ?? null);
            if ($skuId && $p->relationLoaded('skus')) {
                $sku = $p->skus->firstWhere('id', $skuId);
                if ($sku) {
                    $price = (float) ($sku->price_override ?? $p->price);
                    $title = $p->title . ' – ' . $sku->display_name;
                }
            }
            $lineTotal = $price * $qty;
            $subtotal += $lineTotal;
            $cartItems[] = [
                'product_id' => $p->id,
                'sku_id' => $skuId,
                'title' => $title,
                'price' => $price,
                'quantity' => $qty,
                'line_total' => $lineTotal,
                'thumb' => $thumb,
'product_slug' => $p->slug ?: $p->uuid,
            ];
        }

        $appliedCoupon = null;
        $discountAmount = 0;
        if (session('marketplace_applied_coupon_id')) {
            $appliedCoupon = Coupon::find(session('marketplace_applied_coupon_id'));
            $discountAmount = (float) session('marketplace_applied_discount', 0);
        }

        return view('marketplace::public.cart', [
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'total' => max(0, $subtotal - $discountAmount),
            'appliedCoupon' => $appliedCoupon,
            'discountAmount' => $discountAmount,
        ]);
    }

    public function show(Request $request)
    {
        if (! (bool) Settings::get('homepage_show_marketplace', false)) {
            return response()->view('marketplace::public.closed', [], 503);
        }
        if ((bool) Settings::get('marketplace_maintenance', false)) {
            return redirect()->route('marketplace.storefront.index')->with('info', 'A loja está em manutenção.');
        }
        if ((bool) Settings::get('marketplace_showcase_only', false)) {
            return redirect()->route('marketplace.storefront.index')->with('info', 'A loja está em modo vitrine. As compras estão temporariamente desativadas.');
        }

        if ($request->has('add')) {
            $productId = (int) $request->input('add');
            $skuId = $request->filled('sku_id') ? (int) $request->input('sku_id') : null;
            $product = Product::active()->find($productId);
            if ($product) {
                $valid = false;
                if ($skuId) {
                    $sku = $product->skus()->where('id', $skuId)->where('stock', '>', 0)->first();
                    $valid = (bool) $sku;
                } else {
                    $valid = $product->stock > 0;
                }
                if ($valid) {
                    $buyNow = $request->boolean('buy_now', false);
                    $cart = $buyNow ? [] : session('marketplace_cart', []);
                    $found = false;
                    foreach ($cart as &$item) {
                        $sameProduct = (int) ($item['product_id'] ?? 0) === $productId;
                        $sameSku = (isset($item['sku_id']) ? (int) $item['sku_id'] : null) === $skuId;
                        if ($sameProduct && $sameSku) {
                            $item['quantity'] = ($buyNow ? 1 : (($item['quantity'] ?? 1) + 1));
                            $found = true;
                            break;
                        }
                    }
                    if (! $found) {
                        $cart[] = ['product_id' => $productId, 'quantity' => 1] + ($skuId ? ['sku_id' => $skuId] : []);
                    }
                    session(['marketplace_cart' => $cart]);

                    $webUser = Auth::user();
                    $marketplaceUser = Auth::guard('marketplace')->user();

                    if ($buyNow && $webUser) {
                        CartItem::where('user_id', $webUser->id)->delete();
                    }
                    if ($buyNow && $marketplaceUser) {
                        CartItem::where('marketplace_customer_id', $marketplaceUser->id)->delete();
                    }

                    if ($webUser || $marketplaceUser) {
                        $entry = collect($cart)->first(fn ($i) => (int) ($i['product_id'] ?? 0) === $productId && (isset($i['sku_id']) ? (int) $i['sku_id'] : null) === $skuId);
                        $qty = (int) ($entry['quantity'] ?? 1);

                        CartItem::updateOrCreate(
                            [
                                'user_id' => $webUser?->id,
                                'marketplace_customer_id' => $marketplaceUser?->id,
                                'product_id' => $productId,
                                'sku_id' => $skuId,
                            ],
                            ['quantity' => $qty]
                        );
                    }
                }
            }
            return redirect()->route('marketplace.storefront.cart')->with('success', 'Item adicionado ao carrinho.');
        }

        $gateways = PaymentGateway::active()
            ->ordered()
            ->get()
            ->filter(fn ($g) => $g->isConfigured());

        if ($gateways->isEmpty()) {
            return redirect()->route('marketplace.storefront.index')
                ->with('warning', __('marketplace::messages.payment_unavailable'));
        }

        $cart = session('marketplace_cart', []);
        $productIds = array_unique(array_column($cart, 'product_id'));
        $products = Product::with(['skus', 'images' => fn ($q) => $q->orderBy('sort_order')->limit(1)])
            ->whereIn('id', $productIds)->get()->keyBy('id');
        $pickupLocations = PickupLocation::where('is_active', true)->get();
        $campaigns = Campaign::active()->where('is_active', true)->orderBy('name')->get();

        $freightWeightKg = 0;
        $freightLength = 16;
        $freightWidth = 11;
        $freightHeight = 2;
        $campaignName = null;
        $campaignId = null;
        foreach ($cart as $item) {
            $p = $products->get($item['product_id'] ?? 0);
            if ($p) {
                $qty = (int) ($item['quantity'] ?? 1);
                $skuId = isset($item['sku_id']) && $item['sku_id'] ? (int) $item['sku_id'] : null;
                $sku = $skuId ? $p->skus->firstWhere('id', $skuId) : null;
                $weight = $sku && $sku->weight_grams ? $sku->weight_grams : ($p->weight_grams ?? 0);
                $freightWeightKg += $weight / 1000 * $qty;
                if ($sku && $sku->length_cm && $sku->width_cm && $sku->height_cm) {
                    $freightLength = max($freightLength, (float) $sku->length_cm);
                    $freightWidth = max($freightWidth, (float) $sku->width_cm);
                    $freightHeight = max($freightHeight, (float) $sku->height_cm);
                } elseif ($p->length_cm && $p->width_cm && $p->height_cm) {
                    $freightLength = max($freightLength, (float) $p->length_cm);
                    $freightWidth = max($freightWidth, (float) $p->width_cm);
                    $freightHeight = max($freightHeight, (float) $p->height_cm);
                }
                if ($campaignName === null && $p->campaign_id) {
                    $campaignName = $p->campaign?->name;
                    $campaignId = $p->campaign_id;
                }
            }
        }
        if ($freightWeightKg < 0.01) {
            $freightWeightKg = 0.5;
        }

        $appliedCoupon = null;
        $discountAmount = 0;
        $couponCode = session('marketplace_applied_coupon_code');
        $couponId = session('marketplace_applied_coupon_id');
        if ($couponCode && $couponId) {
            $appliedCoupon = Coupon::find($couponId);
            $discountAmount = (float) session('marketplace_applied_discount', 0);
        }

        return view('marketplace::public.checkout', compact(
            'gateways', 'cart', 'products', 'pickupLocations', 'campaigns',
            'freightWeightKg', 'freightLength', 'freightWidth', 'freightHeight',
            'campaignName', 'campaignId', 'appliedCoupon', 'discountAmount'
        ));
    }

    public function applyCoupon(Request $request)
    {
        $request->validate(['code' => 'required|string|max:50']);
        $code = strtoupper(trim($request->input('code')));
        $coupon = Coupon::valid()->where('code', $code)->first();
        if (! $coupon) {
            return response()->json(['success' => false, 'message' => 'Cupom inválido ou expirado.']);
        }
        $cart = session('marketplace_cart', []);
        $productIds = array_unique(array_column($cart, 'product_id'));
        $products = Product::with('skus')->whereIn('id', $productIds)->get()->keyBy('id');
        $subtotal = 0;
        foreach ($cart as $item) {
            $p = $products->get($item['product_id'] ?? 0);
            if (! $p) {
                continue;
            }
            $qty = (int) ($item['quantity'] ?? 1);
            $skuId = isset($item['sku_id']) && $item['sku_id'] ? (int) $item['sku_id'] : null;
            $price = (float) $p->price;
            if ($skuId && $p->relationLoaded('skus')) {
                $sku = $p->skus->firstWhere('id', $skuId);
                if ($sku) {
                    $price = (float) ($sku->price_override ?? $p->price);
                }
            }
            $subtotal += $price * $qty;
        }
        $discountAmount = $coupon->applyTo($subtotal);
        if ($discountAmount <= 0) {
            return response()->json(['success' => false, 'message' => 'Compra mínima não atingida para este cupom.']);
        }
        session([
            'marketplace_applied_coupon_id' => $coupon->id,
            'marketplace_applied_coupon_code' => $coupon->code,
            'marketplace_applied_discount' => $discountAmount,
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Cupom aplicado!',
            'discount' => $discountAmount,
            'code' => $coupon->code,
        ]);
    }

    public function removeCartItem(Request $request)
    {
        $productId = (int) $request->input('product_id', 0);
        $skuId = $request->filled('sku_id') ? (int) $request->input('sku_id') : null;
        if ($productId < 1) {
            return redirect()->back()->with('info', 'Item inválido.');
        }
        $cart = session('marketplace_cart', []);
        $cart = array_values(array_filter($cart, function ($item) use ($productId, $skuId) {
            $sameProduct = (int) ($item['product_id'] ?? 0) === $productId;
            $itemSkuId = isset($item['sku_id']) && $item['sku_id'] ? (int) $item['sku_id'] : null;
            $sameSku = $itemSkuId === $skuId;
            return ! ($sameProduct && $sameSku);
        }));
        session(['marketplace_cart' => $cart]);
        $webUser = Auth::user();
        $marketplaceUser = Auth::guard('marketplace')->user();

        if ($webUser || $marketplaceUser) {
            $q = CartItem::where('product_id', $productId);
            if ($webUser) {
                $q->where('user_id', $webUser->id);
            }
            if ($marketplaceUser) {
                $q->where('marketplace_customer_id', $marketplaceUser->id);
            }
            if ($skuId === null) {
                $q->whereNull('sku_id');
            } else {
                $q->where('sku_id', $skuId);
            }
            $q->delete();
        }

        return redirect()->back()->with('info', 'Item removido do carrinho.');
    }

    public function removeCoupon()
    {
        session()->forget(['marketplace_applied_coupon_id', 'marketplace_applied_coupon_code', 'marketplace_applied_discount']);

        return redirect()->route('marketplace.storefront.checkout')->with('info', 'Cupom removido.');
    }

    public function store(Request $request)
    {
        if (! (bool) Settings::get('homepage_show_marketplace', false)) {
            return response()->view('marketplace::public.closed', [], 503);
        }
        if ((bool) Settings::get('marketplace_maintenance', false) || (bool) Settings::get('marketplace_showcase_only', false)) {
            return redirect()->route('marketplace.storefront.index')->with('error', 'As compras estão temporariamente indisponíveis.');
        }

        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:marketplace_products,id',
            'items.*.sku_id' => 'nullable|integer|exists:marketplace_product_skus,id',
            'items.*.quantity' => 'required|integer|min:1',
            'delivery_type' => 'required|in:local_pickup,shipping',
            'shipping_address' => 'nullable|array',
            'shipping_address.cep' => 'required_if:delivery_type,shipping|nullable|string|size:8',
            'shipping_amount' => 'nullable|numeric|min:0',
            'pickup_location_id' => 'nullable|exists:marketplace_pickup_locations,id',
            'payment_gateway_id' => 'required|exists:payment_gateways,id',
            'payer_name' => 'required|string|max:255',
            'payer_email' => 'required|email|max:255',
            'payer_document' => 'nullable|string|max:20',
            'payment_method' => 'nullable|string',
        ]);

        $webUser = Auth::user();
        $marketplaceUser = Auth::guard('marketplace')->user();

        $validated['user_id'] = $webUser?->id;
        $validated['marketplace_customer_id'] = $marketplaceUser?->id;
        $validated['shipping_amount'] = (float) ($validated['shipping_amount'] ?? 0);
        $validated['coupon_id'] = session('marketplace_applied_coupon_id');
        $validated['discount_amount'] = (float) session('marketplace_applied_discount', 0);
        if (! empty($validated['shipping_address']['cep'])) {
            $validated['shipping_address']['cep'] = preg_replace('/\D/', '', $validated['shipping_address']['cep']);
        }

        try {
            $result = $this->checkoutService->createOrderAndPayment($validated);
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }

        $order = $result['order'];
        $payment = $result['payment'];

        $brickData = $request->has('brick_payload') ? json_decode($request->input('brick_payload'), true) : null;
        try {
            $processResult = $brickData
                ? $this->paymentService->processPaymentBrick($payment, $brickData)
                : $this->paymentService->process($payment);
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }

        if (isset($processResult['error']) || (isset($processResult['status']) && $processResult['status'] === 'failed')) {
            return back()->withErrors(['error' => $processResult['error'] ?? 'Erro ao processar pagamento'])->withInput();
        }

        if (! empty($processResult['redirect_url'])) {
            return redirect($processResult['redirect_url']);
        }

        session()->forget('marketplace_cart');
        $webUser = Auth::user();
        $marketplaceUser = Auth::guard('marketplace')->user();
        if ($webUser) {
            CartItem::where('user_id', $webUser->id)->delete();
        }
        if ($marketplaceUser) {
            CartItem::where('marketplace_customer_id', $marketplaceUser->id)->delete();
        }
        if (session()->has('marketplace_applied_coupon_id')) {
            $coupon = Coupon::find(session('marketplace_applied_coupon_id'));
            if ($coupon) {
                $coupon->incrementUsed();
            }
            session()->forget(['marketplace_applied_coupon_id', 'marketplace_applied_coupon_code', 'marketplace_applied_discount']);
        }

        return redirect()->route('marketplace.storefront.thank-you')
            ->with('success', 'Obrigado por apoiar a obra de Deus! Seu pedido está sendo processado.');
    }
}
