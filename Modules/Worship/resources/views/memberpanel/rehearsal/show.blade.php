@extends('memberpanel::components.layouts.master')

@section('page-title', $setlist->title . ' - Sala de Ensaio')

@section('content')
<div class="fixed inset-0 bg-gray-950 flex flex-col z-[60] overflow-hidden font-sans" x-data="rehearsalRoom()" x-init="initRoom()">

    <!-- Header -->
    <header class="h-16 md:h-20 bg-gray-900/90 backdrop-blur-xl border-b border-white/10 flex items-center justify-between px-4 md:px-8 shrink-0 z-30 shadow-2xl safe-area-inset-top">
        <div class="flex items-center gap-4 md:gap-6">
            <a href="{{ route('worship.member.rehearsal.index') }}" class="group w-10 h-10 bg-white/5 hover:bg-white/10 rounded-xl flex items-center justify-center text-gray-400 hover:text-white transition-all border border-white/10">
                <x-icon name="arrow-left" class="w-4 h-4 font-black" />
            </a>
            <div class="h-8 w-px bg-white/10 hidden md:block"></div>
            <!-- Mobile Toggle -->
            <button type="button" @click="mobileSidebarOpen = true" class="lg:hidden flex items-center gap-2 px-3 py-1.5 bg-white/5 border border-white/10 rounded-lg text-gray-300">
                <x-icon name="list-music" class="w-4 h-4" />
            </button>
            <div class="min-w-0">
                <p class="text-[9px] font-black uppercase tracking-[0.2em] text-blue-400 leading-none mb-1">Sala de Ensaio</p>
                <h1 class="text-white font-black text-lg tracking-tight truncate">{{ $setlist->title }}</h1>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <span class="hidden md:inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/5 border border-white/10 text-xs font-bold text-gray-400">
                <x-icon name="calendar" class="w-3.5 h-3.5" />
                {{ $setlist->scheduled_at->translatedFormat('d \d\e F, H:i') }}
            </span>
            <button @click="toggleAutoScroll()" class="flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition-all shadow-lg" :class="isAutoScrolling ? 'bg-blue-600 text-white shadow-blue-500/20' : 'bg-white/5 text-gray-400 border border-white/10 hover:bg-white/10'">
                <x-icon name="arrow-down-to-line" class="w-4 h-4" />
                <span class="hidden sm:inline" x-text="isAutoScrolling ? 'Pausar Rolagem' : 'Rolagem Auto'"></span>
            </button>
        </div>
    </header>

    <div class="flex flex-1 overflow-hidden relative">

        <!-- Sidebar - Setlist Items -->
        <aside class="w-80 bg-gray-900 border-r border-white/5 flex flex-col shrink-0 hidden lg:flex relative z-20 shadow-2xl shadow-black">
            <div class="p-6 border-b border-white/5 bg-gray-950/50">
                <h3 class="text-xs font-black uppercase tracking-[0.2em] text-gray-500 mb-4">Repertório ({{ $setlist->items->count() }})</h3>
                <div class="space-y-2">
                    <template x-for="(item, index) in items" :key="item.id">
                        <button @click="selectSong(index)"
                            class="w-full text-left flex items-start gap-3 p-3 rounded-2xl border transition-all"
                            :class="currentIndex === index ? 'bg-blue-600/10 border-blue-500/30' : 'bg-white/5 border-transparent hover:bg-white/10'">

                            <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 font-black text-[10px]"
                                 :class="currentIndex === index ? 'bg-blue-500 text-white' : 'bg-gray-800 text-gray-500'" x-text="index + 1"></div>

                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-bold truncate transition-colors" :class="currentIndex === index ? 'text-blue-400' : 'text-gray-200'" x-text="item.song.title"></h4>
                                <p class="text-[10px] text-gray-500 truncate" x-text="item.song.artist || 'Artista Desconhecido'"></p>
                            </div>
                        </button>
                    </template>
                </div>
            </div>

            <div class="p-6 flex-1 overflow-y-auto custom-scrollbar">
                <template x-if="currentSong">
                    <div class="space-y-4">
                        <h4 class="text-[10px] font-black uppercase tracking-widest text-gray-500 border-b border-white/5 pb-2">Detalhes da Música</h4>

                        <div class="grid grid-cols-2 gap-2">
                            <div class="bg-black/40 rounded-xl p-3 border border-white/5">
                                <span class="text-[9px] font-black uppercase tracking-widest text-gray-600 block mb-1">Tom</span>
                                <span class="text-sm font-bold text-gray-300" x-text="currentSong.original_key || '--'"></span>
                            </div>
                            <div class="bg-black/40 rounded-xl p-3 border border-white/5">
                                <span class="text-[9px] font-black uppercase tracking-widest text-gray-600 block mb-1">BPM</span>
                                <span class="text-sm font-bold text-gray-300" x-text="currentSong.bpm || '--'"></span>
                            </div>
                        </div>

                        <template x-if="currentSong.song_structure">
                            <div class="bg-black/40 rounded-xl p-3 border border-white/5">
                                <span class="text-[9px] font-black uppercase tracking-widest text-gray-600 block mb-1">Estrutura</span>
                                <p class="text-xs text-gray-400 leading-relaxed" x-text="currentSong.song_structure"></p>
                            </div>
                        </template>

                        <div class="pt-4 border-t border-white/5 space-y-2">
                            <template x-if="currentSong.multitrack_url">
                                <a :href="currentSong.multitrack_url" target="_blank" class="flex items-center justify-center gap-2 w-full py-2.5 bg-gray-800 hover:bg-gray-700 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all">
                                    <x-icon name="layer-group" class="w-3.5 h-3.5 text-gray-400" />
                                    Multitracks
                                </a>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </aside>

        <!-- Main Panel -->
        <main class="flex-1 bg-black relative flex flex-col md:flex-row overflow-hidden">

            <!-- Lyrics & Chords View -->
            <div class="flex-1 overflow-y-auto custom-scrollbar relative" id="chord-container">
                <template x-if="currentSong">
                    <div class="max-w-4xl mx-auto p-6 md:p-12 lg:p-20 relative z-10">
                        <div class="mb-12 text-center md:text-left">
                            <h2 class="text-4xl md:text-5xl font-black text-white tracking-tight mb-2" x-text="currentSong.title"></h2>
                            <p class="text-lg font-medium text-gray-400" x-text="currentSong.artist"></p>
                        </div>

                        <div class="prose prose-invert max-w-none">
                            <div class="chordpro-render font-mono text-base md:text-lg lg:text-xl leading-loose tracking-wide text-gray-300 space-y-6" x-html="renderChordPro(currentSong.content_chordpro)">
                            </div>
                        </div>

                        <!-- Spacing to allow scrolling to the very end -->
                        <div class="h-64"></div>
                    </div>
                </template>

                <template x-if="!currentSong">
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="text-center">
                            <x-icon name="music" class="w-16 h-16 text-gray-800 mx-auto mb-4" />
                            <p class="text-xl font-bold text-gray-600">Repertório Vazio ou Carregando...</p>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Media Panel (Right Side Desktop / Top Mobile) -->
            <template x-if="currentSong && currentSong.youtube_id">
                <aside class="w-full md:w-80 lg:w-96 bg-gray-950 border-t md:border-t-0 md:border-l border-white/5 flex flex-col shrink-0 shadow-lg z-20">
                    <div class="p-4 border-b border-white/5 bg-gray-900/50">
                        <h4 class="text-[10px] font-black uppercase tracking-widest text-gray-500 flex items-center gap-2">
                            <x-icon name="youtube" class="w-3.5 h-3.5 text-red-500" /> Referência Oficial
                        </h4>
                    </div>
                    <div class="aspect-video w-full bg-black">
                        <iframe width="100%" height="100%" :src="`https://www.youtube.com/embed/${currentSong.youtube_id}`" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    </div>
                    <div class="p-4 flex-1">
                        <p class="text-xs text-gray-400 leading-relaxed font-bold">Use o vídeo acima para estudar os arranjos, bateria e andamento desta versão oficial.</p>
                    </div>
                </aside>
            </template>

        </main>
    </div>

    <!-- Mobile Drawer -->
    <div x-show="mobileSidebarOpen" style="display: none;" class="fixed inset-0 z-[70] lg:hidden">
        <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" @click="mobileSidebarOpen = false"></div>
        <div class="absolute left-0 top-0 bottom-0 w-80 bg-gray-900 shadow-2xl flex flex-col">
            <div class="p-4 border-b border-white/10 flex items-center justify-between">
                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Repertório</span>
                <button type="button" @click="mobileSidebarOpen = false" class="text-white p-2"><x-icon name="xmark" class="w-5 h-5" /></button>
            </div>

            <div class="flex-1 overflow-y-auto p-4 space-y-2">
                <template x-for="(item, index) in items" :key="'mob-'+item.id">
                    <button @click="selectSong(index); mobileSidebarOpen = false"
                        class="w-full text-left flex items-start gap-3 p-3 rounded-xl border transition-all"
                        :class="currentIndex === index ? 'bg-blue-600/20 border-blue-500/50' : 'bg-white/5 border-transparent'">
                        <div class="w-6 h-6 rounded flex items-center justify-center shrink-0 font-black text-[9px]"
                             :class="currentIndex === index ? 'bg-blue-500 text-white' : 'bg-gray-800 text-gray-500'" x-text="index + 1"></div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-bold truncate text-white" x-text="item.song.title"></h4>
                            <p class="text-[10px] text-gray-400 truncate" x-text="item.song.artist"></p>
                        </div>
                    </button>
                </template>
            </div>
        </div>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #374151; border-radius: 10px; }

    .chordpro-render {
        white-space: pre-wrap;
    }
    .chord-line {
        line-height: 1.2;
        margin-top: 1rem;
        display: block;
    }
    /* Estilo premium para acordes */
    .chordpro-chord {
        color: #60A5FA; /* text-blue-400 */
        font-weight: 900;
        background: rgba(37, 99, 235, 0.1);
        padding: 0.1rem 0.25rem;
        border-radius: 0.25rem;
        border: 1px solid rgba(59, 130, 246, 0.2);
        margin: 0 1px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
</style>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('rehearsalRoom', () => ({
        items: @json($setlist->items->map(function($i) { return ['id' => $i->id, 'song' => $i->song]; })),
        currentIndex: 0,
        mobileSidebarOpen: false,
        isAutoScrolling: false,
        scrollInterval: null,

        get currentSong() {
            if (this.items.length === 0) return null;
            return this.items[this.currentIndex]?.song || null;
        },

        initRoom() {
            // Se houver músicas, a primeira já estará selecionada (index 0)
        },

        selectSong(index) {
            this.currentIndex = index;
            this.stopAutoScroll();
            // Scroll to top of lyrics
            const container = document.getElementById('chord-container');
            if (container) container.scrollTop = 0;
        },

        renderChordPro(content) {
            if (!content) return '<p class="text-gray-500 italic">Cifra não disponível.</p>';

            // Basic ChordPro rendering strategy: replace [Chord] with styled span
            let rendered = content.replace(/\[([^\]]+)\]/g, '<span class="chordpro-chord">$1</span>');

            return rendered;
        },

        toggleAutoScroll() {
            if (this.isAutoScrolling) {
                this.stopAutoScroll();
            } else {
                this.startAutoScroll();
            }
        },

        startAutoScroll() {
            this.isAutoScrolling = true;
            const container = document.getElementById('chord-container');
            if (!container) return;

            this.scrollInterval = setInterval(() => {
                container.scrollTop += 1;
                // If reached bottom, stop
                if (container.scrollTop + container.clientHeight >= container.scrollHeight - 10) {
                    this.stopAutoScroll();
                }
            }, 50); // Velocidade do autoscroll (50ms por pixel)
        },

        stopAutoScroll() {
            this.isAutoScrolling = false;
            if (this.scrollInterval) {
                clearInterval(this.scrollInterval);
                this.scrollInterval = null;
            }
        }
    }));
});
</script>
@endpush
@endsection
