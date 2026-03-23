@extends('memberpanel::components.layouts.master')

@section('title', 'Herói da Fé - Arcade')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-slate-950 transition-colors duration-200 pb-12" x-data="heroGame()">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 pt-4 sm:pt-6 space-y-4 sm:space-y-6">

        @include('ebd::memberpanel.arcade.partials.game-header', [
            'gameTitle' => 'Herói da Fé',
            'gameSubtitle' => 'Descubra o personagem bíblico através de dicas progressivas.',
            'icon' => 'user-crown',
            'accent' => 'violet',
        ])

    <!-- Game Area -->
    <div class="max-w-3xl mx-auto relative px-2 sm:px-4">

        <!-- Start Screen -->
        <div x-show="!isPlaying && !gameOver" class="text-center space-y-8 py-10">
            <div class="bg-white dark:bg-gray-900 rounded-2xl md:rounded-3xl p-8 md:p-12 shadow-2xl border border-gray-100 dark:border-gray-800">
                <div class="w-20 h-20 bg-violet-500 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-xl shadow-violet-500/30">
                    <x-icon name="user-crown" style="duotone" class="w-10 h-10 text-white" />
                </div>
                <h2 class="text-2xl md:text-3xl font-black text-gray-900 dark:text-white mb-4">Herói da Fé</h2>
                <p class="text-gray-500 mb-8">Adivinhe o personagem bíblico a partir das dicas! Quanto menos dicas usar, mais pontos você ganha.</p>
                
                <div class="grid grid-cols-3 gap-4 mb-8 max-w-sm mx-auto text-center">
                    <div class="bg-emerald-50 dark:bg-emerald-900/20 p-4 rounded-2xl">
                        <span class="text-2xl font-black text-emerald-600">100</span>
                        <span class="block text-xs text-gray-500 mt-1">1 Dica</span>
                    </div>
                    <div class="bg-amber-50 dark:bg-amber-900/20 p-4 rounded-2xl">
                        <span class="text-2xl font-black text-amber-600">50</span>
                        <span class="block text-xs text-gray-500 mt-1">3 Dicas</span>
                    </div>
                    <div class="bg-red-50 dark:bg-red-900/20 p-4 rounded-2xl">
                        <span class="text-2xl font-black text-red-600">20</span>
                        <span class="block text-xs text-gray-500 mt-1">5 Dicas</span>
                    </div>
                </div>
                
                <button @click="startGame()" class="w-full py-5 bg-violet-600 hover:bg-violet-500 text-white rounded-2xl font-black text-lg uppercase tracking-widest shadow-xl shadow-violet-600/30 transition-all hover:-translate-y-1 active:scale-95 touch-manipulation">
                    <x-icon name="play" style="solid" class="w-5 h-5 inline mr-2" /> Iniciar Jogo
                </button>
            </div>
        </div>

        <div x-show="isPlaying && !gameOver" x-transition class="space-y-6">
            <!-- Round Counter -->
            <div class="text-center">
                <span class="px-4 py-2 bg-violet-100 dark:bg-violet-900/30 text-violet-600 dark:text-violet-300 rounded-full text-xs font-black uppercase tracking-widest">
                    Personagem <span x-text="currentRoundIndex + 1">1</span>/<span x-text="totalRounds">10</span>
                </span>
            </div>

            <!-- Clues Card -->
            <div class="bg-white dark:bg-gray-900 rounded-2xl md:rounded-3xl p-6 sm:p-8 md:p-10 shadow-2xl border border-gray-100 dark:border-gray-800">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 mx-auto bg-violet-100 dark:bg-violet-900/30 rounded-full flex items-center justify-center mb-4">
                        <x-icon name="question" style="duotone" class="w-8 h-8 text-violet-500" />
                    </div>
                    <h3 class="text-lg font-black text-gray-400 uppercase tracking-widest">Quem sou eu?</h3>
                </div>

                <!-- Clues List -->
                <div class="space-y-3 mb-8">
                    <template x-for="(clue, idx) in currentCharacter.clues" :key="idx">
                        <div class="flex items-start gap-3 p-4 rounded-xl transition-all"
                             :class="idx < revealedClues ? 'bg-violet-50 dark:bg-violet-900/20' : 'bg-gray-50 dark:bg-gray-800'">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 font-black text-sm"
                                 :class="idx < revealedClues ? 'bg-violet-500 text-white' : 'bg-gray-300 dark:bg-gray-600 text-gray-500'">
                                <span x-text="idx + 1"></span>
                            </div>
                            <p class="text-base font-medium flex-1"
                               :class="idx < revealedClues ? 'text-gray-800 dark:text-gray-200' : 'text-gray-400 dark:text-gray-600'"
                               x-text="idx < revealedClues ? clue : '???'"></p>
                        </div>
                    </template>
                </div>

                <!-- Reveal More Button -->
                <div class="text-center mb-6" x-show="revealedClues < 5 && !answered">
                    <button @click="revealClue()" class="px-6 py-3 bg-gray-100 dark:bg-gray-800 hover:bg-violet-100 dark:hover:bg-violet-900/30 text-gray-600 dark:text-gray-400 rounded-xl font-bold transition-all touch-manipulation active:scale-95">
                        <x-icon name="lightbulb" style="duotone" class="w-4 h-4 inline mr-2" />
                        Revelar mais uma dica
                    </button>
                </div>

                <!-- Answer Section -->
                <div class="border-t border-gray-100 dark:border-gray-800 pt-6">
                    <div x-show="!answered" class="grid grid-cols-2 gap-3">
                        <template x-for="option in currentOptions" :key="option">
                            <button @click="submitAnswer(option)"
                                    class="p-4 rounded-xl border-2 border-gray-200 dark:border-gray-700 hover:border-violet-500 hover:bg-violet-50 dark:hover:bg-violet-900/10 text-base font-bold text-gray-700 dark:text-gray-300 transition-all touch-manipulation active:scale-95">
                                <span x-text="option"></span>
                            </button>
                        </template>
                    </div>

                    <!-- Answer Result -->
                    <div x-show="answered" class="text-center space-y-4">
                        <div class="w-20 h-20 mx-auto rounded-full flex items-center justify-center"
                             :class="lastAnswerCorrect ? 'bg-emerald-100 dark:bg-emerald-900/30' : 'bg-red-100 dark:bg-red-900/30'">
                            <x-icon name="check" style="solid" class="w-10 h-10 text-emerald-500" x-show="lastAnswerCorrect" />
                            <x-icon name="xmark" style="solid" class="w-10 h-10 text-red-500" x-show="!lastAnswerCorrect" />
                        </div>
                        <div>
                            <p class="text-2xl font-black" :class="lastAnswerCorrect ? 'text-emerald-500' : 'text-red-500'"
                               x-text="lastAnswerCorrect ? '+' + lastPoints + ' pontos!' : 'Incorreto!'"></p>
                            <p class="text-gray-500 mt-2">Era: <span class="font-black text-violet-600" x-text="currentCharacter.name"></span></p>
                        </div>
                        <button @click="nextRound()" class="px-8 py-4 bg-violet-600 hover:bg-violet-500 text-white rounded-xl font-bold transition-all touch-manipulation active:scale-95">
                            <span x-text="currentRoundIndex >= totalRounds - 1 ? 'Ver Resultado' : 'Próximo Personagem'"></span>
                            <x-icon name="arrow-right" style="solid" class="w-4 h-4 inline ml-2" />
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Game Over Modal -->
        <div x-show="gameOver" x-transition class="text-center space-y-6 md:space-y-8 bg-white dark:bg-gray-900 rounded-2xl md:rounded-[3rem] p-8 md:p-16 shadow-2xl border border-gray-100 dark:border-gray-800">
            <div class="w-20 h-20 md:w-24 md:h-24 bg-amber-500 rounded-full flex items-center justify-center mx-auto shadow-2xl shadow-amber-500/30 animate-bounce">
                <x-icon name="trophy" style="duotone" class="w-10 h-10 md:w-12 md:h-12 text-white" />
            </div>
            <div>
                <h2 class="text-3xl md:text-4xl font-black text-gray-900 dark:text-white">Fim de Jogo!</h2>
                <p class="text-gray-500 mt-2">Você conhece bem os heróis da fé!</p>
            </div>
            <div class="text-5xl md:text-6xl font-black text-violet-600 tracking-tighter" x-text="score">0</div>
            
            <div class="grid grid-cols-3 gap-4 max-w-md mx-auto">
                <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-2xl">
                    <span class="block text-xs font-black text-gray-400 uppercase">Acertos</span>
                    <span class="text-2xl font-bold text-emerald-500" x-text="correctAnswers">0</span>
                </div>
                <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-2xl">
                    <span class="block text-xs font-black text-gray-400 uppercase">Sequência</span>
                    <span class="text-2xl font-bold text-amber-500" x-text="maxStreak">0</span>
                </div>
                <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-2xl">
                    <span class="block text-xs font-black text-gray-400 uppercase">XP</span>
                    <span class="text-2xl font-bold text-violet-500">+<span x-text="xpGained">0</span></span>
                </div>
            </div>
            
            <div class="flex flex-col sm:flex-row justify-center gap-3 md:gap-4 pt-4">
                <button @click="restartGame()" class="px-6 md:px-8 py-3 md:py-4 bg-violet-600 hover:bg-violet-500 text-white rounded-xl font-bold transition-all touch-manipulation active:scale-95">
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
function heroGame() {
    return {
        // 100 Biblical characters with 5 clues each
        allCharacters: [
            { name: "Abraão", clues: ["Saí de Ur dos caldeus", "Minha esposa era estéril por muito tempo", "Quase sacrifiquei meu filho", "Sou chamado de pai da fé", "Deus prometeu que minha descendência seria como estrelas"] },
            { name: "Moisés", clues: ["Fui encontrado num cesto no rio", "Fugi para Midiã após matar um egípcio", "Vi uma sarça que ardia mas não queimava", "Libertei meu povo da escravidão", "Recebi os Dez Mandamentos no monte Sinai"] },
            { name: "Davi", clues: ["Era o mais novo de oito irmãos", "Trabalhei como pastor de ovelhas", "Toquei harpa para acalmar um rei", "Derrotei um gigante com uma pedra", "Fui o segundo rei de Israel"] },
            { name: "Salomão", clues: ["Meu pai foi um grande rei", "Pedi sabedoria a Deus", "Construí o primeiro templo de Jerusalém", "Escrevi provérbios e cânticos", "Fui considerado o homem mais sábio"] },
            { name: "Daniel", clues: ["Fui levado cativo para a Babilônia", "Interpretei sonhos de reis", "Recusei comida da mesa real", "Fui lançado numa cova de leões", "Tive visões sobre o fim dos tempos"] },
            { name: "José", clues: ["Tive sonhos que irritaram meus irmãos", "Ganhei uma túnica colorida", "Fui vendido como escravo", "Trabalhei na casa de Potifar", "Me tornei governador do Egito"] },
            { name: "Jonas", clues: ["Deus me mandou pregar em Nínive", "Tentei fugir num navio", "Uma tempestade veio por minha causa", "Fui engolido por um grande peixe", "Finalmente preguei e a cidade se arrependeu"] },
            { name: "Sansão", clues: ["Fui nazireu desde o nascimento", "Nunca cortei meu cabelo", "Matei um leão com as mãos", "Minha força era extraordinária", "Fui traído por uma mulher chamada Dalila"] },
            { name: "Elias", clues: ["Desafiei profetas de Baal", "Fogo caiu do céu por minha oração", "Fugi de uma rainha má", "Fui alimentado por corvos", "Subi ao céu num carro de fogo"] },
            { name: "Noé", clues: ["Vivi numa época muito má", "Deus me mandou construir algo enorme", "Juntei animais em pares", "A chuva durou 40 dias", "Vi um arco-íris como sinal de aliança"] },
            { name: "Pedro", clues: ["Era pescador de profissão", "Andei sobre a água por um momento", "Neguei conhecer meu mestre três vezes", "Preguei no dia de Pentecostes", "Fui o líder dos primeiros discípulos"] },
            { name: "Paulo", clues: ["Perseguia cristãos antes de me converter", "Tive uma visão no caminho de Damasco", "Fiquei cego por três dias", "Fiz várias viagens missionárias", "Escrevi muitas cartas no Novo Testamento"] },
            { name: "Ester", clues: ["Era órfã criada por meu primo", "Participei de um concurso de beleza", "Me tornei rainha da Pérsia", "Arrisquei minha vida pelo meu povo", "Jejuei por três dias antes de agir"] },
            { name: "Rute", clues: ["Era moabita, não israelita", "Meu marido morreu", "Segui minha sogra para outro país", "Trabalhei colhendo espigas", "Me casei com um homem chamado Boaz"] },
            { name: "Samuel", clues: ["Minha mãe orou muito por um filho", "Fui dedicado a Deus ainda criança", "Cresci no templo com Eli", "Ouvi a voz de Deus de noite", "Ungi os dois primeiros reis de Israel"] },
            { name: "Josué", clues: ["Fui ajudante de Moisés", "Espiei a terra prometida", "Liderei a travessia do rio Jordão", "Conquistei Jericó sem lutar", "Disse 'eu e minha casa serviremos ao Senhor'"] },
            { name: "Gideão", clues: ["Um anjo me encontrou debulhando trigo", "Pedi sinais com um velo de lã", "Reduzi meu exército de 32 mil para 300", "Usei tochas, cântaros e trombetas", "Derrotei os midianitas miraculosamente"] },
            { name: "Jeremias", clues: ["Fui chamado ainda jovem", "Sou conhecido como profeta chorão", "Preguei contra a destruição de Jerusalém", "Fui jogado numa cisterna", "Escrevi Lamentações"] },
            { name: "Isaías", clues: ["Tive uma visão de Deus no templo", "Vi serafins voando", "Disse 'eis-me aqui, envia-me'", "Profetizei sobre um servo sofredor", "Falei da virgem que conceberia"] },
            { name: "Ezequiel", clues: ["Vi visões junto ao rio Quebar", "Tive visão de um vale de ossos secos", "Comi um rolo de livro", "Profetizei no exílio babilônico", "Descrevi um novo templo em detalhes"] },
            { name: "João Batista", clues: ["Nasci de pais idosos", "Vivi no deserto", "Me alimentava de gafanhotos e mel", "Batizei muitas pessoas no Jordão", "Preparei o caminho do Messias"] },
            { name: "Maria", clues: ["Recebi a visita de um anjo", "Estava noiva de um carpinteiro", "Disse 'eis aqui a serva do Senhor'", "Visitei minha prima Isabel", "Dei à luz numa manjedoura"] },
            { name: "Adão", clues: ["Fui criado do pó da terra", "Vivi num jardim perfeito", "Dei nome a todos os animais", "Minha esposa veio de minha costela", "Comi do fruto proibido"] },
            { name: "Eva", clues: ["Fui a primeira mulher", "Fui criada de uma costela", "Uma serpente me enganou", "Comi o fruto proibido primeiro", "Sou mãe de toda humanidade"] },
            { name: "Jó", clues: ["Era o homem mais rico do Oriente", "Perdi tudo em um dia", "Meus amigos não me consolaram bem", "Disse 'nu saí do ventre de minha mãe'", "Deus me restaurou em dobro"] },
            { name: "Débora", clues: ["Era profetisa e juíza", "Julgava Israel debaixo de uma palmeira", "Chamei Baraque para a batalha", "Cantei uma canção de vitória", "Sou uma das poucas líderes mulheres"] },
            { name: "Raabe", clues: ["Morava em Jericó", "Escondi espiões israelitas", "Pendurei um cordão vermelho na janela", "Fui salva da destruição da cidade", "Entrei na linhagem do Messias"] },
            { name: "Neemias", clues: ["Era copeiro do rei da Pérsia", "Chorei ao saber de Jerusalém", "Voltei para reconstruir muros", "Enfrentei muita oposição", "Completei os muros em 52 dias"] },
            { name: "Esdras", clues: ["Era sacerdote e escriba", "Conhecia bem a Lei de Moisés", "Liderei um grupo de volta do exílio", "Ensinei a lei ao povo", "Fiquei chocado com casamentos mistos"] },
            { name: "Abraão", clues: ["Saí de Ur", "Recebi promessa de terra e descendência", "Tive um filho na velhice", "Sou pai de muitas nações", "Fui chamado amigo de Deus"] },
        ],

        characters: [],
        currentCharacter: { name: '', clues: [] },
        currentOptions: [],
        currentRoundIndex: 0,
        totalRounds: 10,
        revealedClues: 1,
        score: 0,
        streak: 0,
        maxStreak: 0,
        correctAnswers: 0,
        isPlaying: false,
        gameOver: false,
        answered: false,
        lastAnswerCorrect: false,
        lastPoints: 0,
        xpGained: 0,

        startGame() {
            this.isPlaying = true;
            this.characters = [...this.allCharacters].sort(() => Math.random() - 0.5).slice(0, this.totalRounds);
            this.currentRoundIndex = 0;
            this.score = 0;
            this.streak = 0;
            this.maxStreak = 0;
            this.correctAnswers = 0;
            this.gameOver = false;
            this.loadCharacter();
        },

        loadCharacter() {
            if (this.currentRoundIndex >= this.characters.length) {
                this.endGame();
                return;
            }
            
            this.currentCharacter = this.characters[this.currentRoundIndex];
            this.revealedClues = 1;
            this.answered = false;
            
            // Generate 4 options (1 correct + 3 random wrong)
            const wrongOptions = this.allCharacters
                .filter(c => c.name !== this.currentCharacter.name)
                .sort(() => Math.random() - 0.5)
                .slice(0, 3)
                .map(c => c.name);
            
            this.currentOptions = [this.currentCharacter.name, ...wrongOptions].sort(() => Math.random() - 0.5);
        },

        revealClue() {
            if (this.revealedClues < 5) {
                this.revealedClues++;
                if (navigator.vibrate) navigator.vibrate(30);
            }
        },

        submitAnswer(answer) {
            if (this.answered) return;
            this.answered = true;
            
            if (answer === this.currentCharacter.name) {
                // Points based on clues used
                const pointsTable = [100, 80, 60, 40, 20];
                this.lastPoints = pointsTable[this.revealedClues - 1] || 20;
                
                // Streak bonus
                this.streak++;
                if (this.streak > this.maxStreak) this.maxStreak = this.streak;
                if (this.streak >= 3) this.lastPoints = Math.floor(this.lastPoints * 1.5);
                
                this.score += this.lastPoints;
                this.correctAnswers++;
                this.lastAnswerCorrect = true;
                if (navigator.vibrate) navigator.vibrate([50, 30, 50]);
            } else {
                this.streak = 0;
                this.lastAnswerCorrect = false;
                if (navigator.vibrate) navigator.vibrate([100, 50, 100]);
            }
        },

        nextRound() {
            this.currentRoundIndex++;
            this.loadCharacter();
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
            @php
                $submitPath = parse_url(route('memberpanel.ebd.arcade.submit', ['game' => 'heroi-da-fe']), PHP_URL_PATH) ?: '/ebd/member/arcade/submit/heroi-da-fe';
            @endphp
            const url = "{{ $submitPath }}";
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        score: this.score,
                        metadata: {
                            correct_answers: this.correctAnswers,
                            max_streak: this.maxStreak,
                            duration: 0
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
