<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibo de Entrega - VertexCBAV</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; padding: 20px; color: #333; line-height: 1.5; }
        .container { max-width: 800px; margin: 0 auto; border: 1px solid #ddd; padding: 40px; }
        .header { text-align: center; margin-bottom: 40px; border-bottom: 2px solid #eee; padding-bottom: 20px; }
        .logo { font-size: 24px; font-weight: bold; color: #444; }
        .title { font-size: 18px; margin-top: 10px; font-weight: normal; color: #666; }
        .content { margin-bottom: 40px; }
        .row { display: flex; justify-content: space-between; margin-bottom: 15px; border-bottom: 1px solid #f9f9f9; padding-bottom: 5px; }
        .label { font-weight: bold; width: 150px; color: #555; }
        .value { flex-grow: 1; }
        .footer { text-align: center; font-size: 12px; color: #999; margin-top: 60px; }
        .signatures { display: flex; justify-content: space-between; margin-top: 80px; }
        .signature-line { width: 40%; border-top: 1px solid #333; text-align: center; padding-top: 10px; font-size: 14px; }

        @media print {
            body { padding: 0; }
            .container { border: none; padding: 0; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="container">
        <div class="header">
            <div class="logo">VertexCBAV</div>
            <div class="title">Recibo de Assistência Social</div>
        </div>

        <div class="content">
            <div class="row">
                <span class="label">Protocolo:</span>
                <span class="value">#{{ str_pad($assistance->id, 6, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div class="row">
                <span class="label">Data de Entrega:</span>
                <span class="value">{{ $assistance->registered_at->format('d/m/Y') }}</span>
            </div>
            <div class="row">
                <span class="label">Beneficiário:</span>
                <span class="value">{{ $assistance->beneficiary->full_name }}</span>
            </div>
             <div class="row">
                <span class="label">Contato:</span>
                <span class="value">{{ $assistance->beneficiary->phone ?? 'Não informado' }}</span>
            </div>
            <br>
            <div class="row" style="background-color: #f5f5f5; padding: 10px; border-radius: 5px;">
                <span class="label">Item Entregue:</span>
                <span class="value" style="font-weight: bold;">
                    @if($assistance->kit)
                        KIT: {{ $assistance->kit->name }} ({{ number_format($assistance->quantity, 0) }} un)
                    @elseif($assistance->pantryItem)
                        ITEM: {{ $assistance->pantryItem->name }} ({{ number_format($assistance->quantity, 2, ',', '.') }} {{ $assistance->pantryItem->unit }})
                    @else
                        Outros
                    @endif
                </span>
            </div>

            @if($assistance->notes)
            <br>
            <div class="row">
                <span class="label">Observações:</span>
                <span class="value">{{ $assistance->notes }}</span>
            </div>
            @endif
        </div>

        <div class="signatures">
            <div class="signature-line">
                Responsável (Igreja)
            </div>
            <div class="signature-line">
                Beneficiário
            </div>
        </div>

        <div class="footer">
            <p>Este comprovante atesta o recebimento dos itens acima descritos para fins de assistência social.</p>
            <p>Gerado em {{ date('d/m/Y H:i') }}</p>
        </div>
    </div>
</body>
</html>

