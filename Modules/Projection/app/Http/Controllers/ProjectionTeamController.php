<?php

namespace Modules\Projection\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectionTeamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $users = User::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(15);

        return view('projection::admin.team.index', compact('users', 'search'));
    }

    /**
     * Toggle projection permission for a user.
     */
    public function togglePermission(User $user)
    {
        // Prevent toggling self or critical roles if needed, but 'can_project' is safe.
        // Maybe ensure only admins can do this? Middleware handles auth.

        $user->can_project = !$user->can_project;
        $user->save();

        return response()->json([
            'success' => true,
            'can_project' => $user->can_project,
            'message' => 'Permissão atualizada com sucesso.'
        ]);
    }
}
