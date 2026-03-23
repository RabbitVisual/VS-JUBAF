@extends('memberpanel::components.layouts.master')

@section('title', 'Trio Bíblico - Arcade')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-slate-950 transition-colors duration-200 pb-12" x-data="trioGame()">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 pt-4 sm:pt-6 space-y-4 sm:space-y-6">

        @include('ebd::memberpanel.arcade.partials.game-header', [
            'gameTitle' => 'Trio Bíblico',
            'gameSubtitle' => 'Encontre grupos de 3 itens relacionados nas Escrituras.',
            'icon' => 'clone',
            'accent' => 'violet',
        ])

    <!-- Game Area -->
    <div class="max-w-4xl mx-auto relative px-2 sm:px-4">

        <!-- Start Screen -->
        <div x-show="!isPlaying && !gameOver" class="text-center space-y-8 py-10">
            <div class="bg-white dark:bg-gray-900 rounded-2xl md:rounded-3xl p-8 md:p-12 shadow-2xl border border-gray-100 dark:border-gray-800">
                <div class="w-20 h-20 bg-fuchsia-500 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-xl shadow-fuchsia-500/30">
                    <x-icon name="clone" style="duotone" class="w-10 h-10 text-white" />
                </div>
                <h2 class="text-2xl md:text-3xl font-black text-gray-900 dark:text-white mb-4">Trio Bíblico</h2>
                <p class="text-gray-500 mb-8">Encontre grupos de 3 itens relacionados! Selecione 3 cartas que formam um trio bíblico.</p>
                
                <div class="grid grid-cols-3 gap-4 mb-8 max-w-sm mx-auto text-center">
                    <div class="bg-fuchsia-50 dark:bg-fuchsia-900/20 p-4 rounded-2xl">
                        <span class="text-2xl font-black text-fuchsia-600">3</span>
                        <span class="block text-xs text-gray-500 mt-1">Por Trio</span>
                    </div>
                    <div class="bg-emerald-50 dark:bg-emerald-900/20 p-4 rounded-2xl">
                        <span class="text-2xl font-black text-emerald-600">100</span>
                        <span class="block text-xs text-gray-500 mt-1">Pts/Trio</span>
                    </div>
                    <div class="bg-amber-50 dark:bg-amber-900/20 p-4 rounded-2xl">
                        <span class="text-2xl font-black text-amber-600">6</span>
                        <span class="block text-xs text-gray-500 mt-1">Trios p/ Fase</span>
                    </div>
                </div>
                
                <button @click="startGame()" class="w-full py-5 bg-fuchsia-600 hover:bg-fuchsia-500 text-white rounded-2xl font-black text-lg uppercase tracking-widest shadow-xl shadow-fuchsia-600/30 transition-all hover:-translate-y-1 active:scale-95 touch-manipulation">
                    <x-icon name="play" style="solid" class="w-5 h-5 inline mr-2" /> Iniciar Jogo
                </button>
            </div>
        </div>

        <div x-show="isPlaying && !gameOver" x-transition class="space-y-6">
            <!-- Progress + Timer -->
            <div class="flex flex-wrap justify-between items-center gap-2">
                <span class="text-sm font-bold text-gray-500">Trios: <span x-text="triosFound">0</span>/<span x-text="totalTrios">6</span></span>
                <span class="text-sm font-bold text-fuchsia-500 tabular-nums">
                    <x-icon name="clock" style="duotone" class="w-4 h-4 inline mr-1" />
                    <span x-text="formatTime(timer)">0:00</span>
                </span>
                <span class="text-sm font-bold text-gray-400">Selecionados: <span x-text="selectedCards.length">0</span>/3</span>
            </div>

            <!-- Toast: trio correto -->
            <div x-show="lastFeedback" x-transition
                 class="rounded-xl py-3 px-4 text-center font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800"
                 x-text="lastFeedback">
            </div>

            <!-- Game Grid -->
            <div class="bg-white dark:bg-gray-900 rounded-2xl md:rounded-3xl p-4 sm:p-6 md:p-8 shadow-2xl border border-gray-100 dark:border-gray-800">
                <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-2 sm:gap-3 md:gap-4">
                    <template x-for="card in cards" :key="card.id">
                        <button @click="selectCard(card)"
                                :disabled="card.matched"
                                class="aspect-square p-2 sm:p-3 md:p-4 rounded-xl md:rounded-2xl border-2 font-bold text-xs sm:text-sm transition-all touch-manipulation active:scale-95 flex items-center justify-center text-center leading-tight"
                                :class="{
                                    'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:border-fuchsia-500': !card.selected && !card.matched,
                                    'border-fuchsia-500 bg-fuchsia-50 dark:bg-fuchsia-900/20 text-fuchsia-600 scale-105 shadow-lg': card.selected && !card.matched,
                                    'border-emerald-500 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 opacity-50': card.matched,
                                    'border-red-500 bg-red-50 dark:bg-red-900/20 animate-shake': card.shake
                                }">
                            <span x-text="card.text"></span>
                        </button>
                    </template>
                </div>
            </div>

            <!-- Checking hint (brief) -->
            <div class="text-center min-h-[2rem]" x-show="selectedCards.length === 3 && checking">
                <p class="text-fuchsia-500 text-sm font-bold">
                    <x-icon name="spinner" style="solid" class="w-4 h-4 inline mr-1 animate-spin" />
                    Verificando...
                </p>
            </div>
        </div>

        <!-- Game Over Modal -->
        <div x-show="gameOver" x-transition class="text-center space-y-6 md:space-y-8 bg-white dark:bg-gray-900 rounded-2xl md:rounded-[3rem] p-8 md:p-16 shadow-2xl border border-gray-100 dark:border-gray-800">
            <div class="w-20 h-20 md:w-24 md:h-24 bg-amber-500 rounded-full flex items-center justify-center mx-auto shadow-2xl shadow-amber-500/30 animate-bounce">
                <x-icon name="trophy" style="duotone" class="w-10 h-10 md:w-12 md:h-12 text-white" />
            </div>
            <div>
                <h2 class="text-3xl md:text-4xl font-black text-gray-900 dark:text-white">Parabéns!</h2>
                <p class="text-gray-500 mt-2">Você encontrou todos os trios!</p>
            </div>
            <div class="text-5xl md:text-6xl font-black text-fuchsia-600 tracking-tighter" x-text="score">0</div>
            
            <div class="grid grid-cols-3 gap-4 max-w-md mx-auto">
                <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-2xl">
                    <span class="block text-xs font-black text-gray-400 uppercase">Tempo</span>
                    <span class="text-xl font-bold text-fuchsia-500 tabular-nums" x-text="formatTime(timer)"></span>
                </div>
                <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-2xl">
                    <span class="block text-xs font-black text-gray-400 uppercase">Trios</span>
                    <span class="text-xl font-bold text-emerald-500"><span x-text="triosFound">6</span>/<span x-text="totalTrios">6</span></span>
                </div>
                <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-2xl">
                    <span class="block text-xs font-black text-gray-400 uppercase">XP</span>
                    <span class="text-xl font-bold text-amber-500">+<span x-text="xpGained">0</span></span>
                </div>
            </div>
            
            <div class="flex flex-col sm:flex-row justify-center gap-3 md:gap-4 pt-4">
                <button @click="restartGame()" class="px-6 md:px-8 py-3 md:py-4 bg-fuchsia-600 hover:bg-fuchsia-500 text-white rounded-xl font-bold transition-all touch-manipulation active:scale-95">
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

