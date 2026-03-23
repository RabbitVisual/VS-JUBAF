<?php

namespace Modules\Marketplace\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Marketplace\Services\CorreiosShippingService;

class FreightController extends Controller
{
    public function __construct(
        protected CorreiosShippingService $freightService
    ) {}

    /**
     * Calculate freight options (Sedex/PAC) for given CEP and cart dimensions.
     * POST /api/v1/marketplace/freight
     */
    public function calculate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'cep' => ['required', 'string', 'size:8'],
            'weight_kg' => ['required', 'numeric', 'min:0.01', 'max:30'],
            'length_cm' => ['nullable', 'numeric', 'min:1', 'max:105'],
            'width_cm' => ['nullable', 'numeric', 'min:1', 'max:105'],
            'height_cm' => ['nullable', 'numeric', 'min:1', 'max:105'],
        ]);

        $cepOrigin = preg_replace('/\D/', '', config('marketplace.freight.cep_origin', ''));
        if ($cepOrigin === '' || strlen($cepOrigin) !== 8) {
            $cepOrigin = '01310100';
        }

        $options = $this->freightService->calculate(
            $cepOrigin,
            preg_replace('/\D/', '', $validated['cep']),
            (float) $validated['weight_kg'],
            [
                'length' => (float) ($validated['length_cm'] ?? 16),
                'width' => (float) ($validated['width_cm'] ?? 11),
                'height' => (float) ($validated['height_cm'] ?? 2),
            ]
        );

        return response()->json(['data' => $options]);
    }
}
