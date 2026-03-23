<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Termo de Responsabilidade</title>
    <style>
        body { font-family: sans-serif; line-height: 1.6; color: #333; }
        .header { text-align: center; margin-bottom: 40px; border-bottom: 2px solid #ddd; padding-bottom: 20px; }
        .title { font-size: 24px; font-weight: bold; text-transform: uppercase; margin-bottom: 10px; }
        .subtitle { font-size: 14px; color: #666; }
        .content { margin-bottom: 50px; text-align: justify; }
        .asset-box { background: #f9f9f9; border: 1px solid #eee; padding: 15px; margin: 20px 0; border-radius: 5px; }
        .signatures { margin-top: 80px; display: table; width: 100%; }
        .signature-block { display: table-cell; width: 50%; padding: 0 20px; text-align: center; }
        .line { border-top: 1px solid #000; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Termo de Responsabilidade</div>
        <div class="subtitle">{{ $type == 'loan' ? 'Empréstimo de Bens Patrimoniais' : 'Cessão de Uso de Bens Patrimoniais' }}</div>
    </div>

    <div class="content">
        <p>
            Pelo presente instrumento, declaro ter recebido da <strong>Igreja Vertex CBAV</strong>,
            em perfeitas condições de uso e conservação, o bem patrimonial descrito abaixo.
        </p>

        <div class="asset-box">
            <p><strong>Item:</strong> {{ $asset->name }}</p>
            <p><strong>Código:</strong> {{ $asset->code }}</p>
            <p><strong>Categoria:</strong> {{ $asset->category->name }}</p>
            <p><strong>Descrição:</strong> {{ $asset->description }}</p>
        </div>

        <p>
            Comprometo-me a zelar pela guarda e conservação do referido bem, responsabilizando-me por qualquer dano
            ou extravio que venha a ocorrer por mau uso ou negligência, e a devolvê-lo nas mesmas condições
            quando solicitado ou ao término do período estipulado.
        </p>

        <p class="text-center" style="margin-top: 30px;">
            Cidade, {{ \Carbon\Carbon::parse($date)->format('d \d\e F \d\e Y') }}.
        </p>
    </div>

    <div class="signatures">
        <div class="signature-block">
            <div class="line"></div>
            <strong>Administração Vertex CBAV</strong><br>
            Responsável pelo Patrimônio
        </div>
        <div class="signature-block">
            <div class="line"></div>
            <strong>{{ $user->name }}</strong><br>
            CPF: {{ $user->cpf ?? '___.___.___-__' }}<br>
            Responsável
        </div>
    </div>
</body>
</html>

