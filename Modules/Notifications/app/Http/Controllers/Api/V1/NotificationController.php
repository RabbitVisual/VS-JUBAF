<?php

namespace Modules\Notifications\App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Notifications\App\Http\Resources\UserNotificationResource;
use Modules\Notifications\App\Services\NotificationApiService;

/**
 * API central de notificações (v1).
 * Padrão alinhado à Bible API: respostas com { data } para alimentar
 * todo o sistema (painel admin, painel membro, polling, SPA).
 */
class NotificationController extends Controller
{
    public function __construct(
        private NotificationApiService $api
    ) {}

    /**
     * GET /api/v1/notifications
     * Lista notificações do usuário autenticado (paginado).
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['message' => 'Não autenticado.'], 401);
        }

        $perPage = (int) $request->input('per_page', 15);
        $perPage = min(max($perPage, 1), 50);

        $paginator = $this->api->getMyNotifications($user, $perPage);

        return UserNotificationResource::collection($paginator->items())
            ->additional([
                'meta' => [
                    'current_page' => $paginator->currentPage(),
                    'last_page' => $paginator->lastPage(),
                    'per_page' => $paginator->perPage(),
                    'total' => $paginator->total(),
                ],
            ])
            ->response();
    }

    /**
     * GET /api/v1/notifications/unread-count
     * Retorna a quantidade de notificações não lidas.
     */
    public function unreadCount(): JsonResponse
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['message' => 'Não autenticado.'], 401);
        }

        $result = $this->api->getUnreadCountOrFail($user);
        if ($result['error'] !== null) {
            return response()->json([
                'data' => ['count' => 0, 'last_updated_at' => null],
                'message' => 'Erro ao obter contagem.',
            ], 500);
        }

        return response()->json([
            'data' => [
                'count' => $result['count'],
                'last_updated_at' => $result['last_updated_at'],
            ],
        ]);
    }

    /**
     * POST /api/v1/notifications/{userNotification}/read
     * Marca uma notificação como lida.
     */
    public function markAsRead(int $userNotification): JsonResponse
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['message' => 'Não autenticado.'], 401);
        }

        $ok = $this->api->markAsRead($user, $userNotification);
        if (! $ok) {
            return response()->json(['message' => 'Notificação não encontrada ou não autorizado.'], 403);
        }

        return response()->json(['data' => ['success' => true]]);
    }

    /**
     * POST /api/v1/notifications/read-all
     * Marca todas como lidas.
     */
    public function markAllAsRead(): JsonResponse
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['message' => 'Não autenticado.'], 401);
        }

        $this->api->markAllAsRead($user);

        return response()->json(['data' => ['success' => true]]);
    }

    /**
     * DELETE /api/v1/notifications/clear-all
     * Exclui todas as notificações do usuário.
     */
    public function clearAll(): JsonResponse
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['message' => 'Não autenticado.'], 401);
        }

        $deleted = $this->api->clearAll($user);

        return response()->json(['data' => ['deleted' => $deleted]]);
    }

    /**
     * DELETE /api/v1/notifications/{userNotification}
     * Exclui uma notificação do usuário.
     */
    public function destroy(int $userNotification): JsonResponse
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['message' => 'Não autenticado.'], 401);
        }

        $ok = $this->api->destroy($user, $userNotification);
        if (! $ok) {
            return response()->json(['message' => 'Notificação não encontrada ou não autorizado.'], 403);
        }

        return response()->json(['data' => ['success' => true]]);
    }
}
