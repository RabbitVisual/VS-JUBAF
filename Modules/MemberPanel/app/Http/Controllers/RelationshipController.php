<?php

namespace Modules\MemberPanel\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserRelationship;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Modules\Notifications\App\Services\InAppNotificationService;

/**
 * Vínculos familiares no painel do membro.
 * O membro pode solicitar vínculos (enviando convite ao outro). O convidado (related_user) aceita ou recusa em /painel/vinculos.
 */
class RelationshipController extends Controller
{
    public function __construct(
        protected InAppNotificationService $inAppNotificationService
    ) {}

    /**
     * Lista convites de parentesco pendentes para o usuário logado (quando alguém te marcou como familiar).
     */
    public function pending(): View
    {
        $user = Auth::user();
        $invites = UserRelationship::with(['user', 'invitedBy'])
            ->where('related_user_id', $user->id)
            ->pending()
            ->orderBy('created_at', 'desc')
            ->get();

        return view('memberpanel::relationships.pending', compact('invites'));
    }

    /**
     * Aceitar convite de parentesco (apenas o related_user).
     */
    public function accept(Request $request, UserRelationship $user_relationship): RedirectResponse
    {
        $this->authorizeAsRelatedUser($user_relationship);

        if ($user_relationship->status !== UserRelationship::STATUS_PENDING) {
            return redirect()->route('memberpanel.relationships.pending')
                ->with('warning', 'Este convite já foi respondido.');
        }

        $user_relationship->update(['status' => UserRelationship::STATUS_ACCEPTED]);

        if (class_exists(\Modules\ChurchCouncil\App\Services\CouncilAuditService::class)) {
            app(\Modules\ChurchCouncil\App\Services\CouncilAuditService::class)->log('family_relationship_accepted', $user_relationship, [
                'user_id' => $user_relationship->user_id,
                'related_user_id' => $user_relationship->related_user_id,
            ]);
        }

        $inviter = $user_relationship->user;
        if ($inviter && class_exists(\Modules\Notifications\App\Services\InAppNotificationService::class)) {
            app(\Modules\Notifications\App\Services\InAppNotificationService::class)->sendToUser($inviter, 'Vínculo familiar aceito', Auth::user()->name . ' aceitou o vínculo de ' . $user_relationship->relationship_type_label . '.', [
                'type' => 'success',
                'action_url' => route('memberpanel.profile.show'),
                'action_text' => 'Ver perfil',
            ]);
        }

        return redirect()->route('memberpanel.relationships.pending')
            ->with('success', 'Vínculo familiar aceito com sucesso.');
    }

    /**
     * Recusar convite de parentesco (apenas o related_user).
     */
    public function reject(Request $request, UserRelationship $user_relationship): RedirectResponse
    {
        $this->authorizeAsRelatedUser($user_relationship);

        if ($user_relationship->status !== UserRelationship::STATUS_PENDING) {
            return redirect()->route('memberpanel.relationships.pending')
                ->with('warning', 'Este convite já foi respondido.');
        }

        $user_relationship->update(['status' => UserRelationship::STATUS_REJECTED]);

        return redirect()->route('memberpanel.relationships.pending')
            ->with('success', 'Convite de parentesco recusado.');
    }

    /**
     * Formulário para solicitar um novo vínculo familiar (membro informa parentesco e CPF do outro; o outro recebe convite).
     */
    public function create(): View
    {
        return view('memberpanel::relationships.create');
    }

