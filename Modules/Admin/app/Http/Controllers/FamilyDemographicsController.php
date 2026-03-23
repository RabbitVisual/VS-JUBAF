<?php

namespace Modules\Admin\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\PdfService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Admin\App\Exports\FamilyDemographicsExport;
use Modules\Admin\App\Services\FamilyAnalysisService;
use Modules\Gamification\App\Services\CbavBotChatService;
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
     * Exibe o dashboard demográfico com gráficos e destaques pastorais.
     */
    public function index(): View
    {
        $report = $this->familyAnalysis->getDemographicsReport();

        return view('admin::reports.family_demographics', [
            'composition' => $report['composition'],
            'byNeighborhood' => $report['by_neighborhood'],
            'pastoralHighlights' => $report['pastoral_highlights'],
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
                'pastoralHighlights' => $report['pastoral_highlights'],
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

    /**
     * Análise pastoral do Elias: envia dados demográficos e retorna sugestões em texto.
     */
    public function eliasAnalysis(Request $request): JsonResponse
    {
        $summary = $this->familyAnalysis->getSummaryForElias();
        $user = $request->user();

        $message = 'Com base nos dados demográficos da igreja (composição familiar, núcleos, regiões e destaques), elabore sugestões pastorais objetivas em tópicos. Ex.: eventos para casais com filhos pequenos, ministério de integração para membros individuais, etc.';

        try {
            $chat = App::make(CbavBotChatService::class);
            $reply = $chat->respond($user, $message, null, ['family_demographics' => $summary]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Não foi possível obter a análise do Elias.',
                'reply' => null,
            ], 500);
        }

        return response()->json([
            'success' => true,
            'reply' => $reply ?: 'Nenhuma sugestão disponível no momento. Verifique os dados cadastrados e os vínculos de parentesco.',
        ]);
    }
}
