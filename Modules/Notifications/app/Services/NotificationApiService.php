<?php

namespace Modules\Notifications\App\Services;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Modules\Notifications\App\Models\UserNotification;

/**
 * Serviço central da API de notificações (v1).
 * Toda a lógica de listagem, contagem e ações do usuário autenticado
 * passa por aqui, alimentando a API e garantindo consistência.
 */
class NotificationApiService
{
    /**
     * Lista notificações do usuário (paginado).
     *
     * @return LengthAwarePaginator<UserNotification>
     */
    public function getMyNotifications(User $user, int $perPage = 15): LengthAwarePaginator
    {
        return UserNotification::where('user_id', $user->id)
            ->with('notification')
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Retorna a quantidade de notificações não lidas do usuário.
     */
    public function getUnreadCount(User $user): int
    {
        return UserNotification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();
    }

    /**
     * Marca uma notificação como lida. Retorna true se pertence ao usuário e foi atualizada.
     */
    public function markAsRead(User $user, int $userNotificationId): bool
    {
        $userNotification = UserNotification::where('user_id', $user->id)
            ->where('id', $userNotificationId)
            ->first();

        if (! $userNotification) {
            return false;
        }

        $userNotification->markAsRead();

        return true;
    }

    /**
     * Marca todas as notificações do usuário como lidas. Retorna quantidade atualizada.
     */
    public function markAllAsRead(User $user): int
    {
        return UserNotification::where('user_id', $user->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    /**
     * Exclui todas as notificações do usuário. Retorna quantidade excluída.
     */
    public function clearAll(User $user): int
    {
        return UserNotification::where('user_id', $user->id)->delete();
    }

    /**
     * Exclui uma notificação do usuário. Retorna true se pertence ao usuário e foi excluída.
     */
    public function destroy(User $user, int $userNotificationId): bool
    {
        $userNotification = UserNotification::where('user_id', $user->id)
            ->where('id', $userNotificationId)
            ->first();

        if (! $userNotification) {
            return false;
        }

        $userNotification->delete();

        return true;
    }

    /**
     * Resolve contagem de não lidas + data da mais recente (para smart polling).
     * Retorna last_updated_at para o cliente só recarregar a lista se houver mudança.
     */
    public function getUnreadCountWithLastUpdated(User $user): array
    {
        $base = UserNotification::where('user_id', $user->id);
        $count = (clone $base)->where('is_read', false)->count();
        $last = (clone $base)->latest('updated_at')->value('updated_at');

        return [
            'count' => $count,
            'last_updated_at' => $last?->toIso8601String(),
        ];
    }

    /**
     * Lista recentes (para dropdown) — limitado, sem paginação pesada.
     *
     * @return \Illuminate\Support\Collection<int, UserNotification>
     */
    public function getRecentForDropdown(User $user, int $limit = 5): \Illuminate\Support\Collection
    {
        return UserNotification::where('user_id', $user->id)
            ->with('notification')
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Resolve contagem de não lidas com tratamento de erro (para uso em API).
     */
    public function getUnreadCountOrFail(User $user): array
    {
        try {
            $data = $this->getUnreadCountWithLastUpdated($user);

            return ['count' => $data['count'], 'last_updated_at' => $data['last_updated_at'], 'error' => null];
        } catch (\Throwable $e) {
            Log::error('NotificationApiService::getUnreadCount: '.$e->getMessage());

            return ['count' => 0, 'last_updated_at' => null, 'error' => $e->getMessage()];
        }
    }
}
