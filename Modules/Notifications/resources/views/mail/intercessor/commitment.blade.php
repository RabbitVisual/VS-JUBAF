<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Compromisso de Oração</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f4f4f4; margin: 0; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
        <!-- Header -->
        <div style="background-color: #4f46e5; color: #ffffff; padding: 20px; text-align: center;">
            <h1 style="margin: 0; font-size: 24px;">Você não está só!</h1>
        </div>

        <!-- Body -->
        <div style="padding: 30px;">
            <p style="margin-bottom: 20px;">Olá <strong>{{ $requestOwner->name }}</strong>,</p>
            <p>Temos uma boa notícia para fortalecer sua fé. Um intercessor acabou de se comprometer a orar pelo seu pedido:</p>

            <div style="background-color: #eef2ff; border-radius: 8px; padding: 20px; margin: 20px 0; text-align: center;">
                <h3 style="margin: 0 0 5px; color: #3730a3;">"{{ $prayerRequest->title }}"</h3>
                <div style="font-size: 14px; color: #4338ca; margin-top: 10px;">
                     🙌 <strong>{{ $intercessor->name }}</strong> está intercedendo por você agora.
                </div>
            </div>

            <p>Continue firme! A igreja está unida em propósito com você.</p>

            <!-- CTA Button -->
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ route('member.intercessor.room.show', $prayerRequest->id) }}" style="background-color: #4f46e5; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">
                    Ver Mensagens de Apoio
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div style="background-color: #f3f4f6; padding: 20px; text-align: center; font-size: 12px; color: #6b7280;">
            <p style="margin: 0;">VertexCBAV - Ministério de Intercessão</p>
            <p style="margin: 5px 0 0;">"Porque onde estiverem dois ou três reunidos em meu nome..." (Mt 18:20)</p>
        </div>
    </div>
</body>
</html>

