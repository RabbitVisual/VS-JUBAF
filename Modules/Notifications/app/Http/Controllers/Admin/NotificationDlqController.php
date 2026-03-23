<?php

namespace Modules\Notifications\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Notifications\App\Jobs\SendNotificationEmailJob;
use Modules\Notifications\App\Jobs\SendNotificationSmsJob;
use Modules\Notifications\App\Jobs\SendNotificationWebPushJob;
use Modules\Notifications\App\Models\NotificationFailedDelivery;

class NotificationDlqController extends Controller
{
    public function index(Request $request)
    {
        $query = NotificationFailedDelivery::with(['user', 'notification'])->latest();

        if ($request->filled('channel')) {
            $query->where('channel', $request->channel);
        }

        $failed = $query->paginate(20)->withQueryString();

        return view('notifications::admin.control.dlq', compact('failed'));
    }

    public function retry(NotificationFailedDelivery $failed)
    {
        $user = $failed->user;
        $notification = $failed->notification;

        if (! $user || ! $notification) {
            return redirect()->route('admin.notifications.dlq.index')
                ->with('error', 'Registro inválido ou usuário/notificação removidos.');
        }

        $notification->refresh();

        match ($failed->channel) {
            'email' => SendNotificationEmailJob::dispatch(
                $user,
                $notification,
                $notification->title,
                $notification->message
            ),
            'webpush' => SendNotificationWebPushJob::dispatch(
                $user,
                $notification,
                $notification->title,
                $notification->message,
                $notification->action_url
            ),
            'sms' => SendNotificationSmsJob::dispatch($user, $notification, $notification->message),
            default => null,
        };

        $failed->update(['retry_pending' => true, 'last_attempt_at' => now()]);

        return redirect()->route('admin.notifications.dlq.index')
            ->with('success', 'Reenvio enfileirado. Acompanhe o resultado na fila.');
    }
}
