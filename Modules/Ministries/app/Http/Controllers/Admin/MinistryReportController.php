<?php

namespace Modules\Ministries\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Ministries\App\Models\Ministry;
use Modules\Ministries\App\Models\MinistryPlan;
use Modules\Ministries\App\Models\MinistryReport;

class MinistryReportController extends Controller
{
    public function exportConsolidated(Request $request, Ministry $ministry, PdfService $pdfService)
    {
        $this->authorize('view', $ministry);

        $ministry->load(['leader', 'coLeader']);

        $currentPlan = MinistryPlan::where('ministry_id', $ministry->id)
            ->whereIn('status', [MinistryPlan::STATUS_APPROVED, MinistryPlan::STATUS_IN_EXECUTION])
            ->orderBy('period_end', 'desc')
            ->first();

        $recentReports = MinistryReport::where('ministry_id', $ministry->id)
            ->where('status', MinistryReport::STATUS_SUBMITTED)
            ->orderByDesc('report_year')
            ->orderByDesc('report_month')
            ->limit(3)
            ->get();

        $startDate = $recentReports->count()
            ? $recentReports->sortBy('period_start')->first()->period_start?->toDateString()
            : now()->startOfMonth()->toDateString();

        $endDate = $recentReports->count()
            ? $recentReports->sortByDesc('period_end')->first()->period_end?->toDateString()
            : now()->endOfMonth()->toDateString();

        $treasurySummary = null;
        if (class_exists(\Modules\Treasury\App\Services\TreasuryApiService::class)) {
            try {
                $treasurySummary = app(\Modules\Treasury\App\Services\TreasuryApiService::class)
                    ->getMinistrySummary($ministry->id, $startDate, $endDate, Auth::user());
            } catch (\Throwable $e) {
                $treasurySummary = null;
            }
        }

        $data = [
            'ministry' => $ministry,
            'currentPlan' => $currentPlan,
            'recentReports' => $recentReports,
            'treasurySummary' => $treasurySummary,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'generatedAt' => now(),
        ];

        $filename = 'relatorio-ministerio-'.$ministry->id.'.pdf';

        return $pdfService->downloadView('ministries::admin.reports.consolidated', $data, $filename);
    }
}

