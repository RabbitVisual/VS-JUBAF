<?php

namespace Modules\Sermons\App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Sermons\App\Models\Sermon;
use Modules\Sermons\App\Services\SermonApiService;

/**
 * API central de sermões (v1).
 * Listagem e show com throttle; store/update/destroy com auth. Respostas com { data }.
 */
class SermonController extends Controller
{
    public function __construct(
        private SermonApiService $api
    ) {}

    /**
     * GET /api/v1/sermons – lista sermões (paginado).
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = min(max((int) $request->input('per_page', 15), 1), 50);
        $status = $request->input('status');
        $visibility = $request->input('visibility');
        $categoryId = $request->has('category_id') ? (int) $request->input('category_id') : null;
        $seriesId = $request->has('series_id') ? (int) $request->input('series_id') : null;
        $featuredOnly = $request->boolean('featured');

        $paginator = $this->api->list($perPage, $status, $visibility, $categoryId, $seriesId, $featuredOnly ?: null);

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
     * GET /api/v1/sermons/{id} – sermão por id.
     */
    public function show(int $id): JsonResponse
    {
        $sermon = $this->api->getById($id);
        if (! $sermon) {
            return response()->json(['message' => 'Sermão não encontrado.'], 404);
        }
        $this->authorize('view', $sermon);

        return response()->json(['data' => $sermon]);
    }

    /**
     * GET /api/v1/sermons/slug/{slug} – sermão por slug.
     */
    public function showBySlug(string $slug): JsonResponse
    {
        $sermon = $this->api->getBySlug($slug);
        if (! $sermon) {
            return response()->json(['message' => 'Sermão não encontrado.'], 404);
        }
        return response()->json(['data' => $sermon]);
    }

    /**
     * POST /api/v1/sermons (autenticado).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'introduction' => 'nullable|string',
            'development' => 'nullable|string',
            'conclusion' => 'nullable|string',
            'application' => 'nullable|string',
            'full_content' => 'nullable|string',
            'category_id' => 'nullable|exists:sermon_categories,id',
            'series_id' => 'nullable|exists:bible_series,id',
            'user_id' => 'nullable|exists:users,id',
            'cover_image' => 'nullable|string|max:500',
            'status' => 'nullable|in:draft,published,archived',
            'visibility' => 'nullable|in:public,members,private',
            'is_featured' => 'nullable|boolean',
            'sermon_date' => 'nullable|date',
            'tags' => 'nullable|array',
            'tags.*' => 'nullable',
            'bible_references' => 'nullable|array',
            'bible_references.*' => 'nullable|array',
        ]);
        $validated['user_id'] = $validated['user_id'] ?? $request->user()?->id;
        $validated['visibility'] = $validated['visibility'] ?? Sermon::VISIBILITY_PRIVATE;
        $validated['status'] = $validated['status'] ?? Sermon::STATUS_DRAFT;
        $sermon = $this->api->create($validated);
        return response()->json(['data' => $sermon], 201);
    }

    /**
     * PUT/PATCH /api/v1/sermons/{id} (autenticado).
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $sermon = $this->api->getById($id);
        if (! $sermon) {
            return response()->json(['message' => 'Sermão não encontrado.'], 404);
        }
        $this->authorize('update', $sermon);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'slug' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'introduction' => 'nullable|string',
            'development' => 'nullable|string',
            'conclusion' => 'nullable|string',
            'application' => 'nullable|string',
            'full_content' => 'nullable|string',
            'category_id' => 'nullable|exists:sermon_categories,id',
            'series_id' => 'nullable|exists:bible_series,id',
            'cover_image' => 'nullable|string|max:500',
            'status' => 'nullable|in:draft,published,archived',
            'visibility' => 'nullable|in:public,members,private',
            'is_featured' => 'nullable|boolean',
            'sermon_date' => 'nullable|date',
            'tags' => 'nullable|array',
            'tags.*' => 'nullable',
            'bible_references' => 'nullable|array',
            'bible_references.*' => 'nullable|array',
        ]);
        $sermon = $this->api->update($sermon, $validated);
        return response()->json(['data' => $sermon]);
    }

    /**
     * DELETE /api/v1/sermons/{id} (autenticado).
     */
    public function destroy(int $id): JsonResponse
    {
        $sermon = $this->api->getById($id);
        if (! $sermon) {
            return response()->json(['message' => 'Sermão não encontrado.'], 404);
        }
        $this->authorize('delete', $sermon);

        $this->api->destroy($sermon);
        return response()->json(['data' => ['success' => true]]);
    }
}
