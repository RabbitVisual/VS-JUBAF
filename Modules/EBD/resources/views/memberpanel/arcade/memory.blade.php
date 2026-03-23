@extends('memberpanel::components.layouts.master')

@section('title', 'Arca da Memória - Arcade')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-slate-950 transition-colors duration-200 pb-12" x-data="memoryGame()">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 pt-4 sm:pt-6 space-y-4 sm:space-y-6">

        @include('ebd::memberpanel.arcade.partials.game-header', [
            'gameTitle' => 'Arca da Memória',
            'gameSubtitle' => 'Encontre os pares bíblicos. Escolha a dificuldade e jogue!',
            'icon' => 'cards',
            'accent' => 'emerald',
        ])

        <!-- Difficulty + Stats (when playing) -->
        <div class="flex flex-wrap items-center justify-center sm:justify-between gap-3 p-3 sm:p-4 bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-slate-800 shadow-sm">
            <div x-show="!isPlaying && !gameOver" class="flex gap-2">
                <span class="text-xs font-bold text-gray-500 dark:text-slate-400 uppercase self-center mr-2">Dificuldade:</span>
                <button @click="difficulty = 'easy'" class="px-3 py-2 rounded-xl text-xs font-bold transition-all touch-manipulation"
                        :class="difficulty === 'easy' ? 'bg-emerald-500 text-white' : 'bg-gray-100 dark:bg-slate-800 text-gray-600 dark:text-slate-400 hover:bg-gray-200 dark:hover:bg-slate-700'">Fácil</button>
                <button @click="difficulty = 'medium'" class="px-3 py-2 rounded-xl text-xs font-bold transition-all touch-manipulation"
                        :class="difficulty === 'medium' ? 'bg-amber-500 text-white' : 'bg-gray-100 dark:bg-slate-800 text-gray-600 dark:text-slate-400 hover:bg-gray-200 dark:hover:bg-slate-700'">Médio</button>
                <button @click="difficulty = 'hard'" class="px-3 py-2 rounded-xl text-xs font-bold transition-all touch-manipulation"
                        :class="difficulty === 'hard' ? 'bg-red-500 text-white' : 'bg-gray-100 dark:bg-slate-800 text-gray-600 dark:text-slate-400 hover:bg-gray-200 dark:hover:bg-slate-700'">Difícil</button>
            </div>
            <div x-show="isPlaying || gameOver" class="flex items-center gap-4">
                <div class="flex items-center gap-2 px-4 py-2 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl border border-emerald-100 dark:border-emerald-800">
                    <x-icon name="stopwatch" class="w-5 h-5 text-emerald-500 shrink-0" />
                    <span class="text-lg font-black tabular-nums text-gray-900 dark:text-white" x-text="formatTime(timer)">0:00</span>
                </div>
                <div class="flex items-center gap-2 px-4 py-2 bg-amber-50 dark:bg-amber-900/20 rounded-xl border border-amber-100 dark:border-amber-800">
                    <x-icon name="layer-group" class="w-5 h-5 text-amber-500 shrink-0" />
                    <span class="text-lg font-black tabular-nums text-gray-900 dark:text-white"><span x-text="matchedPairs">0</span>/<span x-text="totalPairs">0</span></span>
                </div>
            </div>
        </div>

    <!-- Start Screen -->
    <div x-show="!isPlaying && !gameOver" class="max-w-2xl mx-auto text-center space-y-6 sm:space-y-8 py-6 sm:py-10">
        <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl p-6 sm:p-8 md:p-12 shadow-xl border border-gray-100 dark:border-slate-800">
            <div class="w-20 h-20 bg-emerald-500 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-xl shadow-emerald-500/30">
                <x-icon name="cards" style="duotone" class="w-10 h-10 text-white" />
            </div>
            <h2 class="text-2xl md:text-3xl font-black text-gray-900 dark:text-white mb-4">Encontre os Pares Bíblicos</h2>
            <p class="text-gray-500 mb-8">Relacione personagens com seus feitos, objetos com seus significados. Teste sua memória bíblica!</p>
            
            <div class="grid grid-cols-3 gap-4 mb-8 text-center">
                <div class="bg-emerald-50 dark:bg-emerald-900/20 p-4 rounded-2xl">
                    <span class="text-2xl font-black text-emerald-600">6</span>
                    <span class="block text-xs text-gray-500 mt-1">Pares (Fácil)</span>
                </div>
                <div class="bg-amber-50 dark:bg-amber-900/20 p-4 rounded-2xl">
                    <span class="text-2xl font-black text-amber-600">12</span>
                    <span class="block text-xs text-gray-500 mt-1">Pares (Médio)</span>
                </div>
                <div class="bg-red-50 dark:bg-red-900/20 p-4 rounded-2xl">
                    <span class="text-2xl font-black text-red-600">18</span>
                    <span class="block text-xs text-gray-500 mt-1">Pares (Difícil)</span>
                </div>
            </div>
            
            <button @click="startGame()" class="w-full py-5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-2xl font-black text-lg uppercase tracking-widest shadow-xl shadow-emerald-600/30 transition-all hover:-translate-y-1 active:scale-95">
                <x-icon name="play" style="solid" class="w-5 h-5 inline mr-2" /> Iniciar Jogo
            </button>
        </div>
    </div>

    <!-- Game Board -->
    <div x-show="isPlaying" x-transition class="max-w-5xl mx-auto px-2 sm:px-4">
        <div class="grid gap-2 sm:gap-3 md:gap-4"
             :class="{
                 'grid-cols-3 sm:grid-cols-4': difficulty === 'easy',
                 'grid-cols-4 sm:grid-cols-6': difficulty === 'medium',
                 'grid-cols-4 sm:grid-cols-6 lg:grid-cols-6': difficulty === 'hard'
             }">
            <template x-for="card in cards" :key="card.id">
                <div class="aspect-square cursor-pointer select-none touch-manipulation" 
                     @click="flipCard(card)"
                     @touchend.prevent="flipCard(card)">
                    <div class="w-full h-full relative transition-transform duration-500 rounded-xl sm:rounded-2xl shadow-lg"
                         :class="{'[transform:rotateY(180deg)]': card.flipped || card.matched}"
                         style="transform-style: preserve-3d;">

                        <!-- Front (Hidden) -->
                        <div class="absolute inset-0 rounded-xl sm:rounded-2xl flex items-center justify-center border-2 border-emerald-600/50 transition-colors bg-gradient-to-br from-emerald-700 to-emerald-900"
                             :class="{'hover:border-emerald-400 hover:from-emerald-600': !card.flipped && !card.matched}"
                             style="backface-visibility: hidden;">
                            <x-icon name="cross" style="duotone" class="w-6 h-6 sm:w-8 sm:h-8 text-emerald-400/50" />
                        </div>

                        <!-- Back (Revealed) -->
                        <div class="absolute inset-0 rounded-xl sm:rounded-2xl flex flex-col items-center justify-center border-2 p-1 sm:p-2 text-center bg-white dark:bg-gray-800"
                             :class="card.matched ? 'border-amber-500 bg-amber-50 dark:bg-amber-900/20' : 'border-emerald-500'"
                             style="backface-visibility: hidden; transform: rotateY(180deg);">
                            <i class="fa-duotone mb-1 sm:mb-2 text-xl sm:text-2xl md:text-3xl"
                               :class="'fa-' + card.icon + ' ' + (card.matched ? 'text-amber-500' : 'text-emerald-600')"></i>
                            <span class="text-[9px] sm:text-[10px] md:text-xs font-bold leading-tight text-gray-700 dark:text-gray-200 line-clamp-2" x-text="card.text"></span>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Game Over Modal -->
    <div x-show="gameOver" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" 
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm p-4">
        <div class="bg-white dark:bg-gray-900 rounded-3xl md:rounded-[3rem] p-8 md:p-16 shadow-2xl border border-gray-100 dark:border-gray-800 text-center space-y-6 md:space-y-8 max-w-md w-full">
            <div class="w-20 h-20 bg-amber-500 rounded-3xl flex items-center justify-center mx-auto shadow-xl shadow-amber-500/30 animate-bounce">
                <x-icon name="trophy" style="duotone" class="w-10 h-10 text-white" />
            </div>
            <div>
                <h2 class="text-3xl md:text-4xl font-black text-gray-900 dark:text-white">Parabéns!</h2>
                <p class="text-gray-500 mt-2">Você completou o desafio!</p>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-2xl">
                    <span class="block text-xs font-black text-gray-400 uppercase">Tempo</span>
                    <span class="text-2xl font-black text-emerald-500" x-text="formatTime(timer)"></span>
                </div>
                <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-2xl">
                    <span class="block text-xs font-black text-gray-400 uppercase">Pontuação</span>
                    <span class="text-2xl font-black text-amber-500" x-text="score"></span>
                </div>
            </div>

            <div class="bg-emerald-50 dark:bg-emerald-900/20 p-4 rounded-2xl">
                <span class="text-sm text-emerald-600 dark:text-emerald-400 font-bold">+<span x-text="xpGained"></span> XP Ganho!</span>
            </div>

            <div class="flex flex-col sm:flex-row justify-center gap-3 md:gap-4 pt-4">
                <button @click="restartGame()" class="px-6 md:px-8 py-3 md:py-4 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl font-bold transition-all shadow-lg">
                    <x-icon name="rotate-right" style="solid" class="w-4 h-4 inline mr-2" /> Jogar Novamente
                </button>
                <a href="{{ route('memberpanel.ebd.arcade.index') }}" class="px-6 md:px-8 py-3 md:py-4 bg-gray-200 dark:bg-gray-800 text-gray-800 dark:text-white rounded-xl font-bold transition-all">
                    <x-icon name="door-open" style="solid" class="w-4 h-4 inline mr-2" /> Sair
                </a>
            </div>
        </div>
    </div>
    </div>
