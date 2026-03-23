@extends('memberpanel::components.layouts.master')

@section('title', 'Caça-Palavras Bíblico - Arcade')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-slate-950 transition-colors duration-200 pb-12" x-data="wordSearchGame()">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 pt-4 sm:pt-6 space-y-4 sm:space-y-6">

        @include('ebd::memberpanel.arcade.partials.game-header', [
            'gameTitle' => 'Caça-Palavras Bíblico',
            'gameSubtitle' => 'Encontre palavras bíblicas escondidas no grid.',
            'icon' => 'magnifying-glass',
            'accent' => 'teal',
        ])

    <!-- Game Area -->
    <div class="max-w-5xl mx-auto relative px-2 sm:px-4">

        <!-- Start Screen -->
        <div x-show="!isPlaying && !gameOver" class="text-center space-y-8 py-10">
            <div class="bg-white dark:bg-gray-900 rounded-2xl md:rounded-3xl p-8 md:p-12 shadow-2xl border border-gray-100 dark:border-gray-800">
                <div class="w-20 h-20 bg-teal-500 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-xl shadow-teal-500/30">
                    <x-icon name="magnifying-glass" style="duotone" class="w-10 h-10 text-white" />
                </div>
                <h2 class="text-2xl md:text-3xl font-black text-gray-900 dark:text-white mb-4">Caça-Palavras Bíblico</h2>
                <p class="text-gray-500 mb-6">Encontre palavras relacionadas a temas bíblicos escondidas no grid!</p>
                
                <!-- Category Selection -->
                <div class="mb-8">
                    <label class="block text-sm font-bold text-gray-600 dark:text-gray-400 mb-3">Escolha o tema:</label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                        <template x-for="(cat, key) in categories" :key="key">
                            <button @click="selectedCategory = key"
                                    class="p-3 rounded-xl border-2 text-sm font-bold transition-all touch-manipulation"
                                    :class="selectedCategory === key ? 'border-teal-500 bg-teal-50 dark:bg-teal-900/20 text-teal-600' : 'border-gray-200 dark:border-gray-700 hover:border-teal-300'">
                                <span x-text="cat.name"></span>
                            </button>
                        </template>
                    </div>
                </div>
                
                <button @click="startGame()" class="w-full py-5 bg-teal-600 hover:bg-teal-500 text-white rounded-2xl font-black text-lg uppercase tracking-widest shadow-xl shadow-teal-600/30 transition-all hover:-translate-y-1 active:scale-95 touch-manipulation">
                    <x-icon name="play" style="solid" class="w-5 h-5 inline mr-2" /> Iniciar Jogo
                </button>
            </div>
        </div>

        <!-- Game Board -->
        <div x-show="isPlaying && !gameOver" x-transition class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Grid -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-900 rounded-2xl md:rounded-3xl p-4 sm:p-6 shadow-2xl border border-gray-100 dark:border-gray-800">
                    <div class="grid gap-0.5 sm:gap-1 select-none touch-manipulation"
                         :style="'grid-template-columns: repeat(' + gridSize + ', minmax(0, 1fr));'"
                         @mouseup="endSelection()"
                         @touchend="endSelection()">
                        <template x-for="(row, rowIdx) in grid" :key="'row-' + rowIdx">
                            <template x-for="(cell, colIdx) in row" :key="'cell-' + rowIdx + '-' + colIdx">
                                <div class="aspect-square flex items-center justify-center text-sm sm:text-base md:text-lg font-black rounded transition-all cursor-pointer"
                                     :class="{
                                         'bg-emerald-500 text-white': isCellFound(rowIdx, colIdx),
                                         'bg-teal-200 dark:bg-teal-800 text-teal-800 dark:text-teal-200': isCellSelected(rowIdx, colIdx) && !isCellFound(rowIdx, colIdx),
                                         'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-teal-100 dark:hover:bg-teal-900/30': !isCellSelected(rowIdx, colIdx) && !isCellFound(rowIdx, colIdx)
                                     }"
                                     @mousedown="startSelection(rowIdx, colIdx)"
                                     @mouseenter="continueSelection(rowIdx, colIdx)"
                                     @touchstart.prevent="startSelection(rowIdx, colIdx)"
                                     @touchmove.prevent="handleTouchMove($event)"
                                     x-text="cell">
                                </div>
                            </template>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Word List -->
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-900 rounded-2xl md:rounded-3xl p-6 shadow-2xl border border-gray-100 dark:border-gray-800 sticky top-4">
                    <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest mb-4">Palavras para Encontrar</h3>
                    <div class="space-y-2 max-h-[400px] overflow-y-auto">
                        <template x-for="word in words" :key="word">
                            <div class="flex items-center gap-3 p-3 rounded-xl transition-all"
                                 :class="foundWords.includes(word) ? 'bg-emerald-50 dark:bg-emerald-900/20' : 'bg-gray-50 dark:bg-gray-800'">
                                <div class="w-6 h-6 rounded-full flex items-center justify-center"
                                     :class="foundWords.includes(word) ? 'bg-emerald-500' : 'bg-gray-300 dark:bg-gray-600'">
                                    <x-icon name="check" style="solid" class="w-3 h-3 text-white" x-show="foundWords.includes(word)" />
                                </div>
                                <span class="font-bold"
                                      :class="foundWords.includes(word) ? 'text-emerald-600 dark:text-emerald-400 line-through' : 'text-gray-700 dark:text-gray-300'"
                                      x-text="word"></span>
                            </div>
                        </template>
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
                <p class="text-gray-500 mt-2">Você encontrou todas as palavras!</p>
            </div>
            
            <div class="grid grid-cols-2 gap-4 max-w-sm mx-auto">
                <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-2xl">
                    <span class="block text-xs font-black text-gray-400 uppercase">Tempo</span>
                    <span class="text-2xl font-bold text-teal-500" x-text="formatTime(timer)"></span>
                </div>
                <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-2xl">
                    <span class="block text-xs font-black text-gray-400 uppercase">Pontuação</span>
                    <span class="text-2xl font-bold text-amber-500" x-text="score"></span>
                </div>
            </div>

            <div class="bg-teal-50 dark:bg-teal-900/20 p-4 rounded-2xl">
                <span class="text-sm text-teal-600 dark:text-teal-400 font-bold">+<span x-text="xpGained"></span> XP Ganho!</span>
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
function wordSearchGame() {
    return {
        categories: {
            apostolos: { name: "12 Apóstolos", words: ["PEDRO", "ANDRE", "TIAGO", "JOAO", "FILIPE", "BARTOLOMEU", "MATEUS", "TOME", "SIMAO", "JUDAS"] },
            frutos: { name: "Frutos do Espírito", words: ["AMOR", "ALEGRIA", "PAZ", "PACIENCIA", "BONDADE", "BENIGNIDADE", "FIDELIDADE", "MANSIDAO", "DOMINIO"] },
            profetas: { name: "Profetas", words: ["ISAIAS", "JEREMIAS", "EZEQUIEL", "DANIEL", "OSEIAS", "JOEL", "AMOS", "JONAS", "MIQUEIAS", "NAUM"] },
            patriarcas: { name: "Patriarcas", words: ["ABRAAO", "ISAQUE", "JACO", "JOSE", "MOISES", "AARAO", "JOSUE", "CALEBE", "SAMUEL", "DAVI"] },
            lugares: { name: "Lugares Bíblicos", words: ["JERUSALEM", "BELEM", "NAZARE", "GALILEA", "SAMARIA", "JERICO", "SINAI", "EGITO", "CANAA", "EDEN"] },
            virtudes: { name: "Virtudes Cristãs", words: ["FE", "ESPERANCA", "CARIDADE", "GRACA", "PERDAO", "HUMILDADE", "ORACAO", "JEJUM", "LOUVOR", "ADORACAO"] },
        },
        selectedCategory: 'frutos',
        grid: [],
        gridSize: 12,
        words: [],
        foundWords: [],
        foundCells: [],
        selectedCells: [],
        isSelecting: false,
        startCell: null,
        timer: 0,
        timerInterval: null,
        isPlaying: false,
        gameOver: false,
        score: 0,
        xpGained: 0,

        startGame() {
            this.isPlaying = true;
            this.words = [...this.categories[this.selectedCategory].words];
            this.foundWords = [];
            this.foundCells = [];
            this.selectedCells = [];
            this.timer = 0;
            this.gameOver = false;
            this.score = 0;
            this.generateGrid();
            
            this.timerInterval = setInterval(() => {
                if (this.isPlaying && !this.gameOver) this.timer++;
            }, 1000);
        },

        generateGrid() {
            // Initialize empty grid
            this.grid = Array(this.gridSize).fill(null).map(() => 
                Array(this.gridSize).fill('')
            );

            // Place words
            for (const word of this.words) {
                this.placeWord(word);
            }

            // Fill remaining cells with random letters
            const letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            for (let r = 0; r < this.gridSize; r++) {
                for (let c = 0; c < this.gridSize; c++) {
                    if (!this.grid[r][c]) {
                        this.grid[r][c] = letters[Math.floor(Math.random() * letters.length)];
                    }
                }
            }
        },

        placeWord(word) {
            const directions = [
                [0, 1],   // horizontal
                [1, 0],   // vertical
                [1, 1],   // diagonal down-right
                [1, -1],  // diagonal down-left
            ];

            let placed = false;
            let attempts = 0;

            while (!placed && attempts < 100) {
                const dir = directions[Math.floor(Math.random() * directions.length)];
                const [dr, dc] = dir;
                
                const maxRow = this.gridSize - (dr > 0 ? word.length : 1);
                const maxCol = dc > 0 ? this.gridSize - word.length : this.gridSize - 1;
                const minCol = dc < 0 ? word.length - 1 : 0;

                if (maxRow < 0 || maxCol < minCol) {
                    attempts++;
                    continue;
                }

                const startRow = Math.floor(Math.random() * (maxRow + 1));
                const startCol = minCol + Math.floor(Math.random() * (maxCol - minCol + 1));

                // Check if word fits
                let canPlace = true;
                for (let i = 0; i < word.length; i++) {
                    const r = startRow + dr * i;
                    const c = startCol + dc * i;
                    if (this.grid[r][c] && this.grid[r][c] !== word[i]) {
                        canPlace = false;
                        break;
                    }
                }

                if (canPlace) {
                    for (let i = 0; i < word.length; i++) {
                        const r = startRow + dr * i;
                        const c = startCol + dc * i;
                        this.grid[r][c] = word[i];
                    }
                    placed = true;
                }

                attempts++;
            }
        },

        startSelection(row, col) {
            this.isSelecting = true;
            this.startCell = { row, col };
            this.selectedCells = [{ row, col }];
        },

        continueSelection(row, col) {
            if (!this.isSelecting || !this.startCell) return;
            
            // Calculate direction
            const dr = Math.sign(row - this.startCell.row);
            const dc = Math.sign(col - this.startCell.col);
            
            // Only allow straight lines
            const isDiagonal = dr !== 0 && dc !== 0 && Math.abs(row - this.startCell.row) === Math.abs(col - this.startCell.col);
            const isStraight = (dr === 0 || dc === 0);
            
            if (!isDiagonal && !isStraight) return;
            
            // Build selection path
            this.selectedCells = [];
            let r = this.startCell.row;
            let c = this.startCell.col;
            
            while (true) {
                this.selectedCells.push({ row: r, col: c });
                if (r === row && c === col) break;
                r += dr;
                c += dc;
                if (r < 0 || r >= this.gridSize || c < 0 || c >= this.gridSize) break;
            }
        },

        handleTouchMove(e) {
            const touch = e.touches[0];
            const element = document.elementFromPoint(touch.clientX, touch.clientY);
            if (element && element.hasAttribute('x-text')) {
                // Parse row/col from element position in grid
                const parent = element.parentElement;
                const children = Array.from(parent.children);
                const index = children.indexOf(element);
                const row = Math.floor(index / this.gridSize);
                const col = index % this.gridSize;
                this.continueSelection(row, col);
            }
        },

        endSelection() {
            if (!this.isSelecting) return;
            this.isSelecting = false;
            
            // Check if selected word matches any word in list
            const selectedWord = this.selectedCells.map(c => this.grid[c.row][c.col]).join('');
            const reversedWord = selectedWord.split('').reverse().join('');
            
            let foundWord = null;
            if (this.words.includes(selectedWord) && !this.foundWords.includes(selectedWord)) {
                foundWord = selectedWord;
            } else if (this.words.includes(reversedWord) && !this.foundWords.includes(reversedWord)) {
                foundWord = reversedWord;
            }
            
            if (foundWord) {
                this.foundWords.push(foundWord);
                this.foundCells.push(...this.selectedCells.map(c => `${c.row}-${c.col}`));
                this.score += 50;
                if (navigator.vibrate) navigator.vibrate([50, 30, 50]);
                
                if (this.foundWords.length === this.words.length) {
                    this.endGame();
                }
            } else {
                if (navigator.vibrate && selectedWord.length > 2) navigator.vibrate(50);
            }
            
            this.selectedCells = [];
            this.startCell = null;
        },

        isCellSelected(row, col) {
            return this.selectedCells.some(c => c.row === row && c.col === col);
        },

        isCellFound(row, col) {
            return this.foundCells.includes(`${row}-${col}`);
        },

        endGame() {
            this.gameOver = true;
            clearInterval(this.timerInterval);
            // Bonus for speed
            const timeBonus = Math.max(0, 300 - this.timer);
            this.score += timeBonus;
            this.saveScore();
        },

        restartGame() {
            this.gameOver = false;
            this.isPlaying = false;
            clearInterval(this.timerInterval);
        },

        formatTime(seconds) {
            const m = Math.floor(seconds / 60);
            const s = seconds % 60;
            return `${m}:${s.toString().padStart(2, '0')}`;
        },

        async saveScore() {
            try {
                const response = await fetch('{{ route("memberpanel.ebd.arcade.submit", "caca-palavras") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        score: this.score,
                        metadata: {
                            category: this.selectedCategory,
                            time_seconds: this.timer,
                            words_found: this.foundWords.length
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
