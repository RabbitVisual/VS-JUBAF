<?php

namespace Modules\SocialAction\App\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\SocialAction\App\Models\SocialAssistance;
use Modules\SocialAction\App\Models\SocialBeneficiary;
use Modules\SocialAction\App\Models\SocialCampaign;

/**
 * Serviço central da API de ação social (v1).
 * Campanhas, beneficiários e listagem de assistências.
 */
class SocialActionApiService
{
    /**
     * Lista campanhas (paginado).
     *
     * @return LengthAwarePaginator<SocialCampaign>
     */
    public function listCampaigns(int $perPage = 15, ?string $status = null): LengthAwarePaginator
    {
        $query = SocialCampaign::with('treasuryCampaign')->latest('start_date');

        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        }

        return $query->paginate($perPage);
    }

    public function getCampaignById(int $id): ?SocialCampaign
    {
        return SocialCampaign::with('treasuryCampaign')->find($id);
    }

    public function createCampaign(array $data): SocialCampaign
    {
        return SocialCampaign::create($data);
    }

    public function updateCampaign(SocialCampaign $campaign, array $data): SocialCampaign
    {
        $campaign->update($data);
        return $campaign->fresh(['treasuryCampaign']);
    }

    public function destroyCampaign(SocialCampaign $campaign): bool
    {
        return $campaign->delete();
    }

    /**
     * Lista assistências (paginado). Filtros opcionais: type, beneficiary_id.
     *
     * @return LengthAwarePaginator<SocialAssistance>
     */
    public function listAssistances(int $perPage = 20, ?string $type = null, ?int $beneficiaryId = null): LengthAwarePaginator
    {
        $query = SocialAssistance::with(['beneficiary', 'kit', 'pantryItem', 'financialEntry'])
            ->latest('registered_at');

        if ($type !== null && $type !== '') {
            $query->where('type', $type);
        }
        if ($beneficiaryId !== null) {
            $query->where('social_beneficiary_id', $beneficiaryId);
        }

        return $query->paginate($perPage);
    }

    /**
     * Lista beneficiários (para selects e API).
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, SocialBeneficiary>
     */
    public function listBeneficiariesForSelect(): \Illuminate\Database\Eloquent\Collection
    {
        return SocialBeneficiary::orderBy('full_name')->get();
    }
}
