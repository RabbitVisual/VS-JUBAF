@extends('memberpanel::components.layouts.master')

@section('page-title', 'Modo Palco - ' . $setlist->title)

@section('content')
<div x-data="worshipStageViewer" class="fixed inset-0 z-50 bg-black text-slate-100 flex flex-col md:flex-row overflow-hidden animate-in fade-in duration-700">

    <!-- Sidebar / Playlist (Mobile Overlay, Desktop Left) -->
    <div :class="sidebarOpen ? 'translate-x-0 w-full md:w-80' : '-translate-x-full md:translate-x-0 w-full md:w-20'"
         class="fixed md:relative inset-y-0 left-0 bg-slate-900 border-r border-white/5 flex flex-col transition-all duration-500 z-100 shadow-2xl md:shadow-none">

        <div class="p-6 flex items-center justify-between border-b border-white/5">
            <template x-if="sidebarOpen">
                <div class="animate-in fade-in slide-in-from-left-4">
                    <h2 class="text-xs font-black text-blue-500 uppercase tracking-[0.3em]">Setlist</h2>
                    <p class="text-[10px] text-slate-500 font-bold truncate max-w-[150px]">{{ $setlist->title }}</p>
                </div>
            </template>
            <button @click="sidebarOpen = !sidebarOpen" class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center hover:bg-white/10 transition-all">
                <i :class="sidebarOpen ? 'fa-chevron-left' : 'fa-list-music'" class="fa-duotone text-lg"></i>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto overflow-x-hidden p-4 space-y-2">
            <template x-for="(song, index) in songs" :key="song.id">
                <button @click="selectSong(index)"
                        :class="selectedSongIndex === index ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/20' : 'hover:bg-white/5 text-slate-400'"
                        class="w-full text-left p-4 rounded-2xl flex items-center gap-4 transition-all group shrink-0">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center text-[10px] font-black shrink-0 border border-white/10"
                         :class="selectedSongIndex === index ? 'bg-white/20' : 'bg-white/5'">
                        <span x-text="index + 1"></span>
                    </div>
                    <template x-if="sidebarOpen">
                        <div class="truncate animate-in fade-in slide-in-from-left-4">
                            <h3 class="text-sm font-black truncate leading-tight" x-text="song.title"></h3>
                            <p class="text-[9px] font-bold opacity-60 truncate uppercase tracking-widest mt-0.5" x-text="song.artist"></p>
                        </div>
                    </template>
                </button>
            </template>
        </div>

        <!-- System Branding -->
        <div class="p-6 border-t border-white/5 text-center mt-auto" x-show="sidebarOpen">
            @if($setlist->status->value === 'live')
                <div class="flex items-center justify-center gap-2 mb-3 animate-pulse">
                    <span class="w-2 h-2 rounded-full bg-red-500 shadow-[0_0_10px_rgba(239,68,68,0.5)]"></span>
                    <span class="text-[10px] font-black text-red-500 uppercase tracking-widest">Ao Vivo</span>
                </div>
            @endif
            <div class="space-y-1 opacity-40">
                <span class="block text-[8px] font-black text-slate-300 uppercase tracking-[0.4em]">Vertex System Control</span>
                <span class="block text-[7px] font-bold text-slate-500 uppercase tracking-[0.2em]">© 2026 Reinan Rodrigues</span>
            </div>
        </div>
    </div>

    <!-- Mobile Sidebar Backdrop -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false"
         class="md:hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-[90] transition-opacity"
         x-transition:enter="duration-300" x-transition:leave="duration-200"></div>

    <!-- Main Viewport -->
    <div class="flex-1 flex flex-col h-full relative overflow-hidden bg-slate-950">

        <!-- Header / Controls -->
        <div class="min-h-[4.5rem] sm:min-h-[5rem] px-3 sm:px-6 md:px-8 border-b border-white/5 bg-slate-900/50 backdrop-blur-xl shrink-0 flex flex-wrap items-center justify-between gap-2 sm:gap-4 py-3 sm:py-4 md:py-0">
            <div class="flex items-center gap-2 sm:gap-4 md:gap-6 flex-1 min-w-0">
                <div class="min-w-0 flex-1 md:flex-initial">
                    <h1 class="text-sm sm:text-base md:text-lg font-black text-white leading-tight uppercase italic truncate max-w-[180px] sm:max-w-[260px] md:max-w-[300px]" x-text="selectedSong?.title"></h1>
                    <p class="text-[9px] md:text-[10px] font-black text-blue-500 uppercase tracking-widest mt-0.5 truncate max-w-[180px] sm:max-w-none" x-text="selectedSong?.artist"></p>
                </div>

                <!-- BPM Indicator / Control -->
                <template x-if="selectedSong?.bpm">
                    <div class="flex items-center gap-1 bg-white/5 rounded-2xl border border-white/5 p-1 shadow-inner shrink-0">
                        <button @click="adjustBpm(-1)" class="w-8 h-8 flex items-center justify-center text-slate-500 hover:text-blue-400 transition-colors">
                            <i class="fa-duotone fa-minus text-[10px]"></i>
                        </button>
                        <div class="flex items-center gap-2 px-1">
                             <div class="w-2 h-2 rounded-full bg-blue-500" :class="isBeat ? 'scale-150 opacity-100 shadow-[0_0_15px_rgba(59,130,246,0.8)]' : 'scale-100 opacity-20' + ' transition-all duration-75'"></div>
                             <span class="text-xs font-black tracking-widest text-slate-200 min-w-[2.5ch] text-center" x-text="selectedSong.bpm"></span>
                        </div>
                        <button @click="adjustBpm(1)" class="w-8 h-8 flex items-center justify-center text-slate-500 hover:text-blue-400 transition-colors">
                            <i class="fa-duotone fa-plus text-[10px]"></i>
                        </button>
                    </div>
                </template>

                 <!-- Auto Scroll: Toggle + Speed -->
                <div class="flex items-center gap-1 sm:gap-2 bg-white/5 rounded-xl p-1 border border-white/10 shrink-0">
                    <button @click="toggleAutoScroll()"
                            :class="isAutoScrolling ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/30' : 'bg-transparent text-slate-400 hover:text-white'"
                            class="w-9 h-9 sm:w-10 sm:h-10 rounded-lg flex items-center justify-center transition-all"
                            title="Play/Pausar rolagem automática">
                        <i class="fa-duotone text-sm" :class="isAutoScrolling ? 'fa-pause' : 'fa-play'"></i>
                    </button>
                    <div class="h-6 w-px bg-white/10 hidden sm:block"></div>
                    <div class="flex items-center gap-0.5 sm:gap-1 px-1" x-show="isAutoScrolling" x-cloak>
                        <button @click="setScrollSpeed(-1)"
                                class="w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center text-slate-400 hover:text-blue-400 transition-colors rounded-lg"
                                :disabled="scrollSpeedMultiplier <= 0.5"
                                title="Mais lento">
                            <i class="fa-duotone fa-minus text-[10px]"></i>
                        </button>
                        <span class="text-[10px] font-black text-slate-300 min-w-[2.5ch] text-center" x-text="scrollSpeedMultiplier + 'x'"></span>
                        <button @click="setScrollSpeed(1)"
                                class="w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center text-slate-400 hover:text-blue-400 transition-colors rounded-lg"
                                :disabled="scrollSpeedMultiplier >= 2"
                                title="Mais rápido">
                            <i class="fa-duotone fa-plus text-[10px]"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2 md:gap-3 flex-wrap justify-end">
                <!-- Font Scaling (visível em todas as telas) -->
                <div class="flex items-center bg-white/5 rounded-lg sm:rounded-xl p-0.5 sm:p-1 border border-white/10 shrink-0">
                    <button @click="fontSize = Math.max(0.8, fontSize - 0.1)" class="w-8 h-8 sm:w-9 sm:h-9 flex items-center justify-center text-slate-400 hover:text-white transition-colors rounded-lg" title="Diminuir fonte">
                        <i class="fa-duotone fa-minus text-[10px]"></i>
                    </button>
                    <div class="w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center bg-white/5 rounded mx-0.5 sm:mx-1">
                        <i class="fa-duotone fa-text-size text-[10px] sm:text-[12px]"></i>
                    </div>
                    <button @click="fontSize = Math.min(3.0, fontSize + 0.1)" class="w-8 h-8 sm:w-9 sm:h-9 flex items-center justify-center text-slate-400 hover:text-white transition-colors rounded-lg" title="Aumentar fonte">
                        <i class="fa-duotone fa-plus text-[10px]"></i>
                    </button>
                </div>

                <!-- Chords Toggle -->
                <button @click="showChords = !showChords"
                        :class="showChords ? 'bg-emerald-600 text-white' : 'bg-white/5 text-slate-400'"
                        class="h-9 sm:h-10 px-3 sm:px-4 rounded-lg sm:rounded-xl border border-white/5 font-black text-[9px] sm:text-[10px] uppercase tracking-widest transition-all shrink-0">
                    Cifras
                </button>

                <!-- Transposer -->
                <div class="flex items-center bg-white/5 rounded-xl px-2 py-1 border border-white/10 shrink-0">
                    <button @click="transpose(-1)" class="w-8 h-8 flex items-center justify-center text-slate-500 hover:text-blue-400 transition-colors">
                        <i class="fa-duotone fa-minus text-[10px]"></i>
                    </button>
                    <div class="px-4 flex flex-col items-center justify-center min-w-[60px]">
                        <span class="text-[7px] font-black text-slate-500 uppercase tracking-[0.2em] mb-1">Tom</span>
                        <span class="text-base font-black text-blue-400 leading-none" x-text="currentKey"></span>
                    </div>
                    <button @click="transpose(1)" class="w-8 h-8 flex items-center justify-center text-slate-500 hover:text-blue-400 transition-colors">
                        <i class="fa-duotone fa-plus text-[10px]"></i>
                    </button>
                </div>

                <!-- Next Song Button -->
                <button @click="nextSong()" class="md:hidden w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center shrink-0">
                    <i class="fa-duotone fa-forward-step text-sm"></i>
                </button>
            </div>
        </div>

        <!-- Stage Alert Overlay -->
        <div x-show="activeAlert"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-4"
             class="fixed top-8 left-1/2 -translate-x-1/2 z-[200] w-full max-w-xl px-4 pointer-events-none">
            <div class="bg-red-600/90 backdrop-blur-xl border border-white/20 rounded-3xl p-6 shadow-2xl flex items-center gap-6 pointer-events-auto">
                <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center shrink-0">
                    <i class="fa-duotone fa-triangle-exclamation text-white text-2xl"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <span class="text-[9px] font-black text-white/60 uppercase tracking-[0.4em] mb-1 block leading-none">Alerta do Operador</span>
                    <h4 class="text-lg md:text-xl font-black text-white leading-tight uppercase" x-text="activeAlert"></h4>
                </div>
            </div>
        </div>

        <!-- Render Surface (touch-friendly scroll) -->
        <div id="stage-viewport" class="flex-1 overflow-y-auto overflow-x-hidden p-4 sm:p-6 md:p-8 lg:p-16 scroll-smooth custom-scrollbar touch-pan-y overscroll-behavior-y-contain">
            <style>
                .chord-wrapper { position: relative; display: inline-block; width: 0; height: 1em; vertical-align: baseline; }
                .chord-text { position: absolute; bottom: 1.8em; left: 0; color: #34d399; font-weight: 900; font-family: 'JetBrains Mono', monospace; white-space: nowrap; transform: translateY(-2px); pointer-events: none; }
                .lyric-line { display: block; margin-top: 4.5rem; line-height: 1.6; font-weight: 600; font-family: 'Outfit', sans-serif; white-space: pre-wrap; position: relative; min-height: 1.5rem; color: #f8fafc; text-align: left; transition: font-size 0.2s; }
                .section-header { margin-top: 5rem; margin-bottom: 2rem; padding-bottom: 1rem; border-bottom: 2px solid rgba(255,255,255,0.05); color: #fbbf24; text-transform: uppercase; font-weight: 950; font-size: 0.9em; letter-spacing: 0.4em; text-align: left; }
                .custom-scrollbar::-webkit-scrollbar { width: 4px; }
                .custom-scrollbar::-webkit-scrollbar-track { background: rgba(255,255,255,0.02); }
                .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
                .no-chords .chord-wrapper { display: none !important; }
                .no-chords .lyric-line { margin-top: 1.5rem; line-height: 1.5; }
            </style>

            <div class="max-w-4xl mx-auto chordpro-render pb-40" :style="{ fontSize: fontSize + 'rem' }" :class="{ 'no-chords': !showChords }" x-html="currentSongHtml"></div>
        </div>

        <!-- Quick Navigation Bar (responsivo) -->
        <div class="h-14 sm:h-16 px-4 sm:px-6 md:px-8 bg-slate-900 border-t border-white/5 flex items-center justify-between gap-2 shrink-0 safe-area-pb">
             <button @click="prevSong()" :disabled="selectedSongIndex === 0" class="flex items-center gap-2 sm:gap-3 py-2 text-[9px] sm:text-[10px] font-black uppercase tracking-widest text-slate-500 hover:text-white disabled:opacity-20 transition-all min-w-0">
                <i class="fa-duotone fa-arrow-left text-xs"></i>
                <span class="hidden sm:inline">Anterior</span>
            </button>
            <div class="flex gap-1 sm:gap-2 overflow-x-auto max-w-[50vw] justify-center py-1">
                <template x-for="(s, i) in songs" :key="s.id">
                    <button @click="selectSong(i)" class="shrink-0 w-2 h-2 sm:w-2.5 sm:h-2.5 rounded-full transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 focus:ring-offset-slate-900"
                             :class="selectedSongIndex === i ? 'bg-blue-500 w-6 h-2 rounded-full' : 'bg-white/10 hover:bg-white/20 w-2 h-2 rounded-full'"
                             :aria-label="'Música ' + (i+1)"></button>
                </template>
            </div>
            <button @click="nextSong()" :disabled="selectedSongIndex === songs.length - 1" class="flex items-center gap-2 sm:gap-3 py-2 text-[9px] sm:text-[10px] font-black uppercase tracking-widest text-slate-500 hover:text-white disabled:opacity-20 transition-all min-w-0">
                <span class="hidden sm:inline">Próxima</span>
                <i class="fa-duotone fa-arrow-right text-xs"></i>
            </button>
        </div>
    </div>
</div>

@php
    $stageSongs = $setlist->items->map(function($item) {
        return [
            'id' => $item->id,
            'title' => $item->song->title,
            'artist' => $item->song->artist ?? 'Artista Desconhecido',
            'content' => $item->song->content_chordpro ?? $item->song->lyrics_only,
            'original_key' => $item->song->original_key->value ?? 'C',
            'plannedKey' => $item->effective_key->value,
            'bpm' => $item->song->bpm ?? 0
        ];
    });
@endphp

@push('scripts')
<script>
    class Transposer {
        constructor() {
            this.keys = ['C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B'];
            this.flats = ['C', 'Db', 'D', 'Eb', 'E', 'F', 'Gb', 'G', 'Ab', 'A', 'Bb', 'B'];
        }

        transposeChord(chord, semitones) {
            if (!chord) return chord;
            return chord.replace(/[A-G][#b]?/g, (match) => {
                let isFlat = match.includes('b');
                let index = isFlat ? this.flats.indexOf(match) : this.keys.indexOf(match);
                if (index === -1) return match;
                let newIndex = (index + semitones) % 12;
                if (newIndex < 0) newIndex += 12;
                return isFlat ? this.flats[newIndex] : this.keys[newIndex];
            });
        }
    }

    document.addEventListener('alpine:init', () => {
        Alpine.data('worshipStageViewer', () => ({
            sidebarOpen: window.innerWidth > 1024,
            selectedSongIndex: 0,
            currentTransposition: 0,
            currentKey: '',
            showChords: true,
            fontSize: 1.5,
            songs: [],
            isBeat: false,
            isAutoScrolling: false,
            scrollInterval: null,
            scrollAccumulator: 0,
            scrollSpeedMultiplier: 1,
            scrollSpeedOptions: [0.5, 0.75, 1, 1.25, 1.5, 2],
            activeAlert: '',
            alertTimeout: null,
            transposer: new Transposer(),

            init() {
                this.songs = @json($stageSongs);
                this.updateKey();
                this.startMetronome();

                this.$watch('scrollSpeedMultiplier', () => {
                    if (this.isAutoScrolling) this.startAutoScroll();
                });
                this.$watch('selectedSongIndex', () => {
                    if (this.isAutoScrolling) this.startAutoScroll();
                });

                setInterval(() => this.checkStageAlerts(), 2000);
                window.addEventListener('keydown', (e) => {
                    if (e.key === 'ArrowRight') this.nextSong();
                    if (e.key === 'ArrowLeft') this.prevSong();
                });
            },

            get selectedSong() { return this.songs[this.selectedSongIndex]; },

            get currentSongHtml() {
                if (!this.selectedSong) return '';
                return this.render(this.selectedSong.content, this.currentTransposition);
            },

            selectSong(index) {
                this.selectedSongIndex = index;
                this.currentTransposition = 0;
                this.updateKey();

                // Stop autoscroll on song change
                if (this.isAutoScrolling) this.toggleAutoScroll();

                document.getElementById('stage-viewport').scrollTo({ top: 0, behavior: 'smooth' });
            },

            nextSong() {
                if (this.selectedSongIndex < this.songs.length - 1) {
                    this.selectSong(this.selectedSongIndex + 1);
                }
            },

            prevSong() {
                if (this.selectedSongIndex > 0) {
                    this.selectSong(this.selectedSongIndex - 1);
                }
            },

            transpose(val) {
                this.currentTransposition += val;
                this.updateKey();
            },

            adjustBpm(val) {
                if (this.selectedSong) {
                    this.selectedSong.bpm = Math.max(40, Math.min(250, parseInt(this.selectedSong.bpm) + val));
                    this.restartMetronome();
                    if (this.isAutoScrolling) {
                        this.stopAutoScroll();
                        this.startAutoScroll();
                    }
                }
            },

            updateKey() {
                if (!this.selectedSong) return;
                const baseKey = this.selectedSong.plannedKey || this.selectedSong.original_key;
                this.currentKey = this.transposer.transposeChord(baseKey, this.currentTransposition);
            },

            restartMetronome() {
                if (this.metronomeInterval) clearInterval(this.metronomeInterval);
                if (this.selectedSong?.bpm > 0) {
                    const interval = (60 / this.selectedSong.bpm) * 1000;
                    this.metronomeInterval = setInterval(() => {
                        this.isBeat = true;
                        setTimeout(() => { this.isBeat = false; }, 100);
                    }, interval);
                }
            },

            startMetronome() {
                this.restartMetronome();
            },

            toggleAutoScroll() {
                this.isAutoScrolling = !this.isAutoScrolling;
                if (this.isAutoScrolling) {
                    this.startAutoScroll();
                } else {
                    this.stopAutoScroll();
                }
            },

            setScrollSpeed(delta) {
                let i = this.scrollSpeedOptions.indexOf(this.scrollSpeedMultiplier);
                if (i < 0) i = 2;
                i = Math.max(0, Math.min(this.scrollSpeedOptions.length - 1, i + delta));
                this.scrollSpeedMultiplier = this.scrollSpeedOptions[i];
            },

            startAutoScroll() {
                const viewport = document.getElementById('stage-viewport');
                if (!viewport) return;

                this.stopAutoScroll();
                this.scrollAccumulator = 0;

                const effectiveBpm = (this.selectedSong && this.selectedSong.bpm > 0) ? parseInt(this.selectedSong.bpm, 10) : 80;
                const mult = typeof this.scrollSpeedMultiplier === 'number' ? this.scrollSpeedMultiplier : 1;
                const pixelsPerSecond = (effectiveBpm / 60) * 35 * mult;
                const frameRate = 60;
                const pixelsPerFrame = pixelsPerSecond / frameRate;

                this.scrollInterval = setInterval(() => {
                    this.scrollAccumulator += pixelsPerFrame;
                    if (this.scrollAccumulator >= 1) {
                        const scrollToMake = Math.floor(this.scrollAccumulator);
                        viewport.scrollBy(0, scrollToMake);
                        this.scrollAccumulator -= scrollToMake;
                    }
                }, 1000 / frameRate);
            },

            stopAutoScroll() {
                if (this.scrollInterval) {
                    clearInterval(this.scrollInterval);
                    this.scrollInterval = null;
                }
            },

            checkStageAlerts() {
                axios.get('/api/v1/projection/state').then(res => {
                    const state = (res.data && res.data.data) ? res.data.data : {};
                    const alert = state.stage_alert || state.alertMessage || '';
                    if (alert && alert !== this.activeAlert) {
                        this.activeAlert = alert;
                        if (this.alertTimeout) clearTimeout(this.alertTimeout);
                        this.alertTimeout = setTimeout(() => {
                            this.activeAlert = '';
                        }, 8000);
                    } else if (!alert) {
                        this.activeAlert = '';
                    }
                }).catch(() => {});
            },

            render(content, transpose) {
                if(!content) return '<p class="text-slate-500 italic">Sem conteúdo disponível.</p>';
                const lines = content.split('\n');
                let html = '';
                lines.forEach(line => {
                    let text = line.trim();
                    if (text.startsWith('{')) return;
                    if (text.match(/^\[(Chorus|Verse|Bridge|Intro|Outro|Refrão|Verso|Ponte|Final|Solo|Interlúdio|Instrumental).*\]$/i)) {
                        html += `<div class="section-header">${text.replace(/[\[\]]/g, '')}</div>`;
                        return;
                    }
                    if (line.includes('[')) {
                        html += '<div class="lyric-line">';
                        html += line.replace(/\[([^\]]+)\]/g, (match, chord) => {
                            let tChord = chord.split('/').map(c => this.transposer.transposeChord(c, transpose)).join('/');
                            return `<span class="chord-wrapper"><span class="chord-text">${tChord}</span></span>`;
                        });
                        html += '</div>';
                    } else if (text === '') html += '<div style="height: 2rem"></div>';
                    else html += `<div class="lyric-line">${line}</div>`;
                });
                return html;
            }
        }));
    });
</script>
@endpush
@endsection

