<?php

namespace Modules\Marketplace\Services;

use Illuminate\Support\Facades\DB;
use Modules\Marketplace\Models\Order;
use Modules\Marketplace\Models\OrderItem;
use Modules\Marketplace\Models\Product;
use Modules\Marketplace\Models\ProductSku;
use Modules\PaymentGateway\App\Services\PaymentService;

class MarketplaceCheckoutService
{
    public function __construct(
        protected PaymentService $paymentService
    ) {}

    /**
     * Create order and payment. Validates stock before creating.
     * Items may include optional sku_id; when present, price and stock are taken from the SKU.
     * Returns ['order' => Order, 'payment' => Payment].
     *
     * @param  array{items: array<{product_id: int, quantity: int, sku_id?: int|null}>, ...}  $data
     * @return array{order: Order, payment: \Modules\PaymentGateway\App\Models\Payment}
     */
    public function createOrderAndPayment(array $data): array
    {
        $items = $data['items'] ?? [];
        if (empty($items)) {
            throw new \InvalidArgumentException(__('marketplace::messages.no_items'));
        }

        $productIds = array_unique(array_column($items, 'product_id'));
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');
        $skuIds = array_filter(array_column($items, 'sku_id'));
        $skus = $skuIds
            ? ProductSku::with('product')->whereIn('id', $skuIds)->get()->keyBy('id')
            : collect();

        $orderItemsData = [];
        $subtotal = 0;
        $campaignId = null;

        foreach ($items as $item) {
            $product = $products->get($item['product_id']);
            if (! $product) {
                throw new \InvalidArgumentException(__('marketplace::messages.product_not_found'));
            }
            $qty = (int) ($item['quantity'] ?? 1);
            if ($qty < 1) {
                continue;
            }
            $skuId = isset($item['sku_id']) && $item['sku_id'] ? (int) $item['sku_id'] : null;

            if ($skuId) {
                $sku = $skus->get($skuId);
                if (! $sku || (int) $sku->product_id !== (int) $product->id) {
                    throw new \InvalidArgumentException(__('marketplace::messages.product_not_found'));
                }
                if ($sku->stock < $qty) {
                    throw new \InvalidArgumentException(
                        __('marketplace::messages.insufficient_stock', ['title' => $product->title . ' - ' . $sku->display_name, 'stock' => $sku->stock])
                    );
                }
                $price = $sku->price_override !== null ? (float) $sku->price_override : (float) $product->price;
                $orderItemsData[] = [
                    'product' => $product,
                    'sku' => $sku,
                    'quantity' => $qty,
                    'price' => $price,
                ];
            } else {
                if ($product->stock < $qty) {
                    throw new \InvalidArgumentException(
                        __('marketplace::messages.insufficient_stock', ['title' => $product->title, 'stock' => $product->stock])
                    );
                }
                $orderItemsData[] = [
                    'product' => $product,
                    'sku' => null,
                    'quantity' => $qty,
                    'price' => (float) $product->price,
                ];
            }
            $row = $orderItemsData[array_key_last($orderItemsData)];
            $subtotal += $row['price'] * $row['quantity'];
            if ($campaignId === null) {
                $campaignId = $product->campaign_id;
            }
        }

        $shippingAmount = (float) ($data['shipping_amount'] ?? 0);
        $discountAmount = (float) ($data['discount_amount'] ?? 0);
        $totalAmount = max(0, $subtotal + $shippingAmount - $discountAmount);

        return DB::transaction(function () use ($data, $orderItemsData, $subtotal, $shippingAmount, $discountAmount, $totalAmount, $campaignId) {
            $order = Order::create([
                'user_id' => $data['user_id'] ?? null,
                'marketplace_customer_id' => $data['marketplace_customer_id'] ?? null,
                'email' => $data['payer_email'],
                'payer_name' => $data['payer_name'],
                'status' => Order::STATUS_PENDING,
                'delivery_type' => $data['delivery_type'],
                'shipping_address' => $data['shipping_address'] ?? null,
                'pickup_location_id' => $data['pickup_location_id'] ?? null,
                'total_amount' => $totalAmount,
                'shipping_amount' => $shippingAmount,
                'discount_amount' => $discountAmount,
                'coupon_id' => $data['coupon_id'] ?? null,
                'campaign_id' => $campaignId,
            ]);

            foreach ($orderItemsData as $row) {
                $title = $row['product']->title;
                $options = null;
                $skuId = null;
                if ($row['sku']) {
                    $title .= ' - ' . $row['sku']->display_name;
                    $options = $row['sku']->attributes;
                    $skuId = $row['sku']->id;
                }
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $row['product']->id,
                    'sku_id' => $skuId,
                    'title' => $title,
                    'price' => $row['price'],
                    'quantity' => $row['quantity'],
                    'options' => $options,
                ]);
                if ($row['sku']) {
                    $row['sku']->decrement('stock', $row['quantity']);
                } else {
                    $row['product']->decrement('stock', $row['quantity']);
                }
            }

            $payment = $this->paymentService->createPayment([
                'payment_gateway_id' => $data['payment_gateway_id'],
                'payment_type' => 'marketplace_order',
                'payable_type' => Order::class,
                'payable_id' => $order->id,
                'amount' => round($totalAmount, 2),
                'description' => 'Loja Missionária - Pedido ' . $order->uuid,
                'payer_name' => $data['payer_name'],
                'payer_email' => $data['payer_email'],
                'payer_document' => $data['payer_document'] ?? null,
                'user_id' => $data['user_id'] ?? null,
                'metadata' => [
                    'order_uuid' => $order->uuid,
                    'marketplace_order_id' => $order->id,
                ],
            ]);

            return ['order' => $order, 'payment' => $payment];
        });
    }
}
