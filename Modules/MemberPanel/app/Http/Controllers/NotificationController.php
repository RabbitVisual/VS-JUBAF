<?php

namespace Modules\MemberPanel\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Notifications\App\Models\UserNotification;

class NotificationController extends Controller
{
    /**
     * Lista notificações do usuário
     */
    public function index()
    {
        $user = Auth::user();

        $notifications = UserNotification::where('user_id', $user->id)
            ->with('notification')
            ->latest()
            ->paginate(15);

        $unreadCount = UserNotification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();

        return view('notifications::memberpanel.notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * Marca notificação como lida.
     * Retorna JSON para chamadas AJAX (sino/API); redireciona com flash para navegação normal.
     */
    public function markAsRead(UserNotification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        $notification->markAsRead();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Notificação marcada como lida.');
    }

    /**
     * Marca todas como lidas
     */
    public function markAllAsRead()
    {
        UserNotification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return back()->with('success', 'Todas as notificações foram marcadas como lidas.');
    }

    /**
     * Remove todas as notificações do usuário (evitar acúmulo).
     */
    public function clearAll()
    {
        $deleted = UserNotification::where('user_id', Auth::id())->delete();

        return back()->with('success', $deleted > 0
            ? 'Todas as notificações foram excluídas.'
            : 'Nenhuma notificação para excluir.');
    }

    /**
     * Remove notificação
     */
    public function destroy(UserNotification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        $notification->delete();

        return back()->with('success', 'Notificação removida.');
    }
}
