<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Dízimos e Ofertas</title>
    <style>
        @page { margin: 1.5cm; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 10px; color: #1f2937; line-height: 1.5; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #3b82f6; padding-bottom: 15px; }
        .header h1 { font-size: 20px; color: #1e3a8a; margin-bottom: 5px; text-transform: uppercase; }
        .header p { font-size: 11px; color: #6b7280; font-weight: bold; }

        .summary-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px; margin-bottom: 20px; }
        .summary-item { display: inline-block; width: 32%; text-align: center; }
        .summary-item .label { font-size: 8px; color: #64748b; text-transform: uppercase; font-weight: 800; letter-spacing: 0.05em; margin-bottom: 5px; }
        .summary-item .value { font-size: 16px; font-weight: 900; color: #1e293b; }
        .text-green { color: #16a34a !important; }

        .section-title { font-size: 10px; font-weight: 900; background: #1e3a8a; color: white; padding: 6px 12px; margin-bottom: 10px; text-transform: uppercase; border-radius: 4px; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background: #f1f5f9; border-bottom: 2px solid #cbd5e1; padding: 10px; text-align: left; font-size: 8px; font-weight: 800; text-transform: uppercase; color: #475569; }
        td { padding: 8px 10px; border-bottom: 1px solid #e2e8f0; font-size: 9px; }
        tr:nth-child(even) { background: #fdfdfd; }

        .member-row { background: #f8fafc !important; font-weight: bold; }
        .entry-row { color: #64748b; font-size: 8px !important; }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }

        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 8px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 10px; }
    </style>
</head>

<body>
    <div class="header">
        <h1>Relatório de Dízimos e Ofertas</h1>
        <p>PERÍODO: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} ATÉ {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</p>
    </div>

    <div class="summary-box">
        <div class="summary-item">
            <div class="label">Total Dízimos</div>
            <div class="value text-green">R$ {{ number_format($byCategory['tithe']['total'] ?? 0, 2, ',', '.') }}</div>
        </div>
        <div class="summary-item">
            <div class="label">Total Ofertas</div>
            <div class="value text-green">R$ {{ number_format($byCategory['offering']['total'] ?? 0, 2, ',', '.') }}</div>
        </div>
        <div class="summary-item">
            <div class="label">Arrecadação Total</div>
            <div class="value text-green">R$ {{ number_format($totalIncome, 2, ',', '.') }}</div>
        </div>
    </div>

    <div class="section-title">Contribuições por Membro (Resumo)</div>
    <table>
        <thead>
            <tr>
                <th>Membro / Contribuinte</th>
                <th class="text-right">Quantidade</th>
                <th class="text-right">Total Contribuído</th>
                <th class="text-right">Representação</th>
            </tr>
        </thead>
        <tbody>
            @foreach($byMember as $member)
            <tr>
                <td class="text-bold">{{ $member['name'] }}</td>
                <td class="text-right">{{ $member['count'] }} entradas</td>
                <td class="text-right text-bold">R$ {{ number_format($member['total'], 2, ',', '.') }}</td>
                <td class="text-right">{{ number_format(($member['total'] / max(1, $totalIncome)) * 100, 1) }}%</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background: #f1f5f9; font-weight: 900;">
                <td colspan="2" class="text-right">TOTAL GERAL:</td>
                <td class="text-right text-green">R$ {{ number_format($totalIncome, 2, ',', '.') }}</td>
                <td class="text-right">100%</td>
            </tr>
        </tfoot>
    </table>

    <div style="page-break-before: always;"></div>

    <div class="section-title">Log Detalhado de Movimentações</div>
    <table>
        <thead>
            <tr>
                <th style="width: 10%">Data</th>
                <th style="width: 25%">Membro</th>
                <th style="width: 15%">Categoria</th>
                <th style="width: 30%">Título/Referência</th>
                <th style="width: 20%" class="text-right">Valor</th>
            </tr>
        </thead>
        <tbody>
            @foreach($entries as $entry)
            <tr>
                <td>{{ $entry->entry_date->format('d/m/Y') }}</td>
                <td>{{ $entry->user->name ?? 'Anônimo' }}</td>
                <td>{{ $entry->category === 'tithe' ? 'Dízimo' : 'Oferta' }}</td>
                <td>{{ $entry->title }}</td>
                <td class="text-right text-bold text-green">R$ {{ number_format($entry->amount, 2, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Vertex CBAV - Sistema de Intendência Eclesiástica • Gerado em {{ now()->format('d/m/Y H:i') }} • Página 1
    </div>
</body>
</html>

