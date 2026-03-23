<?php

namespace Modules\Intercessor\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class IntercessorTeamController extends Controller
{
    public function index()
    {
        $teamMembers = User::whereHas('role', function($q) {
            $q->whereIn('slug', ['intercessor', 'prayer_team']);
        })->paginate(10);

        return view('intercessor::admin.team.index', compact('teamMembers'));
    }

    public function create(Request $request)
    {
        $search = $request->input('search');

        // Find users who are NOT intercessors/admins to be promoted
        $users = User::whereDoesntHave('role', function($q) {
                $q->whereIn('slug', ['intercessor', 'prayer_team', 'admin']);
            })
            ->when($search, function($q) use ($search) {
                $q->where(function($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->take(20)
            ->get();

        return view('intercessor::admin.team.create', compact('users', 'search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $user = User::findOrFail($request->user_id);

        // Find Intercessor Role
        $role = \App\Models\Role::where('slug', 'intercessor')->first();
        if (!$role) {
             // Fallback to prayer_team if intercessor doesn't exist
             $role = \App\Models\Role::where('slug', 'prayer_team')->first();
        }

        if ($role) {
            $user->update(['role_id' => $role->id]);
            return redirect()->route('admin.intercessor.team.index')->with('success', "{$user->name} promovido com sucesso!");
        }

        return back()->with('error', 'Papel de Intercessor não encontrado no sistema.');
    }

    public function destroy(User $user)
    {
        // Revert to 'membro' role
        $role = \App\Models\Role::where('slug', 'membro')->first();
        if ($role) {
            $user->update(['role_id' => $role->id]);
            return back()->with('success', "{$user->name} removido da equipe com sucesso.");
        }

        return back()->with('error', 'Papel de Membro não encontrado.');
    }
}
