<?php

namespace Modules\Treasury\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Modules\Treasury\App\Models\TreasuryPermission;
use Modules\Treasury\App\Services\TreasuryApiService;

class TreasuryPermissionController extends Controller
{
    public function __construct(
        private TreasuryApiService $api
    ) {}

    public function index()
    {
        $permission = TreasuryPermission::forUserOrAdmin(auth()->user());
        $permissions = $this->api->listPermissions(20);
        return view('treasury::admin.permissions.index', compact('permissions', 'permission'));
    }

    public function create()
    {
        $permission = TreasuryPermission::forUserOrAdmin(auth()->user());
        $users = User::whereDoesntHave('treasuryPermission')->orderBy('name')->get();
        return view('treasury::admin.permissions.create', compact('users', 'permission'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id|unique:treasury_permissions,user_id',
            'permission_level' => 'required|in:viewer,editor,admin',
            'can_view_reports' => 'boolean',
            'can_create_entries' => 'boolean',
            'can_edit_entries' => 'boolean',
            'can_delete_entries' => 'boolean',
            'can_manage_campaigns' => 'boolean',
            'can_manage_goals' => 'boolean',
            'can_export_data' => 'boolean',
        ]);
        $this->api->createPermission($validated, auth()->user());
        return redirect()->route('treasury.permissions.index')
            ->with('success', 'Permissão criada com sucesso!');
    }

    public function edit(TreasuryPermission $treasuryPermission)
    {
        $permission = TreasuryPermission::forUserOrAdmin(auth()->user());
        $treasuryPermission->load('user');
        return view('treasury::admin.permissions.edit', compact('treasuryPermission', 'permission'));
    }

    public function update(Request $request, TreasuryPermission $treasuryPermission)
    {
        $validated = $request->validate([
            'permission_level' => 'required|in:viewer,editor,admin',
            'can_view_reports' => 'boolean',
            'can_create_entries' => 'boolean',
            'can_edit_entries' => 'boolean',
            'can_delete_entries' => 'boolean',
            'can_manage_campaigns' => 'boolean',
            'can_manage_goals' => 'boolean',
            'can_export_data' => 'boolean',
        ]);
        $this->api->updatePermission($treasuryPermission, $validated, auth()->user());
        return redirect()->route('treasury.permissions.index')
            ->with('success', 'Permissão atualizada com sucesso!');
    }

    public function destroy(TreasuryPermission $treasuryPermission)
    {
        $this->api->deletePermission($treasuryPermission, auth()->user());
        return redirect()->route('treasury.permissions.index')
            ->with('success', 'Permissão removida com sucesso!');
    }
}
