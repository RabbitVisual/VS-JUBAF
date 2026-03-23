<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Comprovante — {{ $event->title }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; margin: 0; padding: 24px; line-height: 1.5; }
        .container { max-width: 700px; margin: 0 auto; }
        h1 { font-size: 20px; color: #0f172a; border-bottom: 2px solid #f59e0b; padding-bottom: 8px; margin-bottom: 24px; }
        .section { margin-bottom: 24px; }
        .row { display: flex; justify-content: space-between; margin-bottom: 12px; }
        .label { font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: bold; }
        .value { font-size: 14px; font-weight: bold; color: #0f172a; }
        .footer { margin-top: 32px; padding-top: 16px; border-top: 1px solid #e2e8f0; font-size: 11px; color: #64748b; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Comprovante de Inscrição / Pagamento</h1>

        <div class="section">
            <div class="row">
                <span class="label">Evento</span>
                <span class="value">{{ $event->title }}</span>
            </div>
            <div class="row">
                <span class="label">Data do evento</span>
                <span class="value">{{ $event->start_date->format('d/m/Y H:i') }}</span>
            </div>
            <div class="row">
                <span class="label">Local</span>
                <span class="value">{{ $event->location ?? '—' }}</span>
            </div>
        </div>

        <div class="section">
            <div class="row">
                <span class="label">Número da inscrição</span>
                <span class="value">{{ $registration->uuid ?? '#'.$registration->id }}</span>
            </div>
            <div class="row">
                <span class="label">Valor pago</span>
                <span class="value">R$ {{ number_format($registration->total_amount, 2, ',', '.') }}</span>
            </div>
            <div class="row">
                <span class="label">Data do pagamento / confirmação</span>
                <span class="value">{{ ($registration->paid_at ?? $registration->updated_at)->format('d/m/Y H:i') }}</span>
            </div>
            @if($registration->payment_reference)
            <div class="row">
                <span class="label">Referência / Transação</span>
                <span class="value">{{ $registration->payment_reference }}</span>
            </div>
            @endif
        </div>

        <div class="section">
            <div class="label" style="margin-bottom: 8px;">Participante(s)</div>
            @foreach($registration->participants as $p)
                <div class="value" style="font-size: 13px; font-weight: normal; margin-bottom: 4px;">{{ $p->name }} — {{ $p->email }}</div>
            @endforeach
        </div>

        <div class="footer">
            Gerado em {{ now()->format('d/m/Y H:i') }} · Vertex Events
        </div>
    </div>
</body>
</html>
