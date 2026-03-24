<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Ingresso - {{ $event->title }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap');
        body {
            font-family: 'Inter', 'Helvetica', 'Arial', sans-serif;
            color: #1e293b;
            margin: 0;
            padding: 0;
            background-color: #f8fafc;
        }
        .ticket-wrapper {
            max-width: 800px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }
        .header {
            background: #1e3a8a; /* Azul JUBAF */
            color: #ffffff;
            padding: 40px 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0 0 10px 0;
            font-size: 32px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #facc15; /* Amarelo */
        }
        .header p {
            margin: 0;
            font-size: 16px;
            opacity: 0.9;
        }
        .jubaf-logo {
            font-size: 14px;
            font-weight: bold;
            letter-spacing: 3px;
            opacity: 0.7;
            margin-bottom: 20px;
            display: block;
        }
        .content {
            padding: 40px;
        }
        .participant-info {
            background-color: #f1f5f9;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 30px;
        }
        .participant-info h2 {
            margin: 0 0 5px 0;
            font-size: 24px;
            color: #0f172a;
        }
        .participant-info p {
            margin: 0;
            color: #64748b;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .qr-section {
            text-align: center;
            margin: 40px 0;
        }
        .qr-code-box {
            display: inline-block;
            padding: 20px;
            background: #ffffff;
            border: 3px solid #1e3a8a;
            border-radius: 20px;
        }
        .qr-code {
            width: 250px;
            height: 250px;
        }
        .qr-hash {
            font-family: monospace;
            letter-spacing: 3px;
            margin-top: 15px;
            font-size: 18px;
            font-weight: bold;
            color: #334155;
        }
        .details-grid {
            display: table;
            width: 100%;
            margin-top: 20px;
        }
        .details-row {
            display: table-row;
        }
        .details-cell {
            display: table-cell;
            width: 50%;
            padding: 15px 0;
            border-bottom: 1px solid #e2e8f0;
        }
        .label {
            font-weight: bold;
            color: #94a3b8;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .value {
            font-size: 16px;
            font-weight: bold;
            color: #0f172a;
            margin-top: 4px;
        }
        .footer {
            background-color: #0f172a;
            padding: 20px;
            text-align: center;
            color: #94a3b8;
            font-size: 12px;
        }
    </style>
</head>
<body>
    @php
        $igrejaNome = ($registration->user && $registration->user->igreja) ? $registration->user->igreja->nome : 'Igreja Não Informada';
    @endphp
    <div class="ticket-wrapper">
        <div class="header">
            <span class="jubaf-logo">ASSOCIAÇÃO JUBAF</span>
            <h1>{{ $event->title }}</h1>
            <p>{{ $event->start_date->format('d/m/Y \à\s H:i') }} • {{ $event->location ?? 'Local A Definir' }}</p>
        </div>

        <div class="content">
            <div class="participant-info">
                @foreach($registration->participants as $participant)
                    <h2>{{ mb_strtoupper($participant->name) }}</h2>
                    <p>{{ mb_strtoupper($igrejaNome) }}</p>
                @endforeach
            </div>

            <div class="qr-section">
                <div class="qr-code-box">
                    <img src="data:image/png;base64,{{ $qrCode }}" class="qr-code" alt="QR Code">
                </div>
                <div class="qr-hash">{{ mb_strtoupper($registration->ticket_hash ?? 'PENDENTE') }}</div>
            </div>

            <div class="details-grid">
                <div class="details-row">
                    <div class="details-cell">
                        <div class="label">Pedido #</div>
                        <div class="value">{{ strtoupper(substr($registration->uuid, 0, 8)) }}</div>
                    </div>
                    <div class="details-cell">
                        <div class="label">Lote</div>
                        <div class="value">{{ $registration->batch ? $registration->batch->name : 'Padrão' }}</div>
                    </div>
                </div>
            </div>

            <div style="margin-top: 40px; padding: 20px; background: #fffbeb; border-left: 4px solid #f59e0b; border-radius: 0 8px 8px 0;">
                <h3 style="margin: 0 0 10px 0; color: #b45309; font-size: 14px; text-transform: uppercase;">Importante</h3>
                <ul style="margin: 0; padding-left: 20px; color: #92400e; font-size: 13px;">
                    <li>Chegue com 30 minutos de antecedência.</li>
                    <li>Apresente este QR Code na entrada com o brilho no máximo.</li>
                    <li>Este ingresso é nominal e intransferível.</li>
                </ul>
            </div>
        </div>

        <div class="footer">
            Ingresso Oficial - Juventude Batista Feirense (JUBAF) • Gerado em {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>
</body>
</html>
