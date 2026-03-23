<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Relatório Demográfico Familiar</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1f2937; line-height: 1.5; }
        .header { text-align: center; border-bottom: 2px solid #4f46e5; padding-bottom: 12px; margin-bottom: 24px; }
        .church-name { font-size: 18px; font-weight: bold; color: #4f46e5; }
        .report-title { font-size: 14px; color: #6b7280; margin-top: 4px; }
        .generated { font-size: 9px; color: #9ca3af; margin-top: 8px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #e5e7eb; padding: 8px 12px; text-align: left; }
        th { background: #f3f4f6; font-weight: bold; }
        .section { margin-bottom: 24px; }
        .section-title { font-size: 13px; font-weight: bold; color: #374151; margin-bottom: 10px; border-left: 4px solid #4f46e5; padding-left: 10px; }
        ul.highlights { margin: 0; padding-left: 20px; }
        ul.highlights li { margin-bottom: 6px; }
        .footer { margin-top: 30px; font-size: 9px; color: #9ca3af; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <div class="church-name">{{ $churchName }}</div>
        <div class="report-title">Relatório Demográfico Familiar</div>
        <div class="generated">Gerado em {{ $generatedAt }}</div>
    </div>

    <div class="section">
        <div class="section-title">Composição dos Núcleos</div>
        <table>
            <thead>
                <tr>
                    <th>Tipo</th>
                    <th>Quantidade</th>
                    <th>Percentual</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Famílias completas (casal + filhos)</td>
                    <td>{{ $composition['complete_families'] ?? 0 }}</td>
                    <td>{{ $composition['pct_complete'] ?? 0 }}%</td>
                </tr>
                <tr>
                    <td>Famílias monoparentais</td>
                    <td>{{ $composition['monoparental'] ?? 0 }}</td>
                    <td>{{ $composition['pct_monoparental'] ?? 0 }}%</td>
                </tr>
                <tr>
                    <td>Casais (sem filhos no cadastro)</td>
                    <td>{{ $composition['couples'] ?? 0 }}</td>
                    <td>{{ $composition['pct_couples'] ?? 0 }}%</td>
                </tr>
                <tr>
                    <td>Membros individuais</td>
                    <td>{{ $composition['individuals'] ?? 0 }}</td>
                    <td>{{ $composition['pct_individuals'] ?? 0 }}%</td>
                </tr>
                <tr style="font-weight: bold;">
                    <td>Total de núcleos</td>
                    <td>{{ $composition['total_nuclei'] ?? 0 }}</td>
                    <td>100%</td>
                </tr>
            </tbody>
        </table>
    </div>

    @if(!empty($byNeighborhood))
    <div class="section">
        <div class="section-title">Famílias por Região (bairro/cidade)</div>
        <table>
            <thead>
                <tr>
                    <th>Região</th>
                    <th>Quantidade de famílias</th>
                </tr>
            </thead>
            <tbody>
                @foreach($byNeighborhood as $area => $count)
                <tr>
                    <td>{{ $area }}</td>
                    <td>{{ $count }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if(!empty($pastoralHighlights))
    <div class="section">
        <div class="section-title">Destaques Pastorais</div>
        <ul class="highlights">
            @foreach($pastoralHighlights as $h)
            <li>{{ $h }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="footer">
        Documento gerado pelo VertexCBAV. Uso interno – reuniões de conselho e planejamento pastoral.
    </div>
</body>
</html>
