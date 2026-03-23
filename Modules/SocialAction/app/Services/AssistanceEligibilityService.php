<?php

namespace Modules\SocialAction\App\Services;

use Modules\SocialAction\App\Models\SocialBeneficiary;
use Modules\SocialAction\App\Models\SocialAssistance;
use Carbon\Carbon;

class AssistanceEligibilityService
{
    public function canReceiveAssistance(SocialBeneficiary $beneficiary, string $type = 'food'): array
    {
        // Rule: Minimum 30 days between Food Baskets
        if ($type === 'food') {
            $lastAssistance = SocialAssistance::where('beneficiary_id', $beneficiary->id)
                ->where('type', 'food')
                ->where('status', 'delivered')
                ->latest('delivered_at')
                ->first();

            if ($lastAssistance && $lastAssistance->delivered_at->diffInDays(now()) < 30) {
                return [
                    'allowed' => false,
                    'reason' => 'Beneficiário recebeu auxílio recentemente (menos de 30 dias).'
                ];
            }
        }

        return ['allowed' => true];
    }
}
