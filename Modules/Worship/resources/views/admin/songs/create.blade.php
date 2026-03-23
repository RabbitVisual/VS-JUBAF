@extends('admin::components.layouts.master')

@section('title', 'Nova Música | Worship')

@section('content')
<div class="space-y-8 max-w-[1700px] mx-auto">
    <!-- Header (Admin pattern) -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('worship.admin.songs.index') }}" class="p-2 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-500 hover:text-blue-600 dark:hover:text-blue-400 transition-colors shrink-0">
                <x-icon name="arrow-left" class="w-5 h-5" />
            </a>
            <div class="min-w-0">
                <nav class="flex items-center gap-2 text-[10px] font-black text-blue-600 dark:text-blue-500 uppercase tracking-widest mb-1">
                    <a href="{{ route('worship.admin.songs.index') }}" class="hover:underline">Biblioteca</a>
                    <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-600"></span>
                    <span class="text-gray-400 dark:text-gray-500">Nova música</span>
                </nav>
                <h1 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">Nova composição</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Preencha os dados e o letra em ChordPro.</p>
            </div>
        </div>
        <button type="submit" form="song-form" class="inline-flex items-center px-5 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold shadow-lg shadow-blue-500/20 transition-all active:scale-95 shrink-0">
            <x-icon name="check" class="w-5 h-5 mr-2" />
            Salvar música
        </button>
    </div>

        <form id="song-form" action="{{ route('worship.admin.songs.store') }}" method="POST"
              data-initial-content="{{ old('content_chordpro') }}"
              x-data="worshipSongEditor($el.dataset.initialContent)"
              class="space-y-8 w-full">
            @csrf

            <!-- Main Layout: Sidebar + Editor -->
            <div class="grid grid-cols-1 xl:grid-cols-12 gap-10 w-full">

                <!-- Left Column: Form Fields (3 cols) -->
                <div class="xl:col-span-3 space-y-8 min-w-0">
                    <!-- Informações Básicas Card -->
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
                                <input type="text" name="title" required value="{{ old('title') }}" placeholder="Ex: Grande é o Senhor"
                                       class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:text-white">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Intérprete / Autor</label>
                                <input type="text" name="artist" value="{{ old('artist') }}" placeholder="Ex: Adhemar de Campos"
                                       class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:text-white">
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Tom</label>
                                    <select name="original_key" class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 dark:text-white">
                                        @foreach(\Modules\Worship\App\Enums\MusicalKey::cases() as $key)
                                            <option value="{{ $key->value }}" {{ old('original_key') == $key->value ? 'selected' : '' }}>{{ $key->value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">BPM</label>
                                    <input type="number" name="bpm" value="{{ old('bpm') }}" placeholder="78"
                                           class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 dark:text-white">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mídia e Tags Card -->
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
                                <input type="text" name="youtube_id" value="{{ old('youtube_id') }}" placeholder="Ex: dO8K-P5G9sc"
                                       class="block w-full pl-12 pr-4 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 dark:text-white">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">Tags de contexto</label>
                            <div class="grid grid-cols-2 gap-3">
                                @foreach(['Adoração', 'Júbilo', 'Ceia', 'Cruz', 'E. Santo', 'Oferta'] as $theme)
                                    <label class="flex items-center p-3 rounded-xl bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 hover:border-blue-500/30 transition-colors cursor-pointer">
                                        <input type="checkbox" name="themes[]" value="{{ $theme }}" class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500" {{ in_array($theme, old('themes', [])) ? 'checked' : '' }}>
                                        <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">{{ $theme }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Editor ChordPro -->
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
                                    placeholder="[Intro]
[G] [C] [Em] [D]

[Verse 1]
[G]Grande é o [C]Senhor e [Em]mui digno de [D]louvor..."></textarea>
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
        .lyrics-viewer-editor-preview .chord {
            @apply text-blue-600 dark:text-blue-400 font-bold text-xs inline-block -top-2 relative mr-1;
        }
        .lyrics-viewer-editor-preview .lyric-line {
            @apply mb-4 leading-relaxed text-base;
        }
        .lyrics-viewer-editor-preview .section-header {
            @apply font-bold text-amber-600 dark:text-amber-400 text-xs uppercase tracking-wider mb-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-600;
        }

        /* Custom Scrollbar */
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
                lines.forEach(line => {
                    let rawLine = line;
                    line = line.trim();
                    if (!line) { html += '<div class="h-4"></div>'; return; }

                    if (line.match(/^\[[^\]]+\]$/)) {
                        html += `<div class="font-bold text-amber-500 uppercase text-[10px] mt-6 mb-2 tracking-[0.2em] border-b border-white/10 pb-2">${line.replace(/[\[\]]/g, '')}</div>`;
                        return;
                    }

                    if (line.startsWith('{') && line.endsWith('}')) {
                        // Metadata directives: {title: ...}, {artist: ...}, etc.
                        return;
                    }

                    if (line.includes('[')) {
                        html += '<div class="relative flex flex-wrap mt-8 leading-loose min-h-[1.5rem] gap-x-1">';
                        const parts = rawLine.split(/(\[[^\]]+\])/).filter(Boolean);
                        parts.forEach((part, index) => {
                            if (part.startsWith('[')) {
                                const chord = part.replace(/[\[\]]/g, '');
                                // Check if next part is also a chord
                                const nextIsChord = parts[index + 1] && parts[index + 1].startsWith('[');
                                const spacer = nextIsChord ? '<span class="inline-block min-w-[3rem]"></span>' : '';
                                html += `<span class="inline-flex flex-col relative"><span class="absolute -top-7 text-blue-400 font-black text-[11px] tracking-tighter bg-blue-500/10 px-1.5 py-0.5 rounded border border-blue-500/20 whitespace-nowrap">${chord}</span>${spacer}`;
                            } else {
                                if (part.trim().length > 0 || part === ' ') {
                                    html += `<span class="text-gray-300 font-medium">${part}</span></span>`;
                                } else {
                                    html += '</span>';
                                }
                            }
                        });
                        html += '</div>';
                    } else {
                        html += `<div class="text-gray-400 font-medium mt-1">${line}</div>`;
                    }
                });
                return html;
            }
        }));
    });
</script>
@endpush
@endsection

