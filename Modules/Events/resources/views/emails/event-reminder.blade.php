<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lembrete do evento</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <p>Olá, <strong>{{ $userName }}</strong>!</p>
    <p>Este é um lembrete: o evento <strong>{{ $event->title }}</strong> acontece <strong>amanhã</strong>.</p>
    <p><strong>Data e horário:</strong> {{ $event->start_date->format('d/m/Y \à\s H:i') }}</p>
    @if($event->location)
    <p><strong>Local:</strong> {{ $event->location }}</p>
    @endif
    @if($ticketUrl)
    <p><a href="{{ $ticketUrl }}" style="display: inline-block; padding: 12px 24px; background: #4f46e5; color: white; text-decoration: none; border-radius: 8px; font-weight: bold;">Baixar ingresso</a></p>
    @endif
    <p style="margin-top: 24px; font-size: 12px; color: #666;">Até lá!</p>
</body>
</html>
