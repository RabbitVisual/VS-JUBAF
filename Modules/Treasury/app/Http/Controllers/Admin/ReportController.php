<?php

namespace Modules\Treasury\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Treasury\App\Models\FinancialEntry;
use Modules\Treasury\App\Models\TreasuryPermission;
use Modules\Treasury\App\Services\TreasuryApiService;
use Modules\Treasury\Exports\FinancialReportExport;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function __construct(
        protected PdfService $pdfService,
        protected TreasuryApiService $api
    ) {}

    /**
     * Display financial reports.
     */
    public function index(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());
        $data = $this->api->getReportAggregates($startDate, $endDate, auth()->user());

        $monthlyClosing = null;
        $canCouncilApprove = false;

        $user = auth()->user();
        if ($user) {
            $start = \Carbon\Carbon::parse($data['start_date']);
            $end = \Carbon\Carbon::parse($data['end_date']);
            $monthStart = $start->copy()->startOfMonth();
            $monthEnd = $start->copy()->endOfMonth();

            $isSingleMonth = $start->isSameDay($monthStart) && $end->isSameDay($monthEnd);

            if ($isSingleMonth) {
                $isCouncilMember = class_exists(\Modules\ChurchCouncil\App\Models\CouncilMember::class)
                    && \Modules\ChurchCouncil\App\Models\CouncilMember::where('user_id', $user->id)->where('is_active', true)->exists();

                $allowAdminApproval = class_exists(\Modules\ChurchCouncil\App\Services\ChurchCouncilSettings::class)
                    ? \Modules\ChurchCouncil\App\Services\ChurchCouncilSettings::allowAdminApproval()
                    : (bool) \App\Models\Settings::get('church_council_allow_admin_approval', false);

                $isAdminOrPastor = method_exists($user, 'hasRole')
                    ? ($user->hasRole('admin') || $user->hasRole('pastor'))
                    : false;

                $canCouncilApprove = $isCouncilMember || ($allowAdminApproval && $isAdminOrPastor);

                try {
                    $monthlyClosing = $this->api->getOrCreateMonthlyClosing($data['start_date'], $data['end_date'], $user);
                } catch (\Throwable $e) {
                    $monthlyClosing = null;
                }
            }
        }

        return view('treasury::admin.reports.index', [
            'permission' => TreasuryPermission::forUserOrAdmin(auth()->user()),
            'startDate' => $data['start_date'],
            'endDate' => $data['end_date'],
            'totalIncome' => $data['total_income'],
            'totalExpense' => $data['total_expense'],
            'balance' => $data['balance'],
            'incomeByCategory' => $data['income_by_category'],
            'expenseByCategory' => $data['expense_by_category'],
            'incomeByDay' => $data['income_by_day'],
            'expenseByDay' => $data['expense_by_day'],
            'totalEntries' => $data['total_entries'],
            'totalIncomeEntries' => $data['total_income_entries'],
            'totalExpenseEntries' => $data['total_expense_entries'],
            'daysDiff' => $data['days_diff'],
            'avgDailyIncome' => $data['avg_daily_income'],
            'avgDailyExpense' => $data['avg_daily_expense'],
            'largestIncome' => $data['largest_income'],
            'largestExpense' => $data['largest_expense'],
            'incomeByPaymentMethod' => $data['income_by_payment_method'],
            'expenseByPaymentMethod' => $data['expense_by_payment_method'],
            'incomeByMonth' => $data['income_by_month'],
            'expenseByMonth' => $data['expense_by_month'],
            'planoCooperativo' => $data['plano_cooperativo'] ?? null,
            'monthlyClosing' => $monthlyClosing,
            'canCouncilApprove' => $canCouncilApprove,
        ]);
    }

    /**
     * Aprovar um fechamento mensal para apresentação em assembleia (Parecer Fiscal do Conselho).
     */
    public function approveClosingForAssembly(Request $request, \Modules\Treasury\App\Models\TreasuryMonthlyClosing $closing)
    {
        $user = auth()->user();
        $permission = TreasuryPermission::forUserOrAdmin($user);
        if (! $permission->canViewReports()) {
            abort(403, 'Você não tem permissão para aprovar fechamentos.');
        }

        $notes = $request->input('notes');
        $closing = $this->api->markClosingReadyForAssembly($closing, $user, $notes);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Balancete marcado como pronto para assembleia.',
                'closing' => $closing,
            ]);
        }

        return redirect()->route('treasury.reports.index', [
            'start_date' => $closing->period_start->toDateString(),
            'end_date' => $closing->period_end->toDateString(),
        ])->with('success', 'Balancete marcado como pronto para assembleia.');
    }

    /**
     * Export report data to Excel
     */
    public function exportExcel(Request $request)
    {
        $user = auth()->user();
        $permission = TreasuryPermission::forUserOrAdmin($user);

        if (! $permission->canExportData()) {
            abort(403, 'Você não tem permissão para exportar dados.');
        }

        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());

        $entries = FinancialEntry::with(['user', 'campaign', 'ministry'])
            ->period($startDate, $endDate)
            ->orderBy('entry_date')
            ->get();

        $totalIncome = FinancialEntry::income()
            ->period($startDate, $endDate)
            ->sum('amount');

        $totalExpense = FinancialEntry::expense()
            ->period($startDate, $endDate)
            ->sum('amount');

        $balance = $totalIncome - $totalExpense;

        $filename = 'relatorio_financeiro_'.$startDate.'_'.$endDate.'.xlsx';

        return Excel::download(
            new FinancialReportExport($entries, $startDate, $endDate, $totalIncome, $totalExpense, $balance),
            $filename
        );
    }

    /**
     * Export report data to PDF
     */
    public function exportPdf(Request $request): StreamedResponse
    {
        $user = auth()->user();
        $permission = TreasuryPermission::forUserOrAdmin($user);

        if (! $permission->canExportData()) {
            abort(403, 'Você não tem permissão para exportar dados.');
        }

        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());

        $totalIncome = FinancialEntry::income()
            ->period($startDate, $endDate)
            ->sum('amount');

        $totalExpense = FinancialEntry::expense()
            ->period($startDate, $endDate)
            ->sum('amount');

        $balance = $totalIncome - $totalExpense;

        $incomeByCategory = FinancialEntry::income()
            ->period($startDate, $endDate)
            ->select('category', DB::raw('sum(amount) as total'), DB::raw('count(*) as count'))
            ->groupBy('category')
            ->orderBy('total', 'desc')
            ->get();

        $expenseByCategory = FinancialEntry::expense()
            ->period($startDate, $endDate)
            ->select('category', DB::raw('sum(amount) as total'), DB::raw('count(*) as count'))
            ->groupBy('category')
            ->orderBy('total', 'desc')
            ->get();

        $entries = FinancialEntry::with(['user', 'campaign', 'ministry'])
            ->period($startDate, $endDate)
            ->orderBy('entry_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $start = \Carbon\Carbon::parse($startDate);
        $end = \Carbon\Carbon::parse($endDate);
        $isSingleMonth = $start->format('Y-m') === $end->format('Y-m') && $start->day === 1 && $end->day === $end->daysInMonth;
        $reportTitle = $isSingleMonth ? 'Balancete Mensal' : 'Relatório Financeiro';

        $filename = ($isSingleMonth ? 'balancete_mensal_' : 'relatorio_financeiro_').$startDate.'_'.$endDate.'.pdf';

        return $this->pdfService->downloadView(
            'treasury::admin.reports.pdf',
            compact('startDate', 'endDate', 'totalIncome', 'totalExpense', 'balance', 'incomeByCategory', 'expenseByCategory', 'entries', 'reportTitle'),
            $filename,
            'A4',
            'Landscape',
            [10, 10, 10, 10]
        );
    }

    /**
     * Export tithes and offerings data to PDF (Specialized Report)
     */
    public function exportTithesOfferingsPdf(Request $request): StreamedResponse
    {
        $user = auth()->user();
        $permission = TreasuryPermission::forUserOrAdmin($user);

        if (! $permission->canExportData()) {
            abort(403, 'Você não tem permissão para exportar dados.');
        }

        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());

        $categories = ['tithe', 'offering'];

        $entries = FinancialEntry::with(['user'])
            ->whereIn('category', $categories)
            ->period($startDate, $endDate)
            ->orderBy('entry_date', 'asc')
            ->get();

        $totalIncome = $entries->where('type', 'income')->sum('amount');

        $byCategory = $entries->groupBy('category')->map(function ($items) {
            return [
                'total' => $items->sum('amount'),
                'count' => $items->count(),
            ];
        });

        $byMember = $entries->groupBy('user_id')->map(function ($items) {
            return [
                'name' => $items->first()->user->name ?? 'Anônimo',
                'total' => $items->sum('amount'),
                'count' => $items->count(),
                'entries' => $items,
            ];
        })->sortByDesc('total');

        $filename = 'relatorio_dizimos_ofertas_'.$startDate.'_'.$endDate.'.pdf';

        return $this->pdfService->downloadView(
            'treasury::admin.reports.tithes_offerings_pdf',
            compact('startDate', 'endDate', 'totalIncome', 'byCategory', 'byMember', 'entries'),
            $filename,
            'A4',
            'Portrait',
            [15, 15, 15, 15]
        );
    }

    /**
     * Comprovante anual de contribuição (CBAV2026). Por member_id e ano. Membro só pode ver o próprio; tesoureiro/pastor pode ver qualquer.
     */
    public function contributionReceiptPdf(Request $request): StreamedResponse
    {
        $user = auth()->user();
        $permission = TreasuryPermission::forUserOrAdmin($user);
        $memberId = (int) $request->get('member_id', $user->id);
        $year = (int) $request->get('year', now()->year);

        if ($memberId !== $user->id && ! $permission->canExportData()) {
            abort(403, 'Você não tem permissão para emitir recibo de outro membro.');
        }

        $member = \App\Models\User::find($memberId);
        if (! $member) {
            abort(404, 'Membro não encontrado.');
        }

        $startDate = "{$year}-01-01";
        $endDate = "{$year}-12-31";
        $entries = FinancialEntry::with('financialCategory')
            ->where('type', 'income')
            ->where('member_id', $memberId)
            ->whereBetween('entry_date', [$startDate, $endDate])
            ->orderBy('entry_date')
            ->get();

        $byCategory = $entries->groupBy(function ($e) {
            return $e->financialCategory?->name ?? ucfirst(str_replace('_', ' ', $e->category));
        })->map(fn ($items) => [
            'total' => (float) $items->sum('amount'),
            'count' => $items->count(),
        ])->sortByDesc('total');

        $total = (float) $entries->sum('amount');
        $filename = 'comprovante_contribuicao_'.$memberId.'_'.$year.'.pdf';

        return $this->pdfService->downloadView(
            'treasury::admin.reports.contribution_receipt_pdf',
            compact('member', 'year', 'byCategory', 'total', 'entries'),
            $filename,
            'A4',
            'Portrait',
            [15, 15, 15, 15]
        );
    }

    /**
     * Export report data to CSV (legacy)
     */
    public function export(Request $request)
    {
        return $this->exportExcel($request);
    }
}
