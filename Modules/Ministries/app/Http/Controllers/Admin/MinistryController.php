<?php

namespace Modules\Ministries\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Ministries\App\Models\Ministry;
use Modules\Ministries\App\Models\MinistryMember;
use Modules\Ministries\App\Services\MinistryApiService;

class MinistryController extends Controller
{
    public function __construct(
        private MinistryApiService $ministryApi
    ) {}

    /**
     * Lista todos os ministérios
     */
    public function index(): View
    {
        $ministries = Ministry::with(['leader', 'coLeader', 'activeMembers'])
            ->latest()
            ->paginate(15);

        $stats = [
            'total' => Ministry::count(),
            'active' => Ministry::active()->count(),
            'inactive' => Ministry::where('is_active', false)->count(),
            'total_members' => MinistryMember::active()->distinct('user_id')->count('user_id'),
            'pending_approvals' => MinistryMember::where('status', 'pending')->count(),
        ];

        return view('ministries::admin.index', compact('ministries', 'stats'));
    }

    /**
     * Mostra formulário de criação
     */
    public function create(): View
    {
        $users = User::where('is_active', true)->orderBy('name')->get();

        return view('ministries::admin.create', compact('users'));
    }

    /**
     * Cria novo ministério (usa MinistryApiService para sincronizar líderes como membros)
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:50',
            'leader_id' => 'nullable|exists:users,id',
            'co_leader_id' => 'nullable|exists:users,id',
            'is_active' => 'nullable',
            'requires_approval' => 'nullable',
            'max_members' => 'nullable|integer|min:1',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['requires_approval'] = $request->boolean('requires_approval');

        $this->ministryApi->create($validated);

        return redirect()->route('admin.ministries.index')
            ->with('success', 'Ministério criado com sucesso!');
    }

    /**
     * Mostra detalhes do ministério
     */
    public function show(Ministry $ministry): View
    {
        $ministry->load(['leader', 'coLeader', 'members', 'pendingMembers']);
        $users = User::where('is_active', true)->orderBy('name')->get();

        return view('ministries::admin.show', compact('ministry', 'users'));
    }

    /**
     * Mostra formulário de edição
     */
    public function edit(Ministry $ministry): View
    {
        $users = User::where('is_active', true)->orderBy('name')->get();

        return view('ministries::admin.edit', compact('ministry', 'users'));
    }

    /**
     * Atualiza ministério (usa MinistryApiService para sincronizar líderes)
     */
    public function update(Request $request, Ministry $ministry): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:50',
            'leader_id' => 'nullable|exists:users,id',
            'co_leader_id' => 'nullable|exists:users,id',
            'is_active' => 'nullable',
            'requires_approval' => 'nullable',
            'max_members' => 'nullable|integer|min:1',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['requires_approval'] = $request->boolean('requires_approval');

        $this->ministryApi->update($ministry, $validated);

        return redirect()->route('admin.ministries.index')
            ->with('success', 'Ministério atualizado com sucesso!');
    }

    /**
     * Remove ministério
     */
    public function destroy(Ministry $ministry): RedirectResponse
    {
        $ministry->delete();

        return redirect()->route('admin.ministries.index')
            ->with('success', 'Ministério removido com sucesso!');
    }

    /**
     * Adiciona membro ao ministério
     */
    public function addMember(Request $request, Ministry $ministry): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:member,coordinator',
        ]);

        // Verifica se já é membro
        if ($ministry->hasMember(User::find($validated['user_id']))) {
            return back()->with('error', 'Usuário já é membro deste ministério.');
        }

        // Verifica limite de membros
        if (! $ministry->canAddMembers()) {
            return back()->with('error', 'Limite de membros atingido.');
        }

        $status = $ministry->requires_approval ? 'pending' : 'active';

        $ministry->members()->attach($validated['user_id'], [
            'role' => $validated['role'],
            'status' => $status,
            'joined_at' => now(),
            'approved_at' => $status === 'active' ? now() : null,
            'approved_by' => $status === 'active' ? auth()->id() : null,
        ]);

        return back()->with('success', 'Membro adicionado com sucesso!');
    }

    /**
     * Remove membro do ministério
     */
    public function removeMember(Ministry $ministry, User $user): RedirectResponse
    {
        $ministry->members()->updateExistingPivot($user->id, [
            'status' => 'removed',
            'left_at' => now(),
        ]);

        return back()->with('success', 'Membro removido com sucesso!');
    }

    /**
     * Aprova membro pendente
     */
    public function approveMember(Ministry $ministry, User $user): RedirectResponse
    {
        $ministry->members()->updateExistingPivot($user->id, [
            'status' => 'active',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        return back()->with('success', 'Membro aprovado com sucesso!');
    }
}
