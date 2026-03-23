<?php

namespace Modules\Intercessor\App\Http\Controllers\MemberPanel;

use App\Http\Controllers\Controller;
use Modules\Intercessor\App\Models\PrayerRequest;
use Modules\Intercessor\App\Models\PrayerCommitment;
use Illuminate\Support\Facades\Auth;

class IntercessorDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $urgentRequestsCount = PrayerRequest::active()
            ->where('urgency_level', 'critical')
            ->count();

        $pendingRequestsCount = PrayerRequest::active()->count();

        $myCommitmentsCount = PrayerCommitment::where('user_id', $user->id)->count();

        $urgentRequests = PrayerRequest::active()
            ->with(['user', 'category'])
            ->where('urgency_level', 'critical')
            ->latest()
            ->take(3)
            ->get();

        $recentRequests = PrayerRequest::active()
            ->with(['user', 'category'])
            ->latest()
            ->take(5)
            ->get();

        $myCommitments = PrayerCommitment::where('user_id', $user->id)
            ->with(['request.user', 'request.category'])
            ->latest()
            ->take(4)
            ->get();

        return view('intercessor::memberpanel.dashboard', compact(
            'urgentRequestsCount',
            'pendingRequestsCount',
            'myCommitmentsCount',
            'urgentRequests',
            'recentRequests',
            'myCommitments'
        ));
    }
}