<style>
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-4px); }
        75% { transform: translateX(4px); }
    }
    .animate-shake {
        animation: shake 0.3s ease-in-out;
    }
</style>

@push('scripts')
<script>
function trioGame() {
    return {
        // 30+ Biblical Trios
        allTrios: [
            { id: 1, items: ["Pai", "Filho", "Espírito Santo"], category: "Trindade" },
            { id: 2, items: ["Gaspar", "Melquior", "Baltazar"], category: "Reis Magos" },
            { id: 3, items: ["Pedro", "Tiago", "João"], category: "Discípulos íntimos" },
            { id: 4, items: ["Abraão", "Isaque", "Jacó"], category: "Patriarcas" },
            { id: 5, items: ["Moisés", "Arão", "Miriã"], category: "Irmãos de Israel" },
            { id: 6, items: ["Fé", "Esperança", "Amor"], category: "Virtudes teologais" },
            { id: 7, items: ["Sadraque", "Mesaque", "Abede-Nego"], category: "Fornalha ardente" },
            { id: 8, items: ["Noé", "Sem", "Cam"], category: "Família de Noé" },
            { id: 9, items: ["Caim", "Abel", "Sete"], category: "Filhos de Adão" },
            { id: 10, items: ["Maria", "Marta", "Lázaro"], category: "Irmãos de Betânia" },
            { id: 11, items: ["Ouro", "Incenso", "Mirra"], category: "Presentes dos Magos" },
            { id: 12, items: ["Água", "Sangue", "Espírito"], category: "Testemunhos (1 João)" },
            { id: 13, items: ["Céu", "Terra", "Mar"], category: "Criação de Deus" },
            { id: 14, items: ["Leão", "Boi", "Águia"], category: "Seres viventes" },
            { id: 15, items: ["Rúben", "Simeão", "Levi"], category: "Primeiros filhos de Jacó" },
            { id: 16, items: ["Elias", "Eliseu", "Samuel"], category: "Grandes profetas" },
            { id: 17, items: ["Salomão", "Davi", "Saul"], category: "Reis de Israel" },
            { id: 18, items: ["Eva", "Sara", "Rebeca"], category: "Matriarcas" },
            { id: 19, items: ["Gênesis", "Êxodo", "Levítico"], category: "Primeiros livros" },
            { id: 20, items: ["Mateus", "Marcos", "Lucas"], category: "Evangelhos sinóticos" },
            { id: 21, items: ["Corpo", "Alma", "Espírito"], category: "Constituição humana" },
            { id: 22, items: ["Porta", "Caminho", "Verdade"], category: "Jesus é..." },
            { id: 23, items: ["Romanos", "Gálatas", "Efésios"], category: "Cartas de Paulo" },
            { id: 24, items: ["Josué", "Calebe", "Moisés"], category: "Espias de Canaã" },
            { id: 25, items: ["Judá", "Benjamim", "Efraim"], category: "Tribos importantes" },
            { id: 26, items: ["Belém", "Nazaré", "Jerusalém"], category: "Cidades de Jesus" },
            { id: 27, items: ["Batismo", "Tentação", "Transfiguração"], category: "Eventos de Jesus" },
            { id: 28, items: ["Salmos", "Provérbios", "Cantares"], category: "Livros poéticos" },
            { id: 29, items: ["Daniel", "Isaías", "Jeremias"], category: "Profetas maiores" },
            { id: 30, items: ["Jonas", "Amós", "Oséias"], category: "Profetas menores" },
        ],

        trios: [],
        cards: [],
        selectedCards: [],
        triosFound: 0,
        totalTrios: 6,
        timer: 0,
        timerInterval: null,
        score: 0,
        isPlaying: false,
        gameOver: false,
        checking: false,
        xpGained: 0,
        lastFeedback: '',

        startGame() {
            this.isPlaying = true;
            this.trios = [...this.allTrios].sort(() => Math.random() - 0.5).slice(0, this.totalTrios);
            this.cards = [];
            this.selectedCards = [];
            this.triosFound = 0;
            this.timer = 0;
            this.score = 0;
            this.gameOver = false;

            // Create cards from trios
            let cardId = 0;
            this.trios.forEach(trio => {
                trio.items.forEach(item => {
                    this.cards.push({
                        id: cardId++,
                        text: item,
                        trioId: trio.id,
                        selected: false,
                        matched: false,
                        shake: false
                    });
                });
            });

            // Shuffle cards
            this.cards = this.cards.sort(() => Math.random() - 0.5);

            // Start timer
            this.timerInterval = setInterval(() => {
                if (this.isPlaying && !this.gameOver) this.timer++;
            }, 1000);
        },

        selectCard(card) {
            if (card.matched || this.checking) return;

            if (card.selected) {
                // Deselect
                card.selected = false;
                this.selectedCards = this.selectedCards.filter(c => c.id !== card.id);
            } else {
                if (this.selectedCards.length >= 3) return;
                
                // Select
                card.selected = true;
                this.selectedCards.push(card);
                if (navigator.vibrate) navigator.vibrate(30);

                // Check if 3 selected
                if (this.selectedCards.length === 3) {
                    this.checkTrio();
                }
            }
        },

        checkTrio() {
            this.checking = true;

            setTimeout(() => {
                const trioIds = this.selectedCards.map(c => c.trioId);
                const allSame = trioIds.every(id => id === trioIds[0]);

                if (allSame) {
                    // Correct trio!
                    const trio = this.trios.find(t => t.id === trioIds[0]);
                    this.lastFeedback = trio ? `Trio correto! "${trio.category}" +100` : 'Trio correto! +100';
                    setTimeout(() => { this.lastFeedback = ''; }, 2000);

                    this.selectedCards.forEach(card => {
                        card.matched = true;
                        card.selected = false;
                    });
                    this.triosFound++;
                    this.score += 100;
                    if (navigator.vibrate) navigator.vibrate([50, 30, 50, 30, 50]);

                    if (this.triosFound >= this.totalTrios) {
                        this.endGame();
                    }
                } else {
                    // Wrong trio
                    this.selectedCards.forEach(card => {
                        card.shake = true;
                        setTimeout(() => card.shake = false, 300);
                        card.selected = false;
                    });
                    if (navigator.vibrate) navigator.vibrate([100, 50, 100]);
                }

                this.selectedCards = [];
                this.checking = false;
            }, 320);
        },

        endGame() {
            this.gameOver = true;
            clearInterval(this.timerInterval);
            // Time bonus
            const timeBonus = Math.max(0, 300 - this.timer);
            this.score += timeBonus;
            this.saveScore();
        },

        restartGame() {
            this.gameOver = false;
            this.isPlaying = false;
            clearInterval(this.timerInterval);
        },

        formatTime(seconds) {
            const m = Math.floor(seconds / 60);
            const s = seconds % 60;
            return `${m}:${s.toString().padStart(2, '0')}`;
        },

        async saveScore() {
            const url = '{{ route("memberpanel.ebd.arcade.submit", "trio-biblico") }}';
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        score: this.score,
                        metadata: {
                            duration: this.timer,
                            time_seconds: this.timer,
                            trios_found: this.triosFound
                        }
                    })
                });
                const text = await response.text();
                let data = {};
                try {
                    data = text ? JSON.parse(text) : {};
                } catch (_) {
                    this.xpGained = 0;
                    return;
                }
                this.xpGained = data.xp_gained ?? 0;
            } catch (e) {
                console.error("Save score failed", e);
                this.xpGained = 0;
            }
        }
    }
}
</script>
@endpush
@endsection
