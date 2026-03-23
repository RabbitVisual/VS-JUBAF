<?php

namespace Modules\Marketplace\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Settings;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Marketplace\App\Services\CorreiosShippingService;

class FreightApiController extends Controller
{
    public function __construct(
        protected CorreiosShippingService $shippingService
    ) {}

    public function calculate(Request $request): JsonResponse
    {
        $data = $request->validate([
            'cep' => 'required|string|size:8',
            'weight_kg' => 'required|numeric|min:0.01',
            'length_cm' => 'nullable|numeric|min:0',
            'width_cm' => 'nullable|numeric|min:0',
            'height_cm' => 'nullable|numeric|min:0',
        ]);

        $originCep = (string) Settings::get('marketplace_origin_cep', Settings::get('church_cep', '00000000'));

        $options = $this->shippingService->calculate(
            $originCep,
            $data['cep'],
            (float) $data['weight_kg'],
            [
                'length' => (float) ($data['length_cm'] ?? 0),
                'width' => (float) ($data['width_cm'] ?? 0),
                'height' => (float) ($data['height_cm'] ?? 0),
            ]
        );

        return response()->json([
            'data' => $options,
        ]);
    }
}

