<?php

namespace Modules\SocialAction\App\Http\Controllers\MemberPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\SocialAction\App\Models\SocialPrayerRequest;

class MemberPrayerController extends Controller
{
    public function index(): View
    {
        $myRequests = SocialPrayerRequest::where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('socialaction::memberpanel.prayer.index', compact('myRequests'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'request'      => 'required|string|max:2000',
            'is_anonymous' => 'boolean',
        ]);

        SocialPrayerRequest::create([
            'user_id'      => auth()->id(),
            'name'         => $validated['name'],
            'request'      => $validated['request'],
            'is_anonymous' => $request->boolean('is_anonymous'),
            'status'       => 'pending',
        ]);

        return redirect()->route('socialaction.member.prayer.index')
            ->with('success', 'Seu pedido de oração foi enviado. Deus cuida! 🙏');
    }
}
