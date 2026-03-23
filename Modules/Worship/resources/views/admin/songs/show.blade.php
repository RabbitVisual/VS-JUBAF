@extends('admin::components.layouts.master')

@section('title', $song->title . ' | Worship')

@section('content')
<style>
.lyrics-viewer .lyric-line { display: flex; flex-wrap: wrap; align-items: flex-end; gap: 0; margin-bottom: 0.5rem; line-height: 1.625; }
.lyrics-viewer .chord-group { display: inline-flex; flex-direction: column; align-items: flex-start; vertical-align: bottom; }
.lyrics-viewer .chord-group .chord { font-size: 0.75rem; font-weight: 700; color: rgb(37 99 235); line-height: 1.2; font-family: ui-monospace, monospace; }
.lyrics-viewer .chord-group .lyric, .lyrics-viewer .lyric-only { white-space: pre-wrap; }
.lyrics-viewer .lyric-line.lyric-only { display: block; }
.lyrics-viewer .section-header { font-weight: 700; color: rgb(217 119 6); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.75rem; margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid rgb(229 231 235); }
.dark .lyrics-viewer .chord-group .chord { color: rgb(96 165 250); }
.dark .lyrics-viewer .section-header { color: rgb(251 191 36); border-top-color: rgb(75 85 99); }
</style>
<div class="space-y-8 max-w-5xl mx-auto">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
        <div class="flex items-center gap-4 min-w-0">
            <a href="{{ route('worship.admin.songs.index') }}" class="p-2 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-500 hover:text-blue-600 dark:hover:text-blue-400 transition-colors shrink-0">
                <x-icon name="arrow-left" class="w-5 h-5" />
            </a>
            <div class="min-w-0">
                <nav class="flex items-center gap-2 text-[10px] font-black text-blue-600 dark:text-blue-500 uppercase tracking-widest mb-1">
                    <a href="{{ route('worship.admin.songs.index') }}" class="hover:underline">Biblioteca</a>
                    <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-600"></span>
                    <span class="text-gray-400 dark:text-gray-500 truncate">{{ $song->title }}</span>
                </nav>
                <h1 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight truncate">{{ $song->title }}</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ $song->artist ?: 'Composição independente' }}</p>
            </div>
        </div>
        <div class="flex flex-wrap gap-3 shrink-0">
            @if($song->youtube_id)
                <a href="https://youtube.com/watch?v={{ $song->youtube_id }}" target="_blank" rel="noopener" class="inline-flex items-center px-5 py-3 rounded-xl bg-red-500/10 hover:bg-red-500 text-red-600 hover:text-white border border-red-500/20 font-bold text-sm transition-colors">
                    <x-icon name="video" class="w-5 h-5 mr-2" />
                    Assistir
                </a>
            @endif
            <a href="{{ route('worship.admin.songs.edit', $song->id) }}" class="inline-flex items-center px-5 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold shadow-lg shadow-blue-500/20 transition-all">
                <x-icon name="pencil" class="w-5 h-5 mr-2" />
                Editar
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <div class="lg:col-span-8">
            <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-200 dark:border-gray-700 shadow-sm p-6 md:p-8 overflow-hidden">
                <div class="lyrics-viewer font-sans text-lg leading-relaxed text-gray-800 dark:text-gray-200">
                    {!! app(\Modules\Worship\App\Services\ChordProEngine::class)->toHtml($song->content_chordpro ?? '') !!}
                </div>
            </div>
        </div>

        <div class="lg:col-span-4 space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-200 dark:border-gray-700 shadow-sm p-6 sticky top-8">
                <div class="flex items-center gap-3 pb-4 mb-4 border-b border-gray-100 dark:border-gray-700">
                    <div class="w-10 h-10 rounded-2xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400">
                        <x-icon name="music-note" class="w-5 h-5" />
                    </div>
                    <h2 class="text-base font-bold text-gray-900 dark:text-white">Ficha técnica</h2>
                </div>

                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Tom original</span>
                        <span class="text-2xl font-black text-blue-600 dark:text-blue-400">{{ $song->original_key?->value ?? '?' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">BPM</span>
                        <span class="font-mono font-bold text-gray-900 dark:text-white">{{ $song->bpm ?? '—' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Métrica</span>
                        <span class="font-mono font-bold text-gray-900 dark:text-white">{{ $song->time_signature ?? '4/4' }}</span>
                    </div>

                    @if(count($song->themes ?? []) > 0)
                        <div class="pt-4 border-t border-gray-100 dark:border-gray-700">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Temáticas</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach($song->themes ?? [] as $theme)
                                    <span class="px-3 py-1.5 bg-gray-100 dark:bg-gray-700 rounded-lg text-xs font-bold text-gray-700 dark:text-gray-300">
                                        {{ $theme }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
