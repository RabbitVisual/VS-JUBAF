<?php

namespace Modules\Admin\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Modules\Ministries\App\Models\Ministry;
use Modules\Notifications\App\Models\SystemNotification;
use Modules\Notifications\App\Models\UserNotification;

class NotificationController extends Controller
{
    /**
     * Lista todas as notificações (com filtros opcionais).
     */
    public function index(Request $request)
    {
        $query = SystemNotification::with('creator')->latest();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $notifications = $query->paginate(15)->withQueryString();

        return view('notifications::admin.notifications.index', compact('notifications'));
    }

    /**
     * Limpa todas as notificações recebidas (caixa do admin atual).
     */
    public function clearMyInbox()
    {
        $deleted = UserNotification::where('user_id', auth()->id())->delete();

        return redirect()->route('admin.notifications.index')
            ->with('success', $deleted > 0
                ? 'Suas notificações foram excluídas.'
                : 'Nenhuma notificação para excluir.');
    }

    /**
     * Mostra formulário de criação
     */
    public function create()
    {
        $users = User::where('is_active', true)->orderBy('name')->get();
        $roles = \App\Models\Role::all();
        $ministries = Ministry::where('is_active', true)->orderBy('name')->get();

        return view('notifications::admin.notifications.create', compact('users', 'roles', 'ministries'));
    }

    /**
     * Cria nova notificação
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:info,success,warning,error,achievement',
            'priority' => 'required|in:low,normal,high,urgent',
            'target_users' => 'nullable|array',
            'target_users.*' => 'exists:users,id',
            'target_roles' => 'nullable|array',
            'target_roles.*' => 'exists:roles,slug',
            'target_ministries' => 'nullable|array',
            'target_ministries.*' => 'exists:ministries,id',
            'action_url' => 'nullable|string|max:500',
            'action_text' => 'nullable|string|max:100',
            'scheduled_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:scheduled_at',
        ]);

        $validated['created_by'] = auth()->id();

        // Se não tem targets, notifica todos
        if (empty($validated['target_users']) &&
            empty($validated['target_roles']) &&
            empty($validated['target_ministries'])) {
            $validated['target_users'] = null;
            $validated['target_roles'] = null;
            $validated['target_ministries'] = null;
        }

        $notification = SystemNotification::create($validated);

        // Distribui imediatamente se não agendada ou se já passou o horário
        if (! $notification->scheduled_at || $notification->scheduled_at->isPast()) {
            $this->distributeNotification($notification);
        }

        return redirect()->route('admin.notifications.index')
            ->with('success', 'Notificação criada e enviada com sucesso!');
    }

    /**
     * Mostra detalhes da notificação
     */
    public function show(SystemNotification $notification)
    {
        $notification->load(['creator', 'users']);
        $readCount = $notification->users()->wherePivot('is_read', true)->count();
        $totalCount = $notification->users()->count();

        return view('notifications::admin.notifications.show', compact('notification', 'readCount', 'totalCount'));
    }

    /**
     * Remove notificação
     */
    public function destroy(SystemNotification $notification)
    {
        $notification->delete();

        return redirect()->route('admin.notifications.index')
            ->with('success', 'Notificação removida com sucesso!');
    }

    /**
     * Distribui notificação para usuários
     */
    private function distributeNotification(SystemNotification $notification)
    {
        $users = $this->getTargetUsers($notification);

        foreach ($users as $user) {
            // Verifica se já recebeu
            if (! UserNotification::where('user_id', $user->id)->where('notification_id', $notification->id)->exists()) {
                UserNotification::create([
                    'user_id' => $user->id,
                    'notification_id' => $notification->id,
                    'is_read' => false,
                ]);

                // Dispara evento para notificação em tempo real
                event(new \Modules\Notifications\App\Events\NotificationCreated($notification, $user));
            }
        }
    }

    /**
     * Obtém usuários alvo da notificação
     */
    private function getTargetUsers(SystemNotification $notification): \Illuminate\Database\Eloquent\Collection
    {
        $query = User::where('is_active', true);

        // Se tem usuários específicos
        if (! empty($notification->target_users)) {
            return $query->whereIn('id', $notification->target_users)->get();
        }

        // Se tem roles específicas
        if (! empty($notification->target_roles)) {
            $query->whereHas('role', function ($q) use ($notification) {
                $q->whereIn('slug', $notification->target_roles);
            });
        }

        // Se tem ministérios específicos
        if (! empty($notification->target_ministries)) {
            $query->whereHas('ministries', function ($q) use ($notification) {
                $q->whereIn('ministries.id', $notification->target_ministries)
                    ->wherePivot('status', 'active');
            });
        }

        return $query->get();
    }
}
