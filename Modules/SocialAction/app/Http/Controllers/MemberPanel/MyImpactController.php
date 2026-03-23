<?php

namespace Modules\SocialAction\App\Http\Controllers\MemberPanel;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Modules\SocialAction\App\Models\SocialAssistance;
use Modules\SocialAction\App\Models\SocialCampaign;
use Modules\SocialAction\App\Models\SocialVolunteer;
use Modules\SocialAction\App\Services\CampaignEngagementService;
use Modules\SocialAction\App\Services\SocialActionApiService;

class MyImpactController extends Controller
{
    public function __construct(
        protected CampaignEngagementService $engagementService,
        protected SocialActionApiService $api
    ) {}

    public function index(): View
    {
        $user = auth()->user();
        $userVolunteer = SocialVolunteer::where('user_id', $user->id)->first();

        $recentCampaigns = SocialCampaign::with('treasuryCampaign')
            ->where('status', 'active')
            ->orderBy('end_date')
            ->limit(3)
            ->get();

        $stats = [
            'families_helped' => SocialAssistance::distinct('social_beneficiary_id')->count('social_beneficiary_id'),
            'kits_delivered' => SocialAssistance::whereNotNull('kit_id')->count(),
            'total_assistances' => SocialAssistance::count(),
        ];

        $userStats = null;
        if ($userVolunteer) {
            $userStats = [
                'hours' => $userVolunteer->total_hours,
                'assistances' => SocialAssistance::where('volunteer_id', $user->id)->count(),
                'role' => $userVolunteer->role_label,
            ];
        }

        return view('socialaction::memberpanel.impact.index', compact('recentCampaigns', 'stats', 'userStats', 'userVolunteer'));
    }

    public function campaigns(): View
    {
        $campaigns = $this->api->listCampaigns(9, 'active');

        $activeDonorsCount = SocialVolunteer::count();
        if ($activeDonorsCount < 10) {
            $activeDonorsCount += SocialAssistance::distinct('volunteer_id')->count('volunteer_id');
        }

        $stats = [
            'families_helped' => SocialAssistance::distinct('social_beneficiary_id')->count('social_beneficiary_id'),
            'kits_delivered' => SocialAssistance::whereNotNull('kit_id')->count(),
            'total_assistances' => SocialAssistance::count(),
        ];

        return view('socialaction::memberpanel.campaigns.index', compact('campaigns', 'activeDonorsCount', 'stats'));
    }
}
