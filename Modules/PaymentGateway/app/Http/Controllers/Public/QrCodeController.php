<?php

namespace Modules\PaymentGateway\App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

/**
 * Gera QR Code localmente (sem APIs externas) para PIX e outros códigos.
 */
class QrCodeController extends Controller
{
    /**
     * GET /checkout/qr?d=<base64_data>
     * Retorna imagem do QR Code (SVG por padrão; PNG se Imagick disponível).
     */
    public function show(Request $request)
    {
        $encoded = $request->query('d');
        if (! $encoded || ! is_string($encoded)) {
            return response()->json(['error' => 'Missing data'], 400);
        }

        $data = base64_decode(strtr($encoded, ['-' => '+', '_' => '/']), true);
        if ($data === false || strlen($data) > 10000) {
            return response()->json(['error' => 'Invalid data'], 400);
        }

        $size = (int) $request->query('size', 300);
        $size = max(100, min(512, $size));

        try {
            $format = $request->query('format', 'svg');
            if ($format === 'png' && extension_loaded('imagick')) {
                $qr = QrCode::format('png')->size($size)->generate($data);

                return response($qr)
                    ->header('Content-Type', 'image/png')
                    ->header('Cache-Control', 'private, max-age=3600');
            }

            $qr = QrCode::format('svg')->size($size)->generate($data);

            return response($qr)
                ->header('Content-Type', 'image/svg+xml')
                ->header('Cache-Control', 'private, max-age=3600');
        } catch (\Throwable $e) {
            \Log::warning('QR generation failed: ' . $e->getMessage());

            return response()->json(['error' => 'Generation failed'], 500);
        }
    }
}
