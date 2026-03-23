<?php

namespace Modules\SocialAction\App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\SocialAction\App\Services\SocialActionApiService;

/**
 * API central de ação social (v1).
 * Campanhas: listagem, show, store, update, destroy. Respostas com { data }.
 */
class SocialActionController extends Controller
{
    public function __construct(
        private SocialActionApiService $api
    ) {}

    /**
     * GET /api/v1/social-actions – lista campanhas (paginado).
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = min(max((int) $request->input('per_page', 15), 1), 50);
        $status = $request->input('status');
        $paginator = $this->api->listCampaigns($perPage, $status);

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
     * GET /api/v1/social-actions/{id}
     */
    public function show(int $id): JsonResponse
    {
        $campaign = $this->api->getCampaignById($id);
        if (! $campaign) {
            return response()->json(['message' => 'Campanha não encontrada.'], 404);
        }
        return response()->json(['data' => $campaign]);
    }

    /**
     * POST /api/v1/social-actions
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'goal_description' => 'nullable|string',
            'target_amount' => 'nullable|numeric|min:0',
            'current_amount' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'nullable|string|max:50',
        ]);
        $campaign = $this->api->createCampaign($validated);
        return response()->json(['data' => $campaign], 201);
    }

    /**
     * PUT/PATCH /api/v1/social-actions/{id}
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $campaign = $this->api->getCampaignById($id);
        if (! $campaign) {
            return response()->json(['message' => 'Campanha não encontrada.'], 404);
        }
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'goal_description' => 'nullable|string',
            'target_amount' => 'nullable|numeric|min:0',
            'current_amount' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'status' => 'nullable|string|max:50',
        ]);
        $campaign = $this->api->updateCampaign($campaign, $validated);
        return response()->json(['data' => $campaign]);
    }

    /**
     * DELETE /api/v1/social-actions/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        $campaign = $this->api->getCampaignById($id);
        if (! $campaign) {
            return response()->json(['message' => 'Campanha não encontrada.'], 404);
        }
        $this->api->destroyCampaign($campaign);
        return response()->json(['data' => ['success' => true]]);
    }
}
