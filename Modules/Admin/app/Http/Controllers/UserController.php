<?php

namespace Modules\Admin\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRelationship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Modules\Notifications\App\Services\InAppNotificationService;

class UserController extends Controller
{
    public function __construct(
        protected InAppNotificationService $inAppNotificationService
    ) {}
    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        $query = User::with('roles')->withCount('relationships');

        // Filtros
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('cpf', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role_id')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('roles.id', $request->input('role_id'));
            });
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->input('is_active') === '1');
        }

        if ($request->filled('is_baptized')) {
            $query->where('is_baptized', $request->input('is_baptized') === '1');
        }

        if ($request->filled('role_slug')) {
            $roleFilter = $request->input('role_slug');
            $query->whereHas('roles', function ($q) use ($roleFilter) {
                $q->where('name', $roleFilter);
            });
        }

        // Ordenação
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDir = $request->get('sort_dir', 'desc');
        $query->orderBy($sortBy, $sortDir);

        $users = $query->paginate(15);

        // Estatísticas
        $stats = [
            'total' => User::count(),
            'active' => User::where('is_active', true)->count(),
            'inactive' => User::where('is_active', false)->count(),
            'baptized' => User::where('is_baptized', true)->count(),
            'by_role' => DB::table('model_has_roles')
                ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                ->select('roles.id', 'roles.name', DB::raw('count(*) as total'))
                ->where('model_has_roles.model_type', User::class)
                ->groupBy('roles.id', 'roles.name')
                ->get(),
        ];

        $roles = Role::all();

        return view('admin::users.index', compact('users', 'stats', 'roles'));
    }

    /**
     * Busca de membros por nome/CPF/email (para select de parentesco) — legado; preferir searchByCpf para vínculos.
     */
    public function search(Request $request): JsonResponse
    {
        $q = $request->input('q', '');
        $q = trim($q);
        if (strlen($q) < 2) {
            return response()->json(['data' => []]);
        }

        $users = User::query()
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('cpf', 'like', "%{$q}%");
            })
            ->orderBy('name')
            ->limit(15)
            ->get(['id', 'name', 'email', 'cpf', 'avatar']);

        return response()->json([
            'data' => $users->map(fn ($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'cpf' => $u->cpf,
                'photo' => $u->avatar ? \Illuminate\Support\Facades\Storage::url($u->avatar) : null,
            ]),
        ]);
    }

    /**
     * Busca de um único membro apenas por CPF (para vínculo familiar). Só retorna usuário se o CPF existir e for único.
     */
    public function searchByCpf(Request $request): JsonResponse
    {
        $cpf = $request->input('cpf', '');
        $cpf = preg_replace('/\D/', '', $cpf);
        if (strlen($cpf) !== 11) {
            return response()->json(['data' => null, 'message' => 'Informe um CPF válido com 11 dígitos.']);
        }

        // Match CPF whether stored as 11 digits or with punctuation
        $user = User::query()
            ->where(function ($q) use ($cpf) {
                $q->where('cpf', $cpf)
                    ->orWhereRaw("REPLACE(REPLACE(REPLACE(REPLACE(COALESCE(cpf,''),'.',''),'-',''),' ',''),'/','') = ?", [$cpf]);
            })
            ->first(['id', 'name', 'email', 'cpf', 'avatar']);

        if (! $user) {
            return response()->json(['data' => null, 'message' => 'Nenhum membro encontrado com este CPF.']);
        }

        return response()->json([
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'cpf' => $user->cpf,
                'photo' => $user->avatar ? Storage::url($user->avatar) : null,
            ],
        ]);
    }

    public function familyTreeAnalysis(User $user): JsonResponse
    {
        return response()->json([
            'analysis' => 'Análise automática desativada na versão JUBAF.',
        ]);
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $roles = Role::all();

        return view('admin::users.create', compact('roles'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'cpf' => 'nullable|string|max:14|unique:users,cpf',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:M,F,O',
            'marital_status' => 'nullable|in:solteiro,casado,divorciado,viuvo,uniao_estavel',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'cellphone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'address_number' => 'nullable|string|max:20',
            'address_complement' => 'nullable|string|max:100',
            'neighborhood' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|size:2',
            'zip_code' => 'nullable|string|max:10',
            'membership_date' => 'nullable|date',
            'time_congregating_months' => 'nullable|integer|min:0',
            'baptism_date' => 'nullable|date',
            'baptism_place' => 'nullable|string|max:255',
            'is_baptized' => 'boolean',
            'profession' => 'nullable|string|max:100',
            'education_level' => 'nullable|string|max:50',
            'workplace' => 'nullable|string|max:255',
            'emergency_contact_name' => 'nullable|string|max:100',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'emergency_contact_relationship' => 'nullable|string|max:50',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'is_active' => 'boolean',
            'photo' => 'nullable|image|max:2048',
            'notes' => 'nullable|string',
        ]);

        // Gera nome completo
        $validated['name'] = trim($validated['first_name'].' '.$validated['last_name']);

        // Hash da senha
        $validated['password'] = Hash::make($validated['password']);

        // Upload de foto
        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('users/photos', 'public');
        }

        // Remove campos não necessários
        unset($validated['password_confirmation']);

        $roleId = (int) $validated['role_id'];
        unset($validated['role_id']);
        $user = User::create($validated);
        if ($role = Role::find($roleId)) {
            $user->syncRoles([$role->name]);
        }

        $this->syncUserRelationships($user, $request);

        $this->inAppNotificationService->sendToAdmins('Novo membro cadastrado', "O membro {$user->name} foi cadastrado no sistema.", [
            'type' => 'info',
            'priority' => 'normal',
            'action_url' => route('admin.users.show', $user),
            'action_text' => 'Ver perfil',
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Membro criado com sucesso!');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $user->load([
            'role',
            'relationships.relatedUser',
            'financialEntries' => function ($q) {
                $q->orderBy('entry_date', 'desc')->limit(20);
            },
        ]);

        return view('admin::users.show', [
            'user' => $user,
            'level' => null,
            'points' => null,
            'progress' => null,
            'points_max_display' => null,
        ]);
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        $user->load(['role', 'relationships']);

        return view('admin::users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'cpf' => ['nullable', 'string', 'max:14', Rule::unique('users')->ignore($user->id)],
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:M,F,O',
            'marital_status' => 'nullable|in:solteiro,casado,divorciado,viuvo,uniao_estavel',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'cellphone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'address_number' => 'nullable|string|max:20',
            'address_complement' => 'nullable|string|max:100',
            'neighborhood' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|size:2',
            'zip_code' => 'nullable|string|max:10',
            'membership_date' => 'nullable|date',
            'time_congregating_months' => 'nullable|integer|min:0',
            'baptism_date' => 'nullable|date',
            'baptism_place' => 'nullable|string|max:255',
            'is_baptized' => 'boolean',
            'profession' => 'nullable|string|max:100',
            'education_level' => 'nullable|string|max:50',
            'workplace' => 'nullable|string|max:255',
            'emergency_contact_name' => 'nullable|string|max:100',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'emergency_contact_relationship' => 'nullable|string|max:50',
            'password' => 'nullable|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'is_active' => 'boolean',
            'photo' => 'nullable|image|max:2048',
            'notes' => 'nullable|string',
        ]);

        // Gera nome completo
        $validated['name'] = trim($validated['first_name'].' '.$validated['last_name']);

        // Hash da senha se fornecida
        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        // Upload de foto
        if ($request->hasFile('photo')) {
            // Remove foto antiga
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }
            $validated['photo'] = $request->file('photo')->store('users/photos', 'public');
        }

        // Remove campos não necessários
        unset($validated['password_confirmation']);

        $roleId = (int) $validated['role_id'];
        unset($validated['role_id']);
        $user->update($validated);
        if ($role = Role::find($roleId)) {
            $user->syncRoles([$role->name]);
        }

        $this->syncUserRelationships($user, $request);

        return redirect()->route('admin.users.index')
            ->with('success', 'Membro atualizado com sucesso!');
    }

    /**
     * Sincroniza vínculos familiares a partir do request. Cria pendentes para membros e notifica.
     */
    protected function syncUserRelationships(User $user, Request $request): void
    {
        $items = $request->input('relationships', []);
        if (! is_array($items)) {
            return;
        }

        $user->relationships()->delete();

        $types = array_keys(UserRelationship::relationshipTypeLabels());
        foreach ($items as $item) {
            $type = $item['relationship_type'] ?? null;
            $relatedUserId = isset($item['related_user_id']) && $item['related_user_id'] !== '' ? (int) $item['related_user_id'] : null;
            $relatedName = isset($item['related_name']) ? trim((string) $item['related_name']) : null;

            if (! $type || ! in_array($type, $types, true)) {
                continue;
            }
            if (! $relatedUserId && ! $relatedName) {
                continue;
            }

            $status = $relatedUserId ? UserRelationship::STATUS_PENDING : UserRelationship::STATUS_ACCEPTED;
            $rel = $user->relationships()->create([
                'related_user_id' => $relatedUserId ?: null,
                'related_name' => $relatedName ?: null,
                'relationship_type' => $type,
                'status' => $status,
                'invited_by' => auth()->id(),
            ]);

            if ($status === UserRelationship::STATUS_PENDING && $relatedUserId) {
                $relatedUser = User::find($relatedUserId);
                if ($relatedUser) {
                    $this->inAppNotificationService->sendToUser($relatedUser, 'Convite de parentesco', "{$user->name} te marcou como " . ($rel->relationship_type_label) . '. Aceite ou recuse o vínculo.', [
                        'type' => 'info',
                        'action_url' => route('memberpanel.relationships.pending'),
                        'action_text' => 'Ver e responder',
                        'notification_type' => 'family_relationship_invite',
                    ]);
                }
            }
        }
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        // Não permite deletar o próprio usuário
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Você não pode excluir seu próprio usuário!');
        }

        // Remove foto se existir
        if ($user->photo) {
            Storage::disk('public')->delete($user->photo);
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Membro excluído com sucesso!');
    }
}
