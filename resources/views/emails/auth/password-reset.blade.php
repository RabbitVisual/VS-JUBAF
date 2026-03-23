<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperação de Senha</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7fa; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 16px; overflow: hidden; shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .header { background-color: #1e293b; padding: 40px; text-align: center; }
        .content { padding: 40px; color: #334155; line-height: 1.6; }
        .footer { background-color: #f8fafc; padding: 20px; text-align: center; color: #64748b; font-size: 12px; }
        .button { display: inline-block; padding: 14px 32px; background-color: #2563eb; color: #ffffff !important; text-decoration: none; border-radius: 12px; font-weight: bold; margin-top: 25px; box-shadow: 0 4px 6px rgba(37,99,235,0.2); }
        .logo { max-height: 50px; margin-bottom: 20px; }
        h1 { color: #f8fafc; margin: 0; font-size: 24px; font-weight: 700; }
        p { margin-bottom: 20px; }
        .divider { border-top: 1px solid #e2e8f0; margin: 30px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            @php $logo = \App\Models\Settings::get('logo_icon_path', 'storage/image/logo_icon.png'); @endphp
            <img src="{{ asset($logo) }}" alt="Logo" class="logo">
            <h1>{{ \App\Models\Settings::get('recovery_email_title', 'Recuperação de Senha') }}</h1>
        </div>
        <div class="content">
            <p>Olá, <strong>{{ $user->first_name }}</strong>!</p>
            <p>Recebemos uma solicitação para redefinir a senha da sua conta no sistema <strong>{{ \App\Models\Settings::get('site_name', 'Vertex CBAV') }}</strong>.</p>
            <p>Para prosseguir com a alteração, clique no botão abaixo:</p>

            <div style="text-align: center;">
                <a href="{{ $url }}" class="button">Redefinir Minha Senha</a>
            </div>

            <p style="margin-top: 30px; font-size: 14px; color: #64748b;">Este link de recuperação expirará em 60 minutos. Se você não solicitou esta alteração, nenhuma ação adicional é necessária.</p>

            <div class="divider"></div>

            <p style="font-size: 12px; color: #94a3b8;">Se estiver com problemas para clicar no botão, copie e cole a URL abaixo em seu navegador:</p>
            <p style="font-size: 11px; color: #94a3b8; word-break: break-all;">{{ $url }}</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ \App\Models\Settings::get('site_name', 'Igreja Batista Avenida') }}. Todos os direitos reservados.</p>
            <p>{{ \App\Models\Settings::get('recovery_email_footer', 'Powered by Vertex Solutions LTDA') }}</p>
        </div>
    </div>
</body>
</html>
