<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Imprimir Etiquetas</title>
    <style>
        body { font-family: sans-serif; margin: 0; padding: 20px; }
        .label-grid { display: flex; flex-wrap: wrap; gap: 10px; }
        .label-item {
            width: 300px;
            height: 150px;
            border: 1px dashed #ccc;
            padding: 10px;
            box-sizing: border-box;
            display: flex;
            align-items: center;
            page-break-inside: avoid;
        }
        .qr-code { margin-right: 15px; }
        .info { flex: 1; overflow: hidden; }
        .title { font-weight: bold; font-size: 14px; margin-bottom: 5px; }
        .code { font-family: monospace; font-size: 16px; font-weight: bold; }
        .meta { font-size: 10px; color: #666; margin-top: 5px; }

        @media print {
            body { padding: 0; }
            .label-item { border: none; outline: 1px dashed #eee; } /* Visual aid for cutting if needed, but usually pre-cut */
        }
    </style>
</head>
<body>
    <div class="label-grid">
        @foreach ($assets as $asset)
            <div class="label-item">
                <div class="qr-code">
                    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(80)->generate(route('assets.admin.assets.show', $asset->id)) !!}
                </div>
                <div class="info">
                    <div class="title">{{ Str::limit($asset->name, 40) }}</div>
                    <div class="code">{{ $asset->code }}</div>
                    <div class="meta">Vertex CBAV Patrimônio</div>
                </div>
            </div>
        @endforeach
    </div>
    <script>
        window.print();
    </script>
</body>
</html>

