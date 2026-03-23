<?php

namespace Modules\Treasury\App\Http\Controllers\Pastoral;

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

    public function index(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());
        $data = $this->api->getReportAggregates($startDate, $endDate, auth()->user());
        $permission = TreasuryPermission::forUserOrAdmin(auth()->user());

        return view('treasury::pastoralpanel.reports.index', [
            'permission' => $permission,
            'startDate' => $data['start_date'],
            'endDate' => $data['end_date'],
            'totalIncome' => $data['total_income'],
            'totalExpense' => $data['total_expense'],
            'balance' => $data['balance'],
            'incomeByCategory' => $data['income_by_category'],
            'expenseByCategory' => $data['expense_by_category'],
            'incomeByDay' => $data['income_by_day'] ?? collect(),
            'expenseByDay' => $data['expense_by_day'] ?? collect(),
            'totalEntries' => $data['total_entries'],
            'totalIncomeEntries' => $data['total_income_entries'],
            'totalExpenseEntries' => $data['total_expense_entries'],
            'daysDiff' => $data['days_diff'],
        ]);
    }

    public function exportExcel(Request $request)
    {
        $permission = TreasuryPermission::forUserOrAdmin(auth()->user());
        if (! $permission->canExportData()) {
            abort(403, 'Você não tem permissão para exportar dados.');
        }

        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());

        $entries = FinancialEntry::with(['user', 'campaign', 'ministry'])
            ->period($startDate, $endDate)
            ->orderBy('entry_date')
            ->get();

        $totalIncome = FinancialEntry::income()->period($startDate, $endDate)->sum('amount');
        $totalExpense = FinancialEntry::expense()->period($startDate, $endDate)->sum('amount');
        $balance = $totalIncome - $totalExpense;

        $filename = 'relatorio_financeiro_'.$startDate.'_'.$endDate.'.xlsx';

        return Excel::download(
            new FinancialReportExport($entries, $startDate, $endDate, $totalIncome, $totalExpense, $balance),
            $filename
        );
    }

    public function exportPdf(Request $request): StreamedResponse
    {
        $permission = TreasuryPermission::forUserOrAdmin(auth()->user());
        if (! $permission->canExportData()) {
            abort(403, 'Você não tem permissão para exportar dados.');
        }

        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());

        $totalIncome = FinancialEntry::income()->period($startDate, $endDate)->sum('amount');
        $totalExpense = FinancialEntry::expense()->period($startDate, $endDate)->sum('amount');
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
}
