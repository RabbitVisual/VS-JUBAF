@extends('memberpanel::components.layouts.master')

@section('title', 'Mestre do Conhecimento - Arcade')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-slate-950 transition-colors duration-200 pb-12" x-data="quizGame()">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 pt-4 sm:pt-6 space-y-4 sm:space-y-6">

        @include('ebd::memberpanel.arcade.partials.game-header', [
            'gameTitle' => 'Mestre do Conhecimento',
            'gameSubtitle' => 'Quiz bíblico com categorias e combos.',
            'icon' => 'brain-circuit',
            'accent' => 'indigo',
        ])

        <!-- Stats bar (visible during game) -->
        <div x-show="gameStarted && !gameOver" x-transition class="flex flex-wrap items-center justify-center sm:justify-between gap-3 p-3 sm:p-4 bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-slate-800 shadow-sm">
            <div x-show="currentCategory" class="hidden sm:block px-4 py-2 bg-indigo-50 dark:bg-indigo-900/20 rounded-xl border border-indigo-100 dark:border-indigo-800">
                <span class="block text-[9px] font-bold uppercase text-indigo-600 dark:text-indigo-400">Categoria</span>
                <span class="text-sm font-bold text-gray-900 dark:text-white" x-text="currentCategory">-</span>
            </div>
            <div class="flex items-center gap-3 sm:gap-4">
                <div class="flex items-center gap-2 px-4 py-2 rounded-xl border transition-all duration-300"
                     x-bind:class="streak >= 3 ? 'bg-amber-50 dark:bg-amber-900/20 border-amber-200 dark:border-amber-800' : 'bg-gray-50 dark:bg-slate-800 border-gray-100 dark:border-slate-700'">
                    <i class="fa-duotone fa-fire w-5 h-5 shrink-0" x-bind:class="streak >= 3 ? 'text-amber-500' : 'text-gray-400'" aria-hidden="true"></i>
                    <span class="text-lg sm:text-xl font-black tabular-nums text-gray-900 dark:text-white" x-text="streak">0</span>
                    <span class="text-xs font-bold text-amber-500" x-show="streak >= 3" x-text="'x' + multiplier.toFixed(1)">x1.0</span>
                </div>
                <div class="flex items-center gap-2 px-4 py-2 bg-indigo-50 dark:bg-indigo-900/20 rounded-xl border border-indigo-100 dark:border-indigo-800">
                    <x-icon name="star" style="duotone" class="w-5 h-5 text-indigo-500 shrink-0" />
                    <span class="text-lg sm:text-xl font-black tabular-nums text-gray-900 dark:text-white" x-text="score">0</span>
                </div>
            </div>
        </div>

    <!-- Game Area -->
    <div class="min-h-[320px] sm:min-h-[400px] md:min-h-[480px] relative">

        <!-- Loading -->
        <div x-show="loading" class="absolute inset-0 z-50 flex items-center justify-center">
            <div class="text-center space-y-4">
                <div class="w-16 h-16 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin mx-auto"></div>
                <p class="text-sm font-bold text-gray-500 uppercase tracking-widest">Carregando perguntas...</p>
            </div>
        </div>

        <!-- Category Selection -->
        <div x-show="!loading && !gameStarted && !gameOver" class="bg-white dark:bg-gray-900 rounded-2xl md:rounded-[3rem] p-6 sm:p-10 md:p-16 shadow-2xl border border-gray-100 dark:border-gray-800">
            <div class="text-center mb-8">
                <div class="w-20 h-20 bg-indigo-500 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-xl shadow-indigo-500/30">
                    <x-icon name="brain-circuit" style="duotone" class="w-10 h-10 text-white" />
                </div>
                <h2 class="text-2xl md:text-3xl font-black text-gray-900 dark:text-white mb-2">Escolha uma Categoria</h2>
                <p class="text-gray-500">Teste seus conhecimentos bíblicos</p>
            </div>
            
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 md:gap-4 mb-8">
                <button @click="selectedCategory = 'all'" 
                        class="p-4 md:p-6 rounded-2xl border-2 transition-all text-center touch-manipulation active:scale-95"
                        :class="selectedCategory === 'all' ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-indigo-300'">
                    <x-icon name="infinity" style="duotone" class="w-8 h-8 mx-auto mb-2 text-indigo-500" />
                    <span class="block text-sm font-bold text-gray-800 dark:text-white">Todas</span>
                </button>
                <button @click="selectedCategory = 'antigo_testamento'" 
                        class="p-4 md:p-6 rounded-2xl border-2 transition-all text-center touch-manipulation active:scale-95"
                        :class="selectedCategory === 'antigo_testamento' ? 'border-amber-500 bg-amber-50 dark:bg-amber-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-amber-300'">
                    <x-icon name="scroll-old" style="duotone" class="w-8 h-8 mx-auto mb-2 text-amber-500" />
                    <span class="block text-sm font-bold text-gray-800 dark:text-white">Antigo Test.</span>
                </button>
                <button @click="selectedCategory = 'novo_testamento'" 
                        class="p-4 md:p-6 rounded-2xl border-2 transition-all text-center touch-manipulation active:scale-95"
                        :class="selectedCategory === 'novo_testamento' ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-emerald-300'">
                    <x-icon name="book-bible" style="duotone" class="w-8 h-8 mx-auto mb-2 text-emerald-500" />
                    <span class="block text-sm font-bold text-gray-800 dark:text-white">Novo Test.</span>
                </button>
                <button @click="selectedCategory = 'personagens'" 
                        class="p-4 md:p-6 rounded-2xl border-2 transition-all text-center touch-manipulation active:scale-95"
                        :class="selectedCategory === 'personagens' ? 'border-rose-500 bg-rose-50 dark:bg-rose-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-rose-300'">
                    <x-icon name="users" style="duotone" class="w-8 h-8 mx-auto mb-2 text-rose-500" />
                    <span class="block text-sm font-bold text-gray-800 dark:text-white">Personagens</span>
                </button>
                <button @click="selectedCategory = 'geografia'" 
                        class="p-4 md:p-6 rounded-2xl border-2 transition-all text-center touch-manipulation active:scale-95"
                        :class="selectedCategory === 'geografia' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-blue-300'">
                    <x-icon name="earth-americas" style="duotone" class="w-8 h-8 mx-auto mb-2 text-blue-500" />
                    <span class="block text-sm font-bold text-gray-800 dark:text-white">Geografia</span>
                </button>
                <button @click="selectedCategory = 'profecias'" 
                        class="p-4 md:p-6 rounded-2xl border-2 transition-all text-center touch-manipulation active:scale-95"
                        :class="selectedCategory === 'profecias' ? 'border-purple-500 bg-purple-50 dark:bg-purple-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-purple-300'">
                    <x-icon name="eye" style="duotone" class="w-8 h-8 mx-auto mb-2 text-purple-500" />
                    <span class="block text-sm font-bold text-gray-800 dark:text-white">Profecias</span>
                </button>
            </div>

            <button @click="startQuiz()" class="w-full py-4 md:py-5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-2xl font-black text-lg uppercase tracking-widest shadow-xl shadow-indigo-600/30 transition-all hover:-translate-y-1 active:scale-95 touch-manipulation">
                <x-icon name="play" style="solid" class="w-5 h-5 inline mr-2" /> Começar Quiz
            </button>
        </div>

        <!-- Question Card -->
        <div x-show="!loading && gameStarted && !gameOver" x-transition class="bg-white dark:bg-gray-900 rounded-2xl md:rounded-[3rem] p-6 sm:p-10 md:p-16 shadow-2xl border border-gray-100 dark:border-gray-800 relative overflow-hidden">
            <!-- Progress Bar -->
            <div class="absolute top-0 left-0 h-1.5 md:h-2 bg-indigo-500 transition-all duration-500 ease-out rounded-full" :style="'width: ' + ((currentQuestionIndex + 1) / totalQuestions * 100) + '%'"></div>

            <!-- Timer Bar -->
            <div class="absolute top-0 right-0 h-1.5 md:h-2 bg-red-500 transition-all duration-100" :style="'width: ' + (questionTimer / 30 * 100) + '%'" :class="{'animate-pulse': questionTimer <= 5}"></div>

            <div class="mb-6 md:mb-10 text-center space-y-3 md:space-y-4">
                <div class="flex items-center justify-center gap-4">
                    <span class="text-xs font-black text-gray-400 uppercase tracking-[0.2em]">Pergunta <span x-text="currentQuestionIndex + 1">1</span>/<span x-text="totalQuestions">10</span></span>
                    <span class="px-3 py-1 bg-gray-100 dark:bg-gray-800 rounded-full text-xs font-bold text-gray-500" x-text="questionTimer + 's'" :class="{'text-red-500 bg-red-100 dark:bg-red-900/30': questionTimer <= 5}"></span>
                </div>
                <h2 class="text-xl sm:text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white leading-tight" x-text="currentQuestion.question_text">...</h2>
            </div>

            <!-- Options Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-4">
                <template x-for="(option, idx) in currentOptions" :key="option.id">
                    <button @click="selectOption(option)"
                            :disabled="answered"
                            class="relative p-4 sm:p-5 md:p-6 rounded-xl md:rounded-2xl border-2 text-left transition-all duration-200 group active:scale-[0.98] flex items-center justify-between min-h-[60px] touch-manipulation"
                            :class="{
                                'border-gray-200 dark:border-gray-700 hover:border-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-900/10': !answered,
                                'border-emerald-500 bg-emerald-50 dark:bg-emerald-900/20': answered && option.is_correct,
                                'border-red-500 bg-red-50 dark:bg-red-900/20 opacity-60': answered && !option.is_correct && selectedOptionId === option.id,
                                'opacity-40': answered && !option.is_correct && selectedOptionId !== option.id
                            }">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg flex items-center justify-center text-sm font-black shrink-0"
                                  :class="answered && option.is_correct ? 'bg-emerald-500 text-white' : 'bg-gray-100 dark:bg-gray-800 text-gray-500'"
                                  x-text="['A', 'B', 'C', 'D'][idx]"></span>
                            <span class="text-sm sm:text-base md:text-lg font-bold text-gray-800 dark:text-gray-200" x-text="option.answer_text"></span>
                        </div>

                        <!-- Icons for feedback -->
                        <x-icon name="check-circle" style="solid" class="w-6 h-6 text-emerald-500 shrink-0" x-show="answered && option.is_correct" />
                        <x-icon name="xmark-circle" style="solid" class="w-6 h-6 text-red-500 shrink-0" x-show="answered && !option.is_correct && selectedOptionId === option.id" />
                    </button>
                </template>
            </div>

            <!-- Points Animation -->
            <div x-show="showPoints" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                 class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 pointer-events-none">
                <span class="text-4xl md:text-6xl font-black" :class="lastAnswerCorrect ? 'text-emerald-500' : 'text-red-500'" x-text="lastAnswerCorrect ? '+' + lastPoints : '0'"></span>
            </div>

            <!-- Next Button (Shows after answer) -->
            <div class="mt-6 md:mt-8 flex justify-end" x-show="answered">
                <button @click="nextQuestion()" class="px-6 sm:px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold shadow-lg shadow-indigo-500/30 transition-all flex items-center gap-2 touch-manipulation active:scale-95">
                    <span x-text="currentQuestionIndex >= totalQuestions - 1 ? 'Ver Resultado' : 'Próxima'"></span>
                    <x-icon name="arrow-right" class="w-4 h-4" />
                </button>
            </div>
        </div>

        <!-- Game Over Modal -->
        <div x-show="gameOver" x-transition class="text-center space-y-6 md:space-y-8 bg-white dark:bg-gray-900 rounded-2xl md:rounded-[3rem] p-8 md:p-16 shadow-2xl border border-gray-100 dark:border-gray-800">
            <div class="w-20 h-20 md:w-24 md:h-24 bg-amber-500 rounded-full flex items-center justify-center mx-auto shadow-2xl shadow-amber-500/30 animate-bounce">
                <x-icon name="trophy" style="duotone" class="w-10 h-10 md:w-12 md:h-12 text-white" />
            </div>
            <div>
                <h2 class="text-3xl md:text-4xl font-black text-gray-900 dark:text-white mb-2">Fim de Jogo!</h2>
                <p class="text-gray-500 font-bold uppercase tracking-widest text-sm">Sua pontuação final</p>
            </div>
            <div class="text-5xl md:text-6xl font-black text-indigo-600 tracking-tighter" x-text="score">0</div>

            <div class="grid grid-cols-3 gap-3 md:gap-4 max-w-md mx-auto">
                <div class="bg-gray-50 dark:bg-gray-800 p-3 md:p-4 rounded-xl md:rounded-2xl">
                    <span class="block text-[10px] md:text-xs font-black text-gray-400 uppercase">Acertos</span>
                    <span class="text-xl md:text-2xl font-bold text-emerald-500" x-text="correctAnswers">0</span>
                </div>
                <div class="bg-gray-50 dark:bg-gray-800 p-3 md:p-4 rounded-xl md:rounded-2xl">
                    <span class="block text-[10px] md:text-xs font-black text-gray-400 uppercase">Erros</span>
                    <span class="text-xl md:text-2xl font-bold text-red-500" x-text="totalQuestions - correctAnswers">0</span>
                </div>
                <div class="bg-gray-50 dark:bg-gray-800 p-3 md:p-4 rounded-xl md:rounded-2xl">
                    <span class="block text-[10px] md:text-xs font-black text-gray-400 uppercase">Combo Max</span>
                    <span class="text-xl md:text-2xl font-bold text-amber-500" x-text="maxStreak">0</span>
                </div>
            </div>

            <div class="bg-indigo-50 dark:bg-indigo-900/20 p-4 rounded-2xl">
                <span class="text-sm text-indigo-600 dark:text-indigo-400 font-bold">+<span x-text="xpGained"></span> XP Ganho!</span>
            </div>

            <div class="flex flex-col sm:flex-row justify-center gap-3 md:gap-4 pt-4">
                <button @click="restartGame()" class="px-6 md:px-8 py-3 md:py-4 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-900 dark:text-white rounded-xl font-bold transition-all touch-manipulation active:scale-95">
                    <x-icon name="rotate-right" style="solid" class="w-4 h-4 inline mr-2" /> Jogar Novamente
                </button>
                <a href="{{ route('memberpanel.ebd.arcade.index') }}" class="px-6 md:px-8 py-3 md:py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold shadow-lg shadow-indigo-500/30 transition-all">
                    <x-icon name="door-open" style="solid" class="w-4 h-4 inline mr-2" /> Sair
                </a>
            </div>
        </div>

    </div>
    </div>
