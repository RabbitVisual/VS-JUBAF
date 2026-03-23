<?php

namespace Modules\SocialAction\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\SocialAction\App\Models\SocialAssistance;
use Modules\SocialAction\App\Models\SocialBeneficiary;
use Modules\SocialAction\App\Models\SocialKit;
use Modules\SocialAction\App\Services\AssistanceEligibilityService;
use Modules\SocialAction\App\Services\StockManagerService;

class AssistanceController extends Controller
{
    protected $eligibilityService;
    protected $stockManager;

    public function __construct(AssistanceEligibilityService $eligibilityService, StockManagerService $stockManager)
    {
        $this->eligibilityService = $eligibilityService;
        $this->stockManager = $stockManager;
    }

    public function store(Request $request)
    {
        $request->validate([
            'beneficiary_id' => 'required|exists:social_beneficiaries,id',
            'type' => 'required',
            'kit_id' => 'nullable|exists:social_kits,id',
        ]);

        $beneficiary = SocialBeneficiary::findOrFail($request->beneficiary_id);

        $check = $this->eligibilityService->canReceiveAssistance($beneficiary, $request->type);

        if (!$check['allowed']) {
            return back()->with('error', $check['reason']);
        }

        $assistance = SocialAssistance::create($request->all());

        // Auto-deliver logic if status is delivered immediately
        if ($request->status === 'delivered' && $request->kit_id) {
            $kit = SocialKit::find($request->kit_id);
            $this->stockManager->deductKit($kit, 1, auth()->id());
            $assistance->update(['delivered_at' => now()]);
        }

        return redirect()->route('socialaction.admin.assistance.index')->with('success', 'Atendimento registrado!');
    }
}
