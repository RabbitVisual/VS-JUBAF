<?php

namespace Modules\SocialAction\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\SocialAction\App\Models\SocialCampaign;
use Modules\SocialAction\App\Services\SocialActionApiService;
use Modules\SocialAction\App\Services\TreasuryIntegrationService;

class CampaignManagerController extends Controller
{
    public function __construct(
        private SocialActionApiService $api,
        private TreasuryIntegrationService $treasury
    ) {}

    public function index(): View
    {
        $campaigns = $this->api->listCampaigns(10);
        return view('socialaction::admin.campaigns.index', compact('campaigns'));
    }

    public function create(): View
    {
        $treasuryCampaigns = $this->treasury->getTreasuryCampaignsForSelect();
        return view('socialaction::admin.campaigns.create', compact('treasuryCampaigns'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'goal_description' => 'required|string',
            'target_amount' => 'required|numeric|min:0.01',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
            'status' => 'required|in:active,completed,cancelled,paused',
            'treasury_campaign_id' => 'nullable|exists:campaigns,id',
        ]);
        $this->api->createCampaign($validated);
        return redirect()->route('socialaction.admin.campaigns.index')
            ->with('success', 'Campanha criada com sucesso.');
    }

    public function edit(int $id): View
    {
        $campaign = $this->api->getCampaignById($id);
        if (! $campaign) {
            abort(404);
        }
        $treasuryCampaigns = $this->treasury->getTreasuryCampaignsForSelect();
        return view('socialaction::admin.campaigns.edit', compact('campaign', 'treasuryCampaigns'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $campaign = SocialCampaign::findOrFail($id);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'goal_description' => 'required|string',
            'target_amount' => 'required|numeric|min:0.01',
            'current_amount' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
            'status' => 'required|in:active,completed,cancelled,paused',
            'treasury_campaign_id' => 'nullable|exists:campaigns,id',
        ]);
        if ($validated['treasury_campaign_id'] ?? null) {
            unset($validated['current_amount']);
        }
        $this->api->updateCampaign($campaign, $validated);
        if ($campaign->fresh()->treasury_campaign_id) {
            $this->treasury->syncSocialCampaignAmountFromTreasury($campaign->fresh());
        }
        return redirect()->route('socialaction.admin.campaigns.index')
            ->with('success', 'Campanha atualizada com sucesso.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $campaign = SocialCampaign::findOrFail($id);
        $this->api->destroyCampaign($campaign);
        return redirect()->route('socialaction.admin.campaigns.index')
            ->with('success', 'Campanha removida com sucesso.');
    }
}
