<?php

namespace Modules\Marketplace\Services;

use Illuminate\Support\Collection;
use Modules\Marketplace\Models\Order;
use Modules\Marketplace\Models\Product;

class MarketplaceEliasService
{
    public function getLowStockSuggestions(): Collection
    {
        $threshold = config('marketplace.low_stock_threshold', 5);

        return Product::query()
            ->where('stock', '>', 0)
            ->where('stock', '<=', $threshold)
            ->where('is_active', true)
            ->orderBy('stock')
            ->get();
    }

    /**
     * Pedidos em status paid ou preparing há mais de 48 horas (pendentes de envio).
     */
    public function getPendingShipmentOrders(): Collection
    {
        $cutoff = now()->subHours(48);

        return Order::query()
            ->whereIn('status', [Order::STATUS_PAID, Order::STATUS_PREPARING])
            ->where('updated_at', '<', $cutoff)
            ->orderBy('updated_at')
            ->get();
    }
}
