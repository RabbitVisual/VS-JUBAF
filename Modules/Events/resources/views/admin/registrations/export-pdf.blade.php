<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Lista de Presença - {{ $event->title }}</title>
    <style>
        @page { margin: 10mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 9px;
            color: #1f2937;
            line-height: 1.4;
        }
        .header {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
            color: white;
            padding: 20px;
            margin: -10mm -10mm 15px -10mm;
            text-align: center;
        }
        .header h1 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header .subtitle {
            font-size: 11px;
            opacity: 0.9;
        }
        .info-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 4px solid #f59e0b;
            padding: 12px 15px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .info-box p {
            margin: 3px 0;
            font-size: 9px;
        }
        .info-box strong {
            color: #0f172a;
        }
        .stats-row {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        .stat-card {
            display: table-cell;
            width: 25%;
            text-align: center;
            padding: 12px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
        }
        .stat-card:first-child { border-radius: 6px 0 0 6px; }
        .stat-card:last-child { border-radius: 0 6px 6px 0; }
        .stat-card .value {
            font-size: 18px;
            font-weight: bold;
            color: #0f172a;
        }
        .stat-card .label {
            font-size: 8px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 600;
        }
        .stat-card.income .value { color: #16a34a; }
        .section-title {
            background: #0f172a;
            color: white;
            padding: 8px 12px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-radius: 4px;
            margin: 15px 0 10px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        th {
            background: #f1f5f9;
            color: #334155;
            padding: 8px 6px;
            text-align: left;
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
            border-bottom: 2px solid #cbd5e1;
        }
        td {
            padding: 6px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 8px;
            vertical-align: middle;
        }
        tr:nth-child(even) { background: #fafafa; }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 7px;
            font-weight: 600;
        }
        .badge-confirmed { background: #dcfce7; color: #166534; }
        .badge-pending { background: #fef3c7; color: #92400e; }
        .badge-cancelled { background: #fee2e2; color: #991b1b; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 2px solid #e2e8f0;
            text-align: center;
            font-size: 7px;
            color: #64748b;
        }
        .signature-area {
            margin-top: 40px;
            display: table;
            width: 100%;
        }
        .signature-block {
            display: table-cell;
            width: 33%;
            text-align: center;
            padding: 0 15px;
        }
        .signature-line {
            border-top: 1px solid #333;
            margin-bottom: 5px;
        }
        .signature-label {
            font-size: 8px;
            color: #64748b;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $event->title }}</h1>
        <div class="subtitle">Lista de Presença</div>
    </div>

    <div class="info-box">
        <p><strong>Data:</strong> {{ $event->start_date->format('d/m/Y \à\s H:i') }}</p>
        <p><strong>Local:</strong> {{ $event->location ?? __('events::messages.not_informed') }}</p>
        <p><strong>Gerado em:</strong> {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="stats-row">
        <div class="stat-card">
            <div class="value">{{ $registrations->count() }}</div>
            <div class="label">Inscrições</div>
        </div>
        <div class="stat-card">
            <div class="value">{{ $registrations->sum(fn($r) => $r->participants->count()) }}</div>
            <div class="label">Participantes</div>
        </div>
        <div class="stat-card">
            <div class="value">{{ $registrations->where('status', 'confirmed')->count() }}</div>
            <div class="label">Confirmados</div>
        </div>
        <div class="stat-card income">
            <div class="value">R$ {{ number_format($registrations->where('status', 'confirmed')->sum('total_amount'), 2, ',', '.') }}</div>
            <div class="label">Arrecadado</div>
        </div>
    </div>

    <div class="section-title">Inscrições</div>
    <table>
        <thead>
            <tr>
                <th style="width: 5%">#</th>
                <th style="width: 25%">Responsável</th>
                <th style="width: 25%">E-mail</th>
                <th style="width: 12%" class="text-center">Status</th>
                <th style="width: 10%" class="text-right">Valor</th>
                <th style="width: 8%" class="text-center">Partic.</th>
                <th style="width: 15%">Data Inscr.</th>
            </tr>
        </thead>
        <tbody>
            @foreach($registrations as $registration)
            <tr>
                <td>{{ $registration->id }}</td>
                <td>{{ $registration->user->name ?? __('events::messages.visitor') }}</td>
                <td>{{ $registration->user->email ?? 'N/A' }}</td>
                <td class="text-center">
                    @php
                        $badgeClass = match($registration->status) {
                            'confirmed' => 'badge-confirmed',
                            'pending', 'pending_payment' => 'badge-pending',
                            'cancelled', 'expired' => 'badge-cancelled',
                            default => 'badge-pending'
                        };
                    @endphp
                    <span class="badge {{ $badgeClass }}">{{ $registration->status_display }}</span>
                </td>
                <td class="text-right">R$ {{ number_format($registration->total_amount, 2, ',', '.') }}</td>
                <td class="text-center">{{ $registration->participants->count() }}</td>
                <td>{{ $registration->created_at->format('d/m/Y H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">Participantes</div>
    <table>
        <thead>
            <tr>
                <th style="width: 25%">Nome</th>
                <th style="width: 20%">E-mail</th>
                <th style="width: 12%">Nascimento</th>
                <th style="width: 8%" class="text-center">Idade</th>
                <th style="width: 15%">Documento</th>
                <th style="width: 12%">Telefone</th>
                <th style="width: 8%" class="text-center">Inscr.</th>
            </tr>
        </thead>
        <tbody>
            @foreach($registrations as $registration)
                @foreach($registration->participants as $participant)
                <tr>
                    <td>{{ $participant->name }}</td>
                    <td>{{ $participant->email ?? '-' }}</td>
                    <td>{{ $participant->birth_date ? $participant->birth_date->format('d/m/Y') : '-' }}</td>
                    <td class="text-center">{{ $participant->birth_date ? \Carbon\Carbon::parse($participant->birth_date)->age : '-' }}</td>
                    <td>{{ $participant->document ?? '-' }}</td>
                    <td>{{ $participant->phone ?? '-' }}</td>
                    <td class="text-center">#{{ $registration->id }}</td>
                </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>

    <div class="signature-area">
        <div class="signature-block">
            <div class="signature-line"></div>
            <div class="signature-label">Responsável pelo Evento</div>
        </div>
        <div class="signature-block">
            <div class="signature-line"></div>
            <div class="signature-label">Testemunha</div>
        </div>
        <div class="signature-block">
            <div class="signature-line"></div>
            <div class="signature-label">Data: ___/___/______</div>
        </div>
    </div>

    <div class="footer">
        <p>Documento gerado pelo Sistema Vertex CBAV • {{ now()->format('d/m/Y H:i') }}</p>
        <p>© {{ date('Y') }} Vertex Solutions - Todos os direitos reservados</p>
    </div>
</body>
</html>
