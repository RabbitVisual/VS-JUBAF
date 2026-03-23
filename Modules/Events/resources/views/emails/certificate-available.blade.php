<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificado disponível</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <p>Olá, <strong>{{ $userName }}</strong>!</p>
    <p>O certificado de participação do evento <strong>{{ $event->title }}</strong> está disponível para download.</p>
    <p><a href="{{ $downloadUrl }}" style="display: inline-block; padding: 12px 24px; background: #4f46e5; color: white; text-decoration: none; border-radius: 8px; font-weight: bold;">Baixar certificado</a></p>
    <p>Este link é pessoal e pode ser acessado a qualquer momento.</p>
    <p style="margin-top: 32px; font-size: 12px; color: #666;">Este é um e-mail automático. Em caso de dúvidas, entre em contato com a administração.</p>
</body>
</html>
