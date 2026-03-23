<?php

namespace Modules\Marketplace\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Marketplace\Models\CartItem;
use Symfony\Component\HttpFoundation\Response;

class SyncMarketplaceCartFromDb
{
    public function handle(Request $request, Closure $next): Response
    {
        $webUserId = Auth::id();
        $marketplaceCustomerId = Auth::guard('marketplace')->id();

        if ($webUserId || $marketplaceCustomerId) {
            $query = CartItem::query();

            if ($marketplaceCustomerId) {
                $query->where('marketplace_customer_id', $marketplaceCustomerId);
            } else {
                $query->where('user_id', $webUserId);
            }

            $items = $query->get()
                ->map(fn ($row) => [
                    'product_id' => $row->product_id,
                    'sku_id' => $row->sku_id,
                    'quantity' => $row->quantity,
                ])
                ->toArray();

            session(['marketplace_cart' => $items]);
        }

        return $next($request);
    }
}
