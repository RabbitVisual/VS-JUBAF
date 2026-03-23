<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Relatório Consolidado - {{ $ministry->name }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111827; }
        h1, h2, h3 { margin: 0 0 6px 0; }
        .muted { color: #6b7280; }
        .section { margin-bottom: 18px; }
        .box { border: 1px solid #e5e7eb; border-radius: 6px; padding: 10px 12px; margin-top: 6px; }
        .table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        .table th, .table td { border: 1px solid #e5e7eb; padding: 6px 8px; }
        .table th { background: #f3f4f6; font-weight: 600; }
        .small { font-size: 10px; }
    </style>
</head>
<body>
    <h1>Relatório Consolidado de Gestão – {{ $ministry->name }}</h1>
    <p class="muted small">
        Período de referência: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}
        a {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }} |
        Gerado em {{ $generatedAt->format('d/m/Y H:i') }}
    </p>

    <div class="section">
        <h2>Liderança</h2>
        <div class="box">
            <p><strong>Líder:</strong> {{ $ministry->leader->name ?? '—' }}</p>
            <p><strong>Co-líder:</strong> {{ $ministry->coLeader->name ?? '—' }}</p>
        </div>
    </div>

    <div class="section">
        <h2>Plano Atual</h2>
        <div class="box">
            @if($currentPlan)
                <p><strong>Título:</strong> {{ $currentPlan->title }}</p>
                <p><strong>Período:</strong>
                    {{ optional($currentPlan->period_start)->format('d/m/Y') }}
                    – {{ optional($currentPlan->period_end)->format('d/m/Y') }}
                </p>
                <p><strong>Status:</strong> {{ $currentPlan->status }}</p>
                <p><strong>Orçamento planejado:</strong>
                    {{ $currentPlan->budget_requested ? 'R$ '.number_format((float) $currentPlan->budget_requested, 2, ',', '.') : 'Não informado' }}
                </p>
                @if($currentPlan->objectives)
                    <p><strong>Objetivos:</strong></p>
                    <p class="small">{{ $currentPlan->objectives }}</p>
                @endif
            @else
                <p class="muted">Nenhum plano aprovado / em execução para este período.</p>
            @endif
        </div>
    </div>

    <div class="section">
        <h2>Últimos 3 Relatórios Mensais</h2>
        <div class="box">
            @if($recentReports->isEmpty())
                <p class="muted">Nenhum relatório mensal enviado ainda.</p>
            @else
                <table class="table">
                    <thead>
                        <tr>
                            <th>Mês/Ano</th>
                            <th>Resumo</th>
                            <th>Destaques</th>
                            <th>Desafios</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentReports as $report)
                            <tr>
                                <td>{{ sprintf('%02d/%d', $report->report_month, $report->report_year) }}</td>
                                <td class="small">{{ \Illuminate\Support\Str::limit($report->qualitative_summary, 180) }}</td>
                                <td class="small">{{ \Illuminate\Support\Str::limit($report->highlights, 140) }}</td>
                                <td class="small">{{ \Illuminate\Support\Str::limit($report->challenges, 140) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <div class="section">
        <h2>Resumo Financeiro (Tesouraria)</h2>
        <div class="box">
            @if($treasurySummary)
                <p>
                    <strong>Receitas:</strong>
                    R$ {{ number_format((float) ($treasurySummary['total_income'] ?? 0), 2, ',', '.') }} |
                    <strong>Despesas:</strong>
                    R$ {{ number_format((float) ($treasurySummary['total_expense'] ?? 0), 2, ',', '.') }} |
                    <strong>Saldo:</strong>
                    R$ {{ number_format((float) ($treasurySummary['balance'] ?? 0), 2, ',', '.') }}
                </p>
            @else
                <p class="muted">Resumo financeiro indisponível (Tesouraria não configurada ou sem permissões).</p>
            @endif
        </div>
    </div>
</body>
</html>

