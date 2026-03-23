<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Comprovante de Contribuição</title>
    <style>
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 11px; color: #333; line-height: 1.4; padding: 20px; }
        .header { text-align: center; margin-bottom: 25px; border-bottom: 2px solid #1E40AF; padding-bottom: 15px; }
        .header h1 { font-size: 18px; color: #1E40AF; margin-bottom: 5px; }
        .member { font-size: 14px; font-weight: bold; margin: 20px 0 10px; }
        .year { font-size: 12px; color: #6B7280; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { border: 1px solid #E5E7EB; padding: 8px 12px; text-align: left; }
        th { background: #F3F4F6; font-weight: 600; }
        .text-right { text-align: right; }
        .total-row { font-weight: bold; background: #EFF6FF; }
        .footer { margin-top: 30px; font-size: 10px; color: #6B7280; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ \App\Models\Settings::get('site_name', 'Igreja') }}</h1>
        <p>Comprovante de Contribuição para Fins de Declaração</p>
    </div>

    <div class="member">{{ $member->name }}</div>
    <div class="year">Ano de referência: {{ $year }}</div>

    <table>
        <thead>
            <tr>
                <th>Categoria</th>
                <th class="text-right">Quantidade</th>
                <th class="text-right">Total (R$)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($byCategory as $categoryName => $data)
            <tr>
                <td>{{ $categoryName }}</td>
                <td class="text-right">{{ $data['count'] }}</td>
                <td class="text-right">{{ number_format($data['total'], 2, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="2" class="text-right">Total geral</td>
                <td class="text-right">R$ {{ number_format($total, 2, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        Documento gerado em {{ now()->format('d/m/Y H:i') }}. Este comprovante não substitui recibos individuais quando exigidos.
    </div>
</body>
</html>
