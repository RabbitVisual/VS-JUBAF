<?php

namespace Modules\PastoralPanel\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OracaoController extends Controller
{
    /**
     * Lista de pedidos de oração (layout pastoral).
     */
    public function index(): View
    {
        $pendingRequests = collect();
        if (class_exists(\Modules\Intercessor\App\Models\PrayerRequest::class)) {
            $pendingRequests = \Modules\Intercessor\App\Models\PrayerRequest::with('user')
                ->where('status', 'pending')
                ->orderBy('created_at', 'asc')
                ->paginate(15);
        }

        return view('pastoralpanel::oracao.index', compact('pendingRequests'));
    }

    /**
     * Detalhe de um pedido (layout pastoral).
     */
    public function show(int $request): View
    {
        $prayerRequest = \Modules\Intercessor\App\Models\PrayerRequest::with('user')->findOrFail($request);

        return view('pastoralpanel::oracao.show', compact('prayerRequest'));
    }

    /**
     * Marcar pedido como orado (aprovar e disponibilizar na sala de oração).
     * Redireciona de volta ao dashboard ou à lista.
     */
    public function markAsPrayed(Request $req, int $request): RedirectResponse
    {
        $prayerRequest = \Modules\Intercessor\App\Models\PrayerRequest::findOrFail($request);
        $prayerRequest->update(['status' => 'active']);

        if (class_exists(\Modules\Intercessor\App\Notifications\RequestApprovedNotification::class)
            && class_exists(\Modules\Notifications\App\Services\NotificationService::class)) {
            try {
                app(\Modules\Notifications\App\Services\NotificationService::class)
                    ->notifyUser($prayerRequest->user, new \Modules\Intercessor\App\Notifications\RequestApprovedNotification($prayerRequest));
            } catch (\Throwable $e) {
                report($e);
            }
        }
        if (class_exists(\Modules\Notifications\App\Services\InAppNotificationService::class)) {
            try {
                app(\Modules\Notifications\App\Services\InAppNotificationService::class)->sendToUser(
                    $prayerRequest->user,
                    'Pedido de oração aprovado',
                    'Seu pedido de oração foi aprovado e está disponível na sala de oração.',
                    ['type' => 'success', 'action_url' => route('member.intercessor.room.index'), 'action_text' => 'Ver sala de oração']
                );
            } catch (\Throwable $e) {
                report($e);
            }
        }

        $back = $req->input('from', $req->query('from', 'dashboard'));
        if ($back === 'list') {
            return redirect()->route('pastor.oracao.index')->with('success', 'Pedido marcado como orado.');
        }

        return redirect()->route('pastor.dashboard')->with('success', 'Pedido marcado como orado.');
    }
}
