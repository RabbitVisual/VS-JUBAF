@extends('memberpanel::components.layouts.master')

@section('title', 'Complete o Versículo - Arcade')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-slate-950 transition-colors duration-200 pb-12" x-data="fillBlankGame()">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 pt-4 sm:pt-6 space-y-4 sm:space-y-6">

        @include('ebd::memberpanel.arcade.partials.game-header', [
            'gameTitle' => 'Complete o Versículo',
            'gameSubtitle' => 'Preencha as lacunas em versículos famosos da Bíblia.',
            'icon' => 'pen-to-square',
            'accent' => 'sky',
        ])

    <!-- Game Area -->
    <div class="max-w-4xl mx-auto relative px-2 sm:px-4">

        <!-- Start Screen -->
        <div x-show="!isPlaying && !gameOver" class="text-center space-y-8 py-10">
            <div class="bg-white dark:bg-gray-900 rounded-2xl md:rounded-3xl p-8 md:p-12 shadow-2xl border border-gray-100 dark:border-gray-800">
                <div class="w-20 h-20 bg-sky-500 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-xl shadow-sky-500/30">
                    <x-icon name="pen-to-square" style="duotone" class="w-10 h-10 text-white" />
                </div>
                <h2 class="text-2xl md:text-3xl font-black text-gray-900 dark:text-white mb-4">Complete o Versículo</h2>
                <p class="text-gray-500 mb-8">Complete os versículos bíblicos famosos preenchendo as palavras que faltam!</p>
                
                <div class="grid grid-cols-2 gap-4 mb-8 max-w-xs mx-auto text-center">
                    <div class="bg-sky-50 dark:bg-sky-900/20 p-4 rounded-2xl">
                        <span class="text-3xl font-black text-sky-600">10</span>
                        <span class="block text-xs text-gray-500 mt-1">Versículos</span>
                    </div>
                    <div class="bg-emerald-50 dark:bg-emerald-900/20 p-4 rounded-2xl">
                        <span class="text-3xl font-black text-emerald-600">30</span>
                        <span class="block text-xs text-gray-500 mt-1">Pts/Acerto</span>
                    </div>
                </div>
                
                <button @click="startGame()" class="w-full py-5 bg-sky-600 hover:bg-sky-500 text-white rounded-2xl font-black text-lg uppercase tracking-widest shadow-xl shadow-sky-600/30 transition-all hover:-translate-y-1 active:scale-95 touch-manipulation">
                    <x-icon name="play" style="solid" class="w-5 h-5 inline mr-2" /> Iniciar Jogo
                </button>
            </div>
        </div>

        <div x-show="isPlaying && !gameOver" x-transition class="space-y-6">
            <!-- Verse Reference -->
            <div class="text-center">
                <span class="px-4 py-2 bg-sky-100 dark:bg-sky-900/30 text-sky-600 dark:text-sky-300 rounded-full text-xs font-black uppercase tracking-widest" x-text="currentVerse.reference">...</span>
            </div>

            <!-- Verse Card -->
            <div class="bg-white dark:bg-gray-900 rounded-2xl md:rounded-3xl p-6 sm:p-8 md:p-12 shadow-2xl border border-gray-100 dark:border-gray-800">
                <div class="flex flex-wrap items-center justify-center gap-2 text-lg sm:text-xl md:text-2xl leading-relaxed">
                    <template x-for="(segment, idx) in verseSegments" :key="idx">
                        <template x-if="segment.type === 'text'">
                            <span class="text-gray-700 dark:text-gray-300" x-text="segment.value"></span>
                        </template>
                        <template x-if="segment.type === 'blank'">
                            <span class="inline-flex items-center justify-center min-w-[80px] sm:min-w-[100px] px-3 py-2 rounded-lg border-2 border-dashed transition-all font-bold"
                                  :class="{
                                      'border-sky-500 bg-sky-50 dark:bg-sky-900/20 text-sky-600': segment.filled && !showResults,
                                      'border-emerald-500 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600': showResults && segment.correct,
                                      'border-red-500 bg-red-50 dark:bg-red-900/20 text-red-600': showResults && !segment.correct,
                                      'border-gray-300 dark:border-gray-600 text-gray-400': !segment.filled
                                  }"
                                  x-text="segment.filled ? segment.userAnswer : '______'">
                            </span>
                        </template>
                    </template>
                </div>

                <!-- Correction hint -->
                <div x-show="showResults && !allCorrect" class="mt-6 text-center">
                    <p class="text-gray-500 text-sm">Resposta correta: 
                        <template x-for="(word, idx) in currentVerse.blanks" :key="'correct-'+idx">
                            <span class="font-bold text-emerald-600" x-text="word + (idx < currentVerse.blanks.length - 1 ? ', ' : '')"></span>
                        </template>
                    </p>
                </div>
            </div>

            <!-- Word Bank -->
            <div x-show="!showResults" class="bg-gray-100 dark:bg-gray-800 p-4 sm:p-6 md:p-8 rounded-2xl md:rounded-3xl">
                <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4 text-center">Escolha as palavras</h3>
                <div class="flex flex-wrap justify-center gap-2 sm:gap-3">
                    <template x-for="word in wordBank" :key="word.id">
                        <button @click="selectWord(word)"
                                :disabled="word.used"
                                class="px-4 sm:px-6 py-2 sm:py-3 rounded-xl font-bold text-base sm:text-lg transition-all touch-manipulation active:scale-95"
                                :class="word.used ? 'bg-gray-300 dark:bg-gray-600 text-gray-500 cursor-not-allowed' : 'bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 hover:bg-sky-100 dark:hover:bg-sky-900/30 hover:text-sky-600 shadow-md'">
                            <span x-text="word.text"></span>
                        </button>
                    </template>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-center gap-4 pt-4">
                <button @click="checkAnswers()" x-show="!showResults && allFilled" 
                        class="px-6 sm:px-8 py-3 sm:py-4 bg-sky-600 hover:bg-sky-500 text-white rounded-xl font-bold shadow-lg shadow-sky-500/30 transition-all touch-manipulation active:scale-95">
                    <x-icon name="check" style="solid" class="w-5 h-5 inline mr-2" /> Verificar
                </button>
                <button @click="resetBlanks()" x-show="!showResults && anyFilled"
                        class="px-6 py-3 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-bold transition-all touch-manipulation active:scale-95">
                    <x-icon name="rotate-left" style="solid" class="w-4 h-4 inline mr-2" /> Limpar
                </button>
                <button @click="nextVerse()" x-show="showResults" 
                        class="px-6 sm:px-8 py-3 sm:py-4 bg-sky-600 hover:bg-sky-500 text-white rounded-xl font-bold shadow-lg shadow-sky-500/30 transition-all touch-manipulation active:scale-95 animate-pulse">
                    <span x-text="currentVerseIndex >= totalVerses - 1 ? 'Ver Resultado' : 'Próximo Versículo'"></span>
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
                <p class="text-gray-500 mt-2">Você conhece bem as Escrituras!</p>
            </div>
            <div class="text-5xl md:text-6xl font-black text-sky-600 tracking-tighter" x-text="score">0</div>
            
            <div class="grid grid-cols-2 gap-4 max-w-sm mx-auto">
                <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-2xl">
                    <span class="block text-xs font-black text-gray-400 uppercase">Perfeitos</span>
                    <span class="text-2xl font-bold text-emerald-500" x-text="perfectVerses">0</span>
                </div>
                <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-2xl">
                    <span class="block text-xs font-black text-gray-400 uppercase">XP Ganho</span>
                    <span class="text-2xl font-bold text-sky-500">+<span x-text="xpGained">0</span></span>
                </div>
            </div>
            
            <div class="flex flex-col sm:flex-row justify-center gap-3 md:gap-4 pt-4">
                <button @click="restartGame()" class="px-6 md:px-8 py-3 md:py-4 bg-sky-600 hover:bg-sky-500 text-white rounded-xl font-bold transition-all touch-manipulation active:scale-95">
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
function fillBlankGame() {
    return {
        allVerses: [
            { reference: "João 3:16", text: "Porque Deus amou o mundo de tal maneira que deu o seu Filho ___, para que todo aquele que nele crê não ___, mas tenha a vida ___.", blanks: ["unigênito", "pereça", "eterna"] },
            { reference: "Salmos 23:1", text: "O Senhor é o meu ___; nada me ___.", blanks: ["pastor", "faltará"] },
            { reference: "Romanos 8:28", text: "Sabemos que todas as coisas contribuem juntamente para o ___ daqueles que amam a ___.", blanks: ["bem", "Deus"] },
            { reference: "Filipenses 4:13", text: "Posso todas as coisas naquele que me ___.", blanks: ["fortalece"] },
            { reference: "Provérbios 3:5-6", text: "Confia no Senhor de todo o teu ___; e não te estribes no teu próprio ___.", blanks: ["coração", "entendimento"] },
            { reference: "Isaías 40:31", text: "Mas os que esperam no ___ renovarão as suas forças, subirão com ___ como águias.", blanks: ["Senhor", "asas"] },
            { reference: "Jeremias 29:11", text: "Pois eu sei os ___ que tenho a vosso respeito, diz o Senhor; ___ de paz, e não de mal.", blanks: ["pensamentos", "pensamentos"] },
            { reference: "Mateus 6:33", text: "Buscai primeiro o ___ de Deus, e a sua justiça, e todas estas coisas vos serão ___.", blanks: ["reino", "acrescentadas"] },
            { reference: "Salmos 119:105", text: "___ para os meus pés é a tua palavra, e ___ para o meu caminho.", blanks: ["Lâmpada", "luz"] },
            { reference: "2 Timóteo 1:7", text: "Porque Deus não nos deu o espírito de ___, mas de fortaleza, e de amor, e de ___.", blanks: ["temor", "moderação"] },
            { reference: "Gálatas 5:22", text: "Mas o fruto do Espírito é: ___, alegria, paz, longanimidade, benignidade, bondade, fé, ___.", blanks: ["amor", "mansidão"] },
            { reference: "Hebreus 11:1", text: "Ora, a fé é o ___ das coisas que se esperam, e a ___ das que não se veem.", blanks: ["firme fundamento", "prova"] },
            { reference: "1 Coríntios 13:13", text: "Agora, pois, permanecem a fé, a esperança e o amor, estes três; porém o maior destes é o ___.", blanks: ["amor"] },
            { reference: "João 14:6", text: "Eu sou o ___, a ___ e a vida.", blanks: ["caminho", "verdade"] },
            { reference: "Romanos 12:2", text: "E não vos conformeis com este ___; mas transformai-vos pela renovação do vosso ___.", blanks: ["mundo", "entendimento"] },
        ],

        verses: [],
        currentVerse: { reference: '', text: '', blanks: [] },
        currentVerseIndex: 0,
        totalVerses: 10,
        verseSegments: [],
        wordBank: [],
        score: 0,
        perfectVerses: 0,
        isPlaying: false,
        gameOver: false,
        showResults: false,
        allCorrect: false,
        xpGained: 0,

        get allFilled() {
            return this.verseSegments.filter(s => s.type === 'blank').every(s => s.filled);
        },
        get anyFilled() {
            return this.verseSegments.filter(s => s.type === 'blank').some(s => s.filled);
        },

        startGame() {
            this.isPlaying = true;
            this.verses = [...this.allVerses].sort(() => Math.random() - 0.5).slice(0, this.totalVerses);
            this.currentVerseIndex = 0;
            this.score = 0;
            this.perfectVerses = 0;
            this.gameOver = false;
            this.loadVerse();
        },

        loadVerse() {
            if (this.currentVerseIndex >= this.verses.length) {
                this.endGame();
                return;
            }
            
            this.currentVerse = this.verses[this.currentVerseIndex];
            this.showResults = false;
            this.allCorrect = false;
            this.parseVerse();
            this.generateWordBank();
        },

        parseVerse() {
            const parts = this.currentVerse.text.split('___');
            this.verseSegments = [];
            
            parts.forEach((part, idx) => {
                if (part) {
                    this.verseSegments.push({ type: 'text', value: part });
                }
                if (idx < parts.length - 1) {
                    this.verseSegments.push({ 
                        type: 'blank', 
                        index: idx, 
                        filled: false, 
                        userAnswer: '',
                        correct: false,
                        correctAnswer: this.currentVerse.blanks[idx]
                    });
                }
            });
        },

        generateWordBank() {
            // Create word bank with correct answers + distractors
            const correctWords = [...this.currentVerse.blanks];
            const distractors = ["graça", "esperança", "fé", "vida", "morte", "pecado", "salvação", "glória", "poder", "sabedoria", "luz", "trevas", "anjos", "santos", "profetas"];
            
            const shuffledDistractors = distractors
                .filter(d => !correctWords.includes(d))
                .sort(() => Math.random() - 0.5)
                .slice(0, Math.max(2, 6 - correctWords.length));
            
            this.wordBank = [...correctWords, ...shuffledDistractors]
                .sort(() => Math.random() - 0.5)
                .map((word, idx) => ({ id: idx, text: word, used: false }));
        },

        selectWord(word) {
            if (word.used) return;
            
            // Find first empty blank
            const emptyBlank = this.verseSegments.find(s => s.type === 'blank' && !s.filled);
            if (emptyBlank) {
                emptyBlank.filled = true;
                emptyBlank.userAnswer = word.text;
                word.used = true;
                if (navigator.vibrate) navigator.vibrate(30);
            }
        },

        resetBlanks() {
            this.verseSegments.filter(s => s.type === 'blank').forEach(blank => {
                blank.filled = false;
                blank.userAnswer = '';
            });
            this.wordBank.forEach(word => word.used = false);
        },

        checkAnswers() {
            this.showResults = true;
            let correctCount = 0;
            
            this.verseSegments.filter(s => s.type === 'blank').forEach((blank, idx) => {
                const expected = this.currentVerse.blanks[idx].toLowerCase();
                const given = blank.userAnswer.toLowerCase();
                blank.correct = expected === given;
                if (blank.correct) correctCount++;
            });
            
            const totalBlanks = this.currentVerse.blanks.length;
            this.allCorrect = correctCount === totalBlanks;
            
            // Score
            const pointsPerWord = 30;
            this.score += correctCount * pointsPerWord;
            if (this.allCorrect) {
                this.perfectVerses++;
                this.score += 20; // Bonus for perfect
                if (navigator.vibrate) navigator.vibrate([50, 30, 50, 30, 50]);
            } else {
                if (navigator.vibrate) navigator.vibrate([100, 50, 100]);
            }
        },

        nextVerse() {
            this.currentVerseIndex++;
            this.loadVerse();
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
                const response = await fetch('{{ route("memberpanel.ebd.arcade.submit", "complete-o-versiculo") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        score: this.score,
                        metadata: {
                            perfect_verses: this.perfectVerses
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
