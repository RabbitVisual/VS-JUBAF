<?php

namespace Modules\Worship\App\Http\Controllers\MemberPanel;

use App\Http\Controllers\Controller;
use Modules\Worship\App\Models\WorshipRoster;
use Modules\Worship\App\Models\WorshipSetlist;

class MyRosterController extends Controller
{
    public function index()
    {
        $rosters = WorshipRoster::where('user_id', auth()->id())
            ->whereHas('setlist', function ($q) {
                $q->where('scheduled_at', '>=', now()->subDays(1));
            })
            ->with(['setlist', 'instrument'])
            ->orderByDesc(
                \Modules\Worship\App\Models\WorshipSetlist::select('scheduled_at')
                    ->whereColumn('id', 'worship_rosters.setlist_id')
            )
            ->paginate(10);

        return view('worship::memberpanel.my-rosters.index', compact('rosters'));
    }

    public function updateStatus(\Illuminate\Http\Request $request, WorshipRoster $roster)
    {
        // Security check: only the musician assigned can update
        if ($roster->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:confirmed,declined',
        ]);

        $roster->update([
            'status' => $request->status,
            'responded_at' => now(),
        ]);

        $statusLabel = $request->status === 'confirmed' ? 'confirmada' : 'recusada';

        return redirect()->back()->with('success', "Escala {$statusLabel} com sucesso!");
    }
}
