<?php

namespace Modules\SocialAction\App\Services;

use Modules\SocialAction\App\Models\SocialCampaign;

class CampaignEngagementService
{
    /**
     * Calculate the percentage of completion for a campaign.
     */
    public function getProgressPercentage(SocialCampaign $campaign): float
    {
        if ($campaign->target_amount <= 0) {
            return 0;
        }

        $percentage = ($campaign->current_amount / $campaign->target_amount) * 100;

        return min(round($percentage, 2), 100);
    }

    /**
     * Get a motivating status message for the campaign.
     */
    public function getStatusMessage(SocialCampaign $campaign): string
    {
        $percent = $this->getProgressPercentage($campaign);

        if ($percent >= 100) {
            return 'Meta alcançada! Glória a Deus!';
        }

        if ($percent >= 75) {
            return 'Estamos quase lá! Faltam apenas '.($campaign->target_amount - $campaign->current_amount).'!';
        }

        return "Arrecadamos {$campaign->current_amount} de {$campaign->target_amount}. Participe!";
    }
}
