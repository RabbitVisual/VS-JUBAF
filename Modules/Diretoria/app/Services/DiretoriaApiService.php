<?php

namespace Modules\Diretoria\App\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\Diretoria\App\Models\Pauta;
use Modules\Diretoria\App\Models\DiretoriaApproval;
use Modules\Diretoria\App\Models\AtaDocumento;
use Modules\Diretoria\App\Models\Reuniao;
use Modules\Diretoria\App\Models\DiretoriaMember;
use Modules\Diretoria\App\Models\diretoriaProject;

/**
 * Serviço central da API de conselho (v1).
 * Expõe reuniões, membros, pautas, votos, aprovações, documentos e projetos.
 */
class DiretoriaApiService
{
    /**
     * Lista reuniões do conselho (paginado).
     * Filtros opcionais: status, type (meeting_type), date_from (scheduled_date >=).
     *
     * @return LengthAwarePaginator<Reuniao>
     */
    public function listMeetings(
        int $perPage = 15,
        ?string $status = null,
        ?string $type = null,
        ?string $dateFrom = null
    ): LengthAwarePaginator {
        $query = Reuniao::with(['creator', 'president'])->latest('scheduled_date');

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
    public function getMeetingById(int $id): ?Reuniao
    {
        return Reuniao::with(['creator', 'president', 'agendas'])->find($id);
    }

    /**
     * Cria reunião. Aceita created_by, participants (array de ids), etc.
     */
    public function createMeeting(array $data): Reuniao
    {
        if (isset($data['participant_ids'])) {
            $data['participants'] = $data['participant_ids'];
            unset($data['participant_ids']);
        }
        return Reuniao::create($data);
    }

    /**
     * Atualiza reunião.
     */
    public function updateMeeting(Reuniao $meeting, array $data): Reuniao
    {
        $meeting->update($data);
        return $meeting->fresh(['creator', 'president', 'agendas']);
    }

    /**
     * Exclui reunião.
     */
    public function destroyMeeting(Reuniao $meeting): bool
    {
        return $meeting->delete();
    }

    /**
     * Lista membros ativos do conselho.
     *
     * @return Collection<int, DiretoriaMember>
     */
    public function listMembers(): Collection
    {
        return DiretoriaMember::active()->with('user')->orderBy('diretoria_role')->get();
    }

    /**
     * Lista pautas (filtro por meeting_id, status).
     *
     * @return LengthAwarePaginator<Pauta>
     */
    public function listAgendas(int $perPage = 15, ?int $meetingId = null, ?string $status = null): LengthAwarePaginator
    {
        $query = Pauta::with(['meeting', 'presenter.user', 'decisionMaker.user'])->latest();

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
    public function getAgendaById(int $id): ?Pauta
    {
        return Pauta::with(['meeting', 'presenter.user', 'decisionMaker.user', 'votes.DiretoriaMember.user'])->find($id);
    }

    /**
     * Registra voto em pauta (member panel).
     */
    public function castVote(Pauta $agenda, int $DiretoriaMemberId, string $vote, ?string $comments = null): bool
    {
        if (! in_array($vote, ['yes', 'no', 'abstain'], true)) {
            return false;
        }
        $agenda->votes()->updateOrCreate(
            ['diretoria_member_id' => $DiretoriaMemberId],
            ['vote' => $vote, 'comments' => $comments, 'voted_at' => now()]
        );

        return true;
    }

    /**
     * Lista aprovações (filtro por status).
     *
     * @return LengthAwarePaginator<DiretoriaApproval>
     */
    public function listApprovals(int $perPage = 15, ?string $status = null): LengthAwarePaginator
    {
        $query = DiretoriaApproval::with(['requester', 'approver.user'])->latest('submitted_at');

        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        }

        return $query->paginate($perPage);
    }

    /**
     * Lista documentos ativos.
     *
     * @return LengthAwarePaginator<AtaDocumento>
     */
    public function listDocuments(int $perPage = 15, ?string $type = null): LengthAwarePaginator
    {
        $query = AtaDocumento::active()->with('uploader')->latest('document_date');

        if ($type !== null && $type !== '') {
            $query->where('document_type', $type);
        }

        return $query->paginate($perPage);
    }

    /**
     * Lista projetos.
     *
     * @return LengthAwarePaginator<diretoriaProject>
     */
    public function listProjects(int $perPage = 15, ?string $status = null): LengthAwarePaginator
    {
        $query = diretoriaProject::with(['proposer', 'reviewer.user', 'ministry'])->latest();

        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        }

        return $query->paginate($perPage);
    }
}
