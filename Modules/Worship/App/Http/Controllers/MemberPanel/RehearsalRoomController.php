<?php

namespace Modules\Worship\App\Http\Controllers\MemberPanel;

use App\Http\Controllers\Controller;
use Modules\Worship\App\Models\WorshipSetlist;

class RehearsalRoomController extends Controller
{
    public function index()
    {
        $upcomingSetlists = WorshipSetlist::where('scheduled_at', '>=', now())
            ->orderBy('scheduled_at', 'asc')
            ->paginate(12);

        $nextSetlist = $upcomingSetlists->first();

        return view('worship::memberpanel.rehearsal.index', compact('upcomingSetlists', 'nextSetlist'));
    }

    public function show(WorshipSetlist $setlist)
    {
        $setlist->load(['items.song']);

        return view('worship::memberpanel.rehearsal.show', compact('setlist'));
    }
}
