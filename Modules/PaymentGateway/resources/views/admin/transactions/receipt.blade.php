<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Comprovante — {{ $payment->transaction_id }}</title>
    <style>
        body { font-family: system-ui, 'Segoe UI', sans-serif; max-width: 600px; margin: 2rem auto; padding: 1rem; color: #1f2937; }
        .receipt { border: 1px solid #e5e7eb; border-radius: 12px; padding: 1.75rem; }
        .receipt-header { text-align: center; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid #f3f4f6; }
        .receipt-header h1 { font-size: 1.25rem; margin: 0 0 0.25rem 0; }
        .receipt-header .sub { font-size: 0.8rem; color: #6b7280; }
        .thanks { font-size: 0.85rem; color: #4b5563; margin-top: 1rem; padding-top: 1rem; border-top: 1px dashed #e5e7eb; text-align: center; }
        .meta { font-size: 0.75rem; color: #6b7280; margin-bottom: 1rem; }
        table { width: 100%; border-collapse: collapse; font-size: 0.875rem; }
        td { padding: 0.5rem 0; border-bottom: 1px solid #f3f4f6; }
        td:first-child { color: #6b7280; width: 38%; }
        .amount { font-size: 1.5rem; font-weight: 700; }
        .audit { margin-top: 1.25rem; font-size: 0.75rem; color: #6b7280; }
        .audit-title { font-weight: 600; margin-bottom: 0.35rem; }
        .audit ul { margin: 0; padding-left: 1.25rem; }
        .audit li { margin: 0.2rem 0; }
        .no-print { margin-bottom: 1rem; }
        .btn-print { padding: 0.5rem 1rem; background: #4f46e5; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 0.875rem; }
        .btn-print:hover { background: #4338ca; }
        @media print { body { margin: 0; } .no-print { display: none !important; } .receipt { box-shadow: none; } }
    </style>
</head>
<body>
    <div class="no-print">
        <button type="button" onclick="window.print()" class="btn-print">Imprimir comprovante</button>
    </div>
    <div class="receipt">
        <div class="receipt-header">
            <h1>Comprovante de pagamento</h1>
            <p class="sub">{{ \App\Models\Settings::get('site_name', config('app.name')) }}</p>
        </div>
        <div class="meta">Documento gerado em {{ now()->format('d/m/Y H:i') }} · ID {{ $payment->transaction_id }}</div>
        <table>
            <tr><td>Tipo</td><td>{{ match($payment->payment_type ?? '') { 'donation' => 'Doação', 'event_registration' => 'Inscrição em evento', 'offering' => 'Oferta', 'ministry_donation' => 'Doação para ministério', 'campaign' => 'Campanha', default => $payment->payment_type ?? '—' } }}</td></tr>
            <tr><td>Transação</td><td><strong>{{ $payment->transaction_id }}</strong></td></tr>
            <tr><td>Valor</td><td class="amount">R$ {{ number_format($payment->amount, 2, ',', '.') }}</td></tr>
            <tr><td>Gateway</td><td>{{ $payment->gateway->display_name ?? '—' }}</td></tr>
            <tr><td>Método</td><td>{{ $payment->payment_method ?? '—' }}</td></tr>
            <tr><td>Status</td><td>{{ $payment->status }}</td></tr>
            <tr><td>Pagador</td><td>{{ $payment->payer_name ?? 'Anônimo' }}</td></tr>
            <tr><td>E-mail</td><td>{{ $payment->payer_email ?? '—' }}</td></tr>
            <tr><td>Data</td><td>{{ $payment->created_at->format('d/m/Y H:i') }}</td></tr>
            @if($payment->paid_at)
            <tr><td>Confirmado em</td><td>{{ $payment->paid_at->format('d/m/Y H:i') }}</td></tr>
            @endif
            @if($payment->description)
            <tr><td>Descrição</td><td>{{ $payment->description }}</td></tr>
            @endif
        </table>
        @if($payment->auditLogs->isNotEmpty())
        <div class="audit">
            <div class="audit-title">Histórico de status (auditoria)</div>
            <ul>
                @foreach($payment->auditLogs as $log)
                    <li>{{ $log->from_status ?? '—' }} → {{ $log->to_status }} ({{ $log->source }}) — {{ $log->created_at->format('d/m/Y H:i') }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        <p class="thanks">Este comprovante atesta o registro do pagamento. Guarde-o para sua documentação.</p>
    </div>
</body>
</html>
