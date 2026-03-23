@extends('memberpanel::components.layouts.master')

@section('title', 'Palavras Cruzadas - Arcade')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-slate-950 transition-colors duration-200 pb-12" x-data="crosswordGame()">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 pt-4 sm:pt-6 space-y-4 sm:space-y-6">

        @include('ebd::memberpanel.arcade.partials.game-header', [
            'gameTitle' => 'Palavras Cruzadas',
            'gameSubtitle' => 'Complete o grid com termos bíblicos.',
            'icon' => 'grid-2',
            'accent' => 'teal',
        ])

    <!-- Game Area -->
    <div class="max-w-5xl mx-auto relative px-2 sm:px-4">

        <!-- Start Screen -->
        <div x-show="!isPlaying && !gameOver" class="text-center space-y-8 py-10">
            <div class="bg-white dark:bg-gray-900 rounded-2xl md:rounded-3xl p-8 md:p-12 shadow-2xl border border-gray-100 dark:border-gray-800">
                <div class="w-20 h-20 bg-cyan-500 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-xl shadow-cyan-500/30">
                    <x-icon name="grid-2" style="duotone" class="w-10 h-10 text-white" />
                </div>
                <h2 class="text-2xl md:text-3xl font-black text-gray-900 dark:text-white mb-4">Palavras Cruzadas Bíblicas</h2>
                <p class="text-gray-500 mb-8">Complete as palavras cruzadas com termos e nomes bíblicos. Toque em uma dica para selecionar e digite a resposta!</p>
                
                <div class="grid grid-cols-2 gap-4 mb-8 max-w-xs mx-auto text-center">
                    <div class="bg-cyan-50 dark:bg-cyan-900/20 p-4 rounded-2xl">
                        <span class="text-3xl font-black text-cyan-600">20</span>
                        <span class="block text-xs text-gray-500 mt-1">Puzzles</span>
                    </div>
                    <div class="bg-emerald-50 dark:bg-emerald-900/20 p-4 rounded-2xl">
                        <span class="text-3xl font-black text-emerald-600">10</span>
                        <span class="block text-xs text-gray-500 mt-1">Pts/Palavra</span>
                    </div>
                </div>
                
                <button @click="startGame()" class="w-full py-5 bg-cyan-600 hover:bg-cyan-500 text-white rounded-2xl font-black text-lg uppercase tracking-widest shadow-xl shadow-cyan-600/30 transition-all hover:-translate-y-1 active:scale-95 touch-manipulation">
                    <x-icon name="play" style="solid" class="w-5 h-5 inline mr-2" /> Iniciar Jogo
                </button>
            </div>
        </div>

        <div x-show="isPlaying && !gameOver" x-transition class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Crossword Grid -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-900 rounded-2xl md:rounded-3xl p-4 sm:p-6 shadow-2xl border border-gray-100 dark:border-gray-800 overflow-x-auto">
                    <div class="grid gap-0.5 min-w-[300px]" :style="'grid-template-columns: repeat(' + gridSize + ', minmax(28px, 1fr));'">
                        <template x-for="(row, rowIdx) in grid" :key="'row-' + rowIdx">
                            <template x-for="(cell, colIdx) in row" :key="'cell-' + rowIdx + '-' + colIdx">
                                <div class="aspect-square flex items-center justify-center text-sm sm:text-base font-black uppercase rounded transition-all"
                                     :class="{
                                         'bg-gray-900 dark:bg-gray-950': cell.blocked,
                                         'bg-cyan-100 dark:bg-cyan-900/30 border-2 border-cyan-500': cell.selected && !cell.blocked,
                                         'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600': cell.correct && !cell.blocked,
                                         'bg-gray-100 dark:bg-gray-800 hover:bg-cyan-50 dark:hover:bg-cyan-900/20 cursor-pointer': !cell.blocked && !cell.selected && !cell.correct
                                     }"
                                     @click="!cell.blocked && selectCell(rowIdx, colIdx)">
                                    <span x-show="cell.number" class="absolute text-[8px] text-gray-400 -mt-4 -ml-4" x-text="cell.number"></span>
                                    <span x-text="cell.userLetter || ''" class="text-gray-800 dark:text-gray-200"></span>
                                </div>
                            </template>
                        </template>
                    </div>
                </div>

                <!-- Mobile Keyboard -->
                <div class="mt-4 bg-gray-100 dark:bg-gray-800 rounded-2xl p-3 sm:p-4">
                    <div class="grid grid-cols-10 gap-1">
                        <template x-for="letter in 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.split('')" :key="letter">
                            <button @click="typeLetter(letter)"
                                    class="aspect-square rounded-lg bg-white dark:bg-gray-900 font-bold text-sm sm:text-base text-gray-700 dark:text-gray-300 hover:bg-cyan-100 dark:hover:bg-cyan-900/30 transition-colors touch-manipulation active:scale-95">
                                <span x-text="letter"></span>
                            </button>
                        </template>
                        <button @click="deleteLetter()" class="col-span-2 aspect-auto py-2 rounded-lg bg-red-100 dark:bg-red-900/30 text-red-600 font-bold touch-manipulation active:scale-95">
                            <x-icon name="delete-left" style="solid" class="w-5 h-5 mx-auto" />
                        </button>
                    </div>
                </div>
            </div>

            <!-- Clues -->
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-900 rounded-2xl md:rounded-3xl p-6 shadow-2xl border border-gray-100 dark:border-gray-800 sticky top-4 max-h-[70vh] overflow-y-auto">
                    <!-- Horizontal Clues -->
                    <div class="mb-6">
                        <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                            <x-icon name="arrow-right" style="solid" class="w-3 h-3" /> Horizontal
                        </h3>
                        <div class="space-y-2">
                            <template x-for="(clue, hIndex) in (horizontalClues || [])" :key="'h-' + hIndex">
                                <button @click="selectClue(clue, 'horizontal')"
                                        class="w-full text-left p-3 rounded-xl transition-all text-sm touch-manipulation"
                                        :class="{
                                            'bg-cyan-100 dark:bg-cyan-900/30 border-2 border-cyan-500': selectedClue && selectedClue.number === clue.number && selectedDirection === 'horizontal',
                                            'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 line-through': clue.solved,
                                            'bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700': !clue.solved && !(selectedClue && selectedClue.number === clue.number && selectedDirection === 'horizontal')
                                        }">
                                    <span class="font-black mr-2" x-text="clue.number + '.'"></span>
                                    <span x-text="clue.hint"></span>
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- Vertical Clues -->
                    <div>
                        <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                            <x-icon name="arrow-down" style="solid" class="w-3 h-3" /> Vertical
                        </h3>
                        <div class="space-y-2">
                            <template x-for="(clue, vIndex) in (verticalClues || [])" :key="'v-' + vIndex">
                                <button @click="selectClue(clue, 'vertical')"
                                        class="w-full text-left p-3 rounded-xl transition-all text-sm touch-manipulation"
                                        :class="{
                                            'bg-cyan-100 dark:bg-cyan-900/30 border-2 border-cyan-500': selectedClue && selectedClue.number === clue.number && selectedDirection === 'vertical',
                                            'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 line-through': clue.solved,
                                            'bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700': !clue.solved && !(selectedClue && selectedClue.number === clue.number && selectedDirection === 'vertical')
                                        }">
                                    <span class="font-black mr-2" x-text="clue.number + '.'"></span>
                                    <span x-text="clue.hint"></span>
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- Check & Next Buttons -->
                    <div class="mt-6 space-y-3">
                        <button @click="checkPuzzle()" class="w-full py-3 bg-cyan-600 hover:bg-cyan-500 text-white rounded-xl font-bold transition-all touch-manipulation active:scale-95">
                            <x-icon name="check" style="solid" class="w-4 h-4 inline mr-2" /> Verificar
                        </button>
                        <button @click="nextPuzzle()" x-show="puzzleComplete" class="w-full py-3 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl font-bold transition-all touch-manipulation active:scale-95 animate-pulse">
                            Próximo Puzzle <x-icon name="arrow-right" style="solid" class="w-4 h-4 inline ml-2" />
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
                <h2 class="text-3xl md:text-4xl font-black text-gray-900 dark:text-white">Parabéns!</h2>
                <p class="text-gray-500 mt-2">Você completou todos os puzzles!</p>
            </div>
            <div class="text-5xl md:text-6xl font-black text-cyan-600 tracking-tighter" x-text="score">0</div>
            
            <div class="grid grid-cols-2 gap-4 max-w-sm mx-auto">
                <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-2xl">
                    <span class="block text-xs font-black text-gray-400 uppercase">Puzzles</span>
                    <span class="text-2xl font-bold text-emerald-500" x-text="puzzlesCompleted">0</span>
                </div>
                <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-2xl">
                    <span class="block text-xs font-black text-gray-400 uppercase">XP Ganho</span>
                    <span class="text-2xl font-bold text-cyan-500">+<span x-text="xpGained">0</span></span>
                </div>
            </div>
            
            <div class="flex flex-col sm:flex-row justify-center gap-3 md:gap-4 pt-4">
                <button @click="restartGame()" class="px-6 md:px-8 py-3 md:py-4 bg-cyan-600 hover:bg-cyan-500 text-white rounded-xl font-bold transition-all touch-manipulation active:scale-95">
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
function crosswordGame() {
    return {
        puzzles: [
            {
                gridSize: 10,
                words: [
                    { word: "JESUS", hint: "O Filho de Deus", row: 0, col: 2, direction: "horizontal" },
                    { word: "AMOR", hint: "O maior mandamento", row: 2, col: 0, direction: "horizontal" },
                    { word: "CRUZ", hint: "Símbolo da salvação", row: 4, col: 3, direction: "horizontal" },
                    { word: "FE", hint: "Certeza das coisas que se esperam", row: 0, col: 2, direction: "vertical" },
                    { word: "SALMO", hint: "Cântico de louvor", row: 1, col: 4, direction: "vertical" },
                ]
            },
            {
                gridSize: 10,
                words: [
                    { word: "DAVI", hint: "Rei pastor de Israel", row: 0, col: 0, direction: "horizontal" },
                    { word: "MOISES", hint: "Libertador do Egito", row: 2, col: 1, direction: "horizontal" },
                    { word: "NOE", hint: "Construtor da arca", row: 4, col: 2, direction: "horizontal" },
                    { word: "DANIEL", hint: "Profeta na Babilônia", row: 0, col: 0, direction: "vertical" },
                    { word: "JONAS", hint: "Engolido pelo peixe", row: 1, col: 3, direction: "vertical" },
                ]
            },
            {
                gridSize: 10,
                words: [
                    { word: "GENESIS", hint: "Primeiro livro da Bíblia", row: 0, col: 0, direction: "horizontal" },
                    { word: "SALMOS", hint: "Livro de cânticos", row: 2, col: 1, direction: "horizontal" },
                    { word: "ATOS", hint: "História da igreja primitiva", row: 4, col: 3, direction: "horizontal" },
                    { word: "EXODO", hint: "Saída do Egito", row: 0, col: 0, direction: "vertical" },
                    { word: "LUCAS", hint: "Evangelista médico", row: 1, col: 4, direction: "vertical" },
                ]
            },
            {
                gridSize: 10,
                words: [
                    { word: "GRACA", hint: "Favor imerecido de Deus", row: 0, col: 1, direction: "horizontal" },
                    { word: "PAZ", hint: "Fruto do Espírito", row: 2, col: 0, direction: "horizontal" },
                    { word: "ALEGRIA", hint: "Gozo interior", row: 4, col: 0, direction: "horizontal" },
                    { word: "GLORIA", hint: "Honra a Deus", row: 0, col: 1, direction: "vertical" },
                    { word: "PERDAO", hint: "Remissão dos pecados", row: 1, col: 3, direction: "vertical" },
                ]
            },
            {
                gridSize: 10,
                words: [
                    { word: "PEDRO", hint: "Apóstolo pescador", row: 0, col: 0, direction: "horizontal" },
                    { word: "PAULO", hint: "Apóstolo dos gentios", row: 2, col: 1, direction: "horizontal" },
                    { word: "TIAGO", hint: "Irmão de João", row: 4, col: 2, direction: "horizontal" },
                    { word: "JOAO", hint: "Discípulo amado", row: 0, col: 0, direction: "vertical" },
                    { word: "ANDRE", hint: "Irmão de Pedro", row: 1, col: 4, direction: "vertical" },
                ]
            },
            {
                gridSize: 10,
                words: [
                    { word: "BELEM", hint: "Cidade do nascimento de Jesus", row: 0, col: 0, direction: "horizontal" },
                    { word: "NAZARE", hint: "Cidade de criação de Jesus", row: 2, col: 1, direction: "horizontal" },
                    { word: "SINAI", hint: "Monte dos mandamentos", row: 4, col: 2, direction: "horizontal" },
                    { word: "EDEN", hint: "Jardim do paraíso", row: 0, col: 0, direction: "vertical" },
                    { word: "EGITO", hint: "Terra da escravidão", row: 1, col: 3, direction: "vertical" },
                ]
            },
            {
                gridSize: 10,
                words: [
                    { word: "CORDEIRO", hint: "Jesus como sacrifício", row: 0, col: 0, direction: "horizontal" },
                    { word: "PASTOR", hint: "Jesus cuida das ovelhas", row: 2, col: 1, direction: "horizontal" },
                    { word: "REI", hint: "Jesus governa", row: 4, col: 3, direction: "horizontal" },
                    { word: "CAMINHO", hint: "Jesus é o único", row: 0, col: 0, direction: "vertical" },
                    { word: "LUZ", hint: "Jesus ilumina", row: 1, col: 4, direction: "vertical" },
                ]
            },
            {
                gridSize: 10,
                words: [
                    { word: "BATISMO", hint: "Sacramento de iniciação", row: 0, col: 0, direction: "horizontal" },
                    { word: "CEIA", hint: "Memorial da cruz", row: 2, col: 2, direction: "horizontal" },
                    { word: "ORACAO", hint: "Comunicação com Deus", row: 4, col: 1, direction: "horizontal" },
                    { word: "BIBLIA", hint: "Palavra de Deus", row: 0, col: 0, direction: "vertical" },
                    { word: "JEJUM", hint: "Abstinência espiritual", row: 1, col: 4, direction: "vertical" },
                ]
            },
            {
                gridSize: 10,
                words: [
                    { word: "ABRAAO", hint: "Pai da fé", row: 0, col: 0, direction: "horizontal" },
                    { word: "ISAQUE", hint: "Filho da promessa", row: 2, col: 1, direction: "horizontal" },
                    { word: "JACO", hint: "Lutou com o anjo", row: 4, col: 2, direction: "horizontal" },
                    { word: "JOSE", hint: "Vendido pelos irmãos", row: 0, col: 0, direction: "vertical" },
                    { word: "ESAU", hint: "Vendeu a primogenitura", row: 1, col: 4, direction: "vertical" },
                ]
            },
            {
                gridSize: 10,
                words: [
                    { word: "CRIACAO", hint: "Obra de Deus em 6 dias", row: 0, col: 0, direction: "horizontal" },
                    { word: "DILUVIO", hint: "Juízo com água", row: 2, col: 1, direction: "horizontal" },
                    { word: "EXODO", hint: "Saída do Egito", row: 4, col: 2, direction: "horizontal" },
                    { word: "PASCOA", hint: "Libertação de Israel", row: 0, col: 0, direction: "vertical" },
                    { word: "CRUZ", hint: "Morte de Jesus", row: 1, col: 5, direction: "vertical" },
                ]
            },
        ],

        grid: [],
        gridSize: 10,
        horizontalClues: [],
        verticalClues: [],
        selectedClue: null,
        selectedDirection: null,
        selectedRow: -1,
        selectedCol: -1,
        currentPuzzleIndex: 0,
        score: 0,
        puzzlesCompleted: 0,
        isPlaying: false,
        gameOver: false,
        puzzleComplete: false,
        xpGained: 0,

        startGame() {
            this.isPlaying = true;
            this.currentPuzzleIndex = 0;
            this.score = 0;
            this.puzzlesCompleted = 0;
            this.gameOver = false;
            this.loadPuzzle();
        },

        loadPuzzle() {
            if (this.currentPuzzleIndex >= this.puzzles.length) {
                this.endGame();
                return;
            }

            const puzzle = this.puzzles[this.currentPuzzleIndex];
            this.gridSize = puzzle.gridSize;
            this.puzzleComplete = false;
            this.selectedClue = null;
            this.selectedDirection = null;

            // Initialize empty grid
            this.grid = [];
            for (let r = 0; r < this.gridSize; r++) {
                const row = [];
                for (let c = 0; c < this.gridSize; c++) {
                    row.push({ blocked: true, letter: '', userLetter: '', number: null, selected: false, correct: false });
                }
                this.grid.push(row);
            }

            // Place words
            this.horizontalClues = [];
            this.verticalClues = [];
            let clueNumber = 1;

            puzzle.words.forEach(wordData => {
                const { word, hint, row, col, direction } = wordData;
                
                for (let i = 0; i < word.length; i++) {
                    const r = direction === 'horizontal' ? row : row + i;
                    const c = direction === 'horizontal' ? col + i : col;
                    
                    if (r < this.gridSize && c < this.gridSize) {
                        this.grid[r][c].blocked = false;
                        this.grid[r][c].letter = word[i];
                    }
                }

                // Add number to first cell
                if (!this.grid[row][col].number) {
                    this.grid[row][col].number = clueNumber;
                }

                const clue = { number: this.grid[row][col].number || clueNumber, hint, word, row, col, direction, solved: false };
                
                if (direction === 'horizontal') {
                    this.horizontalClues.push(clue);
                } else {
                    this.verticalClues.push(clue);
                }

                if (!this.grid[row][col].number) {
                    this.grid[row][col].number = clueNumber;
                    clueNumber++;
                }
            });
        },

        selectClue(clue, direction) {
            this.selectedClue = clue;
            this.selectedDirection = direction;
            this.selectedRow = clue.row;
            this.selectedCol = clue.col;
            this.highlightCells();
            if (navigator.vibrate) navigator.vibrate(20);
        },

        selectCell(row, col) {
            this.selectedRow = row;
            this.selectedCol = col;
            
            // Find which clue this cell belongs to
            const allClues = [...this.horizontalClues, ...this.verticalClues];
            for (const clue of allClues) {
                for (let i = 0; i < clue.word.length; i++) {
                    const r = clue.direction === 'horizontal' ? clue.row : clue.row + i;
                    const c = clue.direction === 'horizontal' ? clue.col + i : clue.col;
                    if (r === row && c === col) {
                        this.selectedClue = clue;
                        this.selectedDirection = clue.direction;
                        break;
                    }
                }
            }
            
            this.highlightCells();
        },

        highlightCells() {
            // Clear all selections
            for (let r = 0; r < this.gridSize; r++) {
                for (let c = 0; c < this.gridSize; c++) {
                    this.grid[r][c].selected = false;
                }
            }

            if (!this.selectedClue) return;

            // Highlight selected word cells
            for (let i = 0; i < this.selectedClue.word.length; i++) {
                const r = this.selectedDirection === 'horizontal' ? this.selectedClue.row : this.selectedClue.row + i;
                const c = this.selectedDirection === 'horizontal' ? this.selectedClue.col + i : this.selectedClue.col;
                if (r < this.gridSize && c < this.gridSize) {
                    this.grid[r][c].selected = true;
                }
            }
        },

        typeLetter(letter) {
            if (!this.selectedClue || this.selectedRow < 0 || this.selectedCol < 0) return;
            
            this.grid[this.selectedRow][this.selectedCol].userLetter = letter;
            if (navigator.vibrate) navigator.vibrate(20);
            
            // Move to next cell
            if (this.selectedDirection === 'horizontal') {
                if (this.selectedCol < this.selectedClue.col + this.selectedClue.word.length - 1) {
                    this.selectedCol++;
                }
            } else {
                if (this.selectedRow < this.selectedClue.row + this.selectedClue.word.length - 1) {
                    this.selectedRow++;
                }
            }
        },

        deleteLetter() {
            if (this.selectedRow >= 0 && this.selectedCol >= 0) {
                this.grid[this.selectedRow][this.selectedCol].userLetter = '';
                
                // Move to previous cell
                if (this.selectedDirection === 'horizontal') {
                    if (this.selectedCol > this.selectedClue.col) {
                        this.selectedCol--;
                    }
                } else {
                    if (this.selectedRow > this.selectedClue.row) {
                        this.selectedRow--;
                    }
                }
            }
        },

        checkPuzzle() {
            let allCorrect = true;
            let wordsCorrect = 0;

            const allClues = [...this.horizontalClues, ...this.verticalClues];
            
            for (const clue of allClues) {
                let wordCorrect = true;
                
                for (let i = 0; i < clue.word.length; i++) {
                    const r = clue.direction === 'horizontal' ? clue.row : clue.row + i;
                    const c = clue.direction === 'horizontal' ? clue.col + i : clue.col;
                    
                    if (this.grid[r][c].userLetter !== clue.word[i]) {
                        wordCorrect = false;
                        allCorrect = false;
                    } else {
                        this.grid[r][c].correct = true;
                    }
                }
                
                if (wordCorrect) {
                    clue.solved = true;
                    wordsCorrect++;
                }
            }

            this.score += wordsCorrect * 10;

            if (allCorrect) {
                this.puzzleComplete = true;
                this.puzzlesCompleted++;
                this.score += 50; // Bonus for completing puzzle
                if (navigator.vibrate) navigator.vibrate([50, 30, 50, 30, 50]);
            } else {
                if (navigator.vibrate) navigator.vibrate([100, 50, 100]);
            }
        },

        nextPuzzle() {
            this.currentPuzzleIndex++;
            this.loadPuzzle();
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
                const response = await fetch('{{ route("memberpanel.ebd.arcade.submit", "palavras-cruzadas") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        score: this.score,
                        metadata: {
                            puzzles_completed: this.puzzlesCompleted
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
