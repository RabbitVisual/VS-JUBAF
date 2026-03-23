@extends('memberpanel::components.layouts.master')

@section('title', 'Espada Afiada - Arcade')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-slate-950 transition-colors duration-200 pb-12" x-data="swordGame()" x-init="init()">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 pt-4 sm:pt-6 space-y-4 sm:space-y-6">

        @include('ebd::memberpanel.arcade.partials.game-header', [
            'gameTitle' => 'Espada Afiada',
            'gameSubtitle' => 'Toque nos livros da categoria exibida. Reflexo bíblico!',
            'icon' => 'sword',
            'accent' => 'blue',
        ])

        <!-- Stats bar (during game) -->
        <div x-show="isPlaying || gameOver" x-transition class="flex flex-wrap items-center justify-center sm:justify-between gap-3 p-3 sm:p-4 bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-slate-800 shadow-sm">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-gray-500 dark:text-slate-400 uppercase">Vidas</span>
                <div class="flex gap-1">
                    <template x-for="i in 3">
                        <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-full flex items-center justify-center transition-all"
                             :class="i <= lives ? 'bg-red-500 shadow-lg shadow-red-500/30' : 'bg-gray-200 dark:bg-slate-700'">
                            <x-icon name="heart" style="solid" class="w-3 h-3 sm:w-4 sm:h-4" :class="i <= lives ? 'text-white' : 'text-gray-400'" />
                        </div>
                    </template>
                </div>
            </div>
            <div class="flex items-center gap-2 px-4 py-2 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-100 dark:border-blue-800">
                <x-icon name="star" style="duotone" class="w-5 h-5 text-blue-500 shrink-0" />
                <span class="text-lg sm:text-xl font-black tabular-nums text-gray-900 dark:text-white" x-text="score">0</span>
            </div>
        </div>

    <!-- Game Area -->
    <div class="relative">
        <div class="relative bg-gray-900 rounded-2xl sm:rounded-3xl overflow-hidden border border-gray-700 dark:border-slate-700 shadow-xl"
             style="aspect-ratio: 4/3; min-height: 280px; max-height: 70vh;">

            <!-- Start Screen -->
            <div x-show="!isPlaying && !gameOver" x-transition class="absolute inset-0 flex flex-col items-center justify-center p-6 sm:p-10 text-center space-y-6 md:space-y-8 z-20 bg-gray-900/95 backdrop-blur-sm">
                <div class="w-20 h-20 bg-blue-500 rounded-3xl flex items-center justify-center shadow-xl shadow-blue-500/30">
                    <x-icon name="sword" style="duotone" class="w-10 h-10 text-white" />
                </div>
                <div>
                    <h2 class="text-2xl sm:text-3xl md:text-4xl font-black text-white mb-2">Pronto para o Desafio?</h2>
                    <p class="text-gray-400 max-w-md text-sm sm:text-base">Livros da Bíblia vão aparecer. Toque apenas nos livros que pertencem à categoria exibida!</p>
                </div>
                
                <!-- Category Preview -->
                <div class="grid grid-cols-2 gap-3 w-full max-w-sm">
                    <div class="bg-emerald-500/10 border border-emerald-500/30 p-3 rounded-xl text-center">
                        <x-icon name="check" style="solid" class="w-5 h-5 text-emerald-500 mx-auto mb-1" />
                        <span class="text-xs text-emerald-400">Toque nos CERTOS</span>
                    </div>
                    <div class="bg-red-500/10 border border-red-500/30 p-3 rounded-xl text-center">
                        <x-icon name="xmark" style="solid" class="w-5 h-5 text-red-500 mx-auto mb-1" />
                        <span class="text-xs text-red-400">Evite os ERRADOS</span>
                    </div>
                </div>
                
                <button @click="startGame()" class="px-8 sm:px-10 py-4 sm:py-5 bg-blue-600 hover:bg-blue-500 text-white rounded-2xl font-black text-base sm:text-xl uppercase tracking-widest shadow-lg shadow-blue-600/30 transition-all hover:scale-105 active:scale-95 touch-manipulation">
                    <x-icon name="play" style="solid" class="w-5 h-5 inline mr-2" /> INICIAR
                </button>
            </div>

            <!-- Gameplay -->
            <div class="absolute inset-0 overflow-hidden transition-opacity duration-200"
                 x-bind:class="isPlaying ? 'opacity-100' : 'opacity-0 pointer-events-none'">
                
                <!-- HUD -->
                <div class="absolute top-0 left-0 right-0 p-3 sm:p-4 md:p-6 flex justify-between items-start z-10 bg-gradient-to-b from-gray-900 via-gray-900/80 to-transparent">
                    <div class="bg-gray-800/90 backdrop-blur px-4 sm:px-6 py-2 sm:py-3 rounded-xl sm:rounded-2xl border border-gray-700">
                        <span class="block text-[9px] sm:text-[10px] font-black uppercase text-gray-500">Categoria Alvo</span>
                        <span class="text-base sm:text-lg md:text-xl font-black text-blue-400" x-text="currentCategory">...</span>
                    </div>
                    <div class="bg-gray-800/90 backdrop-blur px-4 sm:px-6 py-2 sm:py-3 rounded-xl sm:rounded-2xl border border-gray-700">
                        <span class="block text-[9px] sm:text-[10px] font-black uppercase text-gray-500">Tempo</span>
                        <span class="text-base sm:text-lg md:text-xl font-black text-amber-400 tabular-nums" x-text="gameTime + 's'">60s</span>
                    </div>
                </div>

                <!-- Falling Items -->
                <template x-for="item in items" :key="item.id">
                    <button
                        class="absolute px-3 sm:px-4 md:px-6 py-2 sm:py-3 rounded-lg sm:rounded-xl font-bold text-sm sm:text-base text-white shadow-lg transition-transform active:scale-90 touch-manipulation select-none"
                        :class="item.isTarget ? 'bg-emerald-600 hover:bg-emerald-500 shadow-emerald-500/30' : 'bg-slate-700 hover:bg-slate-600 shadow-slate-500/20'"
                        :style="`left: ${item.x}%; top: ${item.y}%; transform: translateX(-50%) rotate(${item.rotation}deg);`"
                        @click="clickItem(item)"
                        @touchend.prevent="clickItem(item)"
                    >
                        <span x-text="item.text"></span>
                    </button>
                </template>

                <!-- Score Popup -->
                <div x-show="showScorePopup" x-transition:enter="transition ease-out duration-200" 
                     x-transition:enter-start="opacity-0 scale-50" x-transition:enter-end="opacity-100 scale-100"
                     class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 pointer-events-none z-20">
                    <span class="text-4xl sm:text-5xl md:text-6xl font-black"
                          :class="lastScorePositive ? 'text-emerald-500' : 'text-red-500'"
                          x-text="lastScorePositive ? '+10' : '-1'"></span>
                </div>
            </div>

            <!-- Game Over Modal -->
            <div x-show="gameOver" x-transition class="absolute inset-0 flex flex-col items-center justify-center p-6 sm:p-10 text-center space-y-6 md:space-y-8 z-30 bg-gray-900/95 backdrop-blur">
                <div class="w-20 h-20 md:w-24 md:h-24 rounded-full flex items-center justify-center shadow-2xl animate-bounce"
                     :class="lives > 0 ? 'bg-amber-500 shadow-amber-500/30' : 'bg-red-500 shadow-red-500/30'">
                    <x-icon name="trophy" style="duotone" class="w-10 h-10 md:w-12 md:h-12 text-white" x-show="lives > 0" />
                    <x-icon name="skull" style="duotone" class="w-10 h-10 md:w-12 md:h-12 text-white" x-show="lives <= 0" />
                </div>
                <h2 class="text-3xl sm:text-4xl md:text-5xl font-black text-white" x-text="lives > 0 ? 'Tempo Esgotado!' : 'Fim de Jogo!'"></h2>
                <div class="text-5xl sm:text-6xl md:text-7xl font-black text-blue-500 tracking-tighter" x-text="score">0</div>
                
                <div class="grid grid-cols-2 gap-4 w-full max-w-xs">
                    <div class="bg-gray-800 p-4 rounded-2xl">
                        <span class="block text-xs font-black text-gray-500 uppercase">Acertos</span>
                        <span class="text-2xl font-bold text-emerald-500" x-text="correctHits">0</span>
                    </div>
                    <div class="bg-gray-800 p-4 rounded-2xl">
                        <span class="block text-xs font-black text-gray-500 uppercase">XP Ganho</span>
                        <span class="text-2xl font-bold text-blue-500">+<span x-text="xpGained">0</span></span>
                    </div>
                </div>
                
                <div class="flex flex-col sm:flex-row justify-center gap-3 md:gap-4 w-full max-w-sm">
                    <button @click="startGame()" class="flex-1 px-6 md:px-8 py-3 md:py-4 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-bold transition-all touch-manipulation active:scale-95">
                        <x-icon name="rotate-right" style="solid" class="w-4 h-4 inline mr-2" /> Novamente
                    </button>
                    <a href="{{ route('memberpanel.ebd.arcade.index') }}" class="flex-1 px-6 md:px-8 py-3 md:py-4 bg-gray-800 hover:bg-gray-700 text-white rounded-xl font-bold transition-all text-center">
                        <x-icon name="door-open" style="solid" class="w-4 h-4 inline mr-2" /> Sair
                    </a>
                </div>
            </div>

        </div>
    </div>
    </div>
