<?php

namespace Modules\Intercessor\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Intercessor\App\Models\PrayerCommitment;
use Modules\Intercessor\App\Models\PrayerRequest;

class IntercessorReportController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->input('from') ? now()->parse($request->input('from')) : now()->subDays(30);
        $to = $request->input('to') ? now()->parse($request->input('to')) : now();

        $baseQuery = PrayerRequest::whereBetween('created_at', [$from, $to]);

        if ($categoryId = $request->input('category_id')) {
            $baseQuery->where('category_id', $categoryId);
        }

        $totalRequests = (clone $baseQuery)->count();
        $answeredRequests = (clone $baseQuery)->where('status', 'answered')->count();

        $requestsWithPrayer = (clone $baseQuery)
            ->whereHas('commitments')
            ->count();

        $totalCommitments = PrayerCommitment::whereBetween('created_at', [$from, $to])->count();

        $engagementRate = $totalRequests > 0 ? round(($requestsWithPrayer / $totalRequests) * 100, 1) : 0;
        $answerRate = $totalRequests > 0 ? round(($answeredRequests / $totalRequests) * 100, 1) : 0;

        $byCategory = (clone $baseQuery)
            ->select('category_id', DB::raw('count(*) as total'))
            ->groupBy('category_id')
            ->with('category')
            ->get();

        return view('intercessor::admin.reports.index', compact(
            'from',
            'to',
            'totalRequests',
            'answeredRequests',
            'requestsWithPrayer',
            'totalCommitments',
            'engagementRate',
            'answerRate',
            'byCategory'
        ));
    }
}

