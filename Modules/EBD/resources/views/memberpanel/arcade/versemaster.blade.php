@extends('memberpanel::components.layouts.master')

@section('title', 'VerseMaster - Arcade Bíblico')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-slate-950 transition-colors duration-200 pb-12" x-data="verseMasterGame()">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 pt-4 sm:pt-6 space-y-4 sm:space-y-6">

        @include('ebd::memberpanel.arcade.partials.game-header', [
            'gameTitle' => 'VerseMaster',
            'gameSubtitle' => 'Monte versículos arrastando as palavras na ordem correta.',
            'icon' => 'scroll',
            'accent' => 'amber',
        ])

    <!-- Game Board -->
    <div class="relative min-h-[500px] md:min-h-[600px] px-2 sm:px-4">

        <!-- Loading Overlay -->
        <div x-show="loading" class="absolute inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm rounded-2xl md:rounded-[3rem] border border-white/5">
            <div class="text-center space-y-4 md:space-y-6">
                <div class="w-16 h-16 md:w-20 md:h-20 border-8 border-amber-600 border-t-transparent rounded-full animate-spin mx-auto shadow-2xl"></div>
                <p class="text-xs md:text-sm font-black text-white uppercase tracking-[0.3em] italic">Invocando as Escrituras...</p>
            </div>
        </div>

        <!-- Victory Modal -->
        <div x-show="isVictory" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             class="fixed inset-0 z-[100] bg-slate-950/95 backdrop-blur-3xl flex items-center justify-center p-4">
            <div class="relative max-w-lg w-full bg-slate-900 rounded-2xl md:rounded-[3rem] p-8 md:p-12 text-center border border-white/10 shadow-[0_50px_100px_rgba(0,0,0,0.8)] space-y-6 md:space-y-10 overflow-hidden">
                <div class="absolute -top-20 -left-20 w-60 h-60 bg-amber-600/20 rounded-full blur-[100px]"></div>

                <div class="relative z-10 space-y-4 md:space-y-6">
                    <div class="w-20 h-20 md:w-24 md:h-24 bg-amber-600 rounded-3xl md:rounded-4xl flex items-center justify-center mx-auto shadow-2xl shadow-amber-600/30 animate-bounce">
                        <x-icon name="sparkles" style="duotone" class="w-10 h-10 md:w-12 md:h-12 text-white" />
                    </div>
                    <div class="space-y-2">
                        <h2 class="text-4xl md:text-5xl font-black text-white tracking-tighter italic uppercase leading-none">Aleluia!</h2>
                        <p class="text-gray-400 font-bold uppercase tracking-widest text-xs">Você dominou este versículo!</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 md:gap-4 relative z-10">
                    <div class="bg-white/5 p-4 md:p-6 rounded-2xl md:rounded-4xl border border-white/10">
                        <span class="block text-[9px] font-black uppercase text-gray-500 mb-1">XP Ganho</span>
                        <span class="text-2xl md:text-3xl font-black text-emerald-500">+<span x-text="xpGained">50</span></span>
                    </div>
                    <div class="bg-white/5 p-4 md:p-6 rounded-2xl md:rounded-4xl border border-white/10">
                        <span class="block text-[9px] font-black uppercase text-gray-500 mb-1">Pontos</span>
                        <span class="text-2xl md:text-3xl font-black text-amber-500" x-text="score">0</span>
                    </div>
                </div>

                <button @click="nextLevel()"
                        class="w-full py-4 md:py-6 bg-white text-slate-950 rounded-2xl md:rounded-4xl font-black uppercase tracking-widest text-sm hover:bg-amber-500 hover:text-white transition-all transform hover:-translate-y-2 shadow-2xl active:scale-95 relative z-10 touch-manipulation">
                    Próximo Nível <x-icon name="arrow-right-long" class="ml-3 w-4 h-4 inline" />
                </button>
            </div>
        </div>

        <!-- Main Game Area -->
        <div class="max-w-5xl mx-auto space-y-6 md:space-y-10">
            <!-- Drop Zones -->
            <div class="bg-white dark:bg-gray-900 rounded-2xl md:rounded-[3rem] border-4 border-dashed border-gray-100 dark:border-gray-800 p-4 sm:p-6 md:p-10 lg:p-16 shadow-inner flex flex-col justify-center gap-6 md:gap-12 group transition-all duration-700">
                <div class="text-center">
                    <span class="px-4 md:px-6 py-2 bg-slate-900 text-amber-500 rounded-xl md:rounded-2xl text-[9px] md:text-[10px] font-black uppercase tracking-[0.2em] md:tracking-[0.3em] border border-amber-500/20 shadow-2xl" x-text="currentReference">TEXTO BÍBLICO</span>
                </div>

                <div class="flex flex-wrap justify-center gap-2 sm:gap-3 md:gap-4 lg:gap-6 leading-relaxed">
                    <template x-for="(word, index) in puzzleWords" :key="'slot-' + index">
                        <div
                            :data-drop-index="index"
                            class="relative inline-flex items-center justify-center min-w-[70px] sm:min-w-[90px] md:min-w-[100px] lg:min-w-[120px] h-12 sm:h-14 md:h-16 lg:h-20 px-3 sm:px-4 md:px-6 lg:px-8 rounded-xl md:rounded-2xl lg:rounded-3xl border-2 transition-all duration-500 active:scale-95 select-none touch-manipulation"
                            :class="{
                                'border-dashed border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/20': !word.filled,
                                'border-white bg-slate-950 text-white font-black shadow-2xl scale-105 md:scale-110 rotate-1': word.filled,
                                'border-amber-500 bg-amber-500/5 scale-105': isOver === index && !word.filled
                            }"
                            @dragover.prevent="isOver = index"
                            @dragleave="isOver = null"
                            @drop="handleDrop($event, index)"
                            @click="handleSlotClick(index)"
                        >
                            <span class="text-sm sm:text-base md:text-lg lg:text-xl xl:text-2xl tracking-tighter italic truncate max-w-full" x-text="word.text" :class="{'opacity-0': !word.filled}"></span>
                            <div x-show="!word.filled" class="absolute inset-0 rounded-xl md:rounded-2xl lg:rounded-3xl bg-amber-500/5 animate-pulse"></div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Word Bank -->
            <div class="bg-gray-100 dark:bg-gray-800 p-4 sm:p-6 md:p-10 rounded-2xl md:rounded-[3rem] space-y-4 md:space-y-10 border border-gray-200 dark:border-gray-700">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 px-2 md:px-4">
                    <h3 class="text-[9px] md:text-[10px] font-black uppercase tracking-widest text-gray-500">Banco de Inspiração</h3>
                    <div class="flex items-center gap-2 md:gap-3 text-[8px] md:text-[9px] font-black text-amber-600 uppercase tracking-widest bg-amber-500/5 px-3 md:px-4 py-2 rounded-full border border-amber-500/10">
                        <x-icon name="hand-pointer" style="duotone" class="w-3 h-3 md:w-3.5 md:h-3.5" /> Toque para selecionar
                    </div>
                </div>

                <div class="flex flex-wrap justify-center gap-2 sm:gap-3 md:gap-4">
                    <template x-for="item in bankWords" :key="item.id">
                        <div
                            draggable="true"
                            @dragstart="handleDragStart($event, item)"
                            @dragend="handleDragEnd"
                            @touchstart.passive="handleTouchStart($event, item)"
                            @touchmove.prevent="handleTouchMove($event)"
                            @touchend="handleTouchEnd($event)"
                            @click="selectWord(item)"
                            class="bg-white dark:bg-gray-900 border-2 px-4 sm:px-5 md:px-6 lg:px-8 py-3 sm:py-4 md:py-5 rounded-xl md:rounded-2xl lg:rounded-3xl text-base sm:text-lg md:text-xl font-black text-gray-900 dark:text-white cursor-pointer select-none transition-all shadow-lg hover:-translate-y-1 md:hover:-translate-y-2 active:scale-95 relative group/word overflow-hidden touch-manipulation"
                            :class="{
                                'opacity-50 grayscale scale-90': draggingId === item.id,
                                'animate-shake border-red-500 text-red-500 bg-red-50 dark:bg-red-900/10': item.shake,
                                'border-amber-500 bg-amber-50 dark:bg-amber-900/20 ring-2 ring-amber-500/50': selectedWord && selectedWord.id === item.id,
                                'border-gray-100 dark:border-white/5 hover:border-amber-500 hover:text-amber-500': !selectedWord || selectedWord.id !== item.id
                            }"
                        >
                            <span x-text="item.text" class="relative z-10"></span>
                            <div class="absolute inset-0 bg-amber-500/10 opacity-0 group-hover/word:opacity-100 transition-opacity"></div>
                        </div>
                    </template>
                </div>

                <!-- Instructions for mobile -->
                <div class="text-center pt-4 md:hidden">
                    <p class="text-xs text-gray-500">
                        <x-icon name="lightbulb" style="duotone" class="w-4 h-4 inline mr-1 text-amber-500" />
                        Toque na palavra, depois toque no espaço vazio
                    </p>
                </div>
            </div>

            <!-- Stats Info (Hidden on mobile) -->
            <div class="hidden lg:block bg-indigo-600 p-6 md:p-8 rounded-3xl md:rounded-4xl text-white relative overflow-hidden shadow-2xl shadow-indigo-600/20 max-w-xl mx-auto">
                <x-icon name="lightbulb-on" style="duotone" class="absolute -right-4 -bottom-4 w-20 h-20 md:w-24 md:h-24 text-white/10 transform rotate-12" />
                <span class="block text-[10px] font-black uppercase tracking-[0.2em] mb-2 text-indigo-200">Dica Bíblica</span>
                <p class="text-sm font-bold leading-relaxed italic pr-4">"Guardo no coração as tuas palavras para não pecar contra ti." <span class="block mt-1 text-amber-500 not-italic">Salmos 119:11</span></p>
            </div>
        </div>
    </div>
    </div>
