<?php

namespace Modules\ChurchCouncil\App\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\ChurchCouncil\App\Models\CouncilAgenda;
use Modules\ChurchCouncil\App\Models\CouncilApproval;
use Modules\ChurchCouncil\App\Models\CouncilDocument;
use Modules\ChurchCouncil\App\Models\CouncilMeeting;
use Modules\ChurchCouncil\App\Models\CouncilMember;
use Modules\ChurchCouncil\App\Models\CouncilProject;

/**
 * Serviço central da API de conselho (v1).
 * Expõe reuniões, membros, pautas, votos, aprovações, documentos e projetos.
 */
class ChurchCouncilApiService
{
    /**
     * Lista reuniões do conselho (paginado).
     * Filtros opcionais: status, type (meeting_type), date_from (scheduled_date >=).
     *
     * @return LengthAwarePaginator<CouncilMeeting>
     */
    public function listMeetings(
        int $perPage = 15,
        ?string $status = null,
        ?string $type = null,
        ?string $dateFrom = null
    ): LengthAwarePaginator {
        $query = CouncilMeeting::with(['creator', 'president'])->latest('scheduled_date');

        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        }
        if ($type !== null && $type !== '') {
            $query->where('meeting_type', $type);
        }
        if ($dateFrom !== null && $dateFrom !== '') {
            $query->whereDate('scheduled_date', '>=', $dateFrom);
        }

        return $query->paginate($perPage);
    }

    /**
     * Busca reunião por id.
     */
    public function getMeetingById(int $id): ?CouncilMeeting
    {
        return CouncilMeeting::with(['creator', 'president', 'agendas'])->find($id);
    }

    /**
     * Cria reunião. Aceita created_by, participants (array de ids), etc.
     */
    public function createMeeting(array $data): CouncilMeeting
    {
        if (isset($data['participant_ids'])) {
            $data['participants'] = $data['participant_ids'];
            unset($data['participant_ids']);
        }
        return CouncilMeeting::create($data);
    }

    /**
     * Atualiza reunião.
     */
    public function updateMeeting(CouncilMeeting $meeting, array $data): CouncilMeeting
    {
        $meeting->update($data);
        return $meeting->fresh(['creator', 'president', 'agendas']);
    }

    /**
     * Exclui reunião.
     */
    public function destroyMeeting(CouncilMeeting $meeting): bool
    {
        return $meeting->delete();
    }

    /**
     * Lista membros ativos do conselho.
     *
     * @return Collection<int, CouncilMember>
     */
    public function listMembers(): Collection
    {
        return CouncilMember::active()->with('user')->orderBy('council_role')->get();
    }

    /**
     * Lista pautas (filtro por meeting_id, status).
     *
     * @return LengthAwarePaginator<CouncilAgenda>
     */
    public function listAgendas(int $perPage = 15, ?int $meetingId = null, ?string $status = null): LengthAwarePaginator
    {
        $query = CouncilAgenda::with(['meeting', 'presenter.user', 'decisionMaker.user'])->latest();

        if ($meetingId !== null) {
            $query->where('meeting_id', $meetingId);
        }
        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        }

        return $query->paginate($perPage);
    }

    /**
     * Busca pauta por id (com votos).
     */
    public function getAgendaById(int $id): ?CouncilAgenda
    {
        return CouncilAgenda::with(['meeting', 'presenter.user', 'decisionMaker.user', 'votes.councilMember.user'])->find($id);
    }

    /**
     * Registra voto em pauta (member panel).
     */
    public function castVote(CouncilAgenda $agenda, int $councilMemberId, string $vote, ?string $comments = null): bool
    {
        if (! in_array($vote, ['yes', 'no', 'abstain'], true)) {
            return false;
        }
        $agenda->votes()->updateOrCreate(
            ['council_member_id' => $councilMemberId],
            ['vote' => $vote, 'comments' => $comments, 'voted_at' => now()]
        );

        return true;
    }

    /**
     * Lista aprovações (filtro por status).
     *
     * @return LengthAwarePaginator<CouncilApproval>
     */
    public function listApprovals(int $perPage = 15, ?string $status = null): LengthAwarePaginator
    {
        $query = CouncilApproval::with(['requester', 'approver.user'])->latest('submitted_at');

        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        }

        return $query->paginate($perPage);
    }

    /**
     * Lista documentos ativos.
     *
     * @return LengthAwarePaginator<CouncilDocument>
     */
    public function listDocuments(int $perPage = 15, ?string $type = null): LengthAwarePaginator
    {
        $query = CouncilDocument::active()->with('uploader')->latest('document_date');

        if ($type !== null && $type !== '') {
            $query->where('document_type', $type);
        }

        return $query->paginate($perPage);
    }

    /**
     * Lista projetos.
     *
     * @return LengthAwarePaginator<CouncilProject>
     */
    public function listProjects(int $perPage = 15, ?string $status = null): LengthAwarePaginator
    {
        $query = CouncilProject::with(['proposer', 'reviewer.user', 'ministry'])->latest();

        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        }

        return $query->paginate($perPage);
    }
}
