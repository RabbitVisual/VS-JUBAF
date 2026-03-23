<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ __('events::messages.badges') }} - {{ $event->title }}</title>
    <style>
        @page {
            margin: 5mm;
            size: A4;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', 'Helvetica', Arial, sans-serif;
            background-color: white;
        }
        .page {
            width: 200mm;
            min-height: 287mm;
            overflow: hidden;
            page-break-after: always;
        }
        .page:last-child {
            page-break-after: auto;
        }
        .badge {
            width: 95mm;
            height: 60mm;
            float: left;
            margin: 5mm 2.5mm;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            text-align: center;
            position: relative;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            overflow: hidden;
        }
        .badge-header {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
            color: white;
            padding: 8px 10px;
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }
        .badge-body {
            padding: 10px;
            height: 38mm;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .participant-name {
            font-size: 14px;
            font-weight: bold;
            color: #0f172a;
            margin: 5px 0;
            line-height: 1.2;
            max-height: 18mm;
            overflow: hidden;
        }
        .role-badge {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            padding: 4px 16px;
            border-radius: 20px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 8px;
        }
        .badge-footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: #f1f5f9;
            padding: 6px;
            font-size: 7px;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
        }
        .badge-id {
            position: absolute;
            top: 35px;
            right: 8px;
            background: rgba(255,255,255,0.9);
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 7px;
            color: #64748b;
            font-weight: 600;
        }
        .empty-message {
            text-align: center;
            padding: 40px;
            color: #64748b;
        }
        .empty-message h2 {
            font-size: 16px;
            margin-bottom: 10px;
            color: #1e3a8a;
        }
        .empty-message p {
            font-size: 11px;
            margin-bottom: 20px;
        }
        .sample-label {
            position: absolute;
            top: 35px;
            left: 8px;
            background: #dc2626;
            color: white;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 6px;
            font-weight: bold;
            text-transform: uppercase;
        }

        /* Custom template wrapper */
        .custom-badge-wrapper {
            width: 95mm;
            height: 60mm;
            float: left;
            margin: 5mm 2.5mm;
            overflow: hidden;
        }
    </style>
</head>
<body>
    @php
        $participants = $registrations->pluck('participants')->flatten();
        $perPage = $badgesPerPage ?? 8;
        $hasParticipants = $participants->count() > 0;
        $eventDate = $event->start_date->format('d/m/Y');
        $eventLocation = $event->location ?? config('app.name');
        $eventTitle = Str::limit($event->title, 45);
        $participantLabel = __('events::messages.participant');
    @endphp

    @if(!$hasParticipants)
        {{-- Página com modelo de exemplo quando não há participantes --}}
        <div class="page">
            <div class="empty-message">
                <h2>{{ __('events::messages.sample_badge') }}</h2>
                <p>{{ __('events::messages.no_participants_for_badges') }}</p>
                <p>Abaixo está um modelo de como os crachás serão impressos:</p>
            </div>

            {{-- Crachás de exemplo --}}
            @php
                $sampleNames = ['João da Silva', 'Maria Santos', 'Pedro Oliveira', 'Ana Costa', 'Lucas Pereira', 'Julia Souza', 'Carlos Mendes', 'Beatriz Lima'];
            @endphp

            @if(!empty($customTemplate))
                {{-- Template customizado --}}
                @foreach(array_slice($sampleNames, 0, $perPage) as $index => $sampleName)
                <div class="custom-badge-wrapper">
                    {!! str_replace(
                        ['{{ nome }}', '{{ evento }}', '{{ funcao }}', '{{ data }}', '{{ local }}', '{{nome}}', '{{evento}}', '{{funcao}}', '{{data}}', '{{local}}'],
                        [$sampleName, $eventTitle, $participantLabel, $eventDate, Str::limit($eventLocation, 25)],
                        $customTemplate
                    ) !!}
                    <div class="sample-label" style="position: absolute; top: 5px; left: 5px;">EXEMPLO</div>
                </div>
                @endforeach
            @else
                {{-- Template padrão --}}
                @foreach(array_slice($sampleNames, 0, $perPage) as $index => $sampleName)
                <div class="badge">
                    <div class="badge-header">
                        {{ $eventTitle }}
                    </div>
                    <div class="sample-label">EXEMPLO</div>
                    <div class="badge-id">#{{ str_pad($index + 1, 3, '0', STR_PAD_LEFT) }}</div>
                    <div class="badge-body">
                        <div class="participant-name">
                            {{ $sampleName }}
                        </div>
                        <div class="role-badge">
                            {{ $participantLabel }}
                        </div>
                    </div>
                    <div class="badge-footer">
                        {{ $eventDate }} • {{ Str::limit($eventLocation, 25) }}
                    </div>
                </div>
                @endforeach
            @endif
        </div>
    @else
        {{-- Crachás reais dos participantes --}}
        @foreach($participants->chunk($perPage) as $pageIndex => $pageParticipants)
        <div class="page">
            @if(!empty($customTemplate))
                {{-- Template customizado --}}
                @foreach($pageParticipants as $participantIndex => $participant)
                <div class="custom-badge-wrapper">
                    {!! str_replace(
                        ['{{ nome }}', '{{ evento }}', '{{ funcao }}', '{{ data }}', '{{ local }}', '{{nome}}', '{{evento}}', '{{funcao}}', '{{data}}', '{{local}}'],
                        [Str::limit($participant->name, 35), $eventTitle, $participantLabel, $eventDate, Str::limit($eventLocation, 25)],
                        $customTemplate
                    ) !!}
                </div>
                @endforeach
            @else
                {{-- Template padrão --}}
                @foreach($pageParticipants as $participantIndex => $participant)
                <div class="badge">
                    <div class="badge-header">
                        {{ $eventTitle }}
                    </div>
                    <div class="badge-id">#{{ str_pad(($pageIndex * $perPage) + $participantIndex + 1, 3, '0', STR_PAD_LEFT) }}</div>
                    <div class="badge-body">
                        <div class="participant-name">
                            {{ Str::limit($participant->name, 35) }}
                        </div>
                        <div class="role-badge">
                            {{ $participantLabel }}
                        </div>
                    </div>
                    <div class="badge-footer">
                        {{ $eventDate }} • {{ Str::limit($eventLocation, 25) }}
                    </div>
                </div>
                @endforeach
            @endif
        </div>
        @endforeach
    @endif
</body>
</html>
