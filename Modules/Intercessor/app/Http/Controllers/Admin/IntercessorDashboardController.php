<?php

namespace Modules\Intercessor\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Modules\Intercessor\App\Models\PrayerRequest;
use Modules\Intercessor\App\Models\PrayerCommitment;
use Illuminate\Support\Facades\DB;

class IntercessorDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total' => PrayerRequest::count(),
            'active' => PrayerRequest::active()->count(),
            'urgent' => PrayerRequest::active()->where('urgency_level', 'critical')->count(),
            'interactions' => PrayerCommitment::count(),
            'pending' => PrayerRequest::where('status', 'pending')->count(),
        ];

        // Fetch daily commitments for the last 30 days
        $activityData = PrayerCommitment::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('count(*) as count')
        )
        ->where('created_at', '>=', now()->subDays(30))
        ->groupBy('date')
        ->orderBy('date')
        ->get()
        ->pluck('count', 'date')
        ->toArray();

        // Fill missing days with 0
        $chartData = [];
        $chartLabels = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $chartLabels[] = now()->subDays($i)->format('d/m');
            $chartData[] = $activityData[$date] ?? 0;
        }

        return view('intercessor::admin.dashboard', compact('stats', 'chartData', 'chartLabels'));
    }
}
