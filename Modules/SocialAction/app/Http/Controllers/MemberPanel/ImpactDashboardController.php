<?php

namespace Modules\SocialAction\App\Http\Controllers\MemberPanel;

use App\Http\Controllers\Controller;
use Modules\SocialAction\App\Models\SocialAssistance;
use Modules\SocialAction\App\Models\SocialCampaign;

class ImpactDashboardController extends Controller
{
    public function index()
    {
        $totalAssistances = SocialAssistance::where('status', 'delivered')->count();
        $recentCampaigns = SocialCampaign::latest()->take(3)->get();

        return view('socialaction::memberpanel.impact.index', compact('totalAssistances', 'recentCampaigns'));
    }
}
