<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $plan->title }} - Agenda de Leitura</title>
    <!-- Fonts (Local) -->
    @preloadFonts
    <!-- Font Awesome Pro -->
    <link href="{{ asset('vendor/fontawesome-pro/css/all.css') }}" rel="stylesheet">
    <style>
        :root {
            --primary: #0f172a;
            --secondary: #64748b;
            --accent: #3b82f6;
            --border: #e2e8f0;
            --bg-even: #f8fafc;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #e2e8f0; /* Darker bg for screen view to pop the paper */
            margin: 0;
            padding: 40px;
            display: flex;
            justify-content: center;
            min-height: 100vh;
        }

        /* Paper Simulation for Screen */
        .page {
            background: white;
            width: 297mm; /* A4 Landscape width */
            min-height: 210mm; /* At least one page, but allow growth */
            height: auto;
            padding: 15mm;
            box-shadow: 0 20px 40px -10px rgba(0,0,0,0.2);
            position: relative;
            display: flex;
            flex-direction: column;
            margin-bottom: 40px;
        }

        /* Header */
        header {
            text-align: center;
            margin-bottom: 10mm;
            border-bottom: 2px solid var(--primary);
            padding-bottom: 5mm;
        }

        h1 {
            font-size: 18pt;
            text-transform: uppercase;
            color: var(--primary);
            margin: 0;
            letter-spacing: -0.5px;
        }

        .subtitle {
            font-size: 9pt;
            color: var(--secondary);
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-top: 2mm;
            font-weight: 600;
        }

        /* Content Grid */
        .content-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr); /* 4 Columns */
            gap: 5mm;
            width: 100%;
        }

        /* Item Reference */
        .reading-card {
            background: white;
            border: 1px solid #cbd5e1;
            padding: 3mm;
            break-inside: avoid; /* Critical: Do not split a card across pages */
            page-break-inside: avoid;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .reading-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid var(--primary);
            padding-bottom: 2px;
            margin-bottom: 2px;
        }

        .day-label {
            font-size: 8pt;
            font-weight: 800;
            color: var(--primary);
            text-transform: uppercase;
        }

        .reading-content {
            font-size: 8pt;
            color: #334155;
            flex: 1;
        }

        .reading-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2px;
            border-bottom: 1px dashed #f1f5f9;
            padding-bottom: 1px;
        }

        .reading-row:last-child {
            border-bottom: none;
        }

        .check-box {
            width: 12px;
            height: 12px;
            border: 1px solid #94a3b8;
            background: white;
            border-radius: 2px;
        }

        /* Footer */
        footer {
            margin-top: auto;
            border-top: 1px solid var(--border);
            padding-top: 3mm;
            text-align: center;
            font-size: 7pt;
            color: var(--secondary);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Print Button (Screen Only) */
        .fab-print {
            position: fixed;
            bottom: 40px;
            right: 40px;
            background: var(--accent);
            color: white;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.5);
            cursor: pointer;
            transition: transform 0.2s;
            border: none;
            z-index: 100;
        }
        .fab-print:hover {
            transform: scale(1.1);
        }
        .fab-print svg {
            width: 24px;
            height: 24px;
            fill: currentColor;
        }

        /* Print Media Query */
        @media print {
            @page {
                size: A4 landscape;
                margin: 10mm;
            }

            html, body {
                height: auto;
                background: white;
            }

            body {
                margin: 0;
                padding: 0;
                -webkit-print-color-adjust: exact;
                display: block;
            }

            .page {
                box-shadow: none;
                width: 100%;
                max-width: none;
                padding: 0;
                margin: 0;
                height: auto;
                display: block;
                background: white;
            }

            .fab-print {
                display: none;
            }

            header {
                position: relative;
                margin-bottom: 5mm;
                page-break-after: avoid;
            }

            .content-grid {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 5mm;
            }

            /* Hide footer in print/flow to avoid overlaps, or keep it static at bottom of doc */
            footer {
                position: relative;
                margin-top: 5mm;
                border-top: 1px solid #ccc;
            }
        }
    </style>
</head>
<body>

    <!-- Floating Print Button -->
    <button class="fab-print" onclick="window.print()" title="Imprimir / Salvar PDF">
        <i class="fa-duotone fa-print fa-lg"></i>
    </button>

    <div class="page">
        <header>
            <h1>{{ $plan->title }}</h1>
            <div class="subtitle">Plano de Leitura - Vertex CBAV</div>
        </header>

        <div class="content-grid">
            @foreach($days as $day)
                <div class="reading-card">
                    <div class="reading-header">
                        <span class="day-label">Dia {{ str_pad($day->day_number, 2, '0', STR_PAD_LEFT) }}</span>
                    </div>

                    <div class="reading-content">
                        @foreach($day->contents as $content)
                            <div class="reading-row">
                                <span style="font-size: 8pt; color: #334155;">
                                    @if($content->type === 'scripture' && $content->book)
                                        {{-- Scripture --}}
                                        <strong>{{ $content->book->name }}</strong>
                                        {{ $content->chapter_start }}
                                        @if($content->chapter_end && $content->chapter_end != $content->chapter_start)-{{ $content->chapter_end }}@endif
                                        @if($content->verse_start):{{ $content->verse_start }}@endif

                                    @elseif($content->type === 'devotional')
                                        {{-- Devotional --}}
                                        <i class="fa-solid fa-pen-nib text-xs mr-1 opacity-50"></i>
                                        <strong>{{ $content->title ?: 'Devocional' }}</strong>

                                    @elseif($content->type === 'video')
                                        {{-- Video --}}
                                        <i class="fa-solid fa-play text-xs mr-1 opacity-50"></i>
                                        <strong>{{ $content->title ?: 'Vídeo' }}</strong>
                                    @endif
                                </span>
                                <div class="check-box"></div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <footer>
            Documento Gerado em {{ now()->format('d/m/Y') }} • Vertex System
        </footer>
    </div>

</body>
</html>
