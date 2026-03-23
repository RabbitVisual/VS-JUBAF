<?php

namespace Modules\Diretoria\App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Diretoria\App\Models\diretoriaAgenda;
use Modules\Diretoria\App\Services\DiretoriaApiService;

/**
 * API central do conselho (v1).
 * Reuniões, membros, pautas, votos, aprovações, documentos, projetos. Respostas com { data }.
 */
class DiretoriaController extends Controller
{
    public function __construct(
        private DiretoriaApiService $api
    ) {}

    /**
     * GET /api/v1/church-diretoria – lista reuniões (paginado).
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = min(max((int) $request->input('per_page', 15), 1), 50);
        $status = $request->input('status');
        $type = $request->input('type');
        $dateFrom = $request->input('date_from');
        $paginator = $this->api->listMeetings($perPage, $status, $type, $dateFrom);

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
     * GET /api/v1/church-diretoria/{id}
     */
    public function show(int $id): JsonResponse
    {
        $meeting = $this->api->getMeetingById($id);
        if (! $meeting) {
            return response()->json(['message' => 'Reunião não encontrada.'], 404);
        }
        return response()->json(['data' => $meeting]);
    }

    /**
     * POST /api/v1/church-diretoria
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'scheduled_date' => 'required|date',
            'location' => 'nullable|string|max:255',
            'status' => 'nullable|in:scheduled,in_progress,completed,cancelled',
            'meeting_type' => 'nullable|in:ordinary,extraordinary,emergency',
            'created_by' => 'nullable|exists:users,id',
            'president_id' => 'nullable|exists:diretoria_members,id',
            'minutes' => 'nullable|string',
            'participants' => 'nullable|array',
            'meeting_link' => 'nullable|string|max:500',
            'quorum_present' => 'nullable|integer|min:0',
        ]);
        $validated['created_by'] = $validated['created_by'] ?? $request->user()?->id;
        $meeting = $this->api->createMeeting($validated);
        return response()->json(['data' => $meeting], 201);
    }

    /**
     * PUT/PATCH /api/v1/church-diretoria/{id}
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $meeting = $this->api->getMeetingById($id);
        if (! $meeting) {
            return response()->json(['message' => 'Reunião não encontrada.'], 404);
        }
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'scheduled_date' => 'sometimes|date',
            'actual_start_time' => 'nullable|date',
            'actual_end_time' => 'nullable|date',
            'location' => 'nullable|string|max:255',
            'status' => 'nullable|in:scheduled,in_progress,completed,cancelled',
            'meeting_type' => 'nullable|in:ordinary,extraordinary,emergency',
            'president_id' => 'nullable|exists:diretoria_members,id',
            'minutes' => 'nullable|string',
            'participants' => 'nullable|array',
            'meeting_link' => 'nullable|string|max:500',
            'quorum_present' => 'nullable|integer|min:0',
        ]);
        $meeting = $this->api->updateMeeting($meeting, $validated);
        return response()->json(['data' => $meeting]);
    }

    /**
     * DELETE /api/v1/church-diretoria/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        $meeting = $this->api->getMeetingById($id);
        if (! $meeting) {
            return response()->json(['message' => 'Reunião não encontrada.'], 404);
        }
        $this->api->destroyMeeting($meeting);
        return response()->json(['data' => ['success' => true]]);
    }

    /**
     * GET /api/v1/church-diretoria/members
     */
    public function members(): JsonResponse
    {
        $members = $this->api->listMembers();

        return response()->json(['data' => $members]);
    }

    /**
     * GET /api/v1/church-diretoria/agendas
     */
    public function agendas(Request $request): JsonResponse
    {
        $perPage = min(max((int) $request->input('per_page', 15), 1), 50);
        $meetingId = $request->input('meeting_id') ? (int) $request->input('meeting_id') : null;
        $status = $request->input('status');
        $paginator = $this->api->listAgendas($perPage, $meetingId, $status);

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
     * GET /api/v1/church-diretoria/agendas/{id}
     */
    public function agendaShow(int $id): JsonResponse
    {
        $agenda = $this->api->getAgendaById($id);
        if (! $agenda) {
            return response()->json(['message' => 'Pauta não encontrada.'], 404);
        }

        return response()->json(['data' => $agenda]);
    }

    /**
     * POST /api/v1/church-diretoria/agendas/{id}/vote
     */
    public function agendaVote(Request $request, int $id): JsonResponse
    {
        $agenda = diretoriaAgenda::find($id);
        if (! $agenda) {
            return response()->json(['message' => 'Pauta não encontrada.'], 404);
        }
        $member = $request->user()?->diretoriaMember;
        if (! $member || ! $member->isActive()) {
            return response()->json(['message' => 'Você não tem permissão para votar.'], 403);
        }
        if (! $agenda->meeting->isInProgress()) {
            return response()->json(['message' => 'A reunião não está em andamento.'], 422);
        }
        $validated = $request->validate([
            'vote' => 'required|in:yes,no,abstain',
            'comments' => 'nullable|string|max:500',
        ]);
        $this->api->castVote($agenda, $member->id, $validated['vote'], $validated['comments'] ?? null);

        return response()->json(['data' => ['success' => true]]);
    }

    /**
     * GET /api/v1/church-diretoria/approvals
     */
    public function approvals(Request $request): JsonResponse
    {
        $perPage = min(max((int) $request->input('per_page', 15), 1), 50);
        $status = $request->input('status');
        $paginator = $this->api->listApprovals($perPage, $status);

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
     * GET /api/v1/church-diretoria/documents
     */
    public function documents(Request $request): JsonResponse
    {
        $perPage = min(max((int) $request->input('per_page', 15), 1), 50);
        $type = $request->input('type');
        $paginator = $this->api->listDocuments($perPage, $type);

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
     * GET /api/v1/church-diretoria/projects
     */
    public function projects(Request $request): JsonResponse
    {
        $perPage = min(max((int) $request->input('per_page', 15), 1), 50);
        $status = $request->input('status');
        $paginator = $this->api->listProjects($perPage, $status);

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
}
