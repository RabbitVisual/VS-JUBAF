<?php

namespace Modules\Ministries\App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Ministries\App\Services\MinistryApiService;

/**
 * API central de ministérios (v1).
 * Respostas com { data }; listagem com meta de paginação.
 */
class MinistryController extends Controller
{
    public function __construct(
        private MinistryApiService $api
    ) {}

    /**
     * GET /api/v1/ministries
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = min(max((int) $request->input('per_page', 15), 1), 50);
        $activeOnly = $request->boolean('active_only');
        $activeOnly = $request->has('active_only') ? $activeOnly : null;
        $paginator = $this->api->list($perPage, $activeOnly);

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
     * GET /api/v1/ministries/{id}
     */
    public function show(int $id): JsonResponse
    {
        $ministry = $this->api->getById($id);
        if (! $ministry) {
            return response()->json(['message' => 'Ministério não encontrado.'], 404);
        }
        return response()->json(['data' => $ministry]);
    }

    /**
     * POST /api/v1/ministries
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:ministries,slug',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:50',
            'leader_id' => 'nullable|exists:users,id',
            'co_leader_id' => 'nullable|exists:users,id',
            'is_active' => 'nullable|boolean',
            'requires_approval' => 'nullable|boolean',
            'max_members' => 'nullable|integer|min:0',
        ]);
        $ministry = $this->api->create($validated);
        return response()->json(['data' => $ministry], 201);
    }

    /**
     * PUT/PATCH /api/v1/ministries/{id}
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $ministry = $this->api->getById($id);
        if (! $ministry) {
            return response()->json(['message' => 'Ministério não encontrado.'], 404);
        }
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'slug' => 'nullable|string|max:255|unique:ministries,slug,' . $id,
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:50',
            'leader_id' => 'nullable|exists:users,id',
            'co_leader_id' => 'nullable|exists:users,id',
            'is_active' => 'nullable|boolean',
            'requires_approval' => 'nullable|boolean',
            'max_members' => 'nullable|integer|min:0',
        ]);
        $ministry = $this->api->update($ministry, $validated);
        return response()->json(['data' => $ministry]);
    }

    /**
     * DELETE /api/v1/ministries/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        $ministry = $this->api->getById($id);
        if (! $ministry) {
            return response()->json(['message' => 'Ministério não encontrado.'], 404);
        }
        $this->api->destroy($ministry);
        return response()->json(['data' => ['success' => true]]);
    }
}