    /**
     * Salva a solicitação de vínculo: se for membro (CPF), cria pendente e notifica; senão cria apenas com nome (aceito).
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'relationship_type' => 'required|in:' . implode(',', array_keys(UserRelationship::relationshipTypeLabels())),
            'related_user_id' => 'nullable|integer|exists:users,id',
            'related_name' => 'nullable|string|max:255',
        ], [
            'relationship_type.required' => 'Selecione o tipo de parentesco.',
            'relationship_type.in' => 'Tipo de parentesco inválido.',
        ]);

        $me = Auth::user();
        $relatedUserId = isset($validated['related_user_id']) && $validated['related_user_id'] ? (int) $validated['related_user_id'] : null;
        $relatedName = isset($validated['related_name']) ? trim((string) $validated['related_name']) : null;

        if (! $relatedUserId && ! $relatedName) {
            return redirect()->back()->withInput()->withErrors(['related_user_id' => 'Informe o CPF do membro e clique em Buscar, ou o nome da pessoa (se não for membro).']);
        }

        if ($relatedUserId && $relatedUserId === $me->id) {
            return redirect()->back()->withInput()->withErrors(['related_user_id' => 'Você não pode se vincular a si mesmo.']);
        }

        $existing = $me->relationships()
            ->where(function ($q) use ($relatedUserId, $relatedName) {
                if ($relatedUserId) {
                    $q->where('related_user_id', $relatedUserId);
                } else {
                    $q->whereNull('related_user_id')->where('related_name', $relatedName);
                }
            })
            ->first();

        if ($existing) {
            $msg = $existing->status === UserRelationship::STATUS_PENDING
                ? 'Já existe um convite pendente para esta pessoa.'
                : 'Este vínculo já está cadastrado.';
            return redirect()->back()->withInput()->withErrors(['related_user_id' => $msg]);
        }

        $status = $relatedUserId ? UserRelationship::STATUS_PENDING : UserRelationship::STATUS_ACCEPTED;
        $rel = $me->relationships()->create([
            'related_user_id' => $relatedUserId ?: null,
            'related_name' => $relatedName ?: null,
            'relationship_type' => $validated['relationship_type'],
            'status' => $status,
            'invited_by' => $me->id,
        ]);

        if ($status === UserRelationship::STATUS_PENDING && $relatedUserId) {
            $relatedUser = User::find($relatedUserId);
            if ($relatedUser) {
                $this->inAppNotificationService->sendToUser($relatedUser, 'Convite de parentesco', "{$me->name} te marcou como {$rel->relationship_type_label}. Aceite ou recuse em Vínculos.", [
                    'type' => 'info',
                    'action_url' => route('memberpanel.relationships.pending'),
                    'action_text' => 'Ver e responder',
                ]);
            }
        }

        $message = $status === UserRelationship::STATUS_PENDING
            ? 'Convite enviado! A pessoa receberá uma notificação e poderá aceitar ou recusar em "Vínculos" no painel.'
            : 'Vínculo adicionado (pessoa não membro).';

        return redirect()->route('memberpanel.relationships.pending')->with('success', $message);
    }

    /**
     * Busca um membro por CPF (para o formulário de solicitar vínculo). Exclui o próprio usuário.
     */
    public function searchMemberByCpf(Request $request): JsonResponse
    {
        $cpf = $request->input('cpf', '');
        $cpf = preg_replace('/\D/', '', $cpf);
        if (strlen($cpf) !== 11) {
            return response()->json(['data' => null, 'message' => 'Informe um CPF válido com 11 dígitos.']);
        }

        $me = Auth::id();
        $user = User::query()
            ->where('id', '!=', $me)
            ->where(function ($q) use ($cpf) {
                $q->where('cpf', $cpf)
                    ->orWhereRaw("REPLACE(REPLACE(REPLACE(REPLACE(COALESCE(cpf,''),'.',''),'-',''),' ',''),'/','') = ?", [$cpf]);
            })
            ->first(['id', 'name', 'first_name', 'last_name', 'email', 'cpf', 'photo']);

        if (! $user) {
            return response()->json(['data' => null, 'message' => 'Nenhum membro encontrado com este CPF.']);
        }

        return response()->json([
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'cpf' => $user->cpf,
                'photo' => $user->photo ? Storage::url($user->photo) : null,
            ],
        ]);
    }

    private function authorizeAsRelatedUser(UserRelationship $user_relationship): void
    {
        $user = Auth::user();
        $isRelatedUser = $user_relationship->related_user_id && $user_relationship->related_user_id === $user->id;

        if (! $isRelatedUser) {
            abort(403, 'Você não pode aceitar ou recusar este convite.');
        }
    }
}
