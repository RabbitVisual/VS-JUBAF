@extends('memberpanel::components.layouts.master')

@section('title', 'Parábolas em Ação - Arcade')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-slate-950 transition-colors duration-200 pb-12" x-data="parablesGame()">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 pt-4 sm:pt-6 space-y-4 sm:space-y-6">

        @include('ebd::memberpanel.arcade.partials.game-header', [
            'gameTitle' => 'Parábolas em Ação',
            'gameSubtitle' => 'Conecte cada parábola de Jesus com seu ensino correto.',
            'icon' => 'book-open',
            'accent' => 'purple',
        ])

    <!-- Game Area -->
    <div class="max-w-5xl mx-auto relative px-2 sm:px-4">

        <!-- Start Screen -->
        <div x-show="!isPlaying && !gameOver" class="text-center space-y-8 py-10">
            <div class="bg-white dark:bg-gray-900 rounded-2xl md:rounded-3xl p-8 md:p-12 shadow-2xl border border-gray-100 dark:border-gray-800">
                <div class="w-20 h-20 bg-purple-500 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-xl shadow-purple-500/30">
                    <x-icon name="book-open" style="duotone" class="w-10 h-10 text-white" />
                </div>
                <h2 class="text-2xl md:text-3xl font-black text-gray-900 dark:text-white mb-4">Parábolas em Ação</h2>
                <p class="text-gray-500 mb-8">Conecte cada parábola de Jesus com seu ensino correto! Teste seu conhecimento das histórias que Jesus contou.</p>
                
                <div class="grid grid-cols-3 gap-4 mb-8 max-w-md mx-auto text-center">
                    <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-2xl">
                        <span class="text-3xl font-black text-purple-600">40+</span>
                        <span class="block text-xs text-gray-500 mt-1">Parábolas</span>
                    </div>
                    <div class="bg-emerald-50 dark:bg-emerald-900/20 p-4 rounded-2xl">
                        <span class="text-3xl font-black text-emerald-600">10</span>
                        <span class="block text-xs text-gray-500 mt-1">Rodadas</span>
                    </div>
                    <div class="bg-amber-50 dark:bg-amber-900/20 p-4 rounded-2xl">
                        <span class="text-3xl font-black text-amber-600">2x</span>
                        <span class="block text-xs text-gray-500 mt-1">Combo</span>
                    </div>
                </div>
                
                <button @click="startGame()" class="w-full py-5 bg-purple-600 hover:bg-purple-500 text-white rounded-2xl font-black text-lg uppercase tracking-widest shadow-xl shadow-purple-600/30 transition-all hover:-translate-y-1 active:scale-95 touch-manipulation">
                    <x-icon name="play" style="solid" class="w-5 h-5 inline mr-2" /> Iniciar Jogo
                </button>
            </div>
        </div>

        <!-- Game Play -->
        <div x-show="isPlaying && !gameOver" x-transition class="space-y-6">
            
            <!-- Current Parable -->
            <div class="bg-white dark:bg-gray-900 rounded-2xl md:rounded-3xl p-6 md:p-10 shadow-2xl border border-gray-100 dark:border-gray-800 text-center">
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-purple-100 dark:bg-purple-900/30 text-purple-600 rounded-xl mb-4">
                    <x-icon name="scroll" style="duotone" class="w-4 h-4" />
                    <span class="text-xs font-black uppercase tracking-widest">Parábola</span>
                </div>
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-black text-gray-900 dark:text-white mb-2" x-text="currentParable.name">
                    O Bom Samaritano
                </h2>
                <p class="text-gray-500 text-sm" x-text="currentParable.reference">Lucas 10:25-37</p>
            </div>

            <!-- Instructions -->
            <div class="text-center">
                <p class="text-gray-500 text-sm"><x-icon name="hand-pointer" style="solid" class="w-4 h-4 inline mr-1" /> Selecione o ensino correto desta parábola</p>
            </div>

            <!-- Answer Options -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <template x-for="(option, index) in shuffledOptions" :key="index">
                    <button @click="selectAnswer(option)"
                            :disabled="answered"
                            class="p-5 md:p-6 bg-white dark:bg-gray-900 rounded-2xl border-2 text-left transition-all touch-manipulation active:scale-98"
                            :class="{
                                'border-gray-200 dark:border-gray-800 hover:border-purple-400 hover:shadow-lg': !answered,
                                'border-emerald-500 bg-emerald-50 dark:bg-emerald-900/30': answered && option.correct,
                                'border-red-500 bg-red-50 dark:bg-red-900/30': answered && selectedOption === option && !option.correct,
                                'opacity-50': answered && selectedOption !== option && !option.correct
                            }">
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 md:w-12 md:h-12 rounded-xl flex items-center justify-center flex-shrink-0 text-lg font-black"
                                 :class="{
                                     'bg-purple-100 dark:bg-purple-900/30 text-purple-600': !answered,
                                     'bg-emerald-500 text-white': answered && option.correct,
                                     'bg-red-500 text-white': answered && selectedOption === option && !option.correct
                                 }">
                                <span x-show="!answered" x-text="['A', 'B', 'C', 'D'][index]"></span>
                                <x-icon x-show="answered && option.correct" name="check" style="solid" class="w-5 h-5" />
                                <x-icon x-show="answered && selectedOption === option && !option.correct" name="xmark" style="solid" class="w-5 h-5" />
                            </div>
                            <div>
                                <p class="font-bold text-gray-900 dark:text-white text-sm md:text-base" x-text="option.teaching"></p>
                            </div>
                        </div>
                    </button>
                </template>
            </div>

            <!-- Explanation (shown after answer) -->
            <div x-show="answered" x-transition class="bg-violet-50 dark:bg-violet-900/20 rounded-2xl p-6 border border-violet-200 dark:border-violet-800">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 bg-violet-500 rounded-xl flex items-center justify-center flex-shrink-0">
                        <x-icon name="lightbulb" style="duotone" class="w-5 h-5 text-white" />
                    </div>
                    <div>
                        <h4 class="font-black text-violet-700 dark:text-violet-300 mb-1">Contexto</h4>
                        <p class="text-violet-600 dark:text-violet-400 text-sm" x-text="currentParable.explanation"></p>
                    </div>
                </div>
            </div>

            <!-- Next Button -->
            <div x-show="answered" x-transition class="text-center">
                <button @click="nextRound()" class="px-8 py-4 bg-purple-600 hover:bg-purple-500 text-white rounded-2xl font-black uppercase tracking-widest shadow-xl transition-all touch-manipulation active:scale-95">
                    <span x-text="currentRound < totalRounds ? 'Próxima Parábola' : 'Ver Resultado'"></span>
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
                <h2 class="text-3xl md:text-4xl font-black text-gray-900 dark:text-white">Parabéns!</h2>
                <p class="text-gray-500 mt-2">Você completou o desafio!</p>
            </div>
            <div class="text-5xl md:text-6xl font-black text-purple-600 tracking-tighter" x-text="score">0</div>
            
            <div class="grid grid-cols-3 gap-4 max-w-md mx-auto">
                <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-2xl">
                    <span class="block text-xs font-black text-gray-400 uppercase">Acertos</span>
                    <span class="text-2xl font-bold text-emerald-500" x-text="correctAnswers">0</span>
                </div>
                <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-2xl">
                    <span class="block text-xs font-black text-gray-400 uppercase">Maior Seq.</span>
                    <span class="text-2xl font-bold text-amber-500" x-text="maxStreak">0</span>
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
function parablesGame() {
    return {
        parables: [
            { name: "O Bom Samaritano", reference: "Lucas 10:25-37", teaching: "Amar ao próximo sem preconceitos, ajudando quem precisa", explanation: "Jesus ensinou que devemos amar a todos, independente de sua origem. O samaritano, desprezado pelos judeus, foi quem demonstrou verdadeiro amor ao próximo." },
            { name: "O Filho Pródigo", reference: "Lucas 15:11-32", teaching: "O amor incondicional do Pai e o arrependimento", explanation: "Esta parábola revela o coração de Deus que sempre está pronto a receber de volta aqueles que se arrependem, não importa quão longe tenham ido." },
            { name: "O Semeador", reference: "Mateus 13:1-23", teaching: "A importância de receber bem a Palavra de Deus", explanation: "Jesus compara a Palavra de Deus a sementes e nossos corações aos diferentes tipos de solo. Só um coração fértil produz fruto." },
            { name: "A Ovelha Perdida", reference: "Lucas 15:1-7", teaching: "O valor de cada alma para Deus", explanation: "Deus busca cada pessoa perdida com amor e persistência. Há grande alegria no céu quando um pecador se arrepende." },
            { name: "O Fariseu e o Publicano", reference: "Lucas 18:9-14", teaching: "A verdadeira humildade na oração", explanation: "Deus não aceita orações orgulhosas, mas recebe aqueles que reconhecem sua necessidade de perdão com humildade." },
            { name: "Os Talentos", reference: "Mateus 25:14-30", teaching: "Usar bem os dons que Deus nos deu", explanation: "Deus nos dá capacidades e recursos para multiplicarmos. Somos responsáveis por usar nossos dons para Seu reino." },
            { name: "As Dez Virgens", reference: "Mateus 25:1-13", teaching: "Estar sempre preparado para a volta de Cristo", explanation: "Jesus ensina sobre a importância de viver em constante prontidão espiritual, pois não sabemos quando Ele voltará." },
            { name: "O Rico Insensato", reference: "Lucas 12:13-21", teaching: "O perigo da ganância e do materialismo", explanation: "Acumular riquezas sem pensar em Deus e nos outros é tolice, pois a vida não consiste nas posses que temos." },
            { name: "O Grão de Mostarda", reference: "Mateus 13:31-32", teaching: "O crescimento surpreendente do Reino de Deus", explanation: "Mesmo começando pequeno, o Reino de Deus cresce além de todas as expectativas, abrigando muitos." },
            { name: "O Fermento", reference: "Mateus 13:33", teaching: "A influência transformadora do Evangelho", explanation: "Assim como o fermento transforma toda a massa, o Evangelho transforma completamente a sociedade e os corações." },
            { name: "A Pérola de Grande Valor", reference: "Mateus 13:45-46", teaching: "O Reino de Deus vale mais que tudo", explanation: "Vale a pena dar tudo o que temos para alcançar o Reino de Deus, pois nada se compara ao seu valor." },
            { name: "O Tesouro Escondido", reference: "Mateus 13:44", teaching: "A alegria de descobrir o Reino de Deus", explanation: "Encontrar o Reino de Deus traz tamanha alegria que a pessoa dá tudo para possuí-lo." },
            { name: "A Rede", reference: "Mateus 13:47-50", teaching: "O juízo final que separará justos e injustos", explanation: "No fim dos tempos, haverá separação entre os que aceitaram e os que rejeitaram a Deus." },
            { name: "O Joio e o Trigo", reference: "Mateus 13:24-30", teaching: "Bons e maus vivem juntos até o juízo", explanation: "Deus permite que justos e injustos coexistam neste mundo, mas no final haverá separação e juízo." },
            { name: "Os Dois Filhos", reference: "Mateus 21:28-32", teaching: "A obediência verdadeira é mais que palavras", explanation: "O que importa não é o que dizemos, mas o que fazemos. Arrependimento verdadeiro produz ação." },
            { name: "Os Lavradores Maus", reference: "Mateus 21:33-46", teaching: "A rejeição de Israel e aceitação dos gentios", explanation: "Os líderes religiosos rejeitaram os profetas e rejeitariam Jesus, mas o Reino seria dado a outros." },
            { name: "A Festa de Casamento", reference: "Mateus 22:1-14", teaching: "O convite da graça e a necessidade de responder", explanation: "Deus convida todos para Seu banquete, mas devemos responder adequadamente, vestidos de justiça." },
            { name: "O Servo Impiedoso", reference: "Mateus 18:21-35", teaching: "Devemos perdoar como fomos perdoados", explanation: "Quem recebeu o perdão de Deus deve perdoar os outros. A falta de perdão impede a bênção." },
            { name: "Os Trabalhadores da Vinha", reference: "Mateus 20:1-16", teaching: "A generosidade de Deus não segue mérito humano", explanation: "A graça de Deus é soberana e generosa, não baseada em quanto trabalhamos ou merecemos." },
            { name: "A Figueira Estéril", reference: "Lucas 13:6-9", teaching: "O tempo de arrependimento é limitado", explanation: "Deus é paciente, mas há um limite. Devemos produzir frutos de arrependimento enquanto há tempo." },
            { name: "O Amigo Importuno", reference: "Lucas 11:5-8", teaching: "A persistência na oração", explanation: "Devemos perseverar em oração, não por que Deus é relutante, mas porque a persistência fortalece nossa fé." },
            { name: "A Viúva e o Juiz", reference: "Lucas 18:1-8", teaching: "Orar sempre e não desanimar", explanation: "Se um juiz injusto atende por persistência, quanto mais Deus, que é justo e nos ama, responderá." },
            { name: "O Rico e Lázaro", reference: "Lucas 16:19-31", teaching: "As escolhas desta vida têm consequências eternas", explanation: "O destino eterno é determinado nesta vida. Devemos ouvir a Palavra de Deus enquanto há tempo." },
            { name: "A Dracma Perdida", reference: "Lucas 15:8-10", teaching: "Deus busca diligentemente cada pessoa perdida", explanation: "Assim como a mulher procura intensamente a moeda perdida, Deus busca cada alma com dedicação." },
            { name: "O Administrador Infiel", reference: "Lucas 16:1-13", teaching: "Usar recursos terrenos com sabedoria eterna", explanation: "Devemos usar nossos recursos materiais de forma que beneficie nosso destino eterno." },
            { name: "A Construção da Torre", reference: "Lucas 14:28-30", teaching: "Calcular o custo de seguir a Cristo", explanation: "Ser discípulo exige compromisso total. Devemos considerar o custo antes de nos comprometermos." },
            { name: "O Rei que Vai à Guerra", reference: "Lucas 14:31-33", teaching: "Considerar bem antes de fazer compromissos", explanation: "Assim como um rei avalia suas forças, devemos avaliar nosso compromisso antes de seguir Jesus." },
            { name: "A Casa sobre a Rocha", reference: "Mateus 7:24-27", teaching: "Obedecer a Palavra de Jesus dá fundamento firme", explanation: "Ouvir e praticar os ensinamentos de Jesus nos dá uma base sólida que resiste às tempestades da vida." },
            { name: "O Vinho Novo", reference: "Lucas 5:37-39", teaching: "O Evangelho requer renovação total", explanation: "A mensagem de Jesus não pode ser misturada com tradições antigas; requer transformação completa." },
            { name: "A Porta Estreita", reference: "Lucas 13:24-30", teaching: "Poucos encontram o caminho da salvação", explanation: "Entrar no Reino requer esforço e determinação. O caminho é estreito e nem todos entrarão." },
            { name: "Os Servos Vigilantes", reference: "Lucas 12:35-40", teaching: "Viver em constante expectativa da volta do Senhor", explanation: "Devemos viver cada dia como se Jesus fosse voltar hoje, sempre prontos e fiéis." },
            { name: "O Mordomo Fiel", reference: "Lucas 12:42-48", teaching: "Fidelidade na administração do que Deus nos confia", explanation: "A quem muito é dado, muito será exigido. Devemos ser fiéis administradores." },
            { name: "A Grande Ceia", reference: "Lucas 14:15-24", teaching: "Muitos rejeitam o convite de Deus por prioridades mundanas", explanation: "Desculpas terrenas nos impedem de aceitar o convite da graça. Os rejeitados dão lugar a outros." },
            { name: "O Filho Obediente", reference: "Lucas 15:25-32", teaching: "O perigo da religiosidade sem amor", explanation: "O irmão mais velho representa aqueles que servem a Deus por obrigação, sem entender Seu coração de amor." },
            { name: "Os Dois Devedores", reference: "Lucas 7:41-43", teaching: "Quem é mais perdoado, mais ama", explanation: "A consciência de quanto fomos perdoados determina a intensidade do nosso amor e gratidão a Deus." },
            { name: "A Ovelha e os Cabritos", reference: "Mateus 25:31-46", teaching: "Servir os necessitados é servir a Cristo", explanation: "Jesus se identifica com os necessitados. Como tratamos os pobres reflete como tratamos a Ele." },
            { name: "O Pai e os Peixes", reference: "Lucas 11:11-13", teaching: "Deus dá boas dádivas aos que pedem", explanation: "Se pais terrenos sabem dar boas dádivas, quanto mais o Pai celestial dará o Espírito aos que pedem." },
            { name: "A Candeia", reference: "Mateus 5:15-16", teaching: "Nossa luz deve brilhar para glorificar a Deus", explanation: "Não devemos esconder nossa fé, mas deixar que ela ilumine os outros e glorifique a Deus." },
            { name: "O Sal da Terra", reference: "Mateus 5:13", teaching: "Os cristãos devem preservar e dar sabor ao mundo", explanation: "Assim como o sal preserva e dá sabor, os cristãos devem influenciar positivamente a sociedade." },
            { name: "A Videira e os Ramos", reference: "João 15:1-8", teaching: "Permanecer em Cristo para produzir fruto", explanation: "Só podemos produzir fruto espiritual se permanecermos conectados a Jesus, a videira verdadeira." },
        ],

        currentParable: {},
        shuffledOptions: [],
        selectedOption: null,
        answered: false,
        currentRound: 1,
        totalRounds: 10,
        score: 0,
        streak: 0,
        maxStreak: 0,
        correctAnswers: 0,
        isPlaying: false,
        gameOver: false,
        xpGained: 0,
        usedParables: [],

        startGame() {
            this.isPlaying = true;
            this.currentRound = 1;
            this.score = 0;
            this.streak = 0;
            this.maxStreak = 0;
            this.correctAnswers = 0;
            this.gameOver = false;
            this.usedParables = [];
            this.loadRound();
        },

        loadRound() {
            this.answered = false;
            this.selectedOption = null;
            
            // Get unused parables
            let available = this.parables.filter((_, i) => !this.usedParables.includes(i));
            if (available.length < 4) {
                this.usedParables = [];
                available = [...this.parables];
            }
            
            // Select current parable
            const randomIndex = Math.floor(Math.random() * available.length);
            this.currentParable = available[randomIndex];
            this.usedParables.push(this.parables.indexOf(this.currentParable));
            
            // Create options (1 correct + 3 wrong)
            const wrongParables = this.parables
                .filter(p => p.name !== this.currentParable.name)
                .sort(() => Math.random() - 0.5)
                .slice(0, 3);
            
            this.shuffledOptions = [
                { teaching: this.currentParable.teaching, correct: true },
                ...wrongParables.map(p => ({ teaching: p.teaching, correct: false }))
            ].sort(() => Math.random() - 0.5);
        },

        selectAnswer(option) {
            if (this.answered) return;
            
            this.answered = true;
            this.selectedOption = option;
            
            if (option.correct) {
                this.streak++;
                if (this.streak > this.maxStreak) this.maxStreak = this.streak;
                this.correctAnswers++;
                
                const multiplier = Math.min(this.streak, 5);
                this.score += 10 * multiplier;
                
                if (navigator.vibrate) navigator.vibrate([50, 30, 50]);
            } else {
                this.streak = 0;
                if (navigator.vibrate) navigator.vibrate([100, 50, 100]);
            }
        },

        nextRound() {
            if (this.currentRound >= this.totalRounds) {
                this.endGame();
            } else {
                this.currentRound++;
                this.loadRound();
            }
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
                const response = await fetch('{{ route("memberpanel.ebd.arcade.submit", "parabolas") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        score: this.score,
                        metadata: {
                            correct_answers: this.correctAnswers,
                            max_streak: this.maxStreak
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
