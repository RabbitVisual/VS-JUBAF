<?php

namespace Modules\Marketplace\Listeners;

use Illuminate\Auth\Events\Login;
use Modules\Marketplace\Models\CartItem;

class MergeMarketplaceCartOnLogin
{
    public function handle(Login $event): void
    {
        $user = $event->user;
        $guard = property_exists($event, 'guard') ? $event->guard : null;

        $sessionCart = session('marketplace_cart', []);
        if (empty($sessionCart)) {
            return;
        }

        foreach ($sessionCart as $item) {
            $productId = (int) ($item['product_id'] ?? 0);
            $skuId = isset($item['sku_id']) && $item['sku_id'] ? (int) $item['sku_id'] : null;
            $qty = (int) ($item['quantity'] ?? 1);
            if ($productId < 1) {
                continue;
            }
            $query = CartItem::where('product_id', $productId);

            if ($guard === 'marketplace') {
                $query->where('marketplace_customer_id', $user->id);
            } else {
                $query->where('user_id', $user->id);
            }

            $existing = $query
                ->where(function ($q) use ($skuId) {
                    if ($skuId === null) {
                        $q->whereNull('sku_id');
                    } else {
                        $q->where('sku_id', $skuId);
                    }
                })
                ->first();
            if ($existing) {
                $existing->increment('quantity', $qty);
            } else {
                CartItem::create(array_filter([
                    'user_id' => $guard === 'marketplace' ? null : $user->id,
                    'marketplace_customer_id' => $guard === 'marketplace' ? $user->id : null,
                    'product_id' => $productId,
                    'sku_id' => $skuId,
                    'quantity' => $qty,
                ], static fn ($v) => $v !== null));
            }
        }

        $refreshQuery = CartItem::query();
        if ($guard === 'marketplace') {
            $refreshQuery->where('marketplace_customer_id', $user->id);
        } else {
            $refreshQuery->where('user_id', $user->id);
        }

        $items = $refreshQuery->get()
            ->map(fn ($row) => [
                'product_id' => $row->product_id,
                'sku_id' => $row->sku_id,
                'quantity' => $row->quantity,
            ])
            ->toArray();
        session(['marketplace_cart' => $items]);
    }
}
