<?php

namespace Modules\Ministries\App\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Modules\Ministries\App\Models\Ministry;

/**
 * Serviço central da API de ministérios (v1).
 */
class MinistryApiService
{
    /**
     * Lista ministérios (paginado). Filtro opcional por is_active.
     *
     * @return LengthAwarePaginator<Ministry>
     */
    public function list(int $perPage = 15, ?bool $activeOnly = null): LengthAwarePaginator
    {
        $query = Ministry::with(['leader', 'coLeader'])->latest();

        if ($activeOnly !== null) {
            $query->where('is_active', $activeOnly);
        }

        return $query->paginate($perPage);
    }

    /**
     * Busca ministério por id.
     */
    public function getById(int $id): ?Ministry
    {
        return Ministry::with(['leader', 'coLeader', 'members'])->find($id);
    }

    /**
     * Cria ministério e opcionalmente sincroniza líderes como membros.
     */
    public function create(array $data): Ministry
    {
        if (empty($data['slug']) && ! empty($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        $ministry = Ministry::create($data);
        $this->syncLeadersAsMembers($ministry);
        return $ministry->fresh(['leader', 'coLeader']);
    }

    /**
     * Atualiza ministério e sincroniza líderes como membros.
     */
    public function update(Ministry $ministry, array $data): Ministry
    {
        if (array_key_exists('name', $data) && ! array_key_exists('slug', $data)) {
            $data['slug'] = Str::slug($data['name']);
        }
        $ministry->update($data);
        $this->syncLeadersAsMembers($ministry);
        return $ministry->fresh(['leader', 'coLeader']);
    }

    /**
     * Garante que líder e co-líder estejam vinculados como membros ativos.
     */
    public function syncLeadersAsMembers(Ministry $ministry): void
    {
        $ministry->load(['leader', 'coLeader']);
        $attach = [];
        if ($ministry->leader_id && $ministry->leader && ! $ministry->hasMember($ministry->leader)) {
            $attach[$ministry->leader_id] = [
                'role' => 'leader',
                'status' => 'active',
                'joined_at' => now(),
                'approved_at' => now(),
            ];
        }
        if ($ministry->co_leader_id && $ministry->coLeader && ! $ministry->hasMember($ministry->coLeader)) {
            $attach[$ministry->co_leader_id] = [
                'role' => 'co_leader',
                'status' => 'active',
                'joined_at' => now(),
                'approved_at' => now(),
            ];
        }
        foreach ($attach as $userId => $pivot) {
            $ministry->members()->syncWithoutDetaching([$userId => $pivot]);
        }
    }

    /**
     * Exclui ministério (soft delete).
     */
    public function destroy(Ministry $ministry): bool
    {
        return $ministry->delete();
    }
}
