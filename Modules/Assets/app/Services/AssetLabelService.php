<?php

namespace Modules\Assets\App\Services;

use Modules\Assets\App\Models\Asset;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AssetLabelService
{
    /**
     * Generate QR Code for an asset.
     *
     * @return string
     */
    public function generateQrCode(Asset $asset, int $size = 200)
    {
        // Format: URL to view asset or just the code?
        // Usually a URL to scan and view details is best.
        // For now, let's encode the Asset Code or a JSON payload.
        // Let's assume a URL to the pubic/admin view.

        $url = route('assets.show', $asset->id); // We might need to adjust route name later

        return QrCode::size($size)->generate($url);
    }

    /**
     * Prepare data for printing labels.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAssetsForLabels(array $assetIds)
    {
        return Asset::whereIn('id', $assetIds)->get();
    }
}
