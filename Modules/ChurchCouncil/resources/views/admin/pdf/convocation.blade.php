<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Edital de Convocação - Reunião do Conselho</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #111827;
            line-height: 1.6;
            margin: 0;
            padding: 24px 32px;
        }
        h1 {
            font-size: 18px;
            text-transform: uppercase;
            letter-spacing: .12em;
            margin: 0 0 12px;
            text-align: center;
        }
        h2 {
            font-size: 13px;
            margin: 0 0 10px;
            text-align: center;
        }
        p { margin: 0 0 8px; text-align: justify; }
        .muted { color: #6b7280; font-size: 9px; }
        .content {
            margin-top: 18px;
        }
        .signature {
            margin-top: 32px;
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #9ca3af;
            width: 60%;
            margin: 0 auto 4px;
        }
    </style>
</head>
<body>
@php
    $churchName = $settings['church_name'] ?? config('app.name');
    $councilName = $settings['council_name'] ?? 'Conselho da Igreja';
    $city = \App\Models\Settings::get('church_city', null);
    $state = \App\Models\Settings::get('church_state', null);
    $today = now();
@endphp

<h1>{{ $churchName }}</h1>
<h2>{{ $councilName }}<br><span style="font-size: 11px;">Edital de Convocação</span></h2>

<div class="content">
    <p>
        Pelo presente edital, ficam convocados todos os membros do
        <strong>{{ $councilName }}</strong>
        da <strong>{{ $churchName }}</strong>
        para a reunião {{ $meeting->meeting_type_display }},
        a realizar-se no dia <strong>{{ $meeting->scheduled_date?->format('d/m/Y') }}</strong>,
        às <strong>{{ $meeting->scheduled_date?->format('H:i') }}</strong>
        @if($meeting->location)
            , nas dependências da igreja, em <strong>{{ $meeting->location }}</strong>
        @endif
        , para tratar da seguinte ordem do dia:
    </p>

    @if($meeting->agendas->count() > 0)
        <p>
            @foreach($meeting->agendas as $index => $agenda)
                {{ $index + 1 }}. {{ $agenda->title }}@if($index + 1 < $meeting->agendas->count()); @endif
            @endforeach
        </p>
    @else
        <p>1. Assuntos gerais de interesse da igreja e de sua administração.</p>
    @endif

    <p>
        Outros assuntos pertinentes poderão ser incluídos na pauta, a critério da presidência, observando-se o quórum
        estatutário e os princípios de governo congregacional batista.
    </p>

    <p>
        {{ $city ?? '________________' }}, {{ $today->format('d') }} de
        {{ $today->translatedFormat('F') }} de {{ $today->format('Y') }}.
    </p>
</div>

<div class="signature">
    <div class="signature-line"></div>
    @if($meeting->president && $meeting->president->user)
        <div>{{ $meeting->president->user->name }}</div>
    @else
        <div>Presidente do Conselho</div>
    @endif
    <div class="muted">Presidente do Conselho</div>
</div>

<p class="muted" style="margin-top: 18px; text-align: left;">
    Gerado em {{ now()->format('d/m/Y H:i') }} pelo sistema VertexCBAV &mdash; Módulo ChurchCouncil.
</p>
</body>
</html>

