<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Apoio Registrado</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f4f4f4; margin: 0; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
        <div style="background-color: #4f46e5; color: #ffffff; padding: 20px; text-align: center;">
            <h1 style="margin: 0; font-size: 24px;">Você recebeu apoio da liderança</h1>
        </div>

        <div style="padding: 30px;">
            <p style="margin-bottom: 20px;">Olá <strong>{{ $requestOwner->name ?? 'membro' }}</strong>,</p>
            <p>Um líder registrou apoio ao seu alerta e acompanhou a sua solicitação:</p>

            <div style="background-color: #eef2ff; border-radius: 8px; padding: 20px; margin: 20px 0; text-align: center;">
                <h3 style="margin: 0 0 5px; color: #3730a3;">"{{ $prayerRequest->title }}"</h3>
                <div style="font-size: 14px; color: #4338ca; margin-top: 10px;">
                     <strong>{{ $supportAgent->name }}</strong> registrou acompanhamento para você.
                </div>
            </div>

            <p>Continue firme. A igreja permanece ao seu lado.</p>

            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ route('memberpanel.notifications.index') }}" style="background-color: #4f46e5; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">
                    Ver notificações
                </a>
            </div>
        </div>

        <div style="background-color: #f3f4f6; padding: 20px; text-align: center; font-size: 12px; color: #6b7280;">
            <p style="margin: 0;">JUBAF - Central de Notificações</p>
        </div>
    </div>
</body>
</html>
