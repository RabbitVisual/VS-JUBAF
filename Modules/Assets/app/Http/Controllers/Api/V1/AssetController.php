<?php

namespace Modules\Assets\App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Assets\App\Services\AssetApiService;

/**
 * API central de patrimônio (v1).
 * Respostas com { data }; listagem com meta de paginação.
 */
class AssetController extends Controller
{
    public function __construct(
        private AssetApiService $api
    ) {}

    /**
     * GET /api/v1/assets – lista ativos (paginado).
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = min(max((int) $request->input('per_page', 20), 1), 100);
        $paginator = $this->api->list($perPage);

        return response()->json([
            'data' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }

    /**
     * GET /api/v1/assets/{id} – ativo por id.
     */
    public function show(int $id): JsonResponse
    {
        $asset = $this->api->getById($id);
        if (! $asset) {
            return response()->json(['message' => 'Ativo não encontrado.'], 404);
        }

        return response()->json(['data' => $asset]);
    }

    /**
     * GET /api/v1/assets/code/{code} – ativo por código.
     */
    public function getByCode(string $code): JsonResponse
    {
        $asset = $this->api->getByCode($code);
        if (! $asset) {
            return response()->json(['message' => 'Ativo não encontrado.'], 404);
        }

        return response()->json(['data' => $asset]);
    }
}
