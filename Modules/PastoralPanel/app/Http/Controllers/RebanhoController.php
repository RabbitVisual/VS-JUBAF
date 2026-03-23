<?php

namespace Modules\PastoralPanel\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class RebanhoController extends Controller
{
    /**
     * Lista de membros (rebanho) com layout pastoral.
     */
    public function index(Request $request)
    {
        $query = User::with('role')->where('is_active', true);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('pastoralpanel::rebanho.index', compact('users'));
    }

    /**
     * Perfil pastoral do membro: árvore genealógica e histórico ministerial (sem campos técnicos).
     */
    public function show(User $user)
    {
        $user->load([
            'role',
            'ministries',
            'relationships.relatedUser',
        ]);

        return view('pastoralpanel::rebanho.show', compact('user'));
    }
}
