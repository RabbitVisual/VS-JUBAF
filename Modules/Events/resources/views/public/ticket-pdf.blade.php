<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Ingresso - {{ $event->title }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
            line-height: 1.5;
        }
        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #ddd;
        }
        .header {
            background-color: #0f172a; /* Slate 950 */
            color: white;
            padding: 40px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #f59e0b; /* Amber 500 */
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .content {
            padding: 40px;
        }
        .qr-section {
            text-align: center;
            margin: 30px 0;
            padding: 20px;
            background-color: #f8fafc;
            border: 2px dashed #cbd5e1;
            border-radius: 10px;
        }
        .qr-code {
            width: 250px;
            height: 250px;
        }
        .details {
            margin-top: 30px;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        .row {
            margin-bottom: 15px;
        }
        .label {
            font-weight: bold;
            color: #64748b;
            font-size: 12px;
            text-transform: uppercase;
        }
        .value {
            font-size: 16px;
            font-weight: bold;
            color: #0f172a;
        }
        .footer {
            background-color: #f1f5f9;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #64748b;
        }
    </style>
</head>
<body>
    <div class="container">
        @php
            $bannerUrl = $event->banner_path ? (config('app.url') . '/' . ltrim(Storage::url($event->banner_path), '/')) : null;
        @endphp
        <div class="header" style="{{ $bannerUrl ? 'background-image: url(' . e($bannerUrl) . '); background-size: cover; background-position: center; position: relative;' : '' }}">
            @if($event->banner_path)
                <div style="position: absolute; inset: 0; background: rgba(15, 23, 42, 0.7);"></div>
            @endif
            <div style="position: relative; z-index: 10;">
                <h1>{{ $event->title }}</h1>
                <p>{{ $event->start_date->format('d/m/Y \à\s H:i') }}</p>
            </div>
        </div>

        <div class="content">
            <div style="text-align: center;">
                <p>Olá, <strong>{{ $registration->user?->name ?? $registration->participants->first()?->name ?? 'Participante' }}</strong></p>
                <p>Este é o seu ingresso oficial. Apresente o QR Code na entrada.</p>
            </div>

            <div class="qr-section">
                <img src="data:image/png;base64,{{ $qrCode }}" class="qr-code" alt="QR Code">
                <p style="font-family: monospace; letter-spacing: 2px; margin-top: 10px;">{{ $registration->ticket_hash ?? 'PENDENTE' }}</p>
            </div>

            <div class="details">
                <table width="100%">
                    <tr>
                        <td width="50%">
                            <div class="row">
                                <div class="label">Participantes</div>
                                <div class="value" style="font-size: 14px; font-weight: normal;">
                                    @foreach($registration->participants as $participant)
                                        <div>{{ $participant->name }}</div>
                                    @endforeach
                                </div>
                            </div>
                        </td>
                        <td width="50%">
                            <div class="row">
                                <div class="label">Local</div>
                                <div class="value">{{ $event->location }}</div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td width="50%">
                            <div class="row">
                                <div class="label">Pedido #</div>
                                <div class="value">{{ $registration->uuid }}</div>
                            </div>
                        </td>
                        <td width="50%">
                            <div class="row">
                                <div class="label">Valor Pago</div>
                                <div class="value">R$ {{ number_format($registration->total_amount, 2, ',', '.') }}</div>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>

            <div style="margin-top: 40px;">
                <h3 style="border-bottom: 2px solid #f59e0b; padding-bottom: 10px; display: inline-block; color: #0f172a;">Instruções</h3>
                <ul style="color: #475569;">
                    <li>Chegue com 30 minutos de antecedência.</li>
                    <li>O QR Code é único e só pode ser validado uma vez.</li>
                    <li>Mantenha o brilho da tela do celular no máximo ao apresentar.</li>
                </ul>
            </div>
        </div>

        <div class="footer">
            Gerado em {{ now()->format('d/m/Y H:i') }} • Vertex Events
        </div>
    </div>
</body>
</html>