</div>

@push('scripts')
<script>
function memoryGame() {
    return {
        cards: [],
        flippedCards: [],
        matchedPairs: 0,
        totalPairs: 0,
        timer: 0,
        interval: null,
        gameOver: false,
        isPlaying: false,
        isChecking: false,
        difficulty: 'medium',
        score: 0,
        xpGained: 0,

        // Complete biblical pairs database (24 pairs)
        allPairs: [
            // Characters & Actions
            { matchId: 1, text: 'Noé', icon: 'user-beard' },
            { matchId: 1, text: 'Arca', icon: 'ship' },
            { matchId: 2, text: 'Davi', icon: 'crown' },
            { matchId: 2, text: 'Golias', icon: 'person-falling-burst' },
            { matchId: 3, text: 'Moisés', icon: 'wand-magic-sparkles' },
            { matchId: 3, text: 'Mar Vermelho', icon: 'water' },
            { matchId: 4, text: 'Jonas', icon: 'person-drowning' },
            { matchId: 4, text: 'Grande Peixe', icon: 'fish' },
            { matchId: 5, text: 'Daniel', icon: 'hands-praying' },
            { matchId: 5, text: 'Cova dos Leões', icon: 'paw' },
            { matchId: 6, text: 'Jesus', icon: 'cross' },
            { matchId: 6, text: 'Ressurreição', icon: 'sun-bright' },
            // Objects & Meaning
            { matchId: 7, text: 'Sansão', icon: 'dumbbell' },
            { matchId: 7, text: 'Cabelos', icon: 'head-side' },
            { matchId: 8, text: 'Abraão', icon: 'star' },
            { matchId: 8, text: 'Isaque', icon: 'baby' },
            { matchId: 9, text: 'José', icon: 'shirt' },
            { matchId: 9, text: 'Túnica Colorida', icon: 'palette' },
            { matchId: 10, text: 'Elias', icon: 'fire' },
            { matchId: 10, text: 'Carruagem de Fogo', icon: 'horse' },
            { matchId: 11, text: 'Pedro', icon: 'key' },
            { matchId: 11, text: 'Negou 3x', icon: 'bird' },
            { matchId: 12, text: 'Judas', icon: 'coins' },
            { matchId: 12, text: '30 Moedas', icon: 'sack-dollar' },
            // Places & Events
            { matchId: 13, text: 'Belém', icon: 'star-christmas' },
            { matchId: 13, text: 'Nascimento', icon: 'baby-carriage' },
            { matchId: 14, text: 'Jardim do Éden', icon: 'tree' },
            { matchId: 14, text: 'Fruto Proibido', icon: 'apple-whole' },
            { matchId: 15, text: 'Torre de Babel', icon: 'building' },
            { matchId: 15, text: 'Confusão de Línguas', icon: 'comments' },
            { matchId: 16, text: 'Jericó', icon: 'trumpet' },
            { matchId: 16, text: 'Muralhas Caídas', icon: 'landmark-dome' },
            { matchId: 17, text: 'Monte Sinai', icon: 'mountain' },
            { matchId: 17, text: '10 Mandamentos', icon: 'scroll' },
            { matchId: 18, text: 'Gólgota', icon: 'skull' },
            { matchId: 18, text: 'Crucificação', icon: 'cross' },
            // More pairs
            { matchId: 19, text: 'Rute', icon: 'wheat' },
            { matchId: 19, text: 'Noemi', icon: 'heart' },
            { matchId: 20, text: 'Maria', icon: 'person-dress' },
            { matchId: 20, text: 'Mãe de Jesus', icon: 'hands-holding-child' },
            { matchId: 21, text: 'João Batista', icon: 'water' },
            { matchId: 21, text: 'Batismo', icon: 'dove' },
            { matchId: 22, text: 'Salomão', icon: 'gavel' },
            { matchId: 22, text: 'Sabedoria', icon: 'brain' },
            { matchId: 23, text: 'Ester', icon: 'crown' },
            { matchId: 23, text: 'Rainha', icon: 'chess-queen' },
            { matchId: 24, text: 'Jó', icon: 'face-sad-tear' },
            { matchId: 24, text: 'Paciência', icon: 'hourglass' },
        ],

        startGame() {
            this.isPlaying = true;
            this.init();
        },

        init() {
            this.cards = this.generateCards();
            this.totalPairs = this.cards.length / 2;
            this.flippedCards = [];
            this.matchedPairs = 0;
            this.timer = 0;
            this.gameOver = false;
            this.isChecking = false;
            this.score = 0;
            this.xpGained = 0;

            if (this.interval) clearInterval(this.interval);
            this.interval = setInterval(() => {
                if (!this.gameOver && this.isPlaying) this.timer++;
            }, 1000);
        },

        generateCards() {
            let numPairs;
            switch(this.difficulty) {
                case 'easy': numPairs = 6; break;
                case 'medium': numPairs = 12; break;
                case 'hard': numPairs = 18; break;
                default: numPairs = 12;
            }

            // Shuffle and pick pairs
            const shuffledPairs = [...this.allPairs].sort(() => Math.random() - 0.5);
            const selectedCards = [];
            const usedMatchIds = new Set();
            
            for (const card of shuffledPairs) {
                if (usedMatchIds.size >= numPairs) break;
                if (!usedMatchIds.has(card.matchId)) {
                    // Find both cards of this pair
                    const pair = this.allPairs.filter(c => c.matchId === card.matchId);
                    selectedCards.push(...pair);
                    usedMatchIds.add(card.matchId);
                }
            }

            // Create deck with unique IDs
            let deck = selectedCards.map((item, index) => ({
                ...item,
                id: index,
                flipped: false,
                matched: false
            }));

            return deck.sort(() => Math.random() - 0.5);
        },

        flipCard(card) {
            if (card.flipped || card.matched || this.isChecking || !this.isPlaying) return;

            card.flipped = true;
            this.flippedCards.push(card);

            // Haptic feedback
            if (navigator.vibrate) navigator.vibrate(30);

            if (this.flippedCards.length === 2) {
                this.checkMatch();
            }
        },

        checkMatch() {
            this.isChecking = true;
            const [card1, card2] = this.flippedCards;

            if (card1.matchId === card2.matchId) {
                card1.matched = true;
                card2.matched = true;
                this.matchedPairs++;
                this.score += 100;
                this.flippedCards = [];
                this.isChecking = false;

                if (navigator.vibrate) navigator.vibrate([50, 30, 50]);

                if (this.matchedPairs === this.totalPairs) {
                    this.endGame();
                }
            } else {
                setTimeout(() => {
                    card1.flipped = false;
                    card2.flipped = false;
                    this.flippedCards = [];
                    this.isChecking = false;
                }, 1000);
            }
        },

        endGame() {
            clearInterval(this.interval);
            this.gameOver = true;
            
            // Calculate score based on time and difficulty
            const difficultyMultiplier = this.difficulty === 'hard' ? 3 : (this.difficulty === 'medium' ? 2 : 1);
            const timeBonus = Math.max(0, 500 - (this.timer * 2));
            this.score = (this.score + timeBonus) * difficultyMultiplier;
            
            this.saveScore();
        },

        restartGame() {
            this.gameOver = false;
            this.isPlaying = false;
        },

        async saveScore() {
            try {
                const response = await fetch('{{ route("memberpanel.ebd.arcade.submit", "arca-da-memoria") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        score: this.score,
                        metadata: {
                            time_seconds: this.timer,
                            difficulty: this.difficulty,
                            pairs: this.totalPairs
                        }
                    })
                });
                const data = await response.json();
                this.xpGained = data.xp_gained || 0;
            } catch (e) {
                console.error("Save score failed", e);
            }
        },

        formatTime(seconds) {
            const m = Math.floor(seconds / 60);
            const s = seconds % 60;
            return `${m}:${s.toString().padStart(2, '0')}`;
        }
    }
}
</script>
@endpush
@endsection
