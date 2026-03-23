<?php

namespace Modules\Intercessor\App\Http\Controllers\MemberPanel;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Modules\Intercessor\App\Models\PrayerCategory;
use Modules\Intercessor\App\Models\PrayerRequest;
use Modules\Intercessor\App\Notifications\NewPrayerRequestNotification;

class IntercessorController extends Controller
{
    /**
     * Display a listing of my requests.
     */
    public function index()
    {
        $myRequests = PrayerRequest::where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('intercessor::memberpanel.requests.index', compact('myRequests'));
    }

    /**
     * Show the form for creating a new request.
     */
    public function create()
    {
        if (! \Modules\Intercessor\App\Services\IntercessorSettings::get('allow_requests')) {
            return redirect()->route('member.intercessor.requests.index')->with('error', 'Novos pedidos de oração estão temporariamente desabilitados.');
        }

        $categories = PrayerCategory::where('is_active', true)->get();

        return view('intercessor::memberpanel.requests.create', compact('categories'));
    }

    /**
     * Store a newly created request in storage.
     */
    public function store(Request $request)
    {
        if (! \Modules\Intercessor\App\Services\IntercessorSettings::get('allow_requests')) {
            return redirect()->route('member.intercessor.requests.index')->with('error', 'Novos pedidos estão desabilitados.');
        }

        // Check Max Requests Limit
        $limit = \Modules\Intercessor\App\Services\IntercessorSettings::get('max_requests_per_user');
        if ($limit > 0) {
            $count = PrayerRequest::where('user_id', Auth::id())
                ->whereMonth('created_at', now()->month)
                ->count();

            if ($count >= $limit) {
                return back()->with('error', "Você atingiu o limite de {$limit} pedidos por mês.");
            }
        }

        $validated = $request->validate([
            'category_id' => 'required|exists:prayer_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'privacy_level' => 'required|in:public,members_only,intercessors_only,pastoral_only',
            'urgency_level' => 'required|in:normal,high,critical',
            'is_anonymous' => 'boolean',
            'show_identity' => 'boolean',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['is_anonymous'] = $request->boolean('is_anonymous');
        // If show_identity is not present (e.g. unchecked in some forms), handle defaults.
        // Assuming the new UI sends it. Default to true if not provided?
        // Actually, if using checkbox, unchecked = not sent = false.
        // But the field default in DB is true.
        // I will use $request->boolean('show_identity') which returns false if missing.
        // Wait, if the user didn't touch it, they might expect default.
        // But in the UI, I'll make sure it's present.
        $validated['show_identity'] = $request->boolean('show_identity');

        // Sync anonymous if show_identity is false
        if (!$validated['show_identity']) {
            $validated['is_anonymous'] = true;
        } else {
             $validated['is_anonymous'] = false;
        }

        // Status logic based on Require Approval setting
        $requireApproval = \Modules\Intercessor\App\Services\IntercessorSettings::get('require_approval');
        $validated['status'] = $requireApproval ? 'pending' : 'active';

        $prayerRequest = PrayerRequest::create($validated);

        // Notify admins/intercessors if urgent (Legacy Service)
        app(\Modules\Intercessor\App\Services\PrayerNotificationService::class)->notifyUrgentRequest($prayerRequest);

        // New Scoped Notification System
        $this->dispatchScopedNotifications($prayerRequest);

        return redirect()->route('member.intercessor.requests.index')->with('success', 'Pedido de oração criado com sucesso! Aguardando aprovação.');
    }

    /**
     * Dispatch notifications based on privacy scope.
     */
    protected function dispatchScopedNotifications(PrayerRequest $request)
    {
        $recipients = collect();

        if ($request->privacy_level === 'pastoral_only') {
            // Notify Pastors
            $recipients = User::whereHas('role', fn($q) => $q->where('slug', 'pastor'))->get();
        } elseif ($request->privacy_level === 'intercessors_only') {
            // Notify Prayer Team (Intercessors)
            $recipients = User::whereHas('role', fn($q) => $q->whereIn('slug', ['prayer_team', 'intercessor']))->get();
        }

        // Note: For Public/Members, we might want to notify Intercessors as well,
        // but the requirement specified strict rules for Pastoral/Intercessor scopes.
        // Adding Intercessors to Public/Members notifications implies a broader scope logic
        // which matches the goal of a Prayer Wall.
        // However, I will adhere to the explicit instructions for now.

        if ($recipients->isNotEmpty()) {
            Notification::send($recipients, new NewPrayerRequestNotification($request));
        }
    }

    /**
     * Show the form for editing the specified request.
     */
    public function edit(PrayerRequest $request)
    {
        if ($request->user_id !== Auth::id()) {
            abort(403);
        }

        if (in_array($request->status, ['answered', 'archived'])) {
            return redirect()->route('member.intercessor.requests.index')
                ->with('error', 'Pedidos concluídos ou arquivados não podem ser editados.');
        }

        $categories = PrayerCategory::where('is_active', true)->get();

        return view('intercessor::memberpanel.requests.edit', compact('request', 'categories'));
    }

    /**
     * Update the specified request in storage.
     */
    public function update(Request $request, $id)
    {
        $requestModel = PrayerRequest::findOrFail($id);

        if ($requestModel->user_id !== Auth::id()) {
            abort(403);
        }

        if (in_array($requestModel->status, ['answered', 'archived'])) {
            return redirect()->route('member.intercessor.requests.index')
                ->with('error', 'Pedidos concluídos ou arquivados não podem ser editados.');
        }

        $validated = $request->validate([
            'category_id' => 'required|exists:prayer_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'privacy_level' => 'required|in:public,members_only,intercessors_only,pastoral_only',
            'urgency_level' => 'required|in:normal,high,critical',
            'is_anonymous' => 'boolean',
            'show_identity' => 'boolean',
        ]);

        $validated['is_anonymous'] = $request->boolean('is_anonymous');
        $validated['show_identity'] = $request->boolean('show_identity');

        if (!$validated['show_identity']) {
            $validated['is_anonymous'] = true;
        } else {
             $validated['is_anonymous'] = false;
        }

        $requestModel->update($validated);

        return redirect()->route('member.intercessor.requests.index')->with('success', 'Pedido atualizado com sucesso!');
    }

    /**
     * Remove the specified request from storage.
     */
    public function destroy(PrayerRequest $request)
    {
        $user = Auth::user();

        $isOwner = $request->user_id === $user->id;
        $isAdmin = $user->hasRole('admin');

        if (! $isOwner && ! $isAdmin) {
            return redirect()
                ->route('member.intercessor.requests.index')
                ->with('error', 'Você não tem permissão para excluir este pedido.');
        }

        if (in_array($request->status, ['answered', 'archived'])) {
            return redirect()
                ->route('member.intercessor.requests.index')
                ->with('error', 'Pedidos concluídos ou arquivados não podem ser excluídos.');
        }

        $request->delete();

        return redirect()
            ->route('member.intercessor.requests.index')
            ->with('success', 'Pedido removido com sucesso.');
    }

    /**
     * Show the form for writing a testimony.
     */
    public function testimony(PrayerRequest $request)
    {
        if ($request->user_id !== Auth::id()) {
            abort(403);
        }

        if ($request->status === 'answered') {
            return redirect()->route('member.intercessor.requests.index')->with('info', 'Este pedido já possui um testemunho.');
        }

        return view('intercessor::memberpanel.requests.testimony', compact('request'));
    }

    /**
     * Submit a testimony for a prayer request.
     */
    public function submitTestimony(Request $request, $id)
    {
        $prayerRequest = PrayerRequest::findOrFail($id);

        if ($prayerRequest->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'testimony' => 'nullable|string|min:20',
            'is_testimony_public' => 'required|boolean',
        ]);

        $isPublic = $request->boolean('is_testimony_public');

        if ($isPublic && empty($validated['testimony'])) {
            return back()->with('error', 'Para publicar no mural, você precisa escrever um relato.');
        }

        $prayerRequest->update([
            'testimony' => $validated['testimony'],
            'is_testimony_public' => $isPublic,
            'status' => 'answered',
            'answered_at' => now(),
            'testimony_status' => $isPublic ? 'pending' : 'approved',
        ]);

        $message = $isPublic
            ? 'Testemunho enviado para moderação! Glória a Deus por sua vida.'
            : 'Pedido concluído com sucesso. Deus abençoe sua caminhada!';

        return redirect()->route('member.intercessor.requests.index')->with('success', $message);
    }
}
