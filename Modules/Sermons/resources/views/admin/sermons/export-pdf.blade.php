<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>{{ $sermon->title }} – Esboço</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11pt; line-height: 1.4; color: #333; margin: 1cm; }
        h1 { font-size: 14pt; margin-bottom: 0.5em; }
        h2 { font-size: 12pt; margin-top: 1em; margin-bottom: 0.3em; }
        .block { margin-bottom: 0.8em; }
        .transition { background: #fef3c7; padding: 2px 6px; font-weight: bold; }
        .apelo { background: #d1fae5; padding: 2px 6px; font-weight: bold; }
    </style>
</head>
<body>
    <h1>{{ $sermon->title }}</h1>
    @if($sermon->subtitle)<p style="color:#666; font-size:10pt;">{{ $sermon->subtitle }}</p>@endif
    @if($sermon->sermon_date)<p style="font-size:10pt;">{{ $sermon->sermon_date->format('d/m/Y') }}</p>@endif

    @if($format === 'full')
        {{-- Conteúdo principal (Sermon Studio): full_content em primeiro lugar --}}
        @if($sermon->full_content)
            <div class="block">{!! $fullContentHtml !!}</div>
        @endif
        @if($sermon->introduction)
            <h2>Introdução</h2>
            <div class="block">{!! $introductionHtml !!}</div>
        @endif
        @if($sermon->development)
            <h2>Desenvolvimento</h2>
            <div class="block">{!! $developmentHtml !!}</div>
        @endif
        @if($sermon->conclusion)
            <h2>Conclusão</h2>
            <div class="block">{!! $conclusionHtml !!}</div>
        @endif
        @if($sermon->application)
            <h2>Aplicação</h2>
            <div class="block">{!! $applicationHtml !!}</div>
        @endif
    @else
        {{-- topics: tópicos a partir de full_content ou campos legados --}}
        @if(!empty($topicsFromFullContentHtml))
            <div class="block">{!! $topicsFromFullContentHtml !!}</div>
        @endif
        @if($sermon->introduction)
            <h2>Introdução</h2>
            <p>{{ Str::limit(strip_tags($sermon->introduction), 150) }}</p>
        @endif
        @if($sermon->development)
            <h2>Desenvolvimento</h2>
            <p>{{ Str::limit(strip_tags($sermon->development), 150) }}</p>
        @endif
        @if($sermon->conclusion)
            <h2>Conclusão</h2>
            <p>{{ Str::limit(strip_tags($sermon->conclusion), 100) }}</p>
        @endif
        @if($sermon->application)
            <h2>Aplicação</h2>
            <p>{{ Str::limit(strip_tags($sermon->application), 100) }}</p>
        @endif
    @endif
</body>
</html>
