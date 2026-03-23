<?php

namespace Modules\Treasury\App\Http\Controllers\Pastoral;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Treasury\App\Models\FinancialGoal;
use Modules\Treasury\App\Models\TreasuryPermission;
use Modules\Treasury\App\Services\TreasuryApiService;

class FinancialGoalController extends Controller
{
    public function __construct(
        private TreasuryApiService $api
    ) {}

    public function index(): View
    {
        $permission = TreasuryPermission::forUserOrAdmin(auth()->user());
        $goals = $this->api->listGoals(20);

        return view('treasury::pastoralpanel.goals.index', compact('goals', 'permission'));
    }

    public function create(): View
    {
        $permission = TreasuryPermission::forUserOrAdmin(auth()->user());
        $campaigns = $this->api->getEntryFormOptions()['campaigns'];

        return view('treasury::pastoralpanel.goals.create', compact('campaigns', 'permission'));
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

        return redirect()->route('pastor.tesouraria.goals.index')
            ->with('success', 'Meta financeira criada com sucesso!');
    }

    public function show(FinancialGoal $goal): View
    {
        $permission = TreasuryPermission::forUserOrAdmin(auth()->user());
        $goal = $this->api->getGoal($goal->id);

        return view('treasury::pastoralpanel.goals.show', compact('goal', 'permission'));
    }

    public function edit(FinancialGoal $goal): View
    {
        $permission = TreasuryPermission::forUserOrAdmin(auth()->user());
        $campaigns = $this->api->getEntryFormOptions()['campaigns'];

        return view('treasury::pastoralpanel.goals.edit', compact('goal', 'campaigns', 'permission'));
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

        return redirect()->route('pastor.tesouraria.goals.index')
            ->with('success', 'Meta financeira atualizada com sucesso!');
    }

    public function destroy(FinancialGoal $goal)
    {
        $this->api->deleteGoal($goal, auth()->user());

        return redirect()->route('pastor.tesouraria.goals.index')
            ->with('success', 'Meta financeira removida com sucesso!');
    }
}
