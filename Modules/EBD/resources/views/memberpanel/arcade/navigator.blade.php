@extends('memberpanel::components.layouts.master')

@section('title', 'Navegador Bíblico - Arcade')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-slate-950 transition-colors duration-200 pb-12" x-data="navigatorGame()">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 pt-4 sm:pt-6 space-y-4 sm:space-y-6">

        @include('ebd::memberpanel.arcade.partials.game-header', [
            'gameTitle' => 'Navegador Bíblico',
            'gameSubtitle' => 'Encontre versículos na Bíblia no tempo limite.',
            'icon' => 'compass',
            'accent' => 'teal',
        ])

    <!-- Game Area -->
    <div class="max-w-4xl mx-auto relative px-2 sm:px-4">

        <!-- Start Screen -->
        <div x-show="!isPlaying && !gameOver" class="text-center space-y-8 py-10">
            <div class="bg-white dark:bg-gray-900 rounded-2xl md:rounded-3xl p-8 md:p-12 shadow-2xl border border-gray-100 dark:border-gray-800">
                <div class="w-20 h-20 bg-teal-500 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-xl shadow-teal-500/30">
                    <x-icon name="compass" style="duotone" class="w-10 h-10 text-white" />
                </div>
                <h2 class="text-2xl md:text-3xl font-black text-gray-900 dark:text-white mb-4">Navegador Bíblico</h2>
                <p class="text-gray-500 mb-8">Encontre os versículos o mais rápido possível! Selecione o livro, capítulo e versículo corretos antes que o tempo acabe.</p>
                
                <div class="grid grid-cols-3 gap-4 mb-8 max-w-md mx-auto text-center">
                    <div class="bg-teal-50 dark:bg-teal-900/20 p-4 rounded-2xl">
                        <span class="text-3xl font-black text-teal-600">60s</span>
                        <span class="block text-xs text-gray-500 mt-1">Por Rodada</span>
                    </div>
                    <div class="bg-emerald-50 dark:bg-emerald-900/20 p-4 rounded-2xl">
                        <span class="text-3xl font-black text-emerald-600">10</span>
                        <span class="block text-xs text-gray-500 mt-1">Versículos</span>
                    </div>
                    <div class="bg-amber-50 dark:bg-amber-900/20 p-4 rounded-2xl">
                        <span class="text-3xl font-black text-amber-600">+5s</span>
                        <span class="block text-xs text-gray-500 mt-1">Bônus</span>
                    </div>
                </div>

                <!-- Difficulty Selection -->
                <div class="mb-8">
                    <p class="text-sm text-gray-500 mb-3">Dificuldade</p>
                    <div class="flex justify-center gap-3">
                        <button @click="difficulty = 'easy'" 
                                :class="difficulty === 'easy' ? 'bg-emerald-600 text-white' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400'"
                                class="px-6 py-3 rounded-xl font-bold transition-all touch-manipulation">
                            Fácil
                        </button>
                        <button @click="difficulty = 'medium'" 
                                :class="difficulty === 'medium' ? 'bg-amber-600 text-white' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400'"
                                class="px-6 py-3 rounded-xl font-bold transition-all touch-manipulation">
                            Médio
                        </button>
                        <button @click="difficulty = 'hard'" 
                                :class="difficulty === 'hard' ? 'bg-red-600 text-white' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400'"
                                class="px-6 py-3 rounded-xl font-bold transition-all touch-manipulation">
                            Difícil
                        </button>
                    </div>
                </div>
                
                <button @click="startGame()" class="w-full py-5 bg-teal-600 hover:bg-teal-500 text-white rounded-2xl font-black text-lg uppercase tracking-widest shadow-xl shadow-teal-600/30 transition-all hover:-translate-y-1 active:scale-95 touch-manipulation">
                    <x-icon name="play" style="solid" class="w-5 h-5 inline mr-2" /> Iniciar Jogo
                </button>
            </div>
        </div>

        <!-- Game Play -->
        <div x-show="isPlaying && !gameOver" x-transition class="space-y-6">
            
            <!-- Target Reference -->
            <div class="bg-white dark:bg-gray-900 rounded-2xl md:rounded-3xl p-6 md:p-10 shadow-2xl border border-gray-100 dark:border-gray-800 text-center">
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-teal-100 dark:bg-teal-900/30 text-teal-600 rounded-xl mb-4">
                    <x-icon name="magnifying-glass" style="duotone" class="w-4 h-4" />
                    <span class="text-xs font-black uppercase tracking-widest">Encontre</span>
                </div>
                <h2 class="text-3xl sm:text-4xl md:text-5xl font-black text-gray-900 dark:text-white" x-text="targetReference">
                    João 3:16
                </h2>
                <p x-show="difficulty === 'easy'" class="text-gray-500 mt-4 text-sm" x-text="targetHint">Dica sobre o versículo...</p>
            </div>

            <!-- Selection Area -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Book Selection -->
                <div class="bg-white dark:bg-gray-900 rounded-2xl p-4 shadow-xl border border-gray-100 dark:border-gray-800">
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Livro</h3>
                    <div class="relative">
                        <select x-model="selectedBook" @change="onBookChange()"
                                class="w-full p-4 bg-gray-100 dark:bg-gray-800 rounded-xl font-bold text-gray-900 dark:text-white appearance-none cursor-pointer touch-manipulation">
                            <option value="">Selecione...</option>
                            <template x-for="book in books" :key="book.abbrev">
                                <option :value="book.abbrev" x-text="book.name"></option>
                            </template>
                        </select>
                        <x-icon name="chevron-down" style="solid" class="w-4 h-4 absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" />
                    </div>
                </div>

                <!-- Chapter Selection -->
                <div class="bg-white dark:bg-gray-900 rounded-2xl p-4 shadow-xl border border-gray-100 dark:border-gray-800">
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Capítulo</h3>
                    <div class="grid grid-cols-5 gap-2 max-h-40 overflow-y-auto">
                        <template x-for="chapter in availableChapters" :key="chapter">
                            <button @click="selectedChapter = chapter; onChapterChange()"
                                    :class="selectedChapter === chapter ? 'bg-teal-600 text-white' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-teal-100 dark:hover:bg-teal-900/30'"
                                    class="p-2 rounded-lg font-bold text-sm transition-all touch-manipulation active:scale-95"
                                    x-text="chapter">
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Verse Selection -->
                <div class="bg-white dark:bg-gray-900 rounded-2xl p-4 shadow-xl border border-gray-100 dark:border-gray-800">
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Versículo</h3>
                    <div class="grid grid-cols-5 gap-2 max-h-40 overflow-y-auto">
                        <template x-for="verse in availableVerses" :key="verse">
                            <button @click="selectedVerse = verse"
                                    :class="selectedVerse === verse ? 'bg-teal-600 text-white' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-teal-100 dark:hover:bg-teal-900/30'"
                                    class="p-2 rounded-lg font-bold text-sm transition-all touch-manipulation active:scale-95"
                                    x-text="verse">
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Current Selection Display -->
            <div class="bg-gray-100 dark:bg-gray-800 rounded-2xl p-4 text-center">
                <span class="text-sm text-gray-500">Sua seleção: </span>
                <span class="font-black text-gray-900 dark:text-white" x-text="currentSelection || 'Nenhuma'"></span>
            </div>

            <!-- Confirm Button -->
            <button @click="checkAnswer()"
                    :disabled="!selectedBook || !selectedChapter || !selectedVerse"
                    class="w-full py-5 bg-teal-600 hover:bg-teal-500 disabled:bg-gray-300 dark:disabled:bg-gray-700 disabled:cursor-not-allowed text-white rounded-2xl font-black text-lg uppercase tracking-widest shadow-xl transition-all touch-manipulation active:scale-98">
                <x-icon name="check" style="solid" class="w-5 h-5 inline mr-2" /> Confirmar
            </button>

            <!-- Feedback Message -->
            <div x-show="feedbackMessage" x-transition
                 class="p-4 rounded-2xl text-center font-bold"
                 :class="feedbackCorrect ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600' : 'bg-red-100 dark:bg-red-900/30 text-red-600'">
                <span x-text="feedbackMessage"></span>
            </div>
        </div>

        <!-- Game Over Modal -->
        <div x-show="gameOver" x-transition class="text-center space-y-6 md:space-y-8 bg-white dark:bg-gray-900 rounded-2xl md:rounded-[3rem] p-8 md:p-16 shadow-2xl border border-gray-100 dark:border-gray-800">
            <div class="w-20 h-20 md:w-24 md:h-24 bg-amber-500 rounded-full flex items-center justify-center mx-auto shadow-2xl shadow-amber-500/30 animate-bounce">
                <x-icon name="trophy" style="duotone" class="w-10 h-10 md:w-12 md:h-12 text-white" />
            </div>
            <div>
                <h2 class="text-3xl md:text-4xl font-black text-gray-900 dark:text-white">Tempo Esgotado!</h2>
                <p class="text-gray-500 mt-2">Você navegou pela Bíblia com habilidade!</p>
            </div>
            <div class="text-5xl md:text-6xl font-black text-teal-600 tracking-tighter" x-text="score">0</div>
            
            <div class="grid grid-cols-3 gap-4 max-w-md mx-auto">
                <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-2xl">
                    <span class="block text-xs font-black text-gray-400 uppercase">Encontrados</span>
                    <span class="text-2xl font-bold text-emerald-500" x-text="versesFound">0</span>
                </div>
                <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-2xl">
                    <span class="block text-xs font-black text-gray-400 uppercase">Precisão</span>
                    <span class="text-2xl font-bold text-amber-500"><span x-text="accuracy">0</span>%</span>
                </div>
                <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-2xl">
                    <span class="block text-xs font-black text-gray-400 uppercase">XP Ganho</span>
                    <span class="text-2xl font-bold text-teal-500">+<span x-text="xpGained">0</span></span>
                </div>
            </div>
            
            <div class="flex flex-col sm:flex-row justify-center gap-3 md:gap-4 pt-4">
                <button @click="restartGame()" class="px-6 md:px-8 py-3 md:py-4 bg-teal-600 hover:bg-teal-500 text-white rounded-xl font-bold transition-all touch-manipulation active:scale-95">
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
function navigatorGame() {
    return {
        books: [
            { abbrev: 'gn', name: 'Gênesis', chapters: 50 },
            { abbrev: 'ex', name: 'Êxodo', chapters: 40 },
            { abbrev: 'lv', name: 'Levítico', chapters: 27 },
            { abbrev: 'nm', name: 'Números', chapters: 36 },
            { abbrev: 'dt', name: 'Deuteronômio', chapters: 34 },
            { abbrev: 'js', name: 'Josué', chapters: 24 },
            { abbrev: 'jz', name: 'Juízes', chapters: 21 },
            { abbrev: 'rt', name: 'Rute', chapters: 4 },
            { abbrev: '1sm', name: '1 Samuel', chapters: 31 },
            { abbrev: '2sm', name: '2 Samuel', chapters: 24 },
            { abbrev: '1rs', name: '1 Reis', chapters: 22 },
            { abbrev: '2rs', name: '2 Reis', chapters: 25 },
            { abbrev: '1cr', name: '1 Crônicas', chapters: 29 },
            { abbrev: '2cr', name: '2 Crônicas', chapters: 36 },
            { abbrev: 'ed', name: 'Esdras', chapters: 10 },
            { abbrev: 'ne', name: 'Neemias', chapters: 13 },
            { abbrev: 'et', name: 'Ester', chapters: 10 },
            { abbrev: 'jó', name: 'Jó', chapters: 42 },
            { abbrev: 'sl', name: 'Salmos', chapters: 150 },
            { abbrev: 'pv', name: 'Provérbios', chapters: 31 },
            { abbrev: 'ec', name: 'Eclesiastes', chapters: 12 },
            { abbrev: 'ct', name: 'Cânticos', chapters: 8 },
            { abbrev: 'is', name: 'Isaías', chapters: 66 },
            { abbrev: 'jr', name: 'Jeremias', chapters: 52 },
            { abbrev: 'lm', name: 'Lamentações', chapters: 5 },
            { abbrev: 'ez', name: 'Ezequiel', chapters: 48 },
            { abbrev: 'dn', name: 'Daniel', chapters: 12 },
            { abbrev: 'os', name: 'Oséias', chapters: 14 },
            { abbrev: 'jl', name: 'Joel', chapters: 3 },
            { abbrev: 'am', name: 'Amós', chapters: 9 },
            { abbrev: 'ob', name: 'Obadias', chapters: 1 },
            { abbrev: 'jn', name: 'Jonas', chapters: 4 },
            { abbrev: 'mq', name: 'Miquéias', chapters: 7 },
            { abbrev: 'na', name: 'Naum', chapters: 3 },
            { abbrev: 'hc', name: 'Habacuque', chapters: 3 },
            { abbrev: 'sf', name: 'Sofonias', chapters: 3 },
            { abbrev: 'ag', name: 'Ageu', chapters: 2 },
            { abbrev: 'zc', name: 'Zacarias', chapters: 14 },
            { abbrev: 'ml', name: 'Malaquias', chapters: 4 },
            { abbrev: 'mt', name: 'Mateus', chapters: 28 },
            { abbrev: 'mc', name: 'Marcos', chapters: 16 },
            { abbrev: 'lc', name: 'Lucas', chapters: 24 },
            { abbrev: 'jo', name: 'João', chapters: 21 },
            { abbrev: 'at', name: 'Atos', chapters: 28 },
            { abbrev: 'rm', name: 'Romanos', chapters: 16 },
            { abbrev: '1co', name: '1 Coríntios', chapters: 16 },
            { abbrev: '2co', name: '2 Coríntios', chapters: 13 },
            { abbrev: 'gl', name: 'Gálatas', chapters: 6 },
            { abbrev: 'ef', name: 'Efésios', chapters: 6 },
            { abbrev: 'fp', name: 'Filipenses', chapters: 4 },
            { abbrev: 'cl', name: 'Colossenses', chapters: 4 },
            { abbrev: '1ts', name: '1 Tessalonicenses', chapters: 5 },
            { abbrev: '2ts', name: '2 Tessalonicenses', chapters: 3 },
            { abbrev: '1tm', name: '1 Timóteo', chapters: 6 },
            { abbrev: '2tm', name: '2 Timóteo', chapters: 4 },
            { abbrev: 'tt', name: 'Tito', chapters: 3 },
            { abbrev: 'fm', name: 'Filemom', chapters: 1 },
            { abbrev: 'hb', name: 'Hebreus', chapters: 13 },
            { abbrev: 'tg', name: 'Tiago', chapters: 5 },
            { abbrev: '1pe', name: '1 Pedro', chapters: 5 },
            { abbrev: '2pe', name: '2 Pedro', chapters: 3 },
            { abbrev: '1jo', name: '1 João', chapters: 5 },
            { abbrev: '2jo', name: '2 João', chapters: 1 },
            { abbrev: '3jo', name: '3 João', chapters: 1 },
            { abbrev: 'jd', name: 'Judas', chapters: 1 },
            { abbrev: 'ap', name: 'Apocalipse', chapters: 22 },
        ],

        versesToFind: [
            { book: 'jo', bookName: 'João', chapter: 3, verse: 16, hint: 'O versículo mais famoso da Bíblia sobre o amor de Deus' },
            { book: 'gn', bookName: 'Gênesis', chapter: 1, verse: 1, hint: 'O primeiro versículo da Bíblia' },
            { book: 'sl', bookName: 'Salmos', chapter: 23, verse: 1, hint: 'O Senhor é meu pastor...' },
            { book: 'rm', bookName: 'Romanos', chapter: 8, verse: 28, hint: 'Todas as coisas cooperam para o bem...' },
            { book: 'fp', bookName: 'Filipenses', chapter: 4, verse: 13, hint: 'Posso todas as coisas...' },
            { book: 'pv', bookName: 'Provérbios', chapter: 3, verse: 5, hint: 'Confia no Senhor de todo o teu coração...' },
            { book: 'is', bookName: 'Isaías', chapter: 40, verse: 31, hint: 'Os que esperam no Senhor renovarão as forças...' },
            { book: 'jr', bookName: 'Jeremias', chapter: 29, verse: 11, hint: 'Eu sei os planos que tenho para vocês...' },
            { book: 'mt', bookName: 'Mateus', chapter: 28, verse: 19, hint: 'A Grande Comissão' },
            { book: 'jo', bookName: 'João', chapter: 14, verse: 6, hint: 'Eu sou o caminho, a verdade e a vida...' },
            { book: 'ef', bookName: 'Efésios', chapter: 2, verse: 8, hint: 'Pela graça sois salvos...' },
            { book: 'hb', bookName: 'Hebreus', chapter: 11, verse: 1, hint: 'A fé é a certeza das coisas que se esperam...' },
            { book: 'tg', bookName: 'Tiago', chapter: 1, verse: 2, hint: 'Tende por motivo de grande gozo...' },
            { book: '1co', bookName: '1 Coríntios', chapter: 13, verse: 4, hint: 'O amor é paciente, o amor é bondoso...' },
            { book: 'gl', bookName: 'Gálatas', chapter: 5, verse: 22, hint: 'Mas o fruto do Espírito é...' },
            { book: 'ap', bookName: 'Apocalipse', chapter: 21, verse: 4, hint: 'Ele enxugará toda lágrima dos seus olhos...' },
            { book: 'sl', bookName: 'Salmos', chapter: 119, verse: 105, hint: 'Lâmpada para os meus pés é a tua palavra...' },
            { book: '2tm', bookName: '2 Timóteo', chapter: 3, verse: 16, hint: 'Toda a Escritura é inspirada por Deus...' },
            { book: 'rm', bookName: 'Romanos', chapter: 12, verse: 2, hint: 'Não vos conformeis com este mundo...' },
            { book: 'jo', bookName: 'João', chapter: 1, verse: 1, hint: 'No princípio era o Verbo...' },
        ],

        versesPerGame: {
            easy: 30,
            medium: 20,
            hard: 15
        },

        targetReference: '',
        targetHint: '',
        targetBook: '',
        targetChapter: 0,
        targetVerse: 0,
        
        selectedBook: '',
        selectedChapter: null,
        selectedVerse: null,
        availableChapters: [],
        availableVerses: [],
        
        difficulty: 'medium',
        timeRemaining: 60,
        score: 0,
        versesFound: 0,
        totalAttempts: 0,
        isPlaying: false,
        gameOver: false,
        timerInterval: null,
        feedbackMessage: '',
        feedbackCorrect: false,
        xpGained: 0,

        get currentSelection() {
            if (!this.selectedBook) return '';
            const bookObj = this.books.find(b => b.abbrev === this.selectedBook);
            let selection = bookObj ? bookObj.name : '';
            if (this.selectedChapter) selection += ' ' + this.selectedChapter;
            if (this.selectedVerse) selection += ':' + this.selectedVerse;
            return selection;
        },

        get accuracy() {
            return this.totalAttempts > 0 ? Math.round((this.versesFound / this.totalAttempts) * 100) : 0;
        },

        startGame() {
            this.isPlaying = true;
            this.gameOver = false;
            this.score = 0;
            this.versesFound = 0;
            this.totalAttempts = 0;
            this.timeRemaining = 60;
            this.feedbackMessage = '';
            
            this.loadNewVerse();
            this.startTimer();
        },

        loadNewVerse() {
            this.selectedBook = '';
            this.selectedChapter = null;
            this.selectedVerse = null;
            this.availableChapters = [];
            this.availableVerses = [];
            
            const verse = this.versesToFind[Math.floor(Math.random() * this.versesToFind.length)];
            this.targetBook = verse.book;
            this.targetChapter = verse.chapter;
            this.targetVerse = verse.verse;
            this.targetReference = `${verse.bookName} ${verse.chapter}:${verse.verse}`;
            this.targetHint = verse.hint;
        },

        onBookChange() {
            const book = this.books.find(b => b.abbrev === this.selectedBook);
            if (book) {
                this.availableChapters = Array.from({ length: book.chapters }, (_, i) => i + 1);
            } else {
                this.availableChapters = [];
            }
            this.selectedChapter = null;
            this.selectedVerse = null;
            this.availableVerses = [];
        },

        onChapterChange() {
            this.availableVerses = Array.from({ length: this.versesPerGame[this.difficulty] }, (_, i) => i + 1);
            this.selectedVerse = null;
        },

        checkAnswer() {
            this.totalAttempts++;
            
            if (this.selectedBook === this.targetBook && 
                this.selectedChapter === this.targetChapter && 
                this.selectedVerse === this.targetVerse) {
                
                this.versesFound++;
                const timeBonus = Math.max(0, Math.floor(this.timeRemaining / 10));
                this.score += 10 + timeBonus;
                this.timeRemaining = Math.min(this.timeRemaining + 5, 90);
                
                this.feedbackMessage = `✓ Correto! +${10 + timeBonus} pontos, +5 segundos!`;
                this.feedbackCorrect = true;
                
                if (navigator.vibrate) navigator.vibrate([50, 30, 50]);
                
                setTimeout(() => {
                    this.feedbackMessage = '';
                    this.loadNewVerse();
                }, 1500);
            } else {
                this.feedbackMessage = `✗ Incorreto! Era ${this.targetReference}`;
                this.feedbackCorrect = false;
                
                if (navigator.vibrate) navigator.vibrate([100, 50, 100]);
                
                setTimeout(() => {
                    this.feedbackMessage = '';
                    this.loadNewVerse();
                }, 2000);
            }
        },

        startTimer() {
            this.timerInterval = setInterval(() => {
                this.timeRemaining--;
                if (this.timeRemaining <= 0) {
                    this.endGame();
                }
            }, 1000);
        },

        endGame() {
            clearInterval(this.timerInterval);
            this.gameOver = true;
            this.isPlaying = false;
            this.saveScore();
        },

        restartGame() {
            this.gameOver = false;
            this.isPlaying = false;
        },

        formatTime(seconds) {
            const mins = Math.floor(seconds / 60);
            const secs = seconds % 60;
            return `${mins}:${secs.toString().padStart(2, '0')}`;
        },

        async saveScore() {
            try {
                const response = await fetch('{{ route("memberpanel.ebd.arcade.submit", "navegador-biblico") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        score: this.score,
                        metadata: {
                            verses_found: this.versesFound,
                            accuracy: this.accuracy,
                            difficulty: this.difficulty
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
