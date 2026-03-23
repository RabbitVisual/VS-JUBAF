<?php

namespace Modules\Events\App\Services;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeGeneratorService
{
    /**
     * Generate a QR Code in Base64 format.
     *
     * @param string $data
     * @return string
     */
    public function generate(string $data): string
    {
        // Generate raw PNG data
        $qrData = QrCode::format('png')
            ->size(300)
            ->errorCorrection('H')
            ->margin(1)
            ->generate($data);

        // Convert to Base64
        return base64_encode($qrData);
    }
}
