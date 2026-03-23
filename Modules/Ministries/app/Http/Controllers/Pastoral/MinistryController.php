<?php

namespace Modules\Ministries\App\Http\Controllers\Pastoral;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\Ministries\App\Models\Ministry;
use Modules\Ministries\App\Models\MinistryMember;

class MinistryController extends Controller
{
    /**
     * Lista ministérios (visão pastoral: visualização e estatísticas).
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

        return view('ministries::pastoralpanel.index', compact('ministries', 'stats'));
    }

    /**
     * Detalhes do ministério (visão pastoral: líderes, membros, pendentes; pastor pode aprovar).
     */
    public function show(Ministry $ministry): View
    {
        $ministry->load(['leader', 'coLeader', 'members', 'pendingMembers']);

        return view('ministries::pastoralpanel.show', compact('ministry'));
    }

    /**
     * Aprova membro pendente no ministério.
     */
    public function approveMember(Ministry $ministry, User $user): RedirectResponse
    {
        $ministry->members()->updateExistingPivot($user->id, [
            'status' => 'active',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        return redirect()->route('pastor.ministerios.show', $ministry)
            ->with('success', 'Membro aprovado com sucesso!');
    }
}
