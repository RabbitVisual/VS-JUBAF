<?php

namespace Modules\Assets\App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Assets\App\Models\Asset;
use Modules\Assets\App\Models\AssetMovement;

class AssetTrackingService
{
    /**
     * Move an asset to a new location.
     */
    public function moveAsset(Asset $asset, int $newLocationId, string $type, ?string $notes = null, ?int $responsibleId = null)
    {
        return DB::transaction(function () use ($asset, $newLocationId, $type, $notes, $responsibleId) {
            $previousLocationId = $asset->location_id;

            // Create movement record
            AssetMovement::create([
                'asset_id' => $asset->id,
                'user_id' => Auth::id(), // Who performed the move
                'responsible_id' => $responsibleId,
                'previous_location_id' => $previousLocationId,
                'new_location_id' => $newLocationId,
                'type' => $type,
                'notes' => $notes,
                'date' => now(),
            ]);

            // Update asset location
            $asset->update([
                'location_id' => $newLocationId,
                'status' => $this->determineStatusFromType($type),
            ]);

            return $asset;
        });
    }

    private function determineStatusFromType(string $type): string
    {
        return match ($type) {
            'loan' => 'borrowed',
            'maintenance_out' => 'maintenance',
            'disposal' => 'disposed',
            'return', 'transfer', 'maintenance_return' => 'available',
            default => 'available',
        };
    }
}
