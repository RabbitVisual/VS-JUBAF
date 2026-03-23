<?php

namespace Modules\Admin\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\PdfService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Admin\App\Exports\FamilyDemographicsExport;
use Modules\Admin\App\Services\FamilyAnalysisService;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Dashboard de Inteligência Familiar e Relatórios Demográficos.
 * Acesso restrito a administradores (middleware admin).
 */
class FamilyDemographicsController extends Controller
{
    public function __construct(
        protected FamilyAnalysisService $familyAnalysis,
        protected PdfService $pdfService
    ) {}

    /**
     * Exibe o dashboard demográfico com gráficos e destaques liderancaais.
     */
    public function index(): View
    {
        $report = $this->familyAnalysis->getDemographicsReport();

        return view('admin::reports.family_demographics', [
            'composition' => $report['composition'],
            'byNeighborhood' => $report['by_neighborhood'],
            'liderancaalHighlights' => $report['liderancaal_highlights'],
            'ageDistribution' => $report['age_distribution'],
            'totalUsersWithRelations' => $report['total_users_with_relations'],
            'totalRelationships' => $report['total_relationships'],
        ]);
    }

    /**
     * Exporta o relatório em PDF (cabeçalho Igreja Batista Avenida).
     */
    public function exportPdf(): StreamedResponse
    {
        $report = $this->familyAnalysis->getDemographicsReport();
        $churchName = \App\Models\Settings::get('site_name', 'Igreja Batista Avenida');

        $filename = 'relatorio_demografico_familiar_' . now()->format('Y-m-d') . '.pdf';

        return $this->pdfService->downloadView(
            'admin::reports.family_demographics_pdf',
            [
                'composition' => $report['composition'],
                'byNeighborhood' => $report['by_neighborhood'],
                'liderancaalHighlights' => $report['liderancaal_highlights'],
                'churchName' => $churchName,
                'generatedAt' => now()->format('d/m/Y H:i'),
            ],
            $filename,
            'A4',
            'Portrait',
            [15, 15, 15, 15]
        );
    }

    /**
     * Exporta o relatório em Excel.
     */
    public function exportExcel(): StreamedResponse
    {
        $report = $this->familyAnalysis->getDemographicsReport();

        return Excel::download(
            new FamilyDemographicsExport($report),
            'relatorio_demografico_familiar_' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function eliasAnalysis(Request $request): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Análise automática desativada na versão JUBAF.',
            'reply' => null,
        ], 410);
    }
}
