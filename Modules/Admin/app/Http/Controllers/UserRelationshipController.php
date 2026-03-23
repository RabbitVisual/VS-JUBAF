<?php

namespace Modules\Admin\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\UserRelationship;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\ChurchCouncil\App\Services\CouncilAuditService;
use Modules\Notifications\App\Services\InAppNotificationService;

class UserRelationshipController extends Controller
{
    public function __construct(
        protected InAppNotificationService $inAppNotificationService
    ) {}

    /**
     * Aceitar convite de parentesco. Apenas o related_user ou admin pode aceitar.
     */
    public function accept(Request $request, UserRelationship $user_relationship): RedirectResponse
    {
        $this->authorizeAcceptReject($user_relationship);

        if ($user_relationship->status !== UserRelationship::STATUS_PENDING) {
            return back()->with('warning', 'Este convite já foi respondido.');
        }

        $user_relationship->update(['status' => UserRelationship::STATUS_ACCEPTED]);

        if (class_exists(CouncilAuditService::class)) {
            app(CouncilAuditService::class)->log('family_relationship_accepted', $user_relationship, [
                'user_id' => $user_relationship->user_id,
                'related_user_id' => $user_relationship->related_user_id,
            ]);
        }

        $inviter = $user_relationship->user;
        if ($inviter && class_exists(InAppNotificationService::class)) {
            $acceptorName = $user_relationship->related_user_id
                ? $user_relationship->relatedUser?->name
                : Auth::user()->name;
            $this->inAppNotificationService->sendToUser($inviter, 'Vínculo familiar aceito', "{$acceptorName} aceitou o vínculo de {$user_relationship->relationship_type_label}.", [
                'type' => 'success',
                'action_url' => route('admin.users.show', $inviter),
                'action_text' => 'Ver perfil',
            ]);
        }

        return back()->with('success', 'Vínculo familiar aceito com sucesso.');
    }

    /**
     * Recusar convite de parentesco.
     */
    public function reject(Request $request, UserRelationship $user_relationship): RedirectResponse
    {
        $this->authorizeAcceptReject($user_relationship);

        if ($user_relationship->status !== UserRelationship::STATUS_PENDING) {
            return back()->with('warning', 'Este convite já foi respondido.');
        }

        $user_relationship->update(['status' => UserRelationship::STATUS_REJECTED]);

        return back()->with('success', 'Convite de parentesco recusado.');
    }

    private function authorizeAcceptReject(UserRelationship $user_relationship): void
    {
        $user = Auth::user();
        $isAdmin = $user->isAdmin() ?? false;
        $isRelatedUser = $user_relationship->related_user_id && $user_relationship->related_user_id === $user->id;

        if (! $isAdmin && ! $isRelatedUser) {
            abort(403, 'Você não pode aceitar ou recusar este convite.');
        }
    }
}
