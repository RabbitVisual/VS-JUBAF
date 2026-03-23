<?php

namespace Modules\Treasury\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Treasury\App\Models\FinancialGoal;
use Modules\Treasury\App\Models\TreasuryPermission;
use Modules\Treasury\App\Services\TreasuryApiService;

class FinancialGoalController extends Controller
{
    public function __construct(
        private TreasuryApiService $api
    ) {}

    public function index()
    {
        $permission = TreasuryPermission::forUserOrAdmin(auth()->user());
        $goals = $this->api->listGoals(20);
        return view('treasury::admin.goals.index', compact('goals', 'permission'));
    }

    public function create()
    {
        $permission = TreasuryPermission::forUserOrAdmin(auth()->user());
        $campaigns = $this->api->getEntryFormOptions()['campaigns'];
        return view('treasury::admin.goals.create', compact('campaigns', 'permission'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'type' => 'required|in:monthly,yearly,campaign,custom',
            'target_amount' => 'required|numeric|min:0.01',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'category' => 'nullable|string',
            'campaign_id' => 'nullable|exists:campaigns,id',
            'is_active' => 'boolean',
        ]);
        $this->api->createGoal($validated, auth()->user());
        return redirect()->route('treasury.goals.index')
            ->with('success', 'Meta financeira criada com sucesso!');
    }

    public function show(FinancialGoal $goal)
    {
        $permission = TreasuryPermission::forUserOrAdmin(auth()->user());
        $goal = $this->api->getGoal($goal->id);
        return view('treasury::admin.goals.show', compact('goal', 'permission'));
    }

    public function edit(FinancialGoal $goal)
    {
        $permission = TreasuryPermission::forUserOrAdmin(auth()->user());
        $campaigns = $this->api->getEntryFormOptions()['campaigns'];
        return view('treasury::admin.goals.edit', compact('goal', 'campaigns', 'permission'));
    }

    public function update(Request $request, FinancialGoal $goal)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'type' => 'required|in:monthly,yearly,campaign,custom',
            'target_amount' => 'required|numeric|min:0.01',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'category' => 'nullable|string',
            'campaign_id' => 'nullable|exists:campaigns,id',
            'is_active' => 'boolean',
        ]);
        $this->api->updateGoal($goal, $validated, auth()->user());
        return redirect()->route('treasury.goals.index')
            ->with('success', 'Meta financeira atualizada com sucesso!');
    }

    public function destroy(FinancialGoal $goal)
    {
        $this->api->deleteGoal($goal, auth()->user());
        return redirect()->route('treasury.goals.index')
            ->with('success', 'Meta financeira removida com sucesso!');
    }
}
