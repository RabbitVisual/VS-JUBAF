<?php

namespace Modules\Worship\App\Http\Controllers\MemberPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Worship\App\Models\WorshipRoster;
use Modules\Worship\App\Models\WorshipSetlist;
use Modules\Worship\App\Models\AcademyProgress;
use Illuminate\Support\Facades\Auth;

class MusicianDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 1. Próxima Escala Pending ou Confirmed
        $nextRoster = WorshipRoster::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereHas('setlist', function ($q) {
                $q->where('scheduled_at', '>=', now()->startOfDay());
            })
            ->with(['setlist.items.song', 'instrument', 'worshipTeamRole'])
            ->orderBy(
                WorshipSetlist::select('scheduled_at')
                    ->whereColumn('worship_setlists.id', 'worship_rosters.setlist_id')
            )
            ->first();

        // 2. Últimos Cursos da Academia em Andamento
        $recentProgress = AcademyProgress::where('user_id', $user->id)
            ->with(['lesson.module.course'])
            ->latest('completed_at')
            ->take(3)
            ->get();

        return view('worship::memberpanel.dashboard', compact('nextRoster', 'recentProgress'));
    }
}
