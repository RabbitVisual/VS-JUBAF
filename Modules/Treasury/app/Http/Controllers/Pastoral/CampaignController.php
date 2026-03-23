<?php

namespace Modules\Treasury\App\Http\Controllers\Pastoral;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Modules\Treasury\App\Models\Campaign;
use Modules\Treasury\App\Models\TreasuryPermission;
use Modules\Treasury\App\Services\TreasuryApiService;

class CampaignController extends Controller
{
    public function __construct(
        private TreasuryApiService $api
    ) {}

    public function index(): View
    {
        $permission = TreasuryPermission::forUserOrAdmin(auth()->user());
        $campaigns = $this->api->listCampaigns(20);

        return view('treasury::pastoralpanel.campaigns.index', compact('campaigns', 'permission'));
    }

    public function create(): View
    {
        $permission = TreasuryPermission::forUserOrAdmin(auth()->user());

        return view('treasury::pastoralpanel.campaigns.create', compact('permission'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:campaigns,slug',
            'description' => 'nullable|string',
            'target_amount' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'boolean',
            'image' => 'nullable|image|max:2048',
        ]);
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('campaigns', 'public');
        }
        $this->api->createCampaign($validated, auth()->user());

        return redirect()->route('pastor.tesouraria.campaigns.index')
            ->with('success', 'Campanha criada com sucesso!');
    }

    public function show(Campaign $campaign): View
    {
        $permission = TreasuryPermission::forUserOrAdmin(auth()->user());
        $campaign = $this->api->getCampaign($campaign->id);

        return view('treasury::pastoralpanel.campaigns.show', compact('campaign', 'permission'));
    }

    public function edit(Campaign $campaign): View
    {
        $permission = TreasuryPermission::forUserOrAdmin(auth()->user());

        return view('treasury::pastoralpanel.campaigns.edit', compact('campaign', 'permission'));
    }

    public function update(Request $request, Campaign $campaign)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:campaigns,slug,' . $campaign->id,
            'description' => 'nullable|string',
            'target_amount' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'boolean',
            'image' => 'nullable|image|max:2048',
        ]);
        if ($request->hasFile('image')) {
            if ($campaign->image) {
                Storage::disk('public')->delete($campaign->image);
            }
            $validated['image'] = $request->file('image')->store('campaigns', 'public');
        }
        $this->api->updateCampaign($campaign, $validated, auth()->user());

        return redirect()->route('pastor.tesouraria.campaigns.index')
            ->with('success', 'Campanha atualizada com sucesso!');
    }

    public function destroy(Campaign $campaign)
    {
        $this->api->deleteCampaign($campaign, auth()->user());

        return redirect()->route('pastor.tesouraria.campaigns.index')
            ->with('success', 'Campanha removida com sucesso!');
    }
}
