<?php

namespace Modules\Worship\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Worship\App\Models\WorshipTeamRole;

class WorshipTeamController extends Controller
{
    public function index()
    {
        $roles = WorshipTeamRole::withCount('rosters')->get();
        return view('worship::admin.team.index', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:50',
            'description' => 'nullable|string',
        ]);

        WorshipTeamRole::create($validated);
        return redirect()->back()->with('success', 'Papel ministerial criado com sucesso!');
    }

    public function destroy(WorshipTeamRole $role)
    {
        $role->delete();
        return redirect()->back()->with('success', 'Papel ministerial excluído.');
    }
}
