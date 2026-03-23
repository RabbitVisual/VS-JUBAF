<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Resumo Semanal de Acompanhamentos</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f4f4f4; margin: 0; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
        <div style="background-color: #0f172a; color: #ffffff; padding: 20px; text-align: center;">
            <h1 style="margin: 0; font-size: 24px;">Boletim de Acompanhamento</h1>
            <p style="margin: 5px 0 0; opacity: 0.8; font-size: 14px;">Resumo da semana no JUBAF</p>
        </div>

        <div style="padding: 30px;">
            <p>Graça e Paz!</p>
            <p>Confira o resumo semanal de solicitações e retornos de acompanhamento:</p>

            <div style="display: flex; gap: 10px; margin: 20px 0;">
                <div style="flex: 1; background: #f8fafc; padding: 10px; border-radius: 5px; text-align: center; border: 1px solid #e2e8f0;">
                    <strong style="color: #3b82f6; font-size: 20px;">{{ $newRequestsCount }}</strong>
                    <div style="font-size: 12px; color: #64748b;">Novas solicitações</div>
                </div>
                <div style="flex: 1; background: #f8fafc; padding: 10px; border-radius: 5px; text-align: center; border: 1px solid #e2e8f0;">
                    <strong style="color: #22c55e; font-size: 20px;">{{ $answeredCount }}</strong>
                    <div style="font-size: 12px; color: #64748b;">Acompanhamentos concluídos</div>
                </div>
            </div>

            @if($urgentRequests->isNotEmpty())
                <h3 style="border-bottom: 2px solid #ef4444; padding-bottom: 5px; margin-top: 30px; font-size: 18px; color: #b91c1c;">
                    Alertas prioritários
                </h3>
                <ul style="padding: 0; list-style: none;">
                    @foreach($urgentRequests as $req)
                        <li style="padding: 10px 0; border-bottom: 1px dashed #e2e8f0;">
                            <strong style="color: #333;">{{ $req->title }}</strong>
                            <div style="font-size: 13px; color: #64748b;">
                                "...{{ Str::limit($req->description, 80) }}"
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif

            @if($testimonies->isNotEmpty())
                 <h3 style="border-bottom: 2px solid #22c55e; padding-bottom: 5px; margin-top: 30px; font-size: 18px; color: #15803d;">
                    Registros recentes
                </h3>
                <ul style="padding: 0; list-style: none;">
                    @foreach($testimonies as $bg)
                        <li style="padding: 10px 0; border-bottom: 1px dashed #e2e8f0;">
                            <strong style="color: #333;">{{ $bg->title }}</strong>
                        </li>
                    @endforeach
                </ul>
            @endif

             <div style="text-align: center; margin-top: 30px;">
                <a href="{{ route('memberpanel.notifications.index') }}" style="background-color: #0f172a; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold;">
                    Abrir central de notificações
                </a>
            </div>
        </div>

        <div style="background-color: #f3f4f6; padding: 20px; text-align: center; font-size: 12px; color: #6b7280;">
            <p>Você recebeu este resumo por participar do fluxo de notificações do sistema.</p>
        </div>
    </div>
</body>
</html>
