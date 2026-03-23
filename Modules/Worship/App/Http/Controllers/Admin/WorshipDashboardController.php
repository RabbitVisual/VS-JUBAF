<?php

namespace Modules\Worship\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Modules\Worship\App\Models\WorshipRoster;
use Modules\Worship\App\Models\WorshipSetlist;
use Modules\Worship\App\Models\WorshipSong;

class WorshipDashboardController extends Controller
{
    public function index()
    {
        $totalRosters = WorshipRoster::count();
        $acceptedRosters = WorshipRoster::where('status', \Modules\Worship\App\Enums\RosterStatus::CONFIRMED)->count();
        $acceptanceRate = $totalRosters > 0 ? round(($acceptedRosters / $totalRosters) * 100) : 0;

        $stats = [
            'songs' => WorshipSong::count(),
            'upcoming_setlists' => WorshipSetlist::where('scheduled_at', '>=', now())->count(),
            'musicians_scheduled' => WorshipRoster::whereHas('setlist', function ($query) {
                $query->where('scheduled_at', '>=', now());
            })->count(),
            'academy_enrollments' => \Modules\Worship\App\Models\AcademyEnrollment::count(),
            'roster_acceptance_rate' => $acceptanceRate,
        ];

        $nextService = WorshipSetlist::where('scheduled_at', '>=', now())
            ->orderBy('scheduled_at', 'asc')
            ->first();

        $recentSongs = WorshipSong::latest()->take(5)->get();

        return view('worship::admin.dashboard', compact('stats', 'nextService', 'recentSongs'));
    }
}
