@extends('memberpanel::components.layouts.master')

@section('title', 'Forca da Fé - Arcade')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-slate-950 transition-colors duration-200 pb-12" x-data="hangmanGame()">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 pt-4 sm:pt-6 space-y-4 sm:space-y-6">

        @include('ebd::memberpanel.arcade.partials.game-header', [
            'gameTitle' => 'Forca da Fé',
            'gameSubtitle' => 'Adivinhe palavras bíblicas letra por letra. 6 vidas por palavra.',
            'icon' => 'keyboard',
            'accent' => 'purple',
        ])

    <!-- Game Area -->
    <div class="min-h-[380px] sm:min-h-[450px] relative">

        <!-- Start Screen -->
        <div x-show="!isPlaying && !gameOver" class="text-center space-y-6 sm:space-y-8 py-6 sm:py-10">
            <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl p-6 sm:p-8 md:p-12 shadow-xl border border-gray-100 dark:border-slate-800">
                <div class="w-20 h-20 bg-purple-500 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-xl shadow-purple-500/30">
                    <x-icon name="keyboard" style="duotone" class="w-10 h-10 text-white" />
                </div>
                <h2 class="text-2xl md:text-3xl font-black text-gray-900 dark:text-white mb-4">Forca da Fé</h2>
                <p class="text-gray-500 mb-8">Adivinhe palavras bíblicas letra por letra. Você tem 6 vidas por palavra!</p>
                
                <div class="grid grid-cols-2 gap-4 mb-8 max-w-xs mx-auto">
                    <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-2xl">
                        <span class="text-3xl font-black text-purple-600">10</span>
                        <span class="block text-xs text-gray-500 mt-1">Palavras</span>
                    </div>
                    <div class="bg-red-50 dark:bg-red-900/20 p-4 rounded-2xl">
                        <span class="text-3xl font-black text-red-600">6</span>
                        <span class="block text-xs text-gray-500 mt-1">Vidas</span>
                    </div>
                </div>
                
                <button @click="startGame()" class="w-full py-5 bg-purple-600 hover:bg-purple-500 text-white rounded-2xl font-black text-lg uppercase tracking-widest shadow-xl shadow-purple-600/30 transition-all hover:-translate-y-1 active:scale-95 touch-manipulation">
                    <x-icon name="play" style="solid" class="w-5 h-5 inline mr-2" /> Iniciar Jogo
                </button>
            </div>
        </div>

        <div x-show="isPlaying && !gameOver" x-transition class="space-y-6 md:space-y-10">

            <!-- Category & Hint -->
            <div class="text-center space-y-2">
                <span class="px-4 py-2 bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-300 rounded-full text-xs font-black uppercase tracking-widest" x-text="category">CATEGORIA</span>
                <p class="text-gray-500 text-sm transition-all" x-show="lives <= 3" x-transition>
                    <x-icon name="lightbulb" style="duotone" class="w-4 h-4 inline text-amber-500" />
                    Dica: <span class="font-bold" x-text="hint">...</span>
                </p>
            </div>

            <!-- Lives Display -->
            <div class="flex justify-center items-center gap-4">
                <span class="text-xs font-black text-gray-400 uppercase tracking-widest">Vidas:</span>
                <div class="flex gap-1.5">
                    <template x-for="i in 6">
                        <div class="w-6 h-6 md:w-8 md:h-8 rounded-full transition-all flex items-center justify-center"
                             :class="i <= lives ? 'bg-red-500 shadow-lg shadow-red-500/30' : 'bg-gray-200 dark:bg-gray-800'">
                            <x-icon name="heart" style="solid" class="w-3 h-3 md:w-4 md:h-4" 
                                    x-bind:class="i <= lives ? 'text-white' : 'text-gray-400'" />
                        </div>
                    </template>
                </div>
            </div>

            <!-- Word Display -->
            <div class="flex flex-wrap justify-center gap-1.5 sm:gap-2 md:gap-3 min-h-[60px] md:min-h-[80px] px-2">
                <template x-for="(char, index) in word.split('')" :key="index">
                    <div class="flex items-center justify-center text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-black uppercase"
                         :class="char === ' ' ? 'w-4 md:w-6' : 'w-8 h-12 sm:w-10 sm:h-14 md:w-12 md:h-16 lg:w-14 lg:h-20 border-b-4 border-gray-300 dark:border-gray-600 text-gray-800 dark:text-white'">
                        <span x-text="guesses.includes(simplify(char)) || char === ' ' ? char : ''"></span>
                    </div>
                </template>
            </div>

            <!-- Keyboard -->
            <div class="bg-gray-100 dark:bg-gray-800 rounded-2xl md:rounded-3xl p-3 sm:p-4 md:p-6">
                <div class="grid grid-cols-9 sm:grid-cols-9 md:grid-cols-13 gap-1.5 sm:gap-2 max-w-2xl mx-auto">
                    <template x-for="letter in alphabet" :key="letter">
                        <button @click="guess(letter)"
                                :disabled="guesses.includes(letter)"
                                class="aspect-square rounded-lg md:rounded-xl font-bold text-base sm:text-lg md:text-xl transition-all touch-manipulation active:scale-90 min-h-[40px] min-w-[40px]"
                                :class="{
                                    'bg-white dark:bg-gray-900 hover:bg-purple-100 dark:hover:bg-purple-900/20 text-gray-700 dark:text-gray-300 shadow-md hover:shadow-lg': !guesses.includes(letter),
                                    'bg-emerald-500 text-white shadow-emerald-500/30': guesses.includes(letter) && wordSimplified.includes(letter),
                                    'bg-red-500 text-white shadow-red-500/30': guesses.includes(letter) && !wordSimplified.includes(letter)
                                }">
                            <span x-text="letter"></span>
                        </button>
                    </template>
                </div>
            </div>

            <!-- Next Button (Win) -->
            <div class="flex justify-center" x-show="wonRound">
                <button @click="nextRound()" class="px-8 py-4 bg-purple-600 hover:bg-purple-500 text-white rounded-xl font-bold shadow-lg shadow-purple-500/30 transition-all flex items-center gap-2 animate-bounce touch-manipulation active:scale-95">
                    <x-icon name="check" style="solid" class="w-5 h-5" /> 
                    <span x-text="currentWordIndex >= totalRounds - 1 ? 'Ver Resultado' : 'Próxima Palavra'"></span>
                    <x-icon name="arrow-right" class="w-4 h-4" />
                </button>
            </div>
        </div>

        <!-- Game Over Modal -->
        <div x-show="gameOver" x-transition class="text-center space-y-6 md:space-y-8 bg-white dark:bg-gray-900 rounded-2xl md:rounded-[3rem] p-8 md:p-16 shadow-2xl border border-gray-100 dark:border-gray-800">
            <div class="w-20 h-20 md:w-24 md:h-24 rounded-full flex items-center justify-center mx-auto shadow-2xl animate-bounce"
                 :class="lives > 0 ? 'bg-amber-500 shadow-amber-500/30' : 'bg-red-500 shadow-red-500/30'">
                <x-icon name="trophy" style="duotone" class="w-10 h-10 md:w-12 md:h-12 text-white" x-show="lives > 0" />
                <x-icon name="face-sad-tear" style="duotone" class="w-10 h-10 md:w-12 md:h-12 text-white" x-show="lives <= 0" />
            </div>
            <div>
                <h2 class="text-3xl md:text-4xl font-black text-gray-900 dark:text-white" x-text="lives > 0 ? 'Parabéns!' : 'Fim de Jogo!'"></h2>
                <p class="text-gray-500 mt-2" x-show="lives <= 0">A palavra era: <span class="font-black text-purple-600 uppercase" x-text="word"></span></p>
            </div>
            <div class="text-5xl md:text-6xl font-black text-purple-600 tracking-tighter" x-text="score">0</div>
            
            <div class="grid grid-cols-2 gap-4 max-w-sm mx-auto">
                <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-2xl">
                    <span class="block text-xs font-black text-gray-400 uppercase">Palavras</span>
                    <span class="text-2xl font-bold text-emerald-500" x-text="wordsCompleted">0</span>
                </div>
                <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-2xl">
                    <span class="block text-xs font-black text-gray-400 uppercase">XP Ganho</span>
                    <span class="text-2xl font-bold text-purple-500">+<span x-text="xpGained">0</span></span>
                </div>
            </div>
            
            <div class="flex flex-col sm:flex-row justify-center gap-3 md:gap-4 pt-4">
                <button @click="restartGame()" class="px-6 md:px-8 py-3 md:py-4 bg-purple-600 hover:bg-purple-500 text-white rounded-xl font-bold transition-all touch-manipulation active:scale-95">
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
function hangmanGame() {
    return {
        // 100+ Biblical words organized by category
        allWords: [
            // Personagens (Characters)
            { word: 'ABRAAO', category: 'Personagem', hint: 'Pai da fé' },
            { word: 'MOISES', category: 'Personagem', hint: 'Libertou Israel do Egito' },
            { word: 'DAVI', category: 'Personagem', hint: 'Rei pastor de Israel' },
            { word: 'SALOMAO', category: 'Personagem', hint: 'Rei mais sábio' },
            { word: 'DANIEL', category: 'Personagem', hint: 'Na cova dos leões' },
            { word: 'JOSE', category: 'Personagem', hint: 'Vendido pelos irmãos' },
            { word: 'ELIAS', category: 'Personagem', hint: 'Profeta de fogo' },
            { word: 'JONAS', category: 'Personagem', hint: 'Engolido pelo peixe' },
            { word: 'SANSAO', category: 'Personagem', hint: 'Força nos cabelos' },
            { word: 'SAMUEL', category: 'Personagem', hint: 'Profeta desde criança' },
            { word: 'PEDRO', category: 'Personagem', hint: 'Pescador apóstolo' },
            { word: 'PAULO', category: 'Personagem', hint: 'Apóstolo dos gentios' },
            { word: 'MARIA', category: 'Personagem', hint: 'Mãe de Jesus' },
            { word: 'ESTER', category: 'Personagem', hint: 'Rainha corajosa' },
            { word: 'RUTE', category: 'Personagem', hint: 'Moabita fiel' },
            { word: 'JUDAS', category: 'Personagem', hint: 'Traidor de Jesus' },
            { word: 'PILATOS', category: 'Personagem', hint: 'Governador romano' },
            { word: 'GOLIAS', category: 'Personagem', hint: 'Gigante filisteu' },
            { word: 'NOE', category: 'Personagem', hint: 'Construtor da arca' },
            { word: 'ISAQUE', category: 'Personagem', hint: 'Filho da promessa' },
            
            // Lugares (Places)
            { word: 'JERUSALEM', category: 'Lugar', hint: 'Cidade santa' },
            { word: 'BELEM', category: 'Lugar', hint: 'Cidade do nascimento de Jesus' },
            { word: 'NAZARE', category: 'Lugar', hint: 'Cidade de criação de Jesus' },
            { word: 'EDEN', category: 'Lugar', hint: 'Jardim do paraíso' },
            { word: 'SINAI', category: 'Lugar', hint: 'Monte dos mandamentos' },
            { word: 'JERICO', category: 'Lugar', hint: 'Muralhas que caíram' },
            { word: 'EGITO', category: 'Lugar', hint: 'Terra da escravidão' },
            { word: 'CANAA', category: 'Lugar', hint: 'Terra prometida' },
            { word: 'GALILEA', category: 'Lugar', hint: 'Região do ministério de Jesus' },
            { word: 'SAMARIA', category: 'Lugar', hint: 'Mulher junto ao poço' },
            { word: 'BABILONIA', category: 'Lugar', hint: 'Local do exílio' },
            { word: 'GOLGOTA', category: 'Lugar', hint: 'Lugar da crucificação' },
            
            // Livros (Books)
            { word: 'GENESIS', category: 'Livro', hint: 'Primeiro livro da Bíblia' },
            { word: 'SALMOS', category: 'Livro', hint: 'Livro de cânticos' },
            { word: 'PROVERBIOS', category: 'Livro', hint: 'Livro de sabedoria' },
            { word: 'APOCALIPSE', category: 'Livro', hint: 'Último livro da Bíblia' },
            { word: 'EXODO', category: 'Livro', hint: 'Saída do Egito' },
            { word: 'LEVITICO', category: 'Livro', hint: 'Leis sacerdotais' },
            { word: 'MATEUS', category: 'Livro', hint: 'Primeiro evangelho' },
            { word: 'ROMANOS', category: 'Livro', hint: 'Carta de Paulo' },
            { word: 'ATOS', category: 'Livro', hint: 'História da igreja primitiva' },
            { word: 'HEBREUS', category: 'Livro', hint: 'Cristo é superior' },
            
            // Eventos (Events)
            { word: 'CRIACAO', category: 'Evento', hint: 'Em seis dias' },
            { word: 'DILUVIO', category: 'Evento', hint: 'Água sobre a terra' },
            { word: 'PASCOA', category: 'Evento', hint: 'Libertação do Egito' },
            { word: 'PENTECOSTES', category: 'Evento', hint: 'Descida do Espírito' },
            { word: 'BATISMO', category: 'Evento', hint: 'No rio Jordão' },
            { word: 'CRUCIFICACAO', category: 'Evento', hint: 'Morte na cruz' },
            { word: 'RESSURREICAO', category: 'Evento', hint: 'Vitória sobre a morte' },
            { word: 'ASCENSAO', category: 'Evento', hint: 'Subida ao céu' },
            { word: 'TRANSFIGURACAO', category: 'Evento', hint: 'Glória revelada' },
            { word: 'ARREBATAMENTO', category: 'Evento', hint: 'Encontro nas nuvens' },
            
            // Objetos (Objects)
            { word: 'ARCA', category: 'Objeto', hint: 'Construída por Noé' },
            { word: 'TABUAS', category: 'Objeto', hint: 'Escritas por Deus' },
            { word: 'SARÇA', category: 'Objeto', hint: 'Ardia mas não queimava' },
            { word: 'MANA', category: 'Objeto', hint: 'Pão do céu' },
            { word: 'CAJADO', category: 'Objeto', hint: 'De Moisés' },
            { word: 'HARPA', category: 'Objeto', hint: 'Tocada por Davi' },
            { word: 'ESPADA', category: 'Objeto', hint: 'Do Espírito' },
            { word: 'COROA', category: 'Objeto', hint: 'De espinhos' },
            { word: 'TUNICA', category: 'Objeto', hint: 'Colorida de José' },
            { word: 'LAMPADA', category: 'Objeto', hint: 'Para os pés' },
            
            // Conceitos (Concepts)
            { word: 'GRACA', category: 'Conceito', hint: 'Favor imerecido' },
            { word: 'SALVACAO', category: 'Conceito', hint: 'Livramento do pecado' },
            { word: 'REDENCAO', category: 'Conceito', hint: 'Comprado por preço' },
            { word: 'SANTIFICACAO', category: 'Conceito', hint: 'Processo de santidade' },
            { word: 'JUSTIFICACAO', category: 'Conceito', hint: 'Declarado justo' },
            { word: 'ARREPENDIMENTO', category: 'Conceito', hint: 'Mudança de mente' },
            { word: 'PERDAO', category: 'Conceito', hint: 'Cancelar a dívida' },
            { word: 'ALIANCA', category: 'Conceito', hint: 'Pacto com Deus' },
            { word: 'PROFECIA', category: 'Conceito', hint: 'Palavra do Senhor' },
            { word: 'MILAGRE', category: 'Conceito', hint: 'Obra sobrenatural' },
            
            // Frutos do Espírito
            { word: 'AMOR', category: 'Fruto do Espírito', hint: 'O maior de todos' },
            { word: 'ALEGRIA', category: 'Fruto do Espírito', hint: 'Gozo interior' },
            { word: 'PAZ', category: 'Fruto do Espírito', hint: 'Tranquilidade divina' },
            { word: 'PACIENCIA', category: 'Fruto do Espírito', hint: 'Longanimidade' },
            { word: 'BONDADE', category: 'Fruto do Espírito', hint: 'Benevolência' },
            { word: 'FIDELIDADE', category: 'Fruto do Espírito', hint: 'Lealdade constante' },
            { word: 'MANSIDAO', category: 'Fruto do Espírito', hint: 'Humildade gentil' },
            { word: 'DOMINIO', category: 'Fruto do Espírito', hint: 'Autocontrole' },
            
            // Animais bíblicos
            { word: 'SERPENTE', category: 'Animal', hint: 'Tentou Eva' },
            { word: 'CORDEIRO', category: 'Animal', hint: 'Sacrifício pascal' },
            { word: 'POMBA', category: 'Animal', hint: 'Símbolo do Espírito' },
            { word: 'LEAO', category: 'Animal', hint: 'De Judá' },
            { word: 'CAMELO', category: 'Animal', hint: 'Pelo fundo da agulha' },
            { word: 'JUMENTO', category: 'Animal', hint: 'Entrada em Jerusalém' },
            { word: 'PEIXE', category: 'Animal', hint: 'Multiplicado por Jesus' },
            { word: 'OVELHA', category: 'Animal', hint: 'Perdida e encontrada' },
            
            // Tribos de Israel
            { word: 'JUDA', category: 'Tribo', hint: 'Tribo real' },
            { word: 'LEVI', category: 'Tribo', hint: 'Tribo sacerdotal' },
            { word: 'BENJAMIM', category: 'Tribo', hint: 'Tribo de Paulo' },
            { word: 'EFRAIM', category: 'Tribo', hint: 'Filho de José' },
            
            // Apóstolos
            { word: 'MATEUS', category: 'Apóstolo', hint: 'Ex-cobrador de impostos' },
            { word: 'TIAGO', category: 'Apóstolo', hint: 'Filho de Zebedeu' },
            { word: 'JOAO', category: 'Apóstolo', hint: 'O discípulo amado' },
            { word: 'TOME', category: 'Apóstolo', hint: 'Duvidou da ressurreição' },
            { word: 'ANDRE', category: 'Apóstolo', hint: 'Irmão de Pedro' },
            { word: 'FILIPE', category: 'Apóstolo', hint: 'De Betsaida' },
        ],

        words: [],
        currentWordIndex: 0,
        totalRounds: 10,
        word: '',
        wordSimplified: '',
        category: '',
        hint: '',
        guesses: [],
        lives: 6,
        score: 0,
        isPlaying: false,
        gameOver: false,
        wonRound: false,
        wordsCompleted: 0,
        xpGained: 0,
        alphabet: 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.split(''),

        startGame() {
            this.isPlaying = true;
            this.init();
        },

        init() {
            this.words = [...this.allWords].sort(() => Math.random() - 0.5).slice(0, this.totalRounds);
            this.currentWordIndex = 0;
            this.score = 0;
            this.wordsCompleted = 0;
            this.gameOver = false;
            this.loadWord();
        },

        loadWord() {
            if (this.currentWordIndex >= this.words.length) {
                this.endGame();
                return;
            }
            const data = this.words[this.currentWordIndex];
            this.word = data.word;
            this.wordSimplified = this.simplify(this.word);
            this.category = data.category;
            this.hint = data.hint;
            this.guesses = [];
            this.lives = 6;
            this.wonRound = false;
        },

        simplify(str) {
            return str.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toUpperCase();
        },

        guess(letter) {
            if (this.guesses.includes(letter) || this.gameOver || this.wonRound) return;

            this.guesses.push(letter);

            if (!this.wordSimplified.includes(letter)) {
                this.lives--;
                if (navigator.vibrate) navigator.vibrate(100);
                if (this.lives <= 0) {
                    this.gameOver = true;
                    this.saveScore();
                }
            } else {
                if (navigator.vibrate) navigator.vibrate(50);
                this.checkWin();
            }
        },

        checkWin() {
            const uniqueLetters = [...new Set(this.wordSimplified.split('').filter(c => c !== ' '))];
            const allGuessed = uniqueLetters.every(l => this.guesses.includes(l));

            if (allGuessed) {
                const points = 50 + (this.lives * 10);
                this.score += points;
                this.wordsCompleted++;
                this.wonRound = true;
                
                if (navigator.vibrate) navigator.vibrate([50, 30, 50]);
                
                if (this.currentWordIndex >= this.words.length - 1) {
                    setTimeout(() => this.endGame(), 500);
                }
            }
        },

        nextRound() {
            this.currentWordIndex++;
            this.loadWord();
        },

        endGame() {
            this.gameOver = true;
            this.saveScore();
        },

        restartGame() {
            this.gameOver = false;
            this.isPlaying = false;
        },

        async saveScore() {
            try {
                const response = await fetch('{{ route("memberpanel.ebd.arcade.submit", "forca-da-fe") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        score: this.score,
                        metadata: {
                            words_completed: this.wordsCompleted
                        }
                    })
                });
                const data = await response.json();
                this.xpGained = data.xp_gained || 0;
            } catch (e) {
                console.error("Save score failed", e);
            }
        }
    }
}
</script>
@endpush
@endsection
