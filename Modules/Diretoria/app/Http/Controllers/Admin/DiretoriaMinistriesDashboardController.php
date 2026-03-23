<?php

namespace Modules\Diretoria\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Ministries\App\Models\Ministry;
use Modules\Ministries\App\Models\MinistryPlan;
use Modules\Ministries\App\Models\MinistryReport;

class DiretoriaMinistriesDashboardController extends Controller
{
    /**
     * Visão macro dos ministérios para o conselho: plano, líder, status, semáforo.
     */
    public function index(Request $request): View
    {
        $query = Ministry::with(['leader', 'coLeader'])->orderBy('name');

        if ($request->filled('type') && $request->type !== '') {
            $query->where('settings->type', $request->type);
        }

        $ministries = $query->get();

        $year = now()->year;
        $month = now()->month;

        $planStatuses = MinistryPlan::whereIn('ministry_id', $ministries->pluck('id'))
            ->whereIn('status', [MinistryPlan::STATUS_UNDER_diretoria_REVIEW, MinistryPlan::STATUS_APPROVED, MinistryPlan::STATUS_IN_EXECUTION])
            ->get()
            ->groupBy('ministry_id')
            ->map(fn ($plans) => $plans->sortByDesc('period_end')->first());

        $reportSubmitted = MinistryReport::whereIn('ministry_id', $ministries->pluck('id'))
            ->where('report_year', $year)
            ->where('report_month', $month)
            ->where('status', MinistryReport::STATUS_SUBMITTED)
            ->pluck('ministry_id')
            ->flip();

        $trafficLights = [];
        $now = now();
        $refEnd = now()->copy()->setDate($year, $month, 1)->endOfMonth();
        $yellowUntil = $refEnd->copy()->addDays(5);

        foreach ($ministries as $ministry) {
            if ($reportSubmitted->has($ministry->id)) {
                $trafficLights[$ministry->id] = 'green';
                continue;
            }

            if ($now->lte($refEnd)) {
                // Ainda dentro do mês de referência: considerar pendente, mas não crítico
                $trafficLights[$ministry->id] = 'yellow';
            } elseif ($now->lte($yellowUntil)) {
                $trafficLights[$ministry->id] = 'yellow';
            } else {
                $trafficLights[$ministry->id] = 'red';
            }
        }

        $pendingPlanApprovals = \Modules\Diretoria\App\Models\DiretoriaApproval::where('approval_type', \Modules\Diretoria\App\Models\DiretoriaApproval::TYPE_MINISTRY_PLAN)
            ->whereIn('status', ['pending', 'requires_revision'])
            ->with('approvable')
            ->get();

        $types = Ministry::whereNotNull('settings')
            ->get()
            ->pluck('settings')
            ->filter(fn ($s) => is_array($s) && isset($s['type']))
            ->pluck('type')
            ->unique()
            ->filter()
            ->values()
            ->sort()
            ->all();

        return view('diretoria::admin.ministries-dashboard.index', compact(
            'ministries',
            'planStatuses',
            'reportSubmitted',
            'trafficLights',
            'pendingPlanApprovals',
            'types',
            'year',
            'month'
        ));
    }
}
