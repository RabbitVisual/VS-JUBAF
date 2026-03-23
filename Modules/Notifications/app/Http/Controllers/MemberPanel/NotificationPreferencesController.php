<?php

namespace Modules\Notifications\App\Http\Controllers\MemberPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Notifications\App\Models\UserNotificationPreference;

class NotificationPreferencesController extends Controller
{
    protected static array $notificationTypes = [
        'worship_roster' => 'Escalas de louvor publicadas',
        'ebd_lesson' => 'Novas lições da EBD',
        'churchcouncil_minutes' => 'Atas do conselho pendentes de assinatura',
        'sermon_collaboration' => 'Convites para co-autoria de sermões',
        'treasury_approval' => 'Despesas aguardando aprovação (tesouraria)',
        'event_registration' => 'Inscrições em eventos',
        'payment_completed' => 'Pagamentos confirmados',
        'student_leveled_up' => 'Subiu de nível (EBD)',
        'generic' => 'Outras notificações',
    ];

    protected static array $channels = [
        'in_app' => 'No aplicativo (sino)',
        'email' => 'E-mail',
        'webpush' => 'Push no navegador',
        'sms' => 'SMS',
    ];

    public function index()
    {
        $user = Auth::user();
        $preferences = UserNotificationPreference::where('user_id', $user->id)->get()->keyBy('notification_type');

        return view('notifications::memberpanel.preferences.notifications', [
            'notificationTypes' => self::$notificationTypes,
            'channels' => self::$channels,
            'preferences' => $preferences,
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'preferences' => 'nullable|array',
            'preferences.*.channels' => 'nullable|array',
            'preferences.*.channels.*' => 'in:in_app,email,webpush,sms',
            'preferences.*.dnd_from' => 'nullable|date_format:H:i',
            'preferences.*.dnd_to' => 'nullable|date_format:H:i',
        ]);

        $prefsInput = $validated['preferences'] ?? [];

        foreach (array_keys(self::$notificationTypes) as $type) {
            $data = $prefsInput[$type] ?? [];
            $channels = $data['channels'] ?? ['in_app'];
            if (! in_array('in_app', $channels, true)) {
                $channels[] = 'in_app';
            }
            $dndFrom = $data['dnd_from'] ?? null;
            $dndTo = $data['dnd_to'] ?? null;

            UserNotificationPreference::updateOrCreate(
                ['user_id' => $user->id, 'notification_type' => $type],
                [
                    'channels' => $channels,
                    'dnd_from' => $dndFrom ? $dndFrom.':00' : null,
                    'dnd_to' => $dndTo ? $dndTo.':00' : null,
                ]
            );
        }

        return redirect()->route('memberpanel.preferences.notifications.index')
            ->with('success', 'Preferências de notificações salvas.');
    }
}
