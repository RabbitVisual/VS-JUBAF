<?php

namespace Modules\Intercessor\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Modules\Intercessor\App\Models\PrayerRequest;
use Modules\Intercessor\App\Models\PrayerInteraction;
use Illuminate\Http\Request;
use Modules\Notifications\App\Services\InAppNotificationService;
use Modules\Notifications\App\Services\NotificationService;
use Modules\Intercessor\App\Notifications\RequestApprovedNotification;

class IntercessorModerationController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService,
        protected InAppNotificationService $inAppNotificationService
    ) {}

    public function index()
    {
        $pendingRequests = PrayerRequest::where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->get();

        $pendingCount = $pendingRequests->count();
        $approvedToday = PrayerRequest::where('status', 'active')
            ->whereDate('updated_at', today())
            ->count();

        return view('intercessor::admin.moderation.index', compact('pendingRequests', 'pendingCount', 'approvedToday'));
    }

    public function show(PrayerRequest $request)
    {
        $comments = $request->interactions()
            ->where('type', 'comment')
            ->whereNull('parent_id')
            ->with(['user', 'replies.user'])
            ->latest()
            ->paginate(15);

        return view('intercessor::admin.moderation.show', compact('request', 'comments'));
    }

    public function approve(PrayerRequest $request)
    {
        $request->update(['status' => 'active']);

        if (class_exists(RequestApprovedNotification::class)) {
            $this->notificationService->notifyUser($request->user, new RequestApprovedNotification($request));
        }

        $this->inAppNotificationService->sendToUser($request->user, 'Pedido de oração aprovado', 'Seu pedido de oração foi aprovado e está disponível na sala de oração.', [
            'type' => 'success',
            'action_url' => route('member.intercessor.room.index'),
            'action_text' => 'Ver sala de oração',
        ]);

        return redirect()->route('admin.intercessor.moderation.index')
            ->with('success', 'Pedido aprovado com sucesso!');
    }

    public function reject(PrayerRequest $request)
    {
        $request->update(['status' => 'archived']);
        return redirect()->route('admin.intercessor.moderation.index')
            ->with('success', 'Pedido rejeitado.');
    }

    public function destroy(PrayerRequest $request)
    {
        $request->delete();

        return redirect()
            ->route('admin.intercessor.moderation.index')
            ->with('success', 'Pedido e todas as interações foram removidos.');
    }

    public function storeComment(Request $request, PrayerRequest $prayerRequest)
    {
        $request->validate(['body' => 'required|string']);

        $prayerRequest->interactions()->create([
            'user_id' => auth()->id(),
            'type' => 'comment',
            'body' => $request->body,
        ]);

        return back()->with('success', 'Comentário adicionado.');
    }

    public function destroyComment(PrayerRequest $request, PrayerInteraction $interaction)
    {
        $interaction->delete();
        return back()->with('success', 'Comentário removido.');
    }

    public function indexTestimonies()
    {
        $pendingTestimonies = PrayerRequest::where('testimony_status', 'pending')->paginate(10);
        return view('intercessor::admin.moderation.testimonies', compact('pendingTestimonies'));
    }

    public function approveTestimony(PrayerRequest $request)
    {
        $request->update(['testimony_status' => 'approved', 'is_testimony_public' => true]);
        return back()->with('success', 'Testemunho aprovado.');
    }

    public function rejectTestimony(PrayerRequest $request)
    {
        $request->update(['testimony_status' => 'rejected']);
        return back()->with('success', 'Testemunho rejeitado.');
    }
}