</div>

@push('head_scripts')
<script>
window.swordGame = function () {
    return {
        init() {
            this.items = [];
        },
        isPlaying: false,
        gameOver: false,
        score: 0,
        lives: 3,
        items: [],
        gameInterval: null,
        spawnInterval: null,
        timerInterval: null,
        currentCategory: '',
        targetBooks: [],
        distractorBooks: [],
        categoryIndex: 0,
        gameTime: 60,
        correctHits: 0,
        xpGained: 0,
        showScorePopup: false,
        lastScorePositive: true,

        // All 7 divisions of the Bible
        categories: [
            { 
                name: "Pentateuco", 
                targets: ["Gênesis", "Êxodo", "Levítico", "Números", "Deuteronômio"], 
                distractors: ["Josué", "Juízes", "Rute", "Salmos", "Provérbios", "Mateus", "Marcos"] 
            },
            { 
                name: "Livros Históricos", 
                targets: ["Josué", "Juízes", "Rute", "1 Samuel", "2 Samuel", "1 Reis", "2 Reis", "Esdras", "Neemias", "Ester"], 
                distractors: ["Gênesis", "Salmos", "Isaías", "Mateus", "Apocalipse"] 
            },
            { 
                name: "Livros Poéticos", 
                targets: ["Jó", "Salmos", "Provérbios", "Eclesiastes", "Cantares"], 
                distractors: ["Gênesis", "Êxodo", "Isaías", "Mateus", "Romanos"] 
            },
            { 
                name: "Profetas Maiores", 
                targets: ["Isaías", "Jeremias", "Lamentações", "Ezequiel", "Daniel"], 
                distractors: ["Oséias", "Joel", "Amós", "Salmos", "Mateus"] 
            },
            { 
                name: "Profetas Menores", 
                targets: ["Oséias", "Joel", "Amós", "Obadias", "Jonas", "Miqueias", "Naum", "Habacuque", "Sofonias", "Ageu", "Zacarias", "Malaquias"], 
                distractors: ["Isaías", "Jeremias", "Daniel", "Salmos", "Gênesis"] 
            },
            { 
                name: "Evangelhos", 
                targets: ["Mateus", "Marcos", "Lucas", "João"], 
                distractors: ["Atos", "Romanos", "Gênesis", "Salmos", "Apocalipse"] 
            },
            { 
                name: "Cartas de Paulo", 
                targets: ["Romanos", "1 Coríntios", "2 Coríntios", "Gálatas", "Efésios", "Filipenses", "Colossenses", "1 Tessalonicenses", "2 Tessalonicenses", "1 Timóteo", "2 Timóteo", "Tito", "Filemom"], 
                distractors: ["Hebreus", "Tiago", "1 Pedro", "Apocalipse", "Mateus"] 
            }
        ],

        startGame() {
            this.isPlaying = true;
            this.gameOver = false;
            this.score = 0;
            this.lives = 3;
            this.items = [];
            this.gameTime = 60;
            this.correctHits = 0;
            this.categoryIndex = Math.floor(Math.random() * this.categories.length);
            this.pickCategory();

            // Spawn initial items
            for (let i = 0; i < 3; i++) {
                setTimeout(() => this.spawnItem(), i * 300);
            }

            this.gameInterval = setInterval(() => this.updateLoop(), 50);
            this.spawnInterval = setInterval(() => this.spawnItem(), 1200);
            this.timerInterval = setInterval(() => {
                if (this.gameTime > 0) {
                    this.gameTime--;
                } else {
                    this.endGame();
                }
            }, 1000);

            // Change category every 20 seconds
            setInterval(() => {
                if (this.isPlaying && !this.gameOver) {
                    this.categoryIndex = (this.categoryIndex + 1) % this.categories.length;
                    this.pickCategory();
                }
            }, 20000);
        },

        pickCategory() {
            const cat = this.categories[this.categoryIndex];
            this.currentCategory = cat.name;
            this.targetBooks = cat.targets;
            this.distractorBooks = cat.distractors;
        },

        spawnItem() {
            if (!this.isPlaying || this.gameOver) return;
            if (!this.targetBooks || this.targetBooks.length === 0) return;

            const isTarget = Math.random() > 0.5;
            const pool = isTarget ? this.targetBooks : this.distractorBooks;
            const text = pool[Math.floor(Math.random() * pool.length)];

            this.items.push({
                id: Date.now() + Math.random(),
                text: text,
                isTarget: isTarget,
                x: Math.random() * 80 + 10,
                y: -8,
                speed: 0.3 + Math.random() * 0.4 + (60 - this.gameTime) * 0.005, // Speed increases over time
                rotation: Math.random() * 10 - 5
            });
        },

        updateLoop() {
            if (!this.isPlaying || this.gameOver) return;

            this.items.forEach(item => {
                item.y += item.speed;
            });

            // Remove items that fell off and penalize for missed targets
            for (let i = this.items.length - 1; i >= 0; i--) {
                if (this.items[i].y > 105) {
                    if (this.items[i].isTarget) {
                        this.lives--;
                        this.showScoreFeedback(false);
                        if (navigator.vibrate) navigator.vibrate(200);
                        if (this.lives <= 0) this.endGame();
                    }
                    this.items.splice(i, 1);
                }
            }
        },

        clickItem(item) {
            if (!this.isPlaying || this.gameOver) return;

            // Remove item
            this.items = this.items.filter(i => i.id !== item.id);

            if (item.isTarget) {
                this.score += 10;
                this.correctHits++;
                this.showScoreFeedback(true);
                if (navigator.vibrate) navigator.vibrate(50);
            } else {
                this.lives--;
                this.showScoreFeedback(false);
                if (navigator.vibrate) navigator.vibrate([100, 50, 100]);
                if (this.lives <= 0) this.endGame();
            }
        },

        showScoreFeedback(positive) {
            this.lastScorePositive = positive;
            this.showScorePopup = true;
            setTimeout(() => this.showScorePopup = false, 400);
        },

        endGame() {
            this.isPlaying = false;
            this.gameOver = true;
            clearInterval(this.gameInterval);
            clearInterval(this.spawnInterval);
            clearInterval(this.timerInterval);
            this.saveScore();
        },

        async saveScore() {
            try {
                const response = await fetch('{{ route("memberpanel.ebd.arcade.submit", "espada-afiada") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        score: this.score,
                        metadata: {
                            correct_hits: this.correctHits,
                            time_survived: 60 - this.gameTime
                        }
                    })
                });
                const data = await response.json();
                this.xpGained = data.xp_gained || 0;
            } catch (e) {
                console.error("Save score failed", e);
            }
        }
    };
}
</script>
@endpush
@endsection
