<?php

namespace Modules\SocialAction\App\Services;

use Modules\SocialAction\App\Models\SocialKit;
use Modules\SocialAction\App\Models\SocialPantryItem;
use Modules\SocialAction\App\Models\SocialStockMovement;
use Illuminate\Support\Facades\DB;

class StockManagerService
{
    /**
     * Deduct items from a kit.
     */
    public function deductKit(SocialKit $kit, int $quantity = 1, ?int $userId = null)
    {
        DB::transaction(function () use ($kit, $quantity, $userId) {
            foreach ($kit->items as $item) {
                $deductAmount = $item->pivot->quantity * $quantity;

                // Update Quantity
                $item->decrement('current_quantity', $deductAmount);

                // Log Movement
                SocialStockMovement::create([
                    'pantry_item_id' => $item->id,
                    'type' => 'out',
                    'quantity' => $deductAmount,
                    'reason' => "Kit Delivery: {$kit->name}",
                    'user_id' => $userId ?? auth()->id(),
                ]);
            }
        });
    }

    public function adjustStock(SocialPantryItem $item, float $quantity, string $type, string $reason, ?int $userId = null)
    {
        DB::transaction(function () use ($item, $quantity, $type, $reason, $userId) {
            if ($type === 'in') {
                $item->increment('current_quantity', $quantity);
            } else {
                $item->decrement('current_quantity', $quantity);
            }

            SocialStockMovement::create([
                'pantry_item_id' => $item->id,
                'type' => $type,
                'quantity' => $quantity,
                'reason' => $reason,
                'user_id' => $userId ?? auth()->id(),
            ]);
        });
    }
}
