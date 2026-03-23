@extends('memberpanel::components.layouts.master')

@section('title', 'Linha do Tempo - Arcade')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-slate-950 transition-colors duration-200 pb-12" x-data="timelineGame()">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 pt-4 sm:pt-6 space-y-4 sm:space-y-6">

        @include('ebd::memberpanel.arcade.partials.game-header', [
            'gameTitle' => 'Linha do Tempo',
            'gameSubtitle' => 'Organize os eventos bíblicos na ordem cronológica correta.',
            'icon' => 'hourglass-start',
            'accent' => 'amber',
        ])

    <!-- Game Area -->
    <div class="max-w-4xl mx-auto min-h-[450px] md:min-h-[500px] relative px-2 sm:px-4">

        <!-- Start Screen -->
        <div x-show="!isPlaying && !gameOver" class="text-center space-y-8 py-10">
            <div class="bg-white dark:bg-gray-900 rounded-2xl md:rounded-3xl p-8 md:p-12 shadow-2xl border border-gray-100 dark:border-gray-800">
                <div class="w-20 h-20 bg-orange-500 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-xl shadow-orange-500/30">
                    <x-icon name="hourglass-start" style="duotone" class="w-10 h-10 text-white" />
                </div>
                <h2 class="text-2xl md:text-3xl font-black text-gray-900 dark:text-white mb-4">Linha do Tempo Bíblica</h2>
                <p class="text-gray-500 mb-8">Organize os eventos bíblicos na ordem cronológica correta. Arraste ou use os botões para reordenar!</p>
                
                <div class="grid grid-cols-2 gap-4 mb-8 max-w-xs mx-auto">
                    <div class="bg-orange-50 dark:bg-orange-900/20 p-4 rounded-2xl">
                        <span class="text-3xl font-black text-orange-600">10</span>
                        <span class="block text-xs text-gray-500 mt-1">Níveis</span>
                    </div>
                    <div class="bg-amber-50 dark:bg-amber-900/20 p-4 rounded-2xl">
                        <span class="text-3xl font-black text-amber-600">50</span>
                        <span class="block text-xs text-gray-500 mt-1">Pts/Nível</span>
                    </div>
                </div>
                
                <button @click="startGame()" class="w-full py-5 bg-orange-600 hover:bg-orange-500 text-white rounded-2xl font-black text-lg uppercase tracking-widest shadow-xl shadow-orange-600/30 transition-all hover:-translate-y-1 active:scale-95 touch-manipulation">
                    <x-icon name="play" style="solid" class="w-5 h-5 inline mr-2" /> Iniciar Jogo
                </button>
            </div>
        </div>

        <div x-show="isPlaying && !gameOver" x-transition class="space-y-6">
            <!-- Level Title -->
            <div class="text-center">
                <span class="px-4 py-2 bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-300 rounded-full text-xs font-black uppercase tracking-widest" x-text="currentLevelTitle">Era dos Patriarcas</span>
                <p class="text-gray-500 font-bold text-sm mt-2">Ordene do mais antigo (topo) ao mais recente (base)</p>
            </div>

            <!-- Events List with Timeline -->
            <div class="relative">
                <!-- Timeline Line -->
                <div class="absolute left-6 sm:left-8 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 via-amber-500 to-orange-500 rounded-full hidden sm:block"></div>

                <div class="space-y-3 sm:space-y-4" id="sortable-list">
                    <template x-for="(event, index) in events" :key="event.id">
                        <div class="relative flex items-center gap-2 sm:gap-4 group"
                             :data-index="index">
                            
                            <!-- Timeline Dot (hidden on mobile) -->
                            <div class="hidden sm:flex w-12 sm:w-16 shrink-0 items-center justify-center relative z-10">
                                <div class="w-4 h-4 rounded-full transition-all"
                                     :class="checked && event.correctPosition ? 'bg-emerald-500 shadow-lg shadow-emerald-500/50' : (checked && !event.correctPosition ? 'bg-red-500 shadow-lg shadow-red-500/50' : 'bg-orange-500 shadow-lg shadow-orange-500/50')"></div>
                            </div>
                            
                            <!-- Event Card -->
                            <div class="flex-1 bg-white dark:bg-gray-900 p-4 sm:p-5 md:p-6 rounded-xl md:rounded-2xl border-2 shadow-lg flex items-center gap-3 sm:gap-4 transition-all touch-manipulation select-none"
                                 :class="{
                                     'border-gray-100 dark:border-gray-800 cursor-grab active:cursor-grabbing hover:border-orange-500 hover:shadow-xl': !checked,
                                     'border-emerald-500 bg-emerald-50 dark:bg-emerald-900/20': checked && event.correctPosition,
                                     'border-red-500 bg-red-50 dark:bg-red-900/20': checked && !event.correctPosition,
                                     'opacity-50 scale-95': draggingIndex === index
                                 }"
                                 draggable="true"
                                 @dragstart="dragStart($event, index)"
                                 @dragover.prevent="dragOver($event, index)"
                                 @drop="drop($event, index)"
                                 @dragend="dragEnd"
                                 @touchstart.passive="touchStart($event, index)"
                                 @touchmove.prevent="touchMove($event)"
                                 @touchend="touchEnd($event)">

                                <div class="text-2xl sm:text-3xl font-black w-8 sm:w-10 text-center shrink-0"
                                     :class="checked && event.correctPosition ? 'text-emerald-500' : (checked && !event.correctPosition ? 'text-red-500' : 'text-gray-200 dark:text-gray-700')" 
                                     x-text="index + 1"></div>
                                
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white truncate" x-text="event.text"></h3>
                                    <p class="text-xs text-gray-500 hidden sm:block" x-text="event.description || ''"></p>
                                </div>

                                <!-- Reorder Buttons (Mobile-friendly) -->
                                <div class="flex flex-col gap-1 shrink-0" x-show="!checked">
                                    <button @click.stop="moveUp(index)" 
                                            :disabled="index === 0"
                                            class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-500 hover:bg-orange-100 hover:text-orange-600 transition-colors disabled:opacity-30 touch-manipulation">
                                        <x-icon name="chevron-up" style="solid" class="w-4 h-4" />
                                    </button>
                                    <button @click.stop="moveDown(index)" 
                                            :disabled="index === events.length - 1"
                                            class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-500 hover:bg-orange-100 hover:text-orange-600 transition-colors disabled:opacity-30 touch-manipulation">
                                        <x-icon name="chevron-down" style="solid" class="w-4 h-4" />
                                    </button>
                                </div>

                                <!-- Result Icons -->
                                <div class="shrink-0" x-show="checked">
                                    <x-icon name="check-circle" style="solid" class="w-6 h-6 text-emerald-500" x-show="event.correctPosition" />
                                    <x-icon name="xmark-circle" style="solid" class="w-6 h-6 text-red-500" x-show="!event.correctPosition" />
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-center gap-4 pt-4">
                <button @click="checkOrder()" x-show="!checked" class="px-6 sm:px-8 py-3 sm:py-4 bg-orange-600 hover:bg-orange-500 text-white rounded-xl font-bold shadow-lg shadow-orange-500/30 transition-all flex items-center gap-2 touch-manipulation active:scale-95">
                    <x-icon name="check" style="solid" class="w-5 h-5" /> Verificar Ordem
                </button>
                <button @click="nextLevel()" x-show="checked" class="px-6 sm:px-8 py-3 sm:py-4 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl font-bold shadow-lg shadow-indigo-500/30 transition-all flex items-center gap-2 touch-manipulation active:scale-95 animate-pulse">
                    <span x-text="currentLevelIndex >= levels.length - 1 ? 'Ver Resultado' : 'Próximo Nível'"></span>
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
                <p class="text-gray-500 mt-2">Você completou todos os níveis!</p>
            </div>
            <div class="text-5xl md:text-6xl font-black text-orange-600 tracking-tighter" x-text="score">0</div>
            
            <div class="grid grid-cols-2 gap-4 max-w-sm mx-auto">
                <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-2xl">
                    <span class="block text-xs font-black text-gray-400 uppercase">Perfeitos</span>
                    <span class="text-2xl font-bold text-emerald-500" x-text="perfectLevels">0</span>
                </div>
                <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-2xl">
                    <span class="block text-xs font-black text-gray-400 uppercase">XP Ganho</span>
                    <span class="text-2xl font-bold text-orange-500">+<span x-text="xpGained">0</span></span>
                </div>
            </div>
            
            <div class="flex flex-col sm:flex-row justify-center gap-3 md:gap-4 pt-4">
                <button @click="restartGame()" class="px-6 md:px-8 py-3 md:py-4 bg-orange-600 hover:bg-orange-500 text-white rounded-xl font-bold transition-all touch-manipulation active:scale-95">
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
function timelineGame() {
    return {
        levels: [],
        currentLevelIndex: 0,
        currentLevelTitle: '',
        events: [],
        draggingIndex: null,
        checked: false,
        score: 0,
        perfectLevels: 0,
        isPlaying: false,
        gameOver: false,
        xpGained: 0,
        touchStartY: 0,
        touchCurrentIndex: null,
        touchClone: null,

        startGame() {
            this.isPlaying = true;
            this.init();
        },

        init() {
            this.levels = this.generateLevels();
            this.currentLevelIndex = 0;
            this.score = 0;
            this.perfectLevels = 0;
            this.gameOver = false;
            this.loadLevel();
        },

        generateLevels() {
            return [
                {
                    title: 'Era dos Patriarcas',
                    items: [
                        { id: 1, text: "Criação do Mundo", description: "Deus cria céus e terra", order: 1 },
                        { id: 2, text: "Queda de Adão e Eva", description: "Pecado original", order: 2 },
                        { id: 3, text: "Dilúvio de Noé", description: "Juízo sobre a terra", order: 3 },
                        { id: 4, text: "Torre de Babel", description: "Confusão de línguas", order: 4 },
                        { id: 5, text: "Chamado de Abraão", description: "Sai de Ur", order: 5 }
                    ]
                },
                {
                    title: 'Abraão até José',
                    items: [
                        { id: 1, text: "Nascimento de Isaque", description: "Filho da promessa", order: 1 },
                        { id: 2, text: "Sacrifício de Isaque", description: "Provação de Abraão", order: 2 },
                        { id: 3, text: "Jacó engana Esaú", description: "Primogenitura vendida", order: 3 },
                        { id: 4, text: "José vendido como escravo", description: "Pelos irmãos", order: 4 },
                        { id: 5, text: "José governa o Egito", description: "Segundo do Faraó", order: 5 }
                    ]
                },
                {
                    title: 'Êxodo e Conquista',
                    items: [
                        { id: 1, text: "Nascimento de Moisés", description: "Salvo das águas", order: 1 },
                        { id: 2, text: "Pragas do Egito", description: "Dez pragas", order: 2 },
                        { id: 3, text: "Abertura do Mar Vermelho", description: "Libertação de Israel", order: 3 },
                        { id: 4, text: "Dez Mandamentos no Sinai", description: "A Lei de Deus", order: 4 },
                        { id: 5, text: "Queda de Jericó", description: "Muralhas caem", order: 5 }
                    ]
                },
                {
                    title: 'Era dos Juízes',
                    items: [
                        { id: 1, text: "Débora julga Israel", description: "Profetisa e juíza", order: 1 },
                        { id: 2, text: "Gideão e os 300", description: "Vitória improvável", order: 2 },
                        { id: 3, text: "Força de Sansão", description: "Nazireu de Deus", order: 3 },
                        { id: 4, text: "Rute e Noemi", description: "História de fidelidade", order: 4 },
                        { id: 5, text: "Samuel é chamado", description: "Profeta desde menino", order: 5 }
                    ]
                },
                {
                    title: 'Reino Unido de Israel',
                    items: [
                        { id: 1, text: "Saul se torna rei", description: "Primeiro rei de Israel", order: 1 },
                        { id: 2, text: "Davi mata Golias", description: "Gigante derrotado", order: 2 },
                        { id: 3, text: "Davi se torna rei", description: "Pastor vira rei", order: 3 },
                        { id: 4, text: "Salomão constrói o Templo", description: "Casa de Deus", order: 4 },
                        { id: 5, text: "Divisão do Reino", description: "Israel e Judá", order: 5 }
                    ]
                },
                {
                    title: 'Profetas e Exílio',
                    items: [
                        { id: 1, text: "Elias no Monte Carmelo", description: "Fogo do céu", order: 1 },
                        { id: 2, text: "Eliseu sucede Elias", description: "Porção dobrada", order: 2 },
                        { id: 3, text: "Jonas em Nínive", description: "Após o grande peixe", order: 3 },
                        { id: 4, text: "Queda de Jerusalém", description: "Destruição babilônica", order: 4 },
                        { id: 5, text: "Daniel na Babilônia", description: "Exílio e fidelidade", order: 5 }
                    ]
                },
                {
                    title: 'Retorno e Silêncio',
                    items: [
                        { id: 1, text: "Ciro liberta os judeus", description: "Decreto de retorno", order: 1 },
                        { id: 2, text: "Reconstrução do Templo", description: "Segundo templo", order: 2 },
                        { id: 3, text: "Esdras ensina a Lei", description: "Reforma espiritual", order: 3 },
                        { id: 4, text: "Neemias reconstrói muros", description: "52 dias", order: 4 },
                        { id: 5, text: "Ester salva seu povo", description: "Rainha corajosa", order: 5 }
                    ]
                },
                {
                    title: 'Vida de Jesus - Início',
                    items: [
                        { id: 1, text: "Anunciação a Maria", description: "Anjo Gabriel", order: 1 },
                        { id: 2, text: "Nascimento de Jesus", description: "Em Belém", order: 2 },
                        { id: 3, text: "Visita dos Magos", description: "Ouro, incenso, mirra", order: 3 },
                        { id: 4, text: "Fuga para o Egito", description: "Herodes persegue", order: 4 },
                        { id: 5, text: "Jesus aos 12 no Templo", description: "Ensinando doutores", order: 5 }
                    ]
                },
                {
                    title: 'Ministério de Jesus',
                    items: [
                        { id: 1, text: "Batismo de Jesus", description: "Por João Batista", order: 1 },
                        { id: 2, text: "Tentação no deserto", description: "40 dias", order: 2 },
                        { id: 3, text: "Primeiro milagre em Caná", description: "Água em vinho", order: 3 },
                        { id: 4, text: "Sermão da Montanha", description: "Bem-aventuranças", order: 4 },
                        { id: 5, text: "Transfiguração", description: "Glória revelada", order: 5 }
                    ]
                },
                {
                    title: 'Paixão e Igreja',
                    items: [
                        { id: 1, text: "Entrada triunfal", description: "Domingo de Ramos", order: 1 },
                        { id: 2, text: "Última Ceia", description: "Santa Ceia instituída", order: 2 },
                        { id: 3, text: "Crucificação", description: "Na sexta-feira", order: 3 },
                        { id: 4, text: "Ressurreição", description: "Ao terceiro dia", order: 4 },
                        { id: 5, text: "Pentecostes", description: "Descida do Espírito", order: 5 }
                    ]
                }
            ];
        },

        loadLevel() {
            if (this.currentLevelIndex >= this.levels.length) {
                this.endGame();
                return;
            }
            const level = this.levels[this.currentLevelIndex];
            this.currentLevelTitle = level.title;
            this.events = [...level.items].sort(() => Math.random() - 0.5);
            this.checked = false;
        },

        // Desktop Drag & Drop
        dragStart(e, index) {
            this.draggingIndex = index;
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/plain', index);
        },

        dragOver(e, index) {
            e.preventDefault();
            if (this.draggingIndex !== null && this.draggingIndex !== index) {
                this.moveItem(this.draggingIndex, index);
                this.draggingIndex = index;
            }
        },

        drop(e, index) {
            e.preventDefault();
        },

        dragEnd() {
            this.draggingIndex = null;
        },

        // Touch Support
        touchStart(e, index) {
            this.touchCurrentIndex = index;
            this.touchStartY = e.touches[0].clientY;
            this.draggingIndex = index;
            
            if (navigator.vibrate) navigator.vibrate(30);
        },

        touchMove(e) {
            if (this.touchCurrentIndex === null) return;
            
            const touch = e.touches[0];
            const elements = document.querySelectorAll('[data-index]');
            
            for (let el of elements) {
                const rect = el.getBoundingClientRect();
                if (touch.clientY >= rect.top && touch.clientY <= rect.bottom) {
                    const newIndex = parseInt(el.getAttribute('data-index'));
                    if (newIndex !== this.touchCurrentIndex) {
                        this.moveItem(this.touchCurrentIndex, newIndex);
                        this.touchCurrentIndex = newIndex;
                        this.draggingIndex = newIndex;
                        if (navigator.vibrate) navigator.vibrate(20);
                    }
                    break;
                }
            }
        },

        touchEnd(e) {
            this.touchCurrentIndex = null;
            this.draggingIndex = null;
        },

        // Button-based reordering (most reliable on mobile)
        moveUp(index) {
            if (index > 0) {
                this.moveItem(index, index - 1);
                if (navigator.vibrate) navigator.vibrate(30);
            }
        },

        moveDown(index) {
            if (index < this.events.length - 1) {
                this.moveItem(index, index + 1);
                if (navigator.vibrate) navigator.vibrate(30);
            }
        },

        moveItem(from, to) {
            const item = this.events.splice(from, 1)[0];
            this.events.splice(to, 0, item);
        },

        checkOrder() {
            this.checked = true;
            let correctCount = 0;

            this.events.forEach((event, index) => {
                if (event.order === (index + 1)) {
                    event.correctPosition = true;
                    correctCount++;
                } else {
                    event.correctPosition = false;
                }
            });

            if (correctCount === this.events.length) {
                this.score += 50;
                this.perfectLevels++;
                if (navigator.vibrate) navigator.vibrate([50, 30, 50, 30, 50]);
            } else {
                this.score += (correctCount * 5);
                if (navigator.vibrate) navigator.vibrate(100);
            }
        },

        nextLevel() {
            this.currentLevelIndex++;
            this.loadLevel();
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
                const response = await fetch('{{ route("memberpanel.ebd.arcade.submit", "linha-do-tempo") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        score: this.score,
                        metadata: {
                            perfect_levels: this.perfectLevels
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
