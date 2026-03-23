@extends('memberpanel::components.layouts.master')

@section('title', 'Quem Disse? - Arcade')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-slate-950 transition-colors duration-200 pb-12" x-data="whoSaidItGame()">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 pt-4 sm:pt-6 space-y-4 sm:space-y-6">

        @include('ebd::memberpanel.arcade.partials.game-header', [
            'gameTitle' => 'Quem Disse?',
            'gameSubtitle' => 'Adivinhe qual personagem bíblico fez a citação.',
            'icon' => 'comment-quote',
            'accent' => 'rose',
        ])

    <!-- Game Area -->
    <div class="min-h-[380px] sm:min-h-[450px] relative">

        <!-- Start Screen -->
        <div x-show="!isPlaying && !gameOver" class="text-center space-y-8 py-10">
            <div class="bg-white dark:bg-gray-900 rounded-2xl md:rounded-3xl p-8 md:p-12 shadow-2xl border border-gray-100 dark:border-gray-800">
                <div class="w-20 h-20 bg-rose-500 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-xl shadow-rose-500/30">
                    <x-icon name="comment-quote" style="duotone" class="w-10 h-10 text-white" />
                </div>
                <h2 class="text-2xl md:text-3xl font-black text-gray-900 dark:text-white mb-4">Quem Disse Isso?</h2>
                <p class="text-gray-500 mb-8">Identifique quem falou frases famosas da Bíblia. Teste seu conhecimento das Escrituras!</p>
                
                <div class="grid grid-cols-2 gap-4 mb-8 max-w-xs mx-auto">
                    <div class="bg-rose-50 dark:bg-rose-900/20 p-4 rounded-2xl">
                        <span class="text-3xl font-black text-rose-600">15</span>
                        <span class="block text-xs text-gray-500 mt-1">Citações</span>
                    </div>
                    <div class="bg-amber-50 dark:bg-amber-900/20 p-4 rounded-2xl">
                        <span class="text-3xl font-black text-amber-600">20</span>
                        <span class="block text-xs text-gray-500 mt-1">Pts/Acerto</span>
                    </div>
                </div>
                
                <button @click="startGame()" class="w-full py-5 bg-rose-600 hover:bg-rose-500 text-white rounded-2xl font-black text-lg uppercase tracking-widest shadow-xl shadow-rose-600/30 transition-all hover:-translate-y-1 active:scale-95 touch-manipulation">
                    <x-icon name="play" style="solid" class="w-5 h-5 inline mr-2" /> Iniciar Jogo
                </button>
            </div>
        </div>

        <div x-show="isPlaying && !gameOver" x-transition class="space-y-6 md:space-y-8">
            <!-- Quote Card -->
            <div class="bg-white dark:bg-gray-900 rounded-2xl md:rounded-[3rem] p-6 sm:p-10 md:p-16 shadow-2xl border border-gray-100 dark:border-gray-800 text-center relative overflow-hidden">
                <x-icon name="quote-left" style="solid" class="w-8 h-8 sm:w-10 sm:h-10 md:w-12 md:h-12 text-rose-200 dark:text-rose-900/30 absolute top-4 sm:top-6 md:top-8 left-4 sm:left-6 md:left-8" />
                <x-icon name="quote-right" style="solid" class="w-8 h-8 sm:w-10 sm:h-10 md:w-12 md:h-12 text-rose-200 dark:text-rose-900/30 absolute bottom-4 sm:bottom-6 md:bottom-8 right-4 sm:right-6 md:right-8" />

                <h2 class="text-lg sm:text-xl md:text-2xl lg:text-3xl xl:text-4xl font-serif italic text-gray-800 dark:text-gray-200 leading-relaxed relative z-10 px-4 sm:px-8" x-text="currentRound.quote">...</h2>
                
                <!-- Hint (Reference) -->
                <div x-show="showHint" x-transition class="mt-6 inline-flex items-center gap-2 px-4 py-2 bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 rounded-full text-sm font-bold">
                    <x-icon name="lightbulb" style="duotone" class="w-4 h-4" />
                    <span x-text="currentRound.reference || 'Bíblia Sagrada'"></span>
                </div>
                
                <button x-show="!showHint && !answered" @click="showHint = true; usedHint = true" 
                        class="mt-6 text-sm text-gray-400 hover:text-amber-500 transition-colors">
                    <x-icon name="lightbulb" style="duotone" class="w-4 h-4 inline mr-1" /> Mostrar dica (-5 pts)
                </button>
            </div>

            <!-- Options -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 md:gap-4">
                <template x-for="(option, idx) in currentOptions" :key="option.id">
                    <button @click="selectOption(option)"
                            :disabled="answered"
                            class="p-4 sm:p-5 md:p-6 rounded-xl md:rounded-2xl border-2 text-left transition-all duration-200 group active:scale-[0.98] flex items-center gap-3 sm:gap-4 min-h-[70px] touch-manipulation"
                            :class="{
                                'border-gray-200 dark:border-gray-700 hover:border-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/10': !answered,
                                'border-emerald-500 bg-emerald-50 dark:bg-emerald-900/20': answered && option.is_correct,
                                'border-red-500 bg-red-50 dark:bg-red-900/20 opacity-60': answered && !option.is_correct && selectedOptionId === option.id,
                                'opacity-40': answered && !option.is_correct && selectedOptionId !== option.id
                            }">
                        <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-full flex items-center justify-center shrink-0 transition-colors"
                             :class="answered && option.is_correct ? 'bg-emerald-500' : (answered && !option.is_correct && selectedOptionId === option.id ? 'bg-red-500' : 'bg-gray-200 dark:bg-gray-700')">
                            <span class="text-lg sm:text-xl font-black"
                                  :class="answered && (option.is_correct || selectedOptionId === option.id) ? 'text-white' : 'text-gray-500 dark:text-gray-400'"
                                  x-text="option.icon || '👤'"></span>
                        </div>
                        <span class="text-base sm:text-lg font-bold text-gray-800 dark:text-gray-200" x-text="option.name"></span>
                        
                        <!-- Feedback icons -->
                        <div class="ml-auto shrink-0">
                            <x-icon name="check-circle" style="solid" class="w-6 h-6 text-emerald-500" x-show="answered && option.is_correct" />
                            <x-icon name="xmark-circle" style="solid" class="w-6 h-6 text-red-500" x-show="answered && !option.is_correct && selectedOptionId === option.id" />
                        </div>
                    </button>
                </template>
            </div>

            <!-- Next Button -->
            <div class="flex justify-center pt-4" x-show="answered">
                <button @click="nextRound()" class="px-6 sm:px-8 py-3 sm:py-4 bg-rose-600 hover:bg-rose-500 text-white rounded-xl font-bold shadow-lg shadow-rose-500/30 transition-all flex items-center gap-2 touch-manipulation active:scale-95">
                    <span x-text="currentRoundIndex >= totalRounds - 1 ? 'Ver Resultado' : 'Próxima Citação'"></span>
                    <x-icon name="arrow-right" style="solid" class="w-4 h-4" />
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
            <div class="text-5xl md:text-6xl font-black text-rose-600 tracking-tighter" x-text="score">0</div>
            
            <div class="grid grid-cols-2 gap-4 max-w-sm mx-auto">
                <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-2xl">
                    <span class="block text-xs font-black text-gray-400 uppercase">Acertos</span>
                    <span class="text-2xl font-bold text-emerald-500" x-text="correctAnswers">0</span>
                </div>
                <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-2xl">
                    <span class="block text-xs font-black text-gray-400 uppercase">XP Ganho</span>
                    <span class="text-2xl font-bold text-rose-500">+<span x-text="xpGained">0</span></span>
                </div>
            </div>
            
            <div class="flex flex-col sm:flex-row justify-center gap-3 md:gap-4 pt-4">
                <button @click="restartGame()" class="px-6 md:px-8 py-3 md:py-4 bg-rose-600 hover:bg-rose-500 text-white rounded-xl font-bold transition-all touch-manipulation active:scale-95">
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
function whoSaidItGame() {
    return {
        // 80+ Biblical quotes database
        allRounds: [
            // Antigo Testamento - Personagens
            { quote: "Ainda que eu andasse pelo vale da sombra da morte, não temeria mal algum.", reference: "Salmos 23:4", correct: "Davi", icon: "👑" },
            { quote: "No princípio criou Deus os céus e a terra.", reference: "Gênesis 1:1", correct: "Moisés", icon: "📜" },
            { quote: "Eis-me aqui, envia-me a mim.", reference: "Isaías 6:8", correct: "Isaías", icon: "🔥" },
            { quote: "Porventura sou eu guardador do meu irmão?", reference: "Gênesis 4:9", correct: "Caim", icon: "😠" },
            { quote: "Vaidade de vaidades, tudo é vaidade.", reference: "Eclesiastes 1:2", correct: "Salomão", icon: "👑" },
            { quote: "O Senhor é o meu pastor, nada me faltará.", reference: "Salmos 23:1", correct: "Davi", icon: "👑" },
            { quote: "Sê forte e corajoso; não temas, nem te espantes.", reference: "Josué 1:9", correct: "Deus a Josué", icon: "⚔️" },
            { quote: "Até quando coxeareis entre dois pensamentos?", reference: "1 Reis 18:21", correct: "Elias", icon: "🔥" },
            { quote: "Não há Deus em toda a terra, senão em Israel.", reference: "2 Reis 5:15", correct: "Naamã", icon: "🛁" },
            { quote: "Tu és o homem!", reference: "2 Samuel 12:7", correct: "Natã", icon: "👆" },
            { quote: "Eu sei que o meu Redentor vive.", reference: "Jó 19:25", correct: "Jó", icon: "😔" },
            { quote: "Fala, Senhor, porque o teu servo ouve.", reference: "1 Samuel 3:10", correct: "Samuel", icon: "👂" },
            { quote: "Se eu perecer, pereci.", reference: "Ester 4:16", correct: "Ester", icon: "👸" },
            { quote: "Para onde quer que fores, irei eu.", reference: "Rute 1:16", correct: "Rute", icon: "💕" },
            { quote: "O Senhor é a minha luz e a minha salvação.", reference: "Salmos 27:1", correct: "Davi", icon: "👑" },
            { quote: "Ensina-nos a contar os nossos dias.", reference: "Salmos 90:12", correct: "Moisés", icon: "📜" },
            { quote: "Deixa ir o meu povo.", reference: "Êxodo 5:1", correct: "Moisés", icon: "📜" },
            { quote: "Os meus olhos estão continuamente no Senhor.", reference: "Salmos 25:15", correct: "Davi", icon: "👑" },
            { quote: "Se o Senhor é Deus, segui-o.", reference: "1 Reis 18:21", correct: "Elias", icon: "🔥" },
            { quote: "Antes não tivera eu nascido.", reference: "Jó 3:11", correct: "Jó", icon: "😔" },
            
            // Novo Testamento - Jesus
            { quote: "Eu sou o caminho, a verdade e a vida.", reference: "João 14:6", correct: "Jesus", icon: "✝️" },
            { quote: "Amai os vossos inimigos.", reference: "Mateus 5:44", correct: "Jesus", icon: "✝️" },
            { quote: "Vinde a mim, todos os que estais cansados e oprimidos.", reference: "Mateus 11:28", correct: "Jesus", icon: "✝️" },
            { quote: "Eu sou a ressurreição e a vida.", reference: "João 11:25", correct: "Jesus", icon: "✝️" },
            { quote: "Pai, perdoa-lhes, porque não sabem o que fazem.", reference: "Lucas 23:34", correct: "Jesus", icon: "✝️" },
            { quote: "Está consumado.", reference: "João 19:30", correct: "Jesus", icon: "✝️" },
            { quote: "Eu sou a luz do mundo.", reference: "João 8:12", correct: "Jesus", icon: "✝️" },
            { quote: "Eu sou o bom pastor.", reference: "João 10:11", correct: "Jesus", icon: "✝️" },
            { quote: "Eu sou o pão da vida.", reference: "João 6:35", correct: "Jesus", icon: "✝️" },
            { quote: "Eu sou a videira, vós as varas.", reference: "João 15:5", correct: "Jesus", icon: "✝️" },
            { quote: "Deixai vir a mim os pequeninos.", reference: "Marcos 10:14", correct: "Jesus", icon: "✝️" },
            { quote: "Quem não é contra nós é por nós.", reference: "Marcos 9:40", correct: "Jesus", icon: "✝️" },
            { quote: "Nem só de pão viverá o homem.", reference: "Mateus 4:4", correct: "Jesus", icon: "✝️" },
            { quote: "Buscai primeiro o reino de Deus.", reference: "Mateus 6:33", correct: "Jesus", icon: "✝️" },
            { quote: "O Filho do Homem veio buscar e salvar o perdido.", reference: "Lucas 19:10", correct: "Jesus", icon: "✝️" },
            
            // Novo Testamento - Apóstolos
            { quote: "Tudo posso naquele que me fortalece.", reference: "Filipenses 4:13", correct: "Paulo", icon: "✍️" },
            { quote: "Arrependei-vos, porque é chegado o reino dos céus.", reference: "Mateus 3:2", correct: "João Batista", icon: "🌊" },
            { quote: "Tu és o Cristo, o Filho do Deus vivo.", reference: "Mateus 16:16", correct: "Pedro", icon: "🔑" },
            { quote: "Senhor, para quem iremos nós? Tu tens as palavras da vida eterna.", reference: "João 6:68", correct: "Pedro", icon: "🔑" },
            { quote: "Não sou digno de desatar a correia das sandálias dele.", reference: "João 1:27", correct: "João Batista", icon: "🌊" },
            { quote: "Porque para mim o viver é Cristo, e o morrer é ganho.", reference: "Filipenses 1:21", correct: "Paulo", icon: "✍️" },
            { quote: "Já estou crucificado com Cristo.", reference: "Gálatas 2:20", correct: "Paulo", icon: "✍️" },
            { quote: "O amor é sofredor, é benigno.", reference: "1 Coríntios 13:4", correct: "Paulo", icon: "✍️" },
            { quote: "Se Deus é por nós, quem será contra nós?", reference: "Romanos 8:31", correct: "Paulo", icon: "✍️" },
            { quote: "Combati o bom combate, acabei a carreira.", reference: "2 Timóteo 4:7", correct: "Paulo", icon: "✍️" },
            { quote: "Quando sou fraco, então sou forte.", reference: "2 Coríntios 12:10", correct: "Paulo", icon: "✍️" },
            { quote: "Sede praticantes da palavra, e não somente ouvintes.", reference: "Tiago 1:22", correct: "Tiago", icon: "📖" },
            { quote: "A fé sem obras é morta.", reference: "Tiago 2:26", correct: "Tiago", icon: "📖" },
            { quote: "Senhor, se não vi, não creio.", reference: "João 20:25", correct: "Tomé", icon: "🤔" },
            { quote: "Senhor meu e Deus meu!", reference: "João 20:28", correct: "Tomé", icon: "🤔" },
            
            // Outros personagens NT
            { quote: "Não tenho prata nem ouro, mas o que tenho te dou.", reference: "Atos 3:6", correct: "Pedro", icon: "🔑" },
            { quote: "Senhor, não lhes imputes este pecado.", reference: "Atos 7:60", correct: "Estêvão", icon: "💎" },
            { quote: "Crê no Senhor Jesus Cristo e serás salvo.", reference: "Atos 16:31", correct: "Paulo e Silas", icon: "🔗" },
            { quote: "Que farei, Senhor?", reference: "Atos 22:10", correct: "Paulo", icon: "✍️" },
            { quote: "Deus amou o mundo de tal maneira...", reference: "João 3:16", correct: "Jesus (a Nicodemos)", icon: "✝️" },
            { quote: "Dá-me de beber.", reference: "João 4:7", correct: "Jesus (à samaritana)", icon: "✝️" },
            
            // Profetas
            { quote: "Antes que te formasse no ventre te conheci.", reference: "Jeremias 1:5", correct: "Deus a Jeremias", icon: "📢" },
            { quote: "Fostes comprados por bom preço.", reference: "1 Coríntios 6:20", correct: "Paulo", icon: "✍️" },
            { quote: "Nascerá uma estrela de Jacó.", reference: "Números 24:17", correct: "Balaão", icon: "⭐" },
            { quote: "Derramarei o meu Espírito sobre toda a carne.", reference: "Joel 2:28", correct: "Joel", icon: "💨" },
            { quote: "Eis que uma virgem conceberá.", reference: "Isaías 7:14", correct: "Isaías", icon: "🔥" },
            { quote: "Porque um menino nos nasceu, um filho se nos deu.", reference: "Isaías 9:6", correct: "Isaías", icon: "🔥" },
            { quote: "Levanta-te, vai a Nínive.", reference: "Jonas 1:2", correct: "Deus a Jonas", icon: "🐋" },
            { quote: "Que é que o Senhor pede de ti? Que pratiques a justiça.", reference: "Miqueias 6:8", correct: "Miqueias", icon: "⚖️" },
            
            // Maria e outros
            { quote: "Eis aqui a serva do Senhor.", reference: "Lucas 1:38", correct: "Maria", icon: "👼" },
            { quote: "A minha alma engrandece ao Senhor.", reference: "Lucas 1:46", correct: "Maria", icon: "👼" },
            { quote: "Fazei tudo quanto ele vos disser.", reference: "João 2:5", correct: "Maria", icon: "👼" },
            { quote: "Mas uma só coisa é necessária.", reference: "Lucas 10:42", correct: "Jesus (a Marta)", icon: "✝️" },
            { quote: "Senhor, se estivesses aqui, meu irmão não teria morrido.", reference: "João 11:21", correct: "Marta", icon: "😢" },
            
            // Mais citações
            { quote: "Conhecereis a verdade, e a verdade vos libertará.", reference: "João 8:32", correct: "Jesus", icon: "✝️" },
            { quote: "Bem-aventurados os pobres de espírito.", reference: "Mateus 5:3", correct: "Jesus", icon: "✝️" },
            { quote: "Sede santos, porque eu sou santo.", reference: "1 Pedro 1:16", correct: "Deus (citado por Pedro)", icon: "🌟" },
            { quote: "Melhor é dar do que receber.", reference: "Atos 20:35", correct: "Jesus (citado por Paulo)", icon: "✝️" },
            { quote: "Onde está, ó morte, o teu aguilhão?", reference: "1 Coríntios 15:55", correct: "Paulo", icon: "✍️" },
            { quote: "Todas as coisas contribuem para o bem.", reference: "Romanos 8:28", correct: "Paulo", icon: "✍️" },
            { quote: "Nenhuma condenação há para os que estão em Cristo.", reference: "Romanos 8:1", correct: "Paulo", icon: "✍️" },
            { quote: "Alegrai-vos sempre no Senhor.", reference: "Filipenses 4:4", correct: "Paulo", icon: "✍️" },
            { quote: "Tudo tem o seu tempo.", reference: "Eclesiastes 3:1", correct: "Salomão", icon: "👑" },
            { quote: "Lembra-te do teu Criador nos dias da tua mocidade.", reference: "Eclesiastes 12:1", correct: "Salomão", icon: "👑" },
        ],

        rounds: [],
        currentRoundIndex: 0,
        currentRound: {},
        currentOptions: [],
        score: 0,
        correctAnswers: 0,
        totalRounds: 15,
        isPlaying: false,
        gameOver: false,
        answered: false,
        selectedOptionId: null,
        showHint: false,
        usedHint: false,
        xpGained: 0,

        // All possible answer options with icons
        allCharacters: [
            { name: "Davi", icon: "👑" },
            { name: "Moisés", icon: "📜" },
            { name: "Isaías", icon: "🔥" },
            { name: "Salomão", icon: "👑" },
            { name: "Elias", icon: "🔥" },
            { name: "Jó", icon: "😔" },
            { name: "Samuel", icon: "👂" },
            { name: "Jesus", icon: "✝️" },
            { name: "Paulo", icon: "✍️" },
            { name: "Pedro", icon: "🔑" },
            { name: "João Batista", icon: "🌊" },
            { name: "Tiago", icon: "📖" },
            { name: "Tomé", icon: "🤔" },
            { name: "Maria", icon: "👼" },
            { name: "Ester", icon: "👸" },
            { name: "Rute", icon: "💕" },
            { name: "Caim", icon: "😠" },
            { name: "Abraão", icon: "⭐" },
            { name: "Jeremias", icon: "📢" },
            { name: "Daniel", icon: "🦁" },
            { name: "Jonas", icon: "🐋" },
            { name: "Estêvão", icon: "💎" },
        ],

        startGame() {
            this.isPlaying = true;
            this.init();
        },

        init() {
            this.rounds = [...this.allRounds].sort(() => Math.random() - 0.5).slice(0, this.totalRounds);
            this.currentRoundIndex = 0;
            this.score = 0;
            this.correctAnswers = 0;
            this.gameOver = false;
            this.loadRound();
        },

        loadRound() {
            if (this.currentRoundIndex >= this.rounds.length) {
                this.endGame();
                return;
            }
            
            const round = this.rounds[this.currentRoundIndex];
            this.currentRound = round;
            
            // Generate options (1 correct + 3 random wrong)
            const correctOption = {
                id: 1,
                name: round.correct,
                icon: round.icon,
                is_correct: true
            };
            
            // Get 3 random wrong options
            const wrongCharacters = this.allCharacters
                .filter(c => c.name !== round.correct)
                .sort(() => Math.random() - 0.5)
                .slice(0, 3)
                .map((c, i) => ({
                    id: i + 2,
                    name: c.name,
                    icon: c.icon,
                    is_correct: false
                }));
            
            this.currentOptions = [correctOption, ...wrongCharacters].sort(() => Math.random() - 0.5);
            this.answered = false;
            this.selectedOptionId = null;
            this.showHint = false;
            this.usedHint = false;
        },

        selectOption(option) {
            if (this.answered) return;
            this.answered = true;
            this.selectedOptionId = option.id;

            if (option.is_correct) {
                const points = this.usedHint ? 15 : 20;
                this.score += points;
                this.correctAnswers++;
                if (navigator.vibrate) navigator.vibrate(50);
            } else {
                if (navigator.vibrate) navigator.vibrate([100, 50, 100]);
            }
        },

        nextRound() {
            this.currentRoundIndex++;
            this.loadRound();
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
                const response = await fetch('{{ route("memberpanel.ebd.arcade.submit", "quem-disse") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        score: this.score,
                        metadata: {
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
