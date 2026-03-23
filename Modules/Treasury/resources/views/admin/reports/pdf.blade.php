<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório Financeiro</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            color: #333;
            line-height: 1.4;
        }

        .header {
            background: linear-gradient(135deg, #1E40AF 0%, #3B82F6 100%);
            color: white;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 24px;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .header p {
            font-size: 12px;
            opacity: 0.9;
        }

        .summary {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .summary-card {
            display: table-cell;
            width: 33.33%;
            padding: 15px;
            text-align: center;
            border: 1px solid #E5E7EB;
            vertical-align: top;
        }

        .summary-card.income {
            background-color: #F0FDF4;
            border-left: 4px solid #16A34A;
        }

        .summary-card.expense {
            background-color: #FEF2F2;
            border-left: 4px solid #DC2626;
        }

        .summary-card.balance {
            background-color: #F0F9FF;
            border-left: 4px solid #2563EB;
        }

        .summary-card h3 {
            font-size: 11px;
            color: #6B7280;
            margin-bottom: 8px;
            text-transform: uppercase;
            font-weight: 600;
        }

        .summary-card .value {
            font-size: 18px;
            font-weight: bold;
            color: #111827;
        }

        .summary-card.income .value {
            color: #16A34A;
        }

        .summary-card.expense .value {
            color: #DC2626;
        }

        .summary-card.balance .value {
            color: #2563EB;
        }

        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }

        .section-title {
            background-color: #1E40AF;
            color: white;
            padding: 10px 15px;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 10px;
            border-radius: 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        table th {
            background-color: #F3F4F6;
            color: #111827;
            padding: 10px;
            text-align: left;
            font-size: 9px;
            font-weight: bold;
            border: 1px solid #D1D5DB;
            text-transform: uppercase;
        }

        table td {
            padding: 8px 10px;
            border: 1px solid #E5E7EB;
            font-size: 9px;
        }

        table tbody tr:nth-child(even) {
            background-color: #F9FAFB;
        }

        table tbody tr:hover {
            background-color: #F3F4F6;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .text-green {
            color: #16A34A;
            font-weight: bold;
        }

        .text-red {
            color: #DC2626;
            font-weight: bold;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 8px;
            font-weight: 600;
        }

        .badge-income {
            background-color: #D1FAE5;
            color: #065F46;
        }

        .badge-expense {
            background-color: #FEE2E2;
            color: #991B1B;
        }

        .category-table {
            width: 48%;
            display: inline-block;
            vertical-align: top;
            margin-right: 2%;
        }

        .category-table:last-child {
            margin-right: 0;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #E5E7EB;
            text-align: center;
            font-size: 8px;
            color: #6B7280;
        }

        .period-info {
            background-color: #F9FAFB;
            padding: 10px;
            border-left: 4px solid #3B82F6;
            margin-bottom: 20px;
            font-size: 10px;
        }

        .period-info strong {
            color: #1E40AF;
        }

        @page {
            margin: 15mm;
        }
    </style>
</head>

<body>
    <!-- Header (CBAV2026: Balancete Mensal para assembleia quando período é um mês) -->
    <div class="header">
        <h1>{{ $reportTitle ?? 'Relatório Financeiro' }}</h1>
        <p>Período: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} até
            {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</p>
    </div>

    <!-- Period Info -->
    <div class="period-info">
        <strong>Período do Relatório:</strong> {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} até
        {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}<br>
        <strong>Data de Geração:</strong> {{ now()->format('d/m/Y H:i:s') }}<br>
        <strong>Total de Registros:</strong> {{ $entries->count() }} entradas
    </div>

    <!-- Summary Cards -->
    <div class="summary">
        <div class="summary-card income">
            <h3>Total de Receitas</h3>
            <div class="value">R$ {{ number_format($totalIncome, 2, ',', '.') }}</div>
        </div>
        <div class="summary-card expense">
            <h3>Total de Despesas</h3>
            <div class="value">R$ {{ number_format($totalExpense, 2, ',', '.') }}</div>
        </div>
        <div class="summary-card balance">
            <h3>Saldo</h3>
            <div class="value {{ $balance >= 0 ? 'text-green' : 'text-red' }}">
                R$ {{ number_format($balance, 2, ',', '.') }}
            </div>
        </div>
    </div>

    <!-- Categories Summary -->
    <div class="section">
        <div class="section-title">Resumo por Categoria</div>

        <div class="category-table">
            <h4 style="font-size: 10px; margin-bottom: 8px; color: #16A34A; font-weight: bold;">RECEITAS</h4>
            <table>
                <thead>
                    <tr>
                        <th>Categoria</th>
                        <th class="text-right">Total</th>
                        <th class="text-center">Qtd</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($incomeByCategory as $item)
                        <tr>
                            <td>{{ ucfirst(str_replace('_', ' ', $item->category)) }}</td>
                            <td class="text-right text-green">R$ {{ number_format($item->total, 2, ',', '.') }}</td>
                            <td class="text-center">{{ $item->count }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">Nenhuma receita no período</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="category-table">
            <h4 style="font-size: 10px; margin-bottom: 8px; color: #DC2626; font-weight: bold;">DESPESAS</h4>
            <table>
                <thead>
                    <tr>
                        <th>Categoria</th>
                        <th class="text-right">Total</th>
                        <th class="text-center">Qtd</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenseByCategory as $item)
                        <tr>
                            <td>{{ ucfirst(str_replace('_', ' ', $item->category)) }}</td>
                            <td class="text-right text-red">R$ {{ number_format($item->total, 2, ',', '.') }}</td>
                            <td class="text-center">{{ $item->count }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">Nenhuma despesa no período</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Detailed Entries -->
    <div class="section">
        <div class="section-title">Entradas Detalhadas</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 8%;">Data</th>
                    <th style="width: 8%;">Tipo</th>
                    <th style="width: 12%;">Categoria</th>
                    <th style="width: 25%;">Título</th>
                    <th style="width: 10%;" class="text-right">Valor</th>
                    <th style="width: 12%;">Método</th>
                    <th style="width: 15%;">Usuário</th>
                    <th style="width: 10%;">Campanha</th>
                </tr>
            </thead>
            <tbody>
                @forelse($entries as $entry)
                    <tr>
                        <td class="text-center">{{ $entry->entry_date->format('d/m/Y') }}</td>
                        <td class="text-center">
                            <span class="badge {{ $entry->type === 'income' ? 'badge-income' : 'badge-expense' }}">
                                {{ $entry->type === 'income' ? 'Entrada' : 'Saída' }}
                            </span>
                        </td>
                        <td>{{ ucfirst(str_replace('_', ' ', $entry->category)) }}</td>
                        <td>{{ $entry->title }}</td>
                        <td class="text-right {{ $entry->type === 'income' ? 'text-green' : 'text-red' }}">
                            R$ {{ number_format($entry->amount, 2, ',', '.') }}
                        </td>
                        <td>{{ $entry->payment_method ? ucfirst(str_replace('_', ' ', $entry->payment_method)) : '-' }}
                        </td>
                        <td>{{ $entry->user->name ?? '-' }}</td>
                        <td>{{ $entry->campaign->name ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">Nenhuma entrada encontrada no período</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr style="background-color: #F3F4F6; font-weight: bold;">
                    <td colspan="4" class="text-right">TOTAIS:</td>
                    <td class="text-right">R$ {{ number_format($totalIncome, 2, ',', '.') }}</td>
                    <td colspan="3"></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Relatório gerado automaticamente pelo Sistema Vertex CBAV - Tesouraria</p>
        <p>© {{ date('Y') }} Vertex Solutions LTDA - Todos os direitos reservados</p>
    </div>
</body>

</html>