</div>

<style>
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-8px) rotate(-1deg); }
        75% { transform: translateX(8px) rotate(1deg); }
    }
    .animate-shake {
        animation: shake 0.4s cubic-bezier(.36,.07,.19,.97) both;
    }
</style>

@push('scripts')
<script>
function verseMasterGame() {
    return {
        score: 0,
        versesCompleted: 0,
        loading: false,
        isVictory: false,
        currentReference: '...',
        fullText: '',
        puzzleWords: [],
        bankWords: [],
        isOver: null,
        draggingId: null,
        draggedItem: null,
        selectedWord: null,
        xpGained: 0,

        init() {
            this.setVerse(@json($verse->full_reference ?? 'Bíblia Sagrada'), @json($verse->text ?? 'Nenhum versículo encontrado.'));
        },

        setVerse(ref, text) {
            this.currentReference = ref;
            this.fullText = text;
            this.isVictory = false;
            this.selectedWord = null;

            const words = this.fullText.split(/\s+/).filter(w => w.length > 0);

            this.puzzleWords = words.map((w, i) => ({
                text: w,
                filled: false,
                id: i
            }));

            this.bankWords = words.map((w, i) => ({
                id: i,
                text: w,
                shake: false
            })).sort(() => Math.random() - 0.5);

            this.loading = false;
        },

        selectWord(item) {
            if (this.selectedWord && this.selectedWord.id === item.id) {
                this.selectedWord = null;
            } else {
                this.selectedWord = item;
                if (navigator.vibrate) navigator.vibrate(20);
            }
        },

        handleSlotClick(index) {
            if (!this.selectedWord) return;
            
            const slot = this.puzzleWords[index];
            if (slot.filled) return;

            const normalizedSelected = this.normalizeWord(this.selectedWord.text);
            const normalizedSlot = this.normalizeWord(slot.text);

            if (normalizedSelected === normalizedSlot) {
                slot.filled = true;
                this.score += 10;
                this.bankWords = this.bankWords.filter(w => w.id !== this.selectedWord.id);
                this.selectedWord = null;
                if (navigator.vibrate) navigator.vibrate(50);
                this.checkVictory();
            } else {
                this.triggerShake(this.selectedWord);
                if (navigator.vibrate) navigator.vibrate([100, 30, 100]);
            }
        },

        normalizeWord(str) {
            return str.toLowerCase().replace(/[.,!?;:'"]/g, '').trim();
        },

        async nextLevel() {
            this.loading = true;
            try {
                const response = await fetch('{{ route("memberpanel.ebd.arcade.get-verse") }}');
                const result = await response.json();

                if (result.success) {
                    this.setVerse(result.reference, result.text);
                } else {
                    alert('Erro ao carregar versículo.');
                    this.loading = false;
                }
            } catch (error) {
                console.error('Error fetching verse:', error);
                this.loading = false;
            }
        },

        handleDragStart(e, item) {
            this.draggingId = item.id;
            this.draggedItem = item;
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/plain', JSON.stringify(item));
        },

        handleDragEnd() {
            this.draggingId = null;
            this.isOver = null;
        },

        // Touch Support
        touchDragItem: null,
        touchOffsetX: 0,
        touchOffsetY: 0,

        handleTouchStart(e, item) {
            this.draggingId = item.id;
            this.draggedItem = item;

            const touch = e.touches[0];
            const target = e.target.closest('.cursor-pointer, [draggable="true"]');
            if(!target) return;

            const rect = target.getBoundingClientRect();
            this.touchOffsetX = touch.clientX - rect.left;
            this.touchOffsetY = touch.clientY - rect.top;

            this.touchDragItem = target.cloneNode(true);
            this.touchDragItem.style.position = 'fixed';
            this.touchDragItem.style.zIndex = '9999';
            this.touchDragItem.style.pointerEvents = 'none';
            this.touchDragItem.style.opacity = '0.9';
            this.touchDragItem.style.width = rect.width + 'px';
            this.touchDragItem.style.left = (touch.clientX - this.touchOffsetX) + 'px';
            this.touchDragItem.style.top = (touch.clientY - this.touchOffsetY) + 'px';
            this.touchDragItem.style.transform = 'scale(1.1) rotate(3deg)';
            this.touchDragItem.style.boxShadow = '0 20px 40px rgba(0,0,0,0.3)';
            document.body.appendChild(this.touchDragItem);
        },

        handleTouchMove(e) {
            if (!this.touchDragItem) return;
            const touch = e.touches[0];
            this.touchDragItem.style.left = (touch.clientX - this.touchOffsetX) + 'px';
            this.touchDragItem.style.top = (touch.clientY - this.touchOffsetY) + 'px';

            // Find drop zone under touch
            const elemBelow = document.elementFromPoint(touch.clientX, touch.clientY);
            const dropZone = elemBelow ? elemBelow.closest('[data-drop-index]') : null;
            if (dropZone) {
                this.isOver = parseInt(dropZone.getAttribute('data-drop-index'));
            } else {
                this.isOver = null;
            }
        },

        handleTouchEnd(e) {
            if (!this.touchDragItem) return;

            this.touchDragItem.remove();
            this.touchDragItem = null;

            const touch = e.changedTouches[0];
            const target = document.elementFromPoint(touch.clientX, touch.clientY);
            const dropZone = target ? target.closest('[data-drop-index]') : null;

            if (dropZone) {
                const index = parseInt(dropZone.getAttribute('data-drop-index'));
                this.handleDrop(null, index);
            }

            this.handleDragEnd();
        },

        handleDrop(e, index) {
            this.isOver = null;
            const slot = this.puzzleWords[index];

            if (slot.filled) return;

            const normalizedDragged = this.normalizeWord(this.draggedItem.text);
            const normalizedSlot = this.normalizeWord(slot.text);

            if (normalizedDragged === normalizedSlot) {
                slot.filled = true;
                this.score += 10;
                this.bankWords = this.bankWords.filter(w => w.id !== this.draggedItem.id);
                if (navigator.vibrate) navigator.vibrate(50);
                this.checkVictory();
            } else {
                this.triggerShake(this.draggedItem);
                if (navigator.vibrate) navigator.vibrate([100, 30, 100]);
            }
        },

        triggerShake(item) {
            const bankItem = this.bankWords.find(w => w.id === item.id);
            if (bankItem) {
                bankItem.shake = true;
                setTimeout(() => bankItem.shake = false, 500);
            }
        },

        checkVictory() {
            if (this.puzzleWords.every(w => w.filled)) {
                setTimeout(() => {
                    this.isVictory = true;
                    this.versesCompleted++;
                    this.score += 50;
                    this.saveScore();
                }, 500);
            }
        },

        async saveScore() {
            try {
                const response = await fetch('{{ route("memberpanel.ebd.arcade.submit", "versemaster") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        score: this.score,
                        metadata: {
                            verses_completed: this.versesCompleted,
                            last_reference: this.currentReference
                        }
                    })
                });
                const data = await response.json();
                this.xpGained = data.xp_gained || 50;
            } catch (e) {
                console.error('Error saving score:', e);
            }
        }
    }
}
</script>
@endpush
@endsection
