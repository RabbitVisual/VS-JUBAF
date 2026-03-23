@extends('admin::components.layouts.master')

@section('title', 'Editar Música | Worship')

@section('content')
<div class="space-y-8 max-w-[1700px] mx-auto">
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 text-sm font-bold">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-600 dark:text-red-400 text-sm font-bold">
            {{ session('error') }}
        </div>
    @endif

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('worship.admin.songs.show', $song->id) }}" class="p-2 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-500 hover:text-blue-600 dark:hover:text-blue-400 transition-colors shrink-0">
                <x-icon name="arrow-left" class="w-5 h-5" />
            </a>
            <div class="min-w-0">
                <nav class="flex items-center gap-2 text-[10px] font-black text-blue-600 dark:text-blue-500 uppercase tracking-widest mb-1">
                    <a href="{{ route('worship.admin.songs.index') }}" class="hover:underline">Biblioteca</a>
                    <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-600"></span>
                    <a href="{{ route('worship.admin.songs.show', $song->id) }}" class="hover:underline truncate max-w-[180px] inline-block">{{ $song->title }}</a>
                    <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-600"></span>
                    <span class="text-gray-400 dark:text-gray-500">Editar</span>
                </nav>
                <h1 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight truncate">Editar: {{ $song->title }}</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Altere os dados e a letra em ChordPro.</p>
            </div>
        </div>
        <button type="submit" form="song-form" class="inline-flex items-center px-5 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold shadow-lg shadow-blue-500/20 transition-all active:scale-95 shrink-0">
            <x-icon name="check" class="w-5 h-5 mr-2" />
            Salvar alterações
        </button>
    </div>

        <form id="song-form" action="{{ route('worship.admin.songs.update', $song->id) }}" method="POST"
              data-initial-content="{{ old('content_chordpro', $song->content_chordpro) }}"
              x-data="worshipSongEditor($el.dataset.initialContent)"
              class="space-y-8 w-full">
            @csrf
            @method('PUT')

            <!-- Main Layout: Sidebar + Editor -->
            <div class="grid grid-cols-1 xl:grid-cols-12 gap-10 w-full">

                <!-- Left Column: Form Fields (3 cols) -->
                <div class="xl:col-span-3 space-y-8 min-w-0">
                    <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-200 dark:border-gray-700 shadow-sm p-6 space-y-6">
                        <div class="flex items-center gap-3 pb-4 border-b border-gray-100 dark:border-gray-700">
                            <div class="w-10 h-10 rounded-2xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400">
                                <x-icon name="music-note" class="w-5 h-5" />
                            </div>
                            <h2 class="text-lg font-bold text-gray-900 dark:text-white">Informações básicas</h2>
                        </div>
                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Título da obra</label>
                                <input type="text" name="title" required value="{{ old('title', $song->title) }}" placeholder="Ex: Grande é o Senhor"
                                       class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 dark:text-white">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Intérprete / Autor</label>
                                <input type="text" name="artist" value="{{ old('artist', $song->artist) }}" placeholder="Ex: Adhemar de Campos"
                                       class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 dark:text-white">
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Tom</label>
                                    <select name="original_key" class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 dark:text-white">
                                        @foreach(\Modules\Worship\App\Enums\MusicalKey::cases() as $key)
                                            <option value="{{ $key->value }}" {{ old('original_key', $song->original_key->value ?? '') == $key->value ? 'selected' : '' }}>{{ $key->value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">BPM</label>
                                    <input type="number" name="bpm" value="{{ old('bpm', $song->bpm) }}" placeholder="78"
                                           class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 dark:text-white">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-200 dark:border-gray-700 shadow-sm p-6 space-y-6">
                        <div class="flex items-center gap-3 pb-4 border-b border-gray-100 dark:border-gray-700">
                            <div class="w-10 h-10 rounded-2xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center text-amber-600 dark:text-amber-400">
                                <x-icon name="play-circle" class="w-5 h-5" />
                            </div>
                            <h2 class="text-lg font-bold text-gray-900 dark:text-white">Mídia e tags</h2>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">YouTube (ID do vídeo)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                                    <x-icon name="play-circle" class="w-5 h-5" />
                                </div>
                                <input type="text" name="youtube_id" value="{{ old('youtube_id', $song->youtube_id) }}" placeholder="Ex: dO8K-P5G9sc"
                                       class="block w-full pl-12 pr-4 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 dark:text-white">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">Tags de contexto</label>
                            <div class="grid grid-cols-2 gap-3">
                                @foreach(['Adoração', 'Júbilo', 'Ceia', 'Cruz', 'E. Santo', 'Oferta'] as $theme)
                                    <label class="flex items-center p-3 rounded-xl bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 hover:border-blue-500/30 transition-colors cursor-pointer">
                                        <input type="checkbox" name="themes[]" value="{{ $theme }}" class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500" {{ in_array($theme, old('themes', $song->themes ?? [])) ? 'checked' : '' }}>
                                        <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">{{ $theme }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-3xl border border-amber-500/30 dark:border-amber-500/20 shadow-sm p-6 space-y-4">
                        <div class="flex items-center gap-3 pb-3 border-b border-gray-100 dark:border-gray-700">
                            <div class="w-10 h-10 rounded-2xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center text-amber-600 dark:text-amber-400">
                                <x-icon name="file-import" class="w-5 h-5" />
                            </div>
                            <h3 class="text-base font-bold text-gray-900 dark:text-white">Reimportar de arquivo</h3>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Substitui título, autor e letra por um novo .cho/.pro (ChordPro) ou .xml (OpenSong/OpenLP). Mantém a mesma música, sem duplicar.</p>
                        <form action="{{ route('worship.admin.songs.reimport', $song) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                            @csrf
                            <input type="file" name="reimport_file" accept=".cho,.pro,.xml,.txt" required
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:font-bold file:bg-amber-600 file:text-white file:cursor-pointer hover:file:bg-amber-700">
                            <button type="submit" class="w-full py-3 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-black uppercase transition">
                                Reimportar ChordPro ou OpenLP
                            </button>
                        </form>
                    </div>
                </div>

                <div class="xl:col-span-9 flex flex-col gap-6 min-w-0">
                    <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-200 dark:border-gray-700 shadow-sm flex flex-col overflow-hidden min-h-[600px]">
                        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400">
                                    <x-icon name="code" class="w-5 h-5" />
                                </div>
                                <div>
                                    <h2 class="text-base font-bold text-gray-900 dark:text-white">Letra em ChordPro</h2>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Use [Acorde] e [Sessão] para formatação.</p>
                                </div>
                            </div>
                            <span class="text-xs font-medium text-gray-400 dark:text-gray-500">Preview ao vivo</span>
                        </div>
                        <div class="flex-1 grid grid-cols-1 md:grid-cols-2 min-h-[500px]">
                            <div class="p-6 border-r border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                                <textarea name="content_chordpro" required x-model="content"
                                    class="w-full h-[480px] bg-transparent border-none focus:ring-0 font-mono text-sm leading-relaxed resize-none text-gray-900 dark:text-gray-100"
                                    placeholder="[Intro]..."></textarea>
                            </div>
                            <div class="p-6 overflow-y-auto max-h-[550px] bg-gray-50/50 dark:bg-gray-900/30 custom-scrollbar">
                                <div class="lyrics-viewer-editor-preview text-gray-700 dark:text-gray-300" x-html="previewHtml"></div>
                            </div>
                        </div>
                        <div class="px-6 py-3 border-t border-gray-100 dark:border-gray-700 flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                            <span class="flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span> Acorde: [G] [C]</span>
                            <span class="flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Sessão: [Verse 1]</span>
                        </div>
                    </div>
                </div>
            </div>
        </form>
</div>

    <style>
        .lyrics-viewer-editor-preview .lyric-line {
            display: flex;
            flex-wrap: wrap;
            align-items: flex-end;
            gap: 0;
            margin-bottom: 0.5rem;
            line-height: 1.6;
        }
        .lyrics-viewer-editor-preview .chord-group {
            display: inline-flex;
            flex-direction: column;
            align-items: flex-start;
            vertical-align: bottom;
        }
        .lyrics-viewer-editor-preview .chord-group .chord {
            font-size: 0.75rem;
            font-weight: 700;
            color: rgb(37 99 235);
            line-height: 1.2;
            font-family: ui-monospace, monospace;
        }
        .dark .lyrics-viewer-editor-preview .chord-group .chord { color: rgb(96 165 250); }
        .lyrics-viewer-editor-preview .chord-group .lyric,
        .lyrics-viewer-editor-preview .lyric-only { white-space: pre-wrap; }
        .lyrics-viewer-editor-preview .lyric-line.lyric-only { display: block; }
        .lyrics-viewer-editor-preview .section-header { @apply font-bold text-amber-600 dark:text-amber-400 text-xs uppercase tracking-wider mb-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-600; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #333; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #444; }
    </style>
@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('worshipSongEditor', (initialContent = '') => ({
            content: initialContent,
            previewHtml: '',

            init() {
                this.$watch('content', () => this.updatePreview());
                this.updatePreview();
            },

            updatePreview() {
                this.previewHtml = this.parseChordPro(this.content);
            },

            parseChordPro(text) {
                if (!text) return '';
                const lines = text.split('\n');
                let html = '';
                const escape = (s) => s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
                lines.forEach(line => {
                    const rawLine = line;
                    line = line.trim();
                    if (!line) { html += '<div class="h-4"></div>'; return; }

                    if (line.match(/^\[[^\]]+\]$/)) {
                        html += `<div class="section-header">${escape(line.replace(/[\[\]]/g, ''))}</div>`;
                        return;
                    }

                    if (line.startsWith('{') && line.endsWith('}')) return;

                    if (line.includes('[')) {
                        html += '<div class="lyric-line">';
                        const parts = rawLine.split(/(\[[^\]]+\])/).filter(Boolean);
                        let pendingChord = null;
                        parts.forEach((part) => {
                            if (part.startsWith('[')) {
                                if (pendingChord) {
                                    html += `<div class="chord-group"><span class="chord">${escape(pendingChord)}</span><span class="lyric">&nbsp;</span></div>`;
                                }
                                pendingChord = part.replace(/[\[\]]/g, '');
                            } else {
                                if (pendingChord) {
                                    html += `<div class="chord-group"><span class="chord">${escape(pendingChord)}</span><span class="lyric">${escape(part)}</span></div>`;
                                    pendingChord = null;
                                } else {
                                    html += `<span class="lyric-only">${escape(part)}</span>`;
                                }
                            }
                        });
                        if (pendingChord) {
                            html += `<div class="chord-group"><span class="chord">${escape(pendingChord)}</span><span class="lyric">&nbsp;</span></div>`;
                        }
                        html += '</div>';
                    } else {
                        html += `<div class="lyric-line lyric-only">${escape(line)}</div>`;
                    }
                });
                return html;
            }
        }));
    });
</script>
@endpush
@endsection

