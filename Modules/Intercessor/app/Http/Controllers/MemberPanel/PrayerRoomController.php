<?php

namespace Modules\Intercessor\App\Http\Controllers\MemberPanel;

use App\Http\Controllers\Controller;
use Modules\Intercessor\App\Models\PrayerRequest;
use Modules\Intercessor\App\Models\PrayerCommitment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Notifications\App\Services\InAppNotificationService;
use Modules\Notifications\App\Services\NotificationService;
use Modules\Intercessor\App\Notifications\NewCommitmentNotification;
use Modules\Intercessor\App\Events\PrayerCommitmentCreated;
use Illuminate\Support\Facades\Log;

class PrayerRoomController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService,
        protected InAppNotificationService $inAppNotificationService
    ) {}

    public function index(Request $request)
    {
        $user = Auth::user();

        $query = PrayerRequest::active()
            ->with(['user', 'category', 'commitments'])
            ->orderBy('created_at', 'desc');

        // Privacy filter: enforce Baptist-aligned visibility
        $query->where(function ($q) use ($user) {
            $q->where('privacy_level', 'public')
                ->orWhere(function ($q2) {
                    $q2->where('privacy_level', 'members_only');
                })
                ->orWhere(function ($q2) use ($user) {
                    if (! $user) {
                        return;
                    }

                    // Intercessors and prayer team
                    if ($user->role && in_array($user->role->slug, ['intercessor', 'prayer_team'])) {
                        $q2->where('privacy_level', 'intercessors_only');
                    }
                })
                ->orWhere(function ($q2) use ($user) {
                    if (! $user) {
                        return;
                    }

                    // Pastoral and admins
                    if ($user->role && in_array($user->role->slug, ['pastor', 'admin'])) {
                        $q2->where('privacy_level', 'pastoral_only');
                    }
                })
                // Author always sees own requests, regardless of privacy level
                ->orWhere('user_id', optional($user)->id);
        });

        if ($request->has('urgency')) {
            $query->where('urgency_level', $request->urgency);
        }

        if ($request->filter === 'new') {
            $query->whereDate('created_at', '>=', now()->subDays(3));
        }

        $feed = $query->paginate(12);

        return view('intercessor::memberpanel.room.index', compact('feed'));
    }

    public function testimonies()
    {
        $testimonies = PrayerRequest::where('testimony_status', 'approved')
            ->where('is_testimony_public', true)
            ->latest('answered_at')
            ->paginate(12);

        return view('intercessor::memberpanel.room.testimonies', compact('testimonies'));
    }

    public function show(PrayerRequest $request)
    {
        $user = Auth::user();

        // Enforce privacy for direct access
        $canView = false;

        if ($request->privacy_level === 'public') {
            $canView = true;
        } elseif ($request->privacy_level === 'members_only' && $user) {
            $canView = true;
        } elseif ($request->privacy_level === 'intercessors_only' && $user && $user->role && in_array($user->role->slug, ['intercessor', 'prayer_team'])) {
            $canView = true;
        } elseif ($request->privacy_level === 'pastoral_only' && $user && $user->role && in_array($user->role->slug, ['pastor', 'admin'])) {
            $canView = true;
        }

        // Author always can view
        if ($user && $request->user_id === $user->id) {
            $canView = true;
        }

        abort_unless($canView, 403);

        $request->load([
            'interactions' => function($q) {
                $q->with(['user', 'replies.user']);
            },
            'commitments.user'
        ]);

        // Log Access (Jules' Improvement)
        if (class_exists(\Modules\Intercessor\App\Models\PrayerAccessLog::class)) {
            \Modules\Intercessor\App\Models\PrayerAccessLog::create([
                'request_id' => $request->id,
                'user_id' => Auth::id(),
                'accessed_at' => now(),
                'ip_address' => request()->ip(),
            ]);
        }

        return view('intercessor::memberpanel.room.show', compact('request'));
    }

    public function commit(Request $request, PrayerRequest $prayerRequest)
    {
        // Prevent duplicate commitments
        $exists = PrayerCommitment::where('request_id', $prayerRequest->id)
            ->where('user_id', Auth::id())
            ->exists();

        if (!$exists) {
            $commitment = $prayerRequest->commitments()->create([
                'user_id' => Auth::id(),
            ]);

            // Notify via legacy email service (kept for engagement metrics)
            app(\Modules\Intercessor\App\Services\PrayerNotificationService::class)
                ->notifyCommitment($commitment);

            // Dispatch Event for Reverb
            if (class_exists(PrayerCommitmentCreated::class)) {
                broadcast(new PrayerCommitmentCreated($commitment))->toOthers();
            }

            if (class_exists(NewCommitmentNotification::class)) {
                $this->notificationService->notifyUser($prayerRequest->user, new NewCommitmentNotification($prayerRequest, Auth::user()));
            }

            $committerName = Auth::user()->name ?? 'Alguém';
            $this->inAppNotificationService->sendToUser($prayerRequest->user, 'Novo compromisso de oração', "{$committerName} se comprometeu a orar pelo seu pedido.", [
                'type' => 'info',
                'action_url' => route('member.intercessor.room.show', $prayerRequest),
                'action_text' => 'Ver pedido',
            ]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Compromisso de oração registrado!'
            ]);
        }

        return back()->with('success', 'Compromisso de oração registrado!');
    }

    public function finish(Request $request, PrayerRequest $prayerRequest)
    {
        Log::info('Entrou no finish do PrayerRoomController', ['user' => Auth::id(), 'request' => $prayerRequest->id]);
        try {
            // Find the latest commitment for this user and request
            $commitment = PrayerCommitment::where('request_id', $prayerRequest->id)
                ->where('user_id', Auth::id())
                ->latest()
                ->first();

            if ($commitment) {
                $validated = $request->validate([
                    'duration' => 'required|integer|min:1'
                ]);

                $currentDuration = $commitment->duration_seconds ?? 0;
                $commitment->update([
                    'duration_seconds' => $currentDuration + $validated['duration']
                ]);

                return response()->json(['success' => true, 'message' => 'Tempo de oração registrado!']);
            }

            return response()->json(['success' => false, 'message' => 'Compromisso não encontrado.'], 404);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Prayer Room Finish Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erro interno ao finalizar oração: ' . $e->getMessage()], 500);
        }
    }

    public function interact(Request $request, PrayerRequest $prayerRequest)
    {
        $request->validate([
            'body' => 'required|string',
            'parent_id' => 'nullable|exists:prayer_interactions,id',
        ]);

        $userId = Auth::id();
        $isOwner = ($userId === $prayerRequest->user_id);

        if ($isOwner) {
            // Owner MUST reply to someone else
            if (!$request->parent_id) {
                return back()->with('error', 'O autor do pedido só pode responder a comentários de outros intercessores.');
            }

            $parentInteraction = \Modules\Intercessor\App\Models\PrayerInteraction::findOrFail($request->parent_id);

            // Cannot reply to self
            if ($parentInteraction->user_id === $userId) {
                return back()->with('error', 'Você não pode responder ao seu próprio comentário.');
            }

            // Check if already replied to this person in this request
            $targetUserId = $parentInteraction->user_id;
            $alreadyReplied = $prayerRequest->interactions()
                ->where('user_id', $userId)
                ->whereHas('parent', function($q) use ($targetUserId) {
                    $q->where('user_id', $targetUserId);
                })
                ->exists();

            if ($alreadyReplied) {
                return back()->with('error', 'Você já respondeu a este intercessor. Para evitar poluição, apenas uma resposta por pessoa é permitida.');
            }
        }

        $prayerRequest->interactions()->create([
            'user_id' => $userId,
            'parent_id' => $request->parent_id,
            'type' => 'comment',
            'body' => $request->body,
        ]);

        return back()->with('success', 'Mensagem enviada!');
    }
}
