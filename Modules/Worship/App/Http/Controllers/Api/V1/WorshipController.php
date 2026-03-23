<?php

namespace Modules\Worship\App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Worship\App\Services\WorshipApiService;

/**
 * API v1 central de Worship. Respostas no padrão { data }.
 * Alimenta Projection (setlists, slides), Sermons (songs), Academy (cursos, classroom).
 */
class WorshipController extends Controller
{
    public function __construct(
        private WorshipApiService $api
    ) {}

    /**
     * GET /api/v1/worship/setlists
     */
    public function setlists(Request $request): JsonResponse
    {
        $limit = min(max((int) $request->query('limit', 10), 1), 50);
        $data = $this->api->getSetlists($limit);

        return response()->json(['data' => $data]);
    }

    /**
     * GET /api/v1/worship/setlists/{id}
     */
    public function setlist(int $id): JsonResponse
    {
        $data = $this->api->getSetlistWithItems($id);

        return response()->json(['data' => $data]);
    }

    /**
     * GET /api/v1/worship/songs?q=
     */
    public function songs(Request $request): JsonResponse
    {
        $q = $request->query('q', '');
        if (strlen(trim($q)) < 2) {
            return response()->json(['data' => [], 'message' => 'Query mínima 2 caracteres.'], 422);
        }
        $limit = min(max((int) $request->query('limit', 20), 1), 50);
        $data = $this->api->searchSongs($q, $limit);

        return response()->json(['data' => $data]);
    }

    /**
     * GET /api/v1/worship/songs/{id}
     */
    public function song(int $id): JsonResponse
    {
        $song = $this->api->getSong($id);

        return response()->json([
            'data' => [
                'id' => $song->id,
                'title' => $song->title,
                'artist' => $song->artist,
                'content_chordpro' => $song->content_chordpro,
                'lyrics_only' => $song->lyrics_only,
                'original_key' => $song->original_key?->value ?? null,
            ],
        ]);
    }

    /**
     * GET /api/v1/worship/songs/{id}/slides
     */
    public function songSlides(int $id): JsonResponse
    {
        $song = $this->api->getSong($id);
        $data = $this->api->getSongSlides($song);

        return response()->json(['data' => $data]);
    }

    /**
     * GET /api/v1/worship/academy/courses (auth)
     */
    public function academyCourses(): JsonResponse
    {
        $data = $this->api->getAcademyCourses();

        return response()->json(['data' => $data]);
    }

    /**
     * GET /api/v1/worship/academy/courses/{id} (auth) – classroom progress
     */
    public function academyCourse(int $id): JsonResponse
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['message' => 'Não autenticado.'], 401);
        }

        $payload = $this->api->getAcademyCourseWithProgress($id, $user);

        return response()->json([
            'data' => [
                'course' => $payload['course'],
                'enrollment' => $payload['enrollment'],
                'completed_lessons' => $payload['completed_lessons'],
            ],
        ]);
    }

    /**
     * POST /api/v1/worship/academy/lessons/{id}/complete (auth)
     */
    public function academyLessonComplete(int $id): JsonResponse
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['message' => 'Não autenticado.'], 401);
        }

        $result = $this->api->markLessonComplete($id, $user);

        return response()->json(['data' => $result]);
    }

    /**
     * GET /api/v1/worship/academy/courses/{id}/structure (auth) – admin builder
     */
    public function academyCourseStructure(int $id): JsonResponse
    {
        $course = $this->api->getAcademyCourseStructure($id);

        return response()->json(['data' => $course]);
    }

    /**
     * POST /api/v1/worship/academy/courses (auth) – admin create
     */
    public function academyStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'instrument_id' => 'required|exists:worship_instruments,id',
            'level' => 'required|string',
            'description' => 'nullable|string',
            'status' => 'nullable|string|in:draft,published',
        ]);

        $course = $this->api->createAcademyCourse($validated);

        return response()->json(['data' => $course]);
    }

    /**
     * POST /api/v1/worship/academy/courses/{id}/structure (auth) – admin save structure
     */
    public function academyUpdateStructure(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'modules' => 'present|array',
            'modules.*.title' => 'required|string',
            'modules.*.lessons' => 'present|array',
        ]);

        $course = $this->api->updateAcademyCourseStructure($id, $data);

        return response()->json(['data' => ['message' => 'Structure saved', 'course' => $course]]);
    }
}
