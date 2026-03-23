<?php

namespace Modules\Sermons\App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Sermons\App\Models\SermonStudyNote;

class SermonStudyNoteController extends Controller
{
    /**
     * GET /api/v1/sermons/study-notes – list current user's study notes (optional filters).
     */
    public function index(Request $request): JsonResponse
    {
        $query = SermonStudyNote::where('user_id', $request->user()->id)
            ->orderBy('updated_at', 'desc');

        if ($request->filled('reference_text')) {
            $query->where('reference_text', 'like', '%' . $request->input('reference_text') . '%');
        }
        if ($request->filled('sermon_id')) {
            $query->where(function ($q) use ($request) {
                $q->where('sermon_id', $request->input('sermon_id'))
                    ->orWhereNull('sermon_id');
            });
        }
        if ($request->boolean('global_only')) {
            $query->where('is_global', true);
        }

        $notes = $query->paginate(min(max((int) $request->input('per_page', 20), 1), 50));

        return response()->json([
            'data' => $notes->items(),
            'meta' => [
                'current_page' => $notes->currentPage(),
                'last_page' => $notes->lastPage(),
                'per_page' => $notes->perPage(),
                'total' => $notes->total(),
            ],
        ]);
    }

    /**
     * POST /api/v1/sermons/study-notes – create a study note.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'reference_text' => 'required|string|max:100',
            'sermon_id' => 'nullable|exists:sermons,id',
            'book_id' => 'nullable|exists:books,id',
            'chapter_id' => 'nullable|exists:chapters,id',
            'content' => 'required|string',
            'is_global' => 'nullable|boolean',
        ]);

        $validated['user_id'] = $request->user()->id;
        $validated['is_global'] = $validated['is_global'] ?? false;

        $note = SermonStudyNote::create($validated);

        return response()->json(['data' => $note], 201);
    }

    /**
     * GET /api/v1/sermons/study-notes/{id} – show (owner only).
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $note = SermonStudyNote::find($id);
        if (! $note || $note->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Nota não encontrada.'], 404);
        }

        return response()->json(['data' => $note]);
    }

    /**
     * PUT /api/v1/sermons/study-notes/{id} – update (owner only).
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $note = SermonStudyNote::find($id);
        if (! $note || $note->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Nota não encontrada.'], 404);
        }

        $validated = $request->validate([
            'reference_text' => 'sometimes|string|max:100',
            'content' => 'sometimes|string',
            'is_global' => 'nullable|boolean',
        ]);

        $note->update($validated);

        return response()->json(['data' => $note->fresh()]);
    }

    /**
     * DELETE /api/v1/sermons/study-notes/{id} – delete (owner only).
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $note = SermonStudyNote::find($id);
        if (! $note || $note->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Nota não encontrada.'], 404);
        }

        $note->delete();

        return response()->json(['data' => ['success' => true]]);
    }
}
