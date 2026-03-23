<?php

namespace Modules\Bible\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Modules\Bible\App\Models\BiblePlan;
use Modules\Bible\App\Models\UserReadingLog;
use Modules\Bible\App\Services\ReadingCatchUpService;

class BibleReportController extends Controller
{
    public function churchPlan()
    {
        $churchPlans = BiblePlan::where('is_church_plan', true)
            ->with(['subscriptions' => function ($q) {
                $q->with(['user', 'plan']);
            }])
            ->get();

        $subscriptions = $churchPlans->pluck('subscriptions')->flatten()->filter->id->values();
        $catchUp = app(ReadingCatchUpService::class);

        $rows = [];
        $totalRead = 0;
        $totalExpected = 0;

        foreach ($subscriptions as $subscription) {
            $subscription->load('plan');
            $delay = $catchUp->getDelayDays($subscription);
            if ($delay >= ReadingCatchUpService::PRAYER_REQUEST_THRESHOLD_DAYS) {
                $catchUp->ensurePrayerRequestForDelayWhenBehind($subscription);
                $subscription->refresh();
            }
            $daysRead = UserReadingLog::where('subscription_id', $subscription->id)->count();
            $expected = $subscription->plan->days()->count();
            $totalRead += $daysRead;
            $totalExpected += $expected;

            $status = $delay === 0 ? 'em_dia' : ($delay < ReadingCatchUpService::PRAYER_REQUEST_THRESHOLD_DAYS ? 'atraso' : 'critico');
            $rows[] = (object) [
                'subscription' => $subscription,
                'user' => $subscription->user,
                'plan' => $subscription->plan,
                'days_read' => $daysRead,
                'delay' => $delay,
                'status' => $status,
                'expected_days' => $expected,
            ];
        }

        $completionPercent = $totalExpected > 0
            ? round(($totalRead / $totalExpected) * 100, 1)
            : 0;

        return view('bible::admin.reports.church-plan', [
            'churchPlans' => $churchPlans,
            'rows' => $rows,
            'completionPercent' => $completionPercent,
            'totalRead' => $totalRead,
            'totalExpected' => $totalExpected,
        ]);
    }
}
