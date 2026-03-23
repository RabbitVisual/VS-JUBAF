<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscrição Confirmada</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f9f9f9;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .success-icon {
            font-size: 48px;
            margin-bottom: 20px;
        }
        .event-details {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .participant-list {
            margin-top: 20px;
        }
        .participant-item {
            background: white;
            padding: 15px;
            margin: 10px 0;
            border-left: 4px solid #667eea;
            border-radius: 4px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #666;
            font-size: 12px;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        @if($event->banner_path)
            <img src="{{ url(Storage::url($event->banner_path)) }}" alt="{{ $event->title }}" style="width: 100%; height: auto; border-radius: 10px 10px 0 0; display: block; margin-bottom: 20px;">
        @endif
        <div class="success-icon">✓</div>
        <h1>{{ __('events::messages.registration_confirmed_title') ?? 'Inscrição Confirmada!' }}</h1>
    </div>

    <div class="content">
        <p>{{ __('events::messages.hello') ?? 'Olá' }}, <strong>{{ $userName }}</strong>!</p>

        <p>{{ __('events::messages.registration_success_email_msg', ['event' => $event->title]) ?? "Sua inscrição para o evento {$event->title} foi confirmada com sucesso!" }}</p>

        <div class="event-details">
            <h2>{{ __('events::messages.event_details') ?? 'Detalhes do Evento' }}</h2>
            <p><strong>{{ __('events::messages.event') }}:</strong> {{ $event->title }}</p>
            <p><strong>{{ __('events::messages.start_date') }}:</strong> {{ $event->start_date->format('d/m/Y H:i') }}</p>
            @if($event->end_date)
            <p><strong>{{ __('events::messages.end_date') }}:</strong> {{ $event->end_date->format('d/m/Y H:i') }}</p>
            @endif
            @if($event->location)
            <p><strong>{{ __('events::messages.location') }}:</strong> {{ $event->location }}</p>
            @endif
            <p><strong>{{ __('events::messages.total_amount_paid') ?? 'Valor Total Pago' }}:</strong> R$ {{ $totalAmount }}</p>
            <p><strong>{{ __('events::messages.registration_number') ?? 'Número da Inscrição' }}:</strong> #{{ $registration->id }}</p>
        </div>

        <div class="participant-list">
            <h3>{{ __('events::messages.registered_participants') ?? 'Participantes Inscritos:' }}</h3>
            @foreach($participants as $participant)
            <div class="participant-item">
                <strong>{{ $participant->name }}</strong><br>
                {{ __('events::messages.email') }}: {{ $participant->email }}<br>
                @if($participant->document)
                {{ __('events::messages.document') }}: {{ $participant->document }}<br>
                @endif
            </div>
            @endforeach
        </div>

        <div style="text-align: center; margin-top: 24px;">
            @if(!empty($registration->uuid))
            <a href="{{ route('events.public.ticket.download', $registration->uuid) }}" class="button" style="background: #0d9488;">
                {{ __('events::messages.download_ticket') ?? 'Baixar ingresso' }}
            </a>
            @endif
            @if($userId ?? null)
            <a href="{{ route('memberpanel.events.show-registration', $registration->id) }}" class="button" style="margin-left: 10px;">
                {{ __('events::messages.view_registration_details') ?? 'Ver Detalhes da Inscrição' }}
            </a>
            @endif
        </div>

        <div class="footer">
            <p>{{ __('events::messages.automatic_email_msg') ?? 'Este é um email automático, por favor não responda.' }}</p>
            <p>{{ __('events::messages.contact_admin_msg') ?? 'Em caso de dúvidas, entre em contato com a administração.' }}</p>
        </div>
    </div>
</body>
</html>

