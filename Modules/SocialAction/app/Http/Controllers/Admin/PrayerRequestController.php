<?php

namespace Modules\SocialAction\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\SocialAction\App\Models\SocialPrayerRequest;

class PrayerRequestController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status', 'pending');

        $requests = SocialPrayerRequest::when($status && $status !== 'all', fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(20);

        $counts = [
            'pending'  => SocialPrayerRequest::pending()->count(),
            'prayed'   => SocialPrayerRequest::prayed()->count(),
            'archived' => SocialPrayerRequest::where('status', 'archived')->count(),
        ];

        return view('socialaction::admin.prayer.index', compact('requests', 'counts', 'status'));
    }

    public function markPrayed(int $id): RedirectResponse
    {
        SocialPrayerRequest::findOrFail($id)->update([
            'status'   => 'prayed',
            'prayed_at'=> now(),
        ]);

        return back()->with('success', 'Pedido marcado como orado. 🙏');
    }

    public function archive(int $id): RedirectResponse
    {
        SocialPrayerRequest::findOrFail($id)->update(['status' => 'archived']);

        return back()->with('success', 'Pedido arquivado.');
    }

    public function destroy(int $id): RedirectResponse
    {
        SocialPrayerRequest::findOrFail($id)->delete();

        return back()->with('success', 'Pedido removido.');
    }
}
