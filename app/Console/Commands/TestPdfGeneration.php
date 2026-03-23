<?php

namespace App\Console\Commands;

use App\Services\PdfService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class TestPdfGeneration extends Command
{
    protected $signature = 'pdf:test {--output= : Output path for the test PDF}';
    protected $description = 'Test PDF generation with html2pdf.app/mPDF';

    public function handle(PdfService $pdfService): int
    {
        $this->info('🔍 Verificando configuração do PDF Service...');
        $this->newLine();

        if ($pdfService->isCloudEnabled()) {
            $this->info('✅ html2pdf.app está ATIVADO (API Key configurada)');
            $this->line('   Usando: html2pdf.app Cloud → mPDF fallback');
        } else {
            $this->warn('⚠️  html2pdf.app está DESATIVADO (API Key não configurada)');
            $this->line('   Usando: mPDF local apenas');
            $this->line('   Para ativar, adicione HTML2PDF_API_KEY no .env');
            $this->line('   Obtenha grátis em: https://html2pdf.app');
        }

        $this->newLine();
        $this->info('📄 Gerando PDF de teste...');

        $html = $this->getTestHtml();

        try {
            $startTime = microtime(true);
            $pdf = $pdfService->portrait($html, [15, 15, 15, 15]);
            $duration = round((microtime(true) - $startTime) * 1000);

            $outputPath = $this->option('output') ?? 'test-pdf-' . now()->format('Y-m-d-His') . '.pdf';

            Storage::disk('local')->put($outputPath, $pdf);
            $fullPath = storage_path('app/' . $outputPath);

            $this->newLine();
            $this->info('✅ PDF gerado com sucesso!');
            $this->line("   Arquivo: {$fullPath}");
            $this->line("   Tamanho: " . $this->formatBytes(strlen($pdf)));
            $this->line("   Tempo: {$duration}ms");
            $this->newLine();

            $this->table(
                ['Métrica', 'Valor'],
                [
                    ['Backend', $pdfService->isCloudEnabled() ? 'html2pdf.app Cloud' : 'mPDF Local'],
                    ['Tamanho', $this->formatBytes(strlen($pdf))],
                    ['Tempo de Geração', "{$duration}ms"],
                    ['Arquivo', $outputPath],
                ]
            );

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $this->newLine();
            $this->error('❌ Erro ao gerar PDF: ' . $e->getMessage());
            $this->line($e->getTraceAsString());
            return Command::FAILURE;
        }
    }

    protected function getTestHtml(): string
    {
        return <<<'HTML'
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            color: #1f2937;
            line-height: 1.6;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
            color: white;
            padding: 30px;
            text-align: center;
            margin: -20px -20px 30px -20px;
            border-radius: 0 0 10px 10px;
        }
        .header h1 {
            font-size: 28px;
            margin-bottom: 5px;
            letter-spacing: 1px;
        }
        .header p {
            opacity: 0.9;
            font-size: 12px;
        }
        .card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 4px solid #f59e0b;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
        }
        .card h3 {
            color: #0f172a;
            margin-bottom: 10px;
            font-size: 14px;
        }
        .stats {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .stat {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            padding: 15px;
            background: white;
            border: 1px solid #e2e8f0;
        }
        .stat:first-child { border-radius: 8px 0 0 8px; }
        .stat:last-child { border-radius: 0 8px 8px 0; }
        .stat .value {
            font-size: 24px;
            font-weight: bold;
            color: #0f172a;
        }
        .stat .label {
            font-size: 10px;
            color: #64748b;
            text-transform: uppercase;
        }
        .stat.success .value { color: #16a34a; }
        .stat.warning .value { color: #f59e0b; }
        .stat.info .value { color: #3b82f6; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th {
            background: #1e3a8a;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
        }
        td {
            padding: 10px;
            border-bottom: 1px solid #e2e8f0;
        }
        tr:nth-child(even) { background: #f9fafb; }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 9px;
            color: #64748b;
            border-top: 2px solid #e2e8f0;
            padding-top: 15px;
        }
        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: 600;
        }
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-warning { background: #fef3c7; color: #92400e; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Vertex CBAV</h1>
        <p>Teste de Geração de PDF - Sistema Unificado</p>
    </div>

    <div class="card">
        <h3>Informações do Teste</h3>
        <p>Este documento foi gerado automaticamente para testar o sistema de geração de PDFs do Vertex CBAV.</p>
        <p>O sistema utiliza <strong>html2pdf.app</strong> (cloud) como backend principal e <strong>mPDF</strong> como fallback local.</p>
    </div>

    <div class="stats">
        <div class="stat success">
            <div class="value">✓</div>
            <div class="label">Sistema Ativo</div>
        </div>
        <div class="stat info">
            <div class="value">A4</div>
            <div class="label">Formato</div>
        </div>
        <div class="stat warning">
            <div class="value">UTF-8</div>
            <div class="label">Encoding</div>
        </div>
    </div>

    <h3 style="margin-bottom: 10px; color: #0f172a;">Recursos Suportados</h3>
    <table>
        <thead>
            <tr>
                <th>Recurso</th>
                <th>Status</th>
                <th>Descrição</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Cores</td>
                <td><span class="badge badge-success">Ativo</span></td>
                <td>Gradientes, backgrounds, cores CSS</td>
            </tr>
            <tr>
                <td>Tipografia</td>
                <td><span class="badge badge-success">Ativo</span></td>
                <td>DejaVu Sans, Arial, fontes web</td>
            </tr>
            <tr>
                <td>Layout</td>
                <td><span class="badge badge-success">Ativo</span></td>
                <td>Tables, flexbox simulado, borders</td>
            </tr>
            <tr>
                <td>Imagens</td>
                <td><span class="badge badge-success">Ativo</span></td>
                <td>Base64, URLs remotas (html2pdf.app)</td>
            </tr>
            <tr>
                <td>Caracteres Especiais</td>
                <td><span class="badge badge-success">Ativo</span></td>
                <td>Acentos: áéíóú ãõ çÇ ñ — UTF-8 completo</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>Documento gerado em: TIMESTAMP_PLACEHOLDER</p>
        <p>© 2026 Vertex Solutions - Sistema de Gestão Eclesiástica</p>
    </div>
</body>
</html>
HTML;
    }

    protected function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
