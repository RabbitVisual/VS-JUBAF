<?php

namespace Modules\Ministries\App\Http\Controllers\Pastoral;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Ministries\App\Models\Ministry;
use Modules\Ministries\App\Models\MinistryPlan;

class MinistryPlanController extends Controller
{
    /**
     * Lista planos (visão pastoral: somente leitura).
     */
    public function index(Request $request): View
    {
        $query = MinistryPlan::with(['ministry', 'creator', 'councilApproval'])
            ->latest();

        if ($request->filled('ministry_id')) {
            $query->where('ministry_id', $request->ministry_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $plans = $query->paginate(15);
        $ministries = Ministry::active()->orderBy('name')->get(['id', 'name']);

        return view('ministries::pastoralpanel.plans.index', compact('plans', 'ministries'));
    }

    /**
     * Detalhes do plano (somente leitura).
     */
    public function show(Ministry $ministry, MinistryPlan $plan): View
    {
        if ($plan->ministry_id !== $ministry->id) {
            abort(404);
        }
        $plan->load(['ministry', 'creator', 'approver', 'councilApproval']);

        return view('ministries::pastoralpanel.plans.show', compact('ministry', 'plan'));
    }
}
