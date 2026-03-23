@extends('memberpanel::components.layouts.master')

@section('title', 'Desafio dos Livros - Arcade')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-slate-950 transition-colors duration-200 pb-12" x-data="bookChallengeGame()">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 pt-4 sm:pt-6 space-y-4 sm:space-y-6">

        @include('ebd::memberpanel.arcade.partials.game-header', [
            'gameTitle' => 'Desafio dos Livros',
            'gameSubtitle' => 'Categorize e ordene os 66 livros da Bíblia.',
            'icon' => 'book-bible',
            'accent' => 'amber',
        ])

    <!-- Game Area -->
    <div class="relative">

        <!-- Start Screen -->
        <div x-show="!isPlaying && !gameOver" class="text-center space-y-8 py-10">
            <div class="bg-white dark:bg-gray-900 rounded-2xl md:rounded-3xl p-8 md:p-12 shadow-2xl border border-gray-100 dark:border-gray-800">
                <div class="w-20 h-20 bg-amber-500 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-xl shadow-amber-500/30">
                    <x-icon name="book-bible" style="duotone" class="w-10 h-10 text-white" />
                </div>
                <h2 class="text-2xl md:text-3xl font-black text-gray-900 dark:text-white mb-4">Desafio dos Livros</h2>
                <p class="text-gray-500 mb-8">Teste seu conhecimento sobre os 66 livros da Bíblia!</p>
                
                <!-- Mode Selection -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8 max-w-2xl mx-auto">
                    <button @click="gameMode = 'category'"
                            class="p-4 sm:p-6 rounded-2xl border-2 transition-all touch-manipulation"
                            :class="gameMode === 'category' ? 'border-amber-500 bg-amber-50 dark:bg-amber-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-amber-300'">
                        <x-icon name="folder-tree" style="duotone" class="w-8 h-8 mx-auto mb-2 text-amber-500" />
                        <span class="block text-sm font-bold text-gray-800 dark:text-white">Categorizar</span>
                        <span class="block text-xs text-gray-500 mt-1">AT ou NT?</span>
                    </button>
                    <button @click="gameMode = 'division'"
                            class="p-4 sm:p-6 rounded-2xl border-2 transition-all touch-manipulation"
                            :class="gameMode === 'division' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-blue-300'">
                        <x-icon name="sitemap" style="duotone" class="w-8 h-8 mx-auto mb-2 text-blue-500" />
                        <span class="block text-sm font-bold text-gray-800 dark:text-white">Divisão</span>
                        <span class="block text-xs text-gray-500 mt-1">Qual categoria?</span>
                    </button>
                    <button @click="gameMode = 'speed'"
                            class="p-4 sm:p-6 rounded-2xl border-2 transition-all touch-manipulation"
                            :class="gameMode === 'speed' ? 'border-red-500 bg-red-50 dark:bg-red-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-red-300'">
                        <x-icon name="bolt" style="duotone" class="w-8 h-8 mx-auto mb-2 text-red-500" />
                        <span class="block text-sm font-bold text-gray-800 dark:text-white">Velocidade</span>
                        <span class="block text-xs text-gray-500 mt-1">Antes ou depois?</span>
                    </button>
                </div>
                
                <button @click="startGame()" class="w-full py-5 bg-amber-600 hover:bg-amber-500 text-white rounded-2xl font-black text-lg uppercase tracking-widest shadow-xl shadow-amber-600/30 transition-all hover:-translate-y-1 active:scale-95 touch-manipulation">
                    <x-icon name="play" style="solid" class="w-5 h-5 inline mr-2" /> Iniciar Jogo
                </button>
            </div>
        </div>

        <div x-show="isPlaying && !gameOver" x-transition class="space-y-6">
            <!-- Progress -->
            <div class="flex justify-between items-center">
                <span class="text-sm font-bold text-gray-500">Pergunta <span x-text="currentQuestionIndex + 1"></span>/<span x-text="totalQuestions"></span></span>
                <div x-show="gameMode === 'speed'" class="flex items-center gap-2">
                    <x-icon name="stopwatch" style="duotone" class="w-4 h-4 text-red-500" />
                    <span class="text-lg font-black tabular-nums" :class="timer <= 5 ? 'text-red-500 animate-pulse' : 'text-gray-600'" x-text="timer + 's'"></span>
                </div>
            </div>

            <!-- Question Card -->
            <div class="bg-white dark:bg-gray-900 rounded-2xl md:rounded-3xl p-6 sm:p-8 md:p-12 shadow-2xl border border-gray-100 dark:border-gray-800 text-center">
                <div class="w-16 h-16 mx-auto bg-amber-100 dark:bg-amber-900/30 rounded-full flex items-center justify-center mb-6">
                    <x-icon name="book" style="duotone" class="w-8 h-8 text-amber-500" />
                </div>
                
                <h3 class="text-2xl sm:text-3xl md:text-4xl font-black text-gray-900 dark:text-white mb-4" x-text="currentBook.name">...</h3>
                
                <p class="text-gray-500 mb-8" x-text="questionText">...</p>

                <!-- Options based on mode -->
                <div class="grid gap-3 md:gap-4"
                     :class="gameMode === 'category' ? 'grid-cols-2' : (gameMode === 'division' ? 'grid-cols-2 sm:grid-cols-3 md:grid-cols-4' : 'grid-cols-2')">
                    <template x-for="option in currentOptions" :key="option.value">
                        <button @click="selectOption(option)"
                                :disabled="answered"
                                class="p-4 sm:p-5 rounded-xl md:rounded-2xl border-2 font-bold text-sm sm:text-base transition-all touch-manipulation active:scale-95"
                                :class="{
                                    'border-gray-200 dark:border-gray-700 hover:border-amber-500 hover:bg-amber-50 dark:hover:bg-amber-900/10': !answered,
                                    'border-emerald-500 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600': answered && option.isCorrect,
                                    'border-red-500 bg-red-50 dark:bg-red-900/20 text-red-600 opacity-60': answered && !option.isCorrect && selectedOption === option.value,
                                    'opacity-40': answered && !option.isCorrect && selectedOption !== option.value
                                }">
                            <span x-text="option.label"></span>
                        </button>
                    </template>
                </div>

                <!-- Feedback -->
                <div x-show="answered" class="mt-6 p-4 rounded-xl" 
                     :class="lastCorrect ? 'bg-emerald-50 dark:bg-emerald-900/20' : 'bg-red-50 dark:bg-red-900/20'">
                    <p class="font-bold" :class="lastCorrect ? 'text-emerald-600' : 'text-red-600'"
                       x-text="lastCorrect ? '+' + lastPoints + ' pontos!' : 'Incorreto!'"></p>
                    <p x-show="!lastCorrect" class="text-sm text-gray-500 mt-1">
                        Resposta: <span class="font-bold" x-text="correctAnswerText"></span>
                    </p>
                </div>

                <!-- Next Button -->
                <button @click="nextQuestion()" x-show="answered"
                        class="mt-6 px-8 py-4 bg-amber-600 hover:bg-amber-500 text-white rounded-xl font-bold transition-all touch-manipulation active:scale-95">
                    <span x-text="currentQuestionIndex >= totalQuestions - 1 ? 'Ver Resultado' : 'Próxima Pergunta'"></span>
                    <x-icon name="arrow-right" style="solid" class="w-4 h-4 inline ml-2" />
                </button>
            </div>
        </div>

        <!-- Game Over Modal -->
        <div x-show="gameOver" x-transition class="text-center space-y-6 md:space-y-8 bg-white dark:bg-gray-900 rounded-2xl md:rounded-[3rem] p-8 md:p-16 shadow-2xl border border-gray-100 dark:border-gray-800">
            <div class="w-20 h-20 md:w-24 md:h-24 bg-amber-500 rounded-full flex items-center justify-center mx-auto shadow-2xl shadow-amber-500/30 animate-bounce">
                <x-icon name="trophy" style="duotone" class="w-10 h-10 md:w-12 md:h-12 text-white" />
            </div>
            <div>
                <h2 class="text-3xl md:text-4xl font-black text-gray-900 dark:text-white">Fim de Jogo!</h2>
                <p class="text-gray-500 mt-2">Você conhece bem os livros da Bíblia!</p>
            </div>
            <div class="text-5xl md:text-6xl font-black text-amber-600 tracking-tighter" x-text="score">0</div>
            
            <div class="grid grid-cols-2 gap-4 max-w-sm mx-auto">
                <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-2xl">
                    <span class="block text-xs font-black text-gray-400 uppercase">Acertos</span>
                    <span class="text-2xl font-bold text-emerald-500" x-text="correctAnswers">0</span>/<span class="text-gray-400" x-text="totalQuestions"></span>
                </div>
                <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-2xl">
                    <span class="block text-xs font-black text-gray-400 uppercase">XP Ganho</span>
                    <span class="text-2xl font-bold text-amber-500">+<span x-text="xpGained">0</span></span>
                </div>
            </div>
            
            <div class="flex flex-col sm:flex-row justify-center gap-3 md:gap-4 pt-4">
                <button @click="restartGame()" class="px-6 md:px-8 py-3 md:py-4 bg-amber-600 hover:bg-amber-500 text-white rounded-xl font-bold transition-all touch-manipulation active:scale-95">
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
function bookChallengeGame() {
    return {
        // All 66 Bible books with data
        bibleBooks: [
            // Old Testament - Pentateuco (5)
            { name: "Gênesis", testament: "AT", division: "Pentateuco", order: 1 },
            { name: "Êxodo", testament: "AT", division: "Pentateuco", order: 2 },
            { name: "Levítico", testament: "AT", division: "Pentateuco", order: 3 },
            { name: "Números", testament: "AT", division: "Pentateuco", order: 4 },
            { name: "Deuteronômio", testament: "AT", division: "Pentateuco", order: 5 },
            // Históricos (12)
            { name: "Josué", testament: "AT", division: "Históricos", order: 6 },
            { name: "Juízes", testament: "AT", division: "Históricos", order: 7 },
            { name: "Rute", testament: "AT", division: "Históricos", order: 8 },
            { name: "1 Samuel", testament: "AT", division: "Históricos", order: 9 },
            { name: "2 Samuel", testament: "AT", division: "Históricos", order: 10 },
            { name: "1 Reis", testament: "AT", division: "Históricos", order: 11 },
            { name: "2 Reis", testament: "AT", division: "Históricos", order: 12 },
            { name: "1 Crônicas", testament: "AT", division: "Históricos", order: 13 },
            { name: "2 Crônicas", testament: "AT", division: "Históricos", order: 14 },
            { name: "Esdras", testament: "AT", division: "Históricos", order: 15 },
            { name: "Neemias", testament: "AT", division: "Históricos", order: 16 },
            { name: "Ester", testament: "AT", division: "Históricos", order: 17 },
            // Poéticos (5)
            { name: "Jó", testament: "AT", division: "Poéticos", order: 18 },
            { name: "Salmos", testament: "AT", division: "Poéticos", order: 19 },
            { name: "Provérbios", testament: "AT", division: "Poéticos", order: 20 },
            { name: "Eclesiastes", testament: "AT", division: "Poéticos", order: 21 },
            { name: "Cantares", testament: "AT", division: "Poéticos", order: 22 },
            // Profetas Maiores (5)
            { name: "Isaías", testament: "AT", division: "Profetas Maiores", order: 23 },
            { name: "Jeremias", testament: "AT", division: "Profetas Maiores", order: 24 },
            { name: "Lamentações", testament: "AT", division: "Profetas Maiores", order: 25 },
            { name: "Ezequiel", testament: "AT", division: "Profetas Maiores", order: 26 },
            { name: "Daniel", testament: "AT", division: "Profetas Maiores", order: 27 },
            // Profetas Menores (12)
            { name: "Oséias", testament: "AT", division: "Profetas Menores", order: 28 },
            { name: "Joel", testament: "AT", division: "Profetas Menores", order: 29 },
            { name: "Amós", testament: "AT", division: "Profetas Menores", order: 30 },
            { name: "Obadias", testament: "AT", division: "Profetas Menores", order: 31 },
            { name: "Jonas", testament: "AT", division: "Profetas Menores", order: 32 },
            { name: "Miqueias", testament: "AT", division: "Profetas Menores", order: 33 },
            { name: "Naum", testament: "AT", division: "Profetas Menores", order: 34 },
            { name: "Habacuque", testament: "AT", division: "Profetas Menores", order: 35 },
            { name: "Sofonias", testament: "AT", division: "Profetas Menores", order: 36 },
            { name: "Ageu", testament: "AT", division: "Profetas Menores", order: 37 },
            { name: "Zacarias", testament: "AT", division: "Profetas Menores", order: 38 },
            { name: "Malaquias", testament: "AT", division: "Profetas Menores", order: 39 },
            // New Testament - Evangelhos (4)
            { name: "Mateus", testament: "NT", division: "Evangelhos", order: 40 },
            { name: "Marcos", testament: "NT", division: "Evangelhos", order: 41 },
            { name: "Lucas", testament: "NT", division: "Evangelhos", order: 42 },
            { name: "João", testament: "NT", division: "Evangelhos", order: 43 },
            // Histórico NT (1)
            { name: "Atos", testament: "NT", division: "Histórico", order: 44 },
            // Cartas de Paulo (13)
            { name: "Romanos", testament: "NT", division: "Cartas de Paulo", order: 45 },
            { name: "1 Coríntios", testament: "NT", division: "Cartas de Paulo", order: 46 },
            { name: "2 Coríntios", testament: "NT", division: "Cartas de Paulo", order: 47 },
            { name: "Gálatas", testament: "NT", division: "Cartas de Paulo", order: 48 },
            { name: "Efésios", testament: "NT", division: "Cartas de Paulo", order: 49 },
            { name: "Filipenses", testament: "NT", division: "Cartas de Paulo", order: 50 },
            { name: "Colossenses", testament: "NT", division: "Cartas de Paulo", order: 51 },
            { name: "1 Tessalonicenses", testament: "NT", division: "Cartas de Paulo", order: 52 },
            { name: "2 Tessalonicenses", testament: "NT", division: "Cartas de Paulo", order: 53 },
            { name: "1 Timóteo", testament: "NT", division: "Cartas de Paulo", order: 54 },
            { name: "2 Timóteo", testament: "NT", division: "Cartas de Paulo", order: 55 },
            { name: "Tito", testament: "NT", division: "Cartas de Paulo", order: 56 },
            { name: "Filemom", testament: "NT", division: "Cartas de Paulo", order: 57 },
            // Cartas Gerais (8)
            { name: "Hebreus", testament: "NT", division: "Cartas Gerais", order: 58 },
            { name: "Tiago", testament: "NT", division: "Cartas Gerais", order: 59 },
            { name: "1 Pedro", testament: "NT", division: "Cartas Gerais", order: 60 },
            { name: "2 Pedro", testament: "NT", division: "Cartas Gerais", order: 61 },
            { name: "1 João", testament: "NT", division: "Cartas Gerais", order: 62 },
            { name: "2 João", testament: "NT", division: "Cartas Gerais", order: 63 },
            { name: "3 João", testament: "NT", division: "Cartas Gerais", order: 64 },
            { name: "Judas", testament: "NT", division: "Cartas Gerais", order: 65 },
            // Profético NT (1)
            { name: "Apocalipse", testament: "NT", division: "Profético", order: 66 },
        ],

        gameMode: 'category',
        modeNames: {
            'category': 'AT ou NT',
            'division': 'Divisões',
            'speed': 'Ordem'
        },
        questions: [],
        currentQuestionIndex: 0,
        totalQuestions: 15,
        currentBook: { name: '', testament: '', division: '', order: 0 },
        currentOptions: [],
        questionText: '',
        correctAnswerText: '',
        selectedOption: null,
        answered: false,
        lastCorrect: false,
        lastPoints: 0,
        score: 0,
        correctAnswers: 0,
        isPlaying: false,
        gameOver: false,
        timer: 10,
        timerInterval: null,
        xpGained: 0,

        startGame() {
            this.isPlaying = true;
            this.questions = [...this.bibleBooks].sort(() => Math.random() - 0.5).slice(0, this.totalQuestions);
            this.currentQuestionIndex = 0;
            this.score = 0;
            this.correctAnswers = 0;
            this.gameOver = false;
            this.loadQuestion();
        },

        loadQuestion() {
            if (this.currentQuestionIndex >= this.questions.length) {
                this.endGame();
                return;
            }
            
            this.currentBook = this.questions[this.currentQuestionIndex];
            this.answered = false;
            this.selectedOption = null;
            
            if (this.gameMode === 'category') {
                this.questionText = 'Este livro pertence ao Antigo ou Novo Testamento?';
                this.currentOptions = [
                    { value: 'AT', label: 'Antigo Testamento', isCorrect: this.currentBook.testament === 'AT' },
                    { value: 'NT', label: 'Novo Testamento', isCorrect: this.currentBook.testament === 'NT' }
                ];
                this.correctAnswerText = this.currentBook.testament === 'AT' ? 'Antigo Testamento' : 'Novo Testamento';
            } else if (this.gameMode === 'division') {
                this.questionText = 'A qual divisão este livro pertence?';
                const allDivisions = ['Pentateuco', 'Históricos', 'Poéticos', 'Profetas Maiores', 'Profetas Menores', 'Evangelhos', 'Cartas de Paulo', 'Cartas Gerais'];
                const correctDiv = this.currentBook.division;
                const wrongDivs = allDivisions.filter(d => d !== correctDiv).sort(() => Math.random() - 0.5).slice(0, 3);
                this.currentOptions = [
                    { value: correctDiv, label: correctDiv, isCorrect: true },
                    ...wrongDivs.map(d => ({ value: d, label: d, isCorrect: false }))
                ].sort(() => Math.random() - 0.5);
                this.correctAnswerText = correctDiv;
            } else { // speed
                const otherBook = this.bibleBooks.filter(b => b.order !== this.currentBook.order).sort(() => Math.random() - 0.5)[0];
                this.questionText = `"${this.currentBook.name}" vem antes ou depois de "${otherBook.name}"?`;
                const isBefore = this.currentBook.order < otherBook.order;
                this.currentOptions = [
                    { value: 'before', label: 'Antes', isCorrect: isBefore },
                    { value: 'after', label: 'Depois', isCorrect: !isBefore }
                ];
                this.correctAnswerText = isBefore ? 'Antes' : 'Depois';
                
                // Start timer
                this.timer = 10;
                if (this.timerInterval) clearInterval(this.timerInterval);
                this.timerInterval = setInterval(() => {
                    if (this.timer > 0 && !this.answered) {
                        this.timer--;
                    } else if (this.timer <= 0 && !this.answered) {
                        this.timeOut();
                    }
                }, 1000);
            }
        },

        timeOut() {
            this.answered = true;
            this.lastCorrect = false;
            this.lastPoints = 0;
            if (navigator.vibrate) navigator.vibrate([200, 100, 200]);
        },

        selectOption(option) {
            if (this.answered) return;
            this.answered = true;
            this.selectedOption = option.value;
            
            if (this.timerInterval) clearInterval(this.timerInterval);
            
            if (option.isCorrect) {
                this.lastCorrect = true;
                this.correctAnswers++;
                // Bonus for speed mode based on time
                if (this.gameMode === 'speed') {
                    this.lastPoints = 10 + this.timer * 2;
                } else {
                    this.lastPoints = this.gameMode === 'division' ? 15 : 10;
                }
                this.score += this.lastPoints;
                if (navigator.vibrate) navigator.vibrate(50);
            } else {
                this.lastCorrect = false;
                this.lastPoints = 0;
                if (navigator.vibrate) navigator.vibrate([100, 50, 100]);
            }
        },

        nextQuestion() {
            this.currentQuestionIndex++;
            this.loadQuestion();
        },

        endGame() {
            this.gameOver = true;
            if (this.timerInterval) clearInterval(this.timerInterval);
            this.saveScore();
        },

        restartGame() {
            this.gameOver = false;
            this.isPlaying = false;
        },

        async saveScore() {
            try {
                const response = await fetch('{{ route("memberpanel.ebd.arcade.submit", "desafio-dos-livros") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        score: this.score,
                        metadata: {
                            mode: this.gameMode,
                            correct_answers: this.correctAnswers
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
