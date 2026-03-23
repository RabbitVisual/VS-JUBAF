@extends('layouts.print')

@section('title', $setlist->title . ' - Impressão')

@push('styles')
<style>
    @media print {
        body { font-size: 12pt; background: white; color: #111; }
        .no-print { display: none !important; }
        .page-break { page-break-before: always; }
    }
    body { font-family: system-ui, 'Segoe UI', sans-serif; background: #f3f4f6; color: #111; }
    .sheet {
        background: white;
        max-width: 210mm;
        margin: 20px auto;
        padding: 20mm;
        box-shadow: 0 4px 14px rgba(0,0,0,0.08);
        border-radius: 8px;
    }
    .sheet-header {
        border-bottom: 2px solid #111;
        padding-bottom: 1rem;
        margin-bottom: 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        gap: 1rem;
    }
    .sheet-header h1 { font-size: 1.5rem; font-weight: 800; margin: 0; text-transform: none; }
    .sheet-header .meta { font-size: 0.875rem; color: #4b5563; margin-top: 0.25rem; }
    .sheet-footer {
        margin-top: 2rem;
        padding-top: 1rem;
        border-top: 1px solid #e5e7eb;
        text-align: center;
        font-size: 0.75rem;
        color: #6b7280;
    }
    .chord { color: #333; font-weight: 700; position: relative; top: -0.5em; font-size: 0.85em; }
    .lyric-line { line-height: 2; position: relative; }
    .section-header { margin-top: 1em; font-weight: 700; text-transform: uppercase; font-size: 0.8em; border-bottom: 1px solid #ddd; display: inline-block; padding-bottom: 0.25em; }
    .toolbar {
        position: fixed;
        top: 1rem;
        right: 1rem;
        display: flex;
        gap: 0.5rem;
        z-index: 50;
    }
    .toolbar button {
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-weight: 700;
        font-size: 0.875rem;
        cursor: pointer;
        border: none;
        transition: opacity 0.2s;
    }
    .toolbar button:hover { opacity: 0.9; }
    .toolbar .btn-print { background: #4f46e5; color: white; }
    .toolbar .btn-close { background: #4b5563; color: white; }
</style>
@endpush

@section('content')
    <div class="no-print toolbar">
        <button type="button" onclick="window.print()" class="btn-print">Imprimir</button>
        <button type="button" onclick="window.close()" class="btn-close">Fechar</button>
    </div>

    @forelse($setlist->items as $item)
        <div class="sheet {{ !$loop->first ? 'page-break' : '' }}">
            <div class="sheet-header">
                <div>
                    <h1>{{ $item->song->title }}</h1>
                    <p class="meta">{{ $item->song->artist }}</p>
                </div>
                <div class="text-right">
                    <p style="font-weight: 700; font-size: 1.25rem;">Tom: {{ $item->effective_key?->value ?? '—' }}</p>
                    <p class="meta">BPM: {{ $item->song->bpm ?? '—' }} · {{ $item->song->time_signature ?? '4/4' }}</p>
                </div>
            </div>

            <div class="lyrics-body">
                @php
                    $content = $item->song->content_chordpro ?? $item->song->lyrics_only ?? '';
                    $html = $content ? app(\Modules\Worship\App\Services\ChordProEngine::class)->toHtml($content) : '';
                @endphp
                @if($html)
                    {!! $html !!}
                @else
                    @php $lines = explode("\n", $item->song->lyrics_only ?? ''); @endphp
                    @foreach($lines as $line)
                        @if(preg_match('/^\[[^\]]+\]$/', trim($line)))
                            <div class="section-header">{{ trim($line) }}</div>
                        @elseif(empty(trim($line)))
                            <div class="h-4"></div>
                        @else
                            <div class="lyric-line">
                                {!! preg_replace('/\[([^\]]+)\]/', '<span class="chord">$1</span>', e($line)) !!}
                            </div>
                        @endif
                    @endforeach
                @endif
            </div>

            <div class="sheet-footer">
                {{ $setlist->title }} · {{ $setlist->scheduled_at->format('d/m/Y') }} · VertexCBAV
            </div>
        </div>
    @empty
        <div class="sheet">
            <p class="text-center text-gray-500">Nenhuma música no repertório.</p>
        </div>
    @endforelse
@endsection
