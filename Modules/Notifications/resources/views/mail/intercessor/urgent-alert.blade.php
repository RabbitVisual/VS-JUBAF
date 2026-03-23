<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pedido de Oração Urgente</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f4f4f4; margin: 0; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
        <!-- Header -->
        <div style="background-color: #dc2626; color: #ffffff; padding: 20px; text-align: center;">
            <h1 style="margin: 0; font-size: 24px; text-transform: uppercase;">Alerta de Oração</h1>
            <p style="margin: 5px 0 0; font-size: 14px; opacity: 0.9;">Urgência: {{ ucfirst($request->urgency_level) }}</p>
        </div>

        <!-- Body -->
        <div style="padding: 30px;">
            <p style="margin-bottom: 20px;">Graça e Paz, Intercessor(a).</p>
            <p>Um novo pedido de oração urgente foi cadastrado e precisa da sua cobertura espiritual.</p>

            <div style="background-color: #fef2f2; border-left: 4px solid #dc2626; padding: 15px; margin: 20px 0;">
                <h2 style="margin: 0 0 10px; color: #991b1b; font-size: 18px;">{{ $request->title }}</h2>
                <div style="color: #4b5563; font-style: italic;">
                    "{{ Str::limit($request->description, 200) }}"
                </div>
                <div style="margin-top: 10px; font-size: 12px; color: #6b7280;">
                    Solicitado por: <strong>{{ $request->is_anonymous ? 'Anônimo' : $request->user->name }}</strong>
                </div>
            </div>

            <p>Sua oração faz a diferença! Clique abaixo para ver os detalhes completos e registrar seu compromisso de oração.</p>

            <!-- CTA Button -->
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ route('member.intercessor.room.show', $request->id) }}" style="background-color: #dc2626; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">
                    Ir para Sala de Oração
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div style="background-color: #f3f4f6; padding: 20px; text-align: center; font-size: 12px; color: #6b7280;">
            <p style="margin: 0;">VertexCBAV - Ministério de Intercessão</p>
            <p style="margin: 5px 0 0;">"Orai sem cessar." (1 Ts 5:17)</p>
        </div>
    </div>
</body>
</html>

