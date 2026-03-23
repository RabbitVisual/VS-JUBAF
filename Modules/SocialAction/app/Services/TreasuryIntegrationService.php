<?php

namespace Modules\SocialAction\App\Services;

use App\Models\User;
use Modules\Treasury\App\Models\FinancialEntry;
use Modules\Treasury\App\Models\Campaign as TreasuryCampaign;
use Modules\Treasury\App\Services\TreasuryApiService;

/**
 * Integração Ação Social ↔ Tesouraria.
 * - Vincula campanhas sociais à campanha financeira (doações).
 * - Registra despesa na tesouraria quando assistência é auxílio financeiro.
 */
class TreasuryIntegrationService
{
    public const EXPENSE_CATEGORY = 'social_action';

    public function __construct(
        private TreasuryApiService $treasuryApi
    ) {}

    /**
     * Lista campanhas da tesouraria para vínculo (ativas).
     *
     * @return \Illuminate\Support\Collection<int, TreasuryCampaign>
     */
    public function getTreasuryCampaignsForSelect(): \Illuminate\Support\Collection
    {
        return TreasuryCampaign::orderBy('name')
            ->get(['id', 'name', 'slug', 'target_amount', 'current_amount', 'start_date', 'end_date']);
    }

    /**
     * Cria despesa na tesouraria para assistência financeira e retorna a entrada.
     * Requer que o usuário tenha permissão canCreateEntries na tesouraria.
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException 403 se sem permissão
     */
    public function createExpenseForAssistance(
        string $title,
        float $amount,
        string $description,
        string $entryDate,
        ?int $beneficiaryId = null,
        ?int $socialAssistanceId = null,
        User|null $user = null
    ): FinancialEntry {
        $user = $user ?? auth()->user();
        $data = [
            'type' => 'expense',
            'category' => self::EXPENSE_CATEGORY,
            'title' => $title,
            'description' => $description,
            'amount' => $amount,
            'entry_date' => $entryDate,
            'campaign_id' => null,
            'goal_id' => null,
            'ministry_id' => null,
            'payment_method' => null,
            'reference_number' => null,
        ];
        if ($socialAssistanceId) {
            $data['description'] = ($data['description'] ? $data['description'] . "\n" : '') . 'Ação Social - Assistência #' . $socialAssistanceId;
        }
        return $this->treasuryApi->createEntry($data, $user);
    }

    /**
     * Sincroniza current_amount da campanha social com a campanha da tesouraria (se vinculada).
     */
    public function syncSocialCampaignAmountFromTreasury(\Modules\SocialAction\App\Models\SocialCampaign $socialCampaign): void
    {
        if (! $socialCampaign->treasury_campaign_id) {
            return;
        }
        $treasury = TreasuryCampaign::find($socialCampaign->treasury_campaign_id);
        if ($treasury) {
            $treasury->updateCurrentAmount();
            $socialCampaign->update(['current_amount' => $treasury->fresh()->current_amount]);
        }
    }

    /**
     * Verifica se o usuário atual pode criar entradas na tesouraria (para exibir opção de auxílio financeiro).
     */
    public function canCurrentUserCreateTreasuryEntries(): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }
        $perm = \Modules\Treasury\App\Models\TreasuryPermission::where('user_id', $user->id)->first();

        return $perm && $perm->canCreateEntries();
    }
}
