<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Ata da Reunião da Diretoria</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #111827;
            line-height: 1.5;
            margin: 0;
            padding: 24px 32px;
        }

        h1,
        h2,
        h3,
        h4 {
            margin: 0 0 6px;
            font-weight: 700;
        }

        h1 {
            font-size: 18px;
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        h2 {
            font-size: 14px;
        }

        p {
            margin: 0 0 6px;
        }

        .header {
            text-align: center;
            margin-bottom: 18px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 8px;
        }

        .header strong {
            display: block;
        }

        .meta-grid {
            width: 100%;
            margin-bottom: 14px;
            border-collapse: collapse;
        }

        .meta-grid td {
            padding: 2px 0;
            vertical-align: top;
            font-size: 10px;
        }

        .meta-label {
            width: 26%;
            font-weight: 600;
            color: #4b5563;
        }

        .section {
            margin-top: 10px;
        }

        .section-title {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #374151;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 4px;
            margin-bottom: 6px;
        }

        .minutes-body {
            margin-top: 4px;
            white-space: pre-line;
            text-align: justify;
        }

        .agenda-list {
            margin: 0;
            padding-left: 16px;
        }

        .agenda-list li {
            margin-bottom: 2px;
        }

        .signatures {
            margin-top: 28px;
            width: 100%;
        }

        .signatures td {
            width: 50%;
            padding-top: 24px;
            text-align: center;
            font-size: 10px;
        }

        .sign-line {
            border-top: 1px solid #9ca3af;
            margin: 0 auto 4px;
            width: 80%;
            height: 2px;
        }

        .muted {
            color: #6b7280;
            font-size: 9px;
        }
    </style>
</head>

<body>
    @php
        $churchName = $settings['church_name'] ?? config('app.name');
        $councilName = $settings['council_name'] ?? 'Diretoria';
        $minutesContent = $latestMinutes?->content ?? ($meeting->minutes ?? '');
    @endphp

    <div class="header">
        <strong>{{ $churchName }}</strong>
        <strong>{{ $councilName }}</strong>
        <div class="muted">Ata da Reunião da Diretoria</div>
    </div>

    <table class="meta-grid">
        <tr>
            <td class="meta-label">Data e horário:</td>
            <td>
                {{ $meeting->scheduled_date?->format('d/m/Y \\à\\s H:i') }}
                @if ($meeting->actual_start_time)
                    &mdash; Início efetivo: {{ $meeting->actual_start_time->format('H:i') }}
                @endif
                @if ($meeting->actual_end_time)
                    &mdash; Término: {{ $meeting->actual_end_time->format('H:i') }}
                @endif
            </td>
        </tr>
        @if ($meeting->location)
            <tr>
                <td class="meta-label">Local:</td>
                <td>{{ $meeting->location }}</td>
            </tr>
        @endif
        @if ($meeting->president && $meeting->president->user)
            <tr>
                <td class="meta-label">Presidente:</td>
                <td>{{ $meeting->president->user->name }}</td>
            </tr>
        @endif
        <tr>
            <td class="meta-label">Tipo de reunião:</td>
            <td>{{ $meeting->meeting_type_display }}</td>
        </tr>
        <tr>
            <td class="meta-label">Quórum presente:</td>
            <td>
                {{ $meeting->quorum_present ?? $meeting->participant_members->count() }}
                conselheiros presentes
            </td>
        </tr>
    </table>

    @if ($meeting->participant_members->count() > 0)
        <div class="section">
            <div class="section-title">Conselheiros presentes</div>
            <p>
                @foreach ($meeting->participant_members as $index => $member)
                    {{ $member->user->name }}@if ($index + 1 < $meeting->participant_members->count())
                        ,
                    @endif
                @endforeach
            </p>
        </div>
    @endif

    @if ($meeting->agendas->count() > 0)
        <div class="section">
            <div class="section-title">Pautas tratadas</div>
            <ol class="agenda-list">
                @foreach ($meeting->agendas as $agenda)
                    <li>
                        <strong>{{ $agenda->title }}</strong>
                        @if ($agenda->decision)
                            &mdash; Decisão: {{ $agenda->decision }}
                        @else
                            &mdash; Status: {{ $agenda->status_display }}
                        @endif
                    </li>
                @endforeach
            </ol>
        </div>
    @endif

    @if ($minutesContent)
        <div class="section">
            <div class="section-title">Ata</div>
            <div class="minutes-body">
                {!! nl2br(e($minutesContent)) !!}
            </div>
        </div>
    @endif

    <table class="signatures">
        <tr>
            <td>
                <div class="sign-line"></div>
                <div>Secretário(a)</div>
            </td>
            <td>
                <div class="sign-line"></div>
                <div>lideranca / Presidente da Diretoria</div>
            </td>
        </tr>
    </table>

    @if (isset($latestMinutes) && $latestMinutes && $latestMinutes->signatures->count() > 0)
        <p class="muted" style="margin-top: 8px;">
            Visto digital por:
            {{ $latestMinutes->signatures->map(fn($s) => $s->user?->name)->filter()->implode(', ') }}.
        </p>
    @endif

    <p class="muted" style="margin-top: 10px;">
        Gerado em {{ now()->format('d/m/Y H:i') }} pelo sistema VertexCBAV &mdash; Módulo ChurchCouncil.
    </p>
</body>

</html>