</div>

@push('head_scripts')
<script>
function quizGame() {
    return {
        loading: true,
        gameStarted: false,
        gameOver: false,
        questions: [],
        currentQuestionIndex: 0,
        currentQuestion: {},
        currentOptions: [],
        currentCategory: '',
        selectedCategory: 'all',
        score: 0,
        streak: 0,
        maxStreak: 0,
        multiplier: 1.0,
        correctAnswers: 0,
        answered: false,
        selectedOptionId: null,
        totalQuestions: 0,
        questionTimer: 30,
        timerInterval: null,
        showPoints: false,
        lastPoints: 0,
        lastAnswerCorrect: false,
        xpGained: 0,

        async init() {
            this.loading = false;
        },

        async startQuiz() {
            this.loading = true;
            this.gameStarted = true;
            
            try {
                let url = '{{ route("memberpanel.ebd.arcade.quiz.data") }}';
                if (this.selectedCategory !== 'all') {
                    url += '?category=' + this.selectedCategory;
                }
                
                const response = await fetch(url);
                const data = await response.json();

                this.questions = data.questions.sort(() => Math.random() - 0.5);
                this.totalQuestions = Math.min(this.questions.length, 15);
                this.questions = this.questions.slice(0, this.totalQuestions);
                
                this.loadQuestion();
                this.loading = false;
            } catch (e) {
                console.error("Failed to load quiz data", e);
                alert("Erro ao carregar o jogo.");
                this.loading = false;
                this.gameStarted = false;
            }
        },

        loadQuestion() {
            if (this.currentQuestionIndex >= this.questions.length) {
                this.endGame();
                return;
            }

            this.currentQuestion = this.questions[this.currentQuestionIndex];
            this.currentCategory = this.currentQuestion.category || this.selectedCategory;
            this.currentOptions = [...this.currentQuestion.answers].sort(() => Math.random() - 0.5);
            this.answered = false;
            this.selectedOptionId = null;
            this.showPoints = false;
            
            // Start timer
            this.questionTimer = 30;
            if (this.timerInterval) clearInterval(this.timerInterval);
            this.timerInterval = setInterval(() => {
                if (this.questionTimer > 0 && !this.answered) {
                    this.questionTimer--;
                } else if (this.questionTimer <= 0 && !this.answered) {
                    this.timeOut();
                }
            }, 1000);
        },

        timeOut() {
            this.answered = true;
            this.handleWrong();
            if (navigator.vibrate) navigator.vibrate([200, 100, 200]);
        },

        selectOption(option) {
            if (this.answered) return;

            this.answered = true;
            this.selectedOptionId = option.id;
            clearInterval(this.timerInterval);

            if (option.is_correct) {
                this.handleCorrect();
            } else {
                this.handleWrong();
            }
        },

        handleCorrect() {
            this.streak++;
            if (this.streak > this.maxStreak) this.maxStreak = this.streak;
            this.correctAnswers++;

            // Calculate Multiplier
            if (this.streak >= 7) this.multiplier = 3.0;
            else if (this.streak >= 5) this.multiplier = 2.0;
            else if (this.streak >= 3) this.multiplier = 1.5;
            else this.multiplier = 1.0;

            // Time bonus
            const timeBonus = Math.floor(this.questionTimer / 3);
            const points = Math.round((10 + timeBonus) * this.multiplier);
            this.score += points;
            
            this.lastPoints = points;
            this.lastAnswerCorrect = true;
            this.showPoints = true;
            setTimeout(() => this.showPoints = false, 1000);

            if (navigator.vibrate) navigator.vibrate(50);
        },

        handleWrong() {
            this.streak = 0;
            this.multiplier = 1.0;
            this.lastPoints = 0;
            this.lastAnswerCorrect = false;
            this.showPoints = true;
            setTimeout(() => this.showPoints = false, 1000);
            
            if (navigator.vibrate) navigator.vibrate([100, 50, 100]);
        },

        nextQuestion() {
            this.currentQuestionIndex++;
            this.loadQuestion();
        },

        endGame() {
            this.gameOver = true;
            clearInterval(this.timerInterval);
            this.saveScore();
        },

        restartGame() {
            this.score = 0;
            this.streak = 0;
            this.multiplier = 1.0;
            this.correctAnswers = 0;
            this.currentQuestionIndex = 0;
            this.gameOver = false;
            this.gameStarted = false;
            this.loading = false;
        },

        async saveScore() {
            try {
                const response = await fetch('{{ route("memberpanel.ebd.arcade.submit", "mestre-do-conhecimento") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        score: this.score,
                        metadata: {
                            correct: this.correctAnswers,
                            max_streak: this.maxStreak,
                            category: this.selectedCategory
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
