@extends('memberpanel::components.layouts.master')

@section('title', 'Bíblia Interlinear - Estudo Profundo')

@section('content')
<div class="transition-colors duration-500 pb-32"
     :class="isStudyMode ? 'fixed inset-0 z-100 bg-white dark:bg-slate-950 overflow-y-auto' : 'min-h-screen bg-gray-50 dark:bg-slate-950'"
     x-data="interlinearApp()"
     x-init="init()"
     @scroll.window="updateProgress()">

    <!-- Progress Bar -->
    <div class="fixed top-0 left-0 h-1 bg-linear-to-r from-indigo-500 via-purple-500 to-pink-500 z-100 transition-all duration-300"
         :style="'width: ' + scrollProgress + '%'"></div>

    <!-- Sticky Header -->
    <header class="sticky top-0 z-40 bg-white/90 dark:bg-slate-950/90 backdrop-blur-xl border-b border-gray-200 dark:border-slate-800 transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-18 sm:h-20 flex items-center justify-between gap-4">

            <!-- Left: Title & Back -->
            <div class="flex items-center gap-4">
                <a href="{{ route('memberpanel.bible.index') }}"
                   class="flex items-center justify-center w-10 h-10 rounded-xl bg-gray-100 dark:bg-slate-800 hover:bg-gray-200 dark:hover:bg-slate-700 text-gray-500 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white transition-all">
                    <x-icon name="arrow-left" class="w-5 h-5" />
                </a>
                <div class="hidden sm:block">
                    <h1 class="text-lg font-black text-gray-900 dark:text-white tracking-tight leading-none">Bíblia <span class="text-indigo-600 dark:text-indigo-400">Interlinear</span></h1>
                    <p class="text-[10px] font-bold text-gray-500 dark:text-slate-500 uppercase tracking-widest mt-1">Ferramenta de Exegese</p>
                </div>
            </div>

            <!-- Center: Navigation Controls -->
            <div class="flex items-center gap-2 sm:gap-3 bg-gray-100 dark:bg-slate-900/50 p-1.5 rounded-2xl border border-gray-200 dark:border-slate-800">

                <!-- Testament Toggle -->
                <div class="flex bg-white dark:bg-slate-800 rounded-xl shadow-sm p-0.5">
                    <button @click="selectedTestament = 'old'; loadBooks()"
                            :class="selectedTestament === 'old' ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-500 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700'"
                            class="px-3 py-1.5 rounded-lg text-xs font-black uppercase tracking-widest transition-all">AT</button>
                    <button @click="selectedTestament = 'new'; loadBooks()"
                            :class="selectedTestament === 'new' ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-500 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700'"
                            class="px-3 py-1.5 rounded-lg text-xs font-black uppercase tracking-widest transition-all">NT</button>
                </div>

                <div class="w-px h-6 bg-gray-300 dark:bg-slate-700 mx-1 hidden sm:block"></div>

                <!-- Book Select -->
                <div class="relative group">
                    <select x-model="selectedBook" @change="loadChapters()"
                            class="appearance-none bg-transparent pl-2 pr-8 py-1 text-sm font-bold text-gray-800 dark:text-white outline-none cursor-pointer hover:text-indigo-600 transition-colors uppercase tracking-wide max-w-[100px] sm:max-w-none">
                        <template x-for="book in books" :key="book.name">
                            <option :value="book.name" x-text="book.name" class="bg-white dark:bg-slate-900"></option>
                        </template>
                    </select>
                    <x-icon name="caret-down" class="w-3 h-3 absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400" />
                </div>

                <!-- Chapter Nav -->
                <div class="flex items-center gap-1">
                    <button @click="prevChapter()" class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-white dark:hover:bg-slate-700 text-gray-500 hover:text-indigo-600 transition-colors">
                        <x-icon name="chevron-left" class="w-3 h-3" />
                    </button>
                    <span class="text-sm font-black text-gray-900 dark:text-white w-8 text-center" x-text="selectedChapter"></span>
                    <button @click="nextChapter()" class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-white dark:hover:bg-slate-700 text-gray-500 hover:text-indigo-600 transition-colors">
                        <x-icon name="chevron-right" class="w-3 h-3" />
                    </button>
                </div>
            </div>

            <!-- Right: Settings / Keyboard Hints -->
            <div class="hidden md:flex items-center gap-3">
                 <button @click="isStudyMode = !isStudyMode"
                         :class="isStudyMode ? 'bg-indigo-600 text-white border-indigo-500' : 'bg-white dark:bg-slate-900 text-gray-600 dark:text-slate-300 border-gray-200 dark:border-slate-800'"
                         class="flex items-center gap-2 px-4 py-2 rounded-xl border font-bold text-xs uppercase tracking-wider transition-all shadow-sm hover:shadow active:scale-95">
                    <x-icon name="book-open-reader" class="w-4 h-4" />
                    <span>Modo Foco</span>
                </button>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 relative">

        <!-- Loading State -->
        <div x-show="loadingData" class="flex flex-col items-center justify-center py-40 animate-pulse">
            <div class="w-16 h-16 border-4 border-indigo-600 border-t-transparent rounded-full animate-spin mb-6"></div>
            <p class="text-xs font-black text-gray-400 dark:text-slate-500 uppercase tracking-widest">Carregando Escrituras...</p>
        </div>

        <!-- Content Area -->
        <div x-show="!loadingData" class="space-y-16" style="display: none;">
            <template x-for="(verse, index) in data.verses" :key="index">
                <div class="relative group/verse">

                    <!-- Verse Number Badge -->
                    <div class="absolute -left-3 sm:-left-8 top-0">
                         <span class="flex items-center justify-center w-8 h-8 rounded-xl bg-gray-100 dark:bg-slate-800 text-gray-500 dark:text-slate-400 font-black text-xs border border-gray-200 dark:border-slate-700 group-hover/verse:bg-indigo-600 group-hover/verse:text-white group-hover/verse:scale-110 transition-all duration-300 shadow-sm" x-text="index + 1"></span>
                    </div>

                    <div class="pl-6 sm:pl-8">
                        <!-- Original Text Flow -->
                        <div class="flex flex-wrap gap-x-4 gap-y-10 items-end" :class="selectedTestament === 'old' ? 'flex-row-reverse text-right' : 'flex-row text-left'">
                            <template x-for="(segment, sIndex) in verse" :key="sIndex">
                                <div class="flex flex-col items-center gap-1 group/word cursor-pointer transition-transform hover:-translate-y-1"
                                     @click="showStrong(segment.strong, segment.lemma_pt, segment.pt_suggested)">

                                    <!-- Strong Number (Top) -->
                                    <span class="text-[9px] font-bold text-gray-300 dark:text-slate-600 uppercase tracking-wider opacity-0 group-hover/word:opacity-100 transition-opacity"
                                          x-text="cleanStrong(segment.strong)"></span>

                                    <!-- Original Word -->
                                    <span class="text-2xl sm:text-3xl font-serif font-medium text-gray-800 dark:text-slate-200 leading-none px-2 py-1 rounded-lg group-hover/word:bg-indigo-50 dark:group-hover/word:bg-indigo-900/20 group-hover/word:text-indigo-700 dark:group-hover/word:text-indigo-300 transition-colors duration-200"
                                          x-text="segment.word"></span>

                                    <!-- Transliteration -->
                                    <span class="text-xs font-semibold text-gray-400 dark:text-slate-500 italic"
                                          x-text="segment.xlit"></span>

                                    <!-- Portuguese/BSRTB Definition (Badge) -->
                                    <template x-if="segment.lemma_pt">
                                        <span class="mt-1 px-2.5 py-1 rounded-xs bg-indigo-600 text-white text-[10px] font-black uppercase tracking-wide shadow-md shadow-indigo-500/20 border-b-2 border-indigo-800"
                                              x-text="segment.lemma_pt"></span>
                                    </template>
                                </div>
                            </template>
                        </div>

                        <!-- Translation Context (KJF) -->
                        <div class="mt-8 pt-6 border-t border-gray-100 dark:border-slate-800/50" x-show="data.translation[index]">
                            <div class="flex items-start gap-4 p-4 rounded-2xl bg-gray-50 dark:bg-slate-900/50 border border-gray-100 dark:border-slate-800">
                                <div class="mt-1 p-1.5 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400">
                                    <x-icon name="language" class="w-4 h-4" />
                                </div>
                                <div>
                                    <p class="text-xs font-black text-gray-400 dark:text-slate-500 uppercase tracking-widest mb-1">Tradução Contextual (KJF)</p>
                                    <p class="text-base text-gray-700 dark:text-slate-300 italic font-medium leading-relaxed"
                                       x-text="data.translation[index]"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </template>
        </div>
    </main>

    <!-- Lexicon Sidebar (Slide Over) -->
    <div class="fixed inset-0 z-70 pointer-events-none overflow-hidden" x-show="showSidebar">
        <div class="absolute inset-0 bg-gray-900/20 dark:bg-black/40 backdrop-blur-sm transition-opacity pointer-events-auto"
             x-show="showSidebar"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="showSidebar = false"></div>

        <div class="absolute inset-y-0 right-0 max-w-md w-full pointer-events-auto flex">
            <div class="w-full h-full bg-white dark:bg-slate-950 shadow-2xl overflow-y-auto transform transition-transform border-l border-gray-200 dark:border-slate-800"
                 x-show="showSidebar"
                 x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700"
                 x-transition:enter-start="translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="translate-x-full">

                <!-- Sidebar Header -->
                <div class="sticky top-0 z-10 px-6 py-4 bg-white/80 dark:bg-slate-950/80 backdrop-blur-md border-b border-gray-100 dark:border-slate-800 flex items-center justify-between">
                    <h2 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest flex items-center gap-2">
                        <x-icon name="book-open" class="w-4 h-4 text-indigo-500" />
                        Léxico BSRTB
                    </h2>
                    <button @click="showSidebar = false" class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-slate-800 text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                        <x-icon name="xmark" class="w-5 h-5" />
                    </button>
                </div>

                <!-- Sidebar Content -->
                <div class="p-6 space-y-8">

                    <!-- Loading State -->
                    <div x-show="loadingStrong" class="py-20 text-center">
                         <div class="w-10 h-10 border-4 border-indigo-600 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
                         <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Consultando dicionário...</p>
                    </div>

                    <div x-show="!loadingStrong && strongDef" class="animate-fade-in">

                        <!-- Header Card -->
                        <div class="bg-linear-to-br from-indigo-50 to-purple-50 dark:from-indigo-950/30 dark:to-purple-950/30 rounded-3xl p-6 border border-indigo-100 dark:border-indigo-900/50">
                            <div class="flex items-center justify-between mb-4">
                                <span class="px-3 py-1 rounded-lg bg-white dark:bg-slate-900 text-[10px] font-black uppercase tracking-widest text-indigo-600 dark:text-indigo-400 shadow-sm border border-indigo-100 dark:border-indigo-900" x-text="selectedTestament === 'old' ? 'Hebraico' : 'Grego'">
                                </span>
                                <span class="text-2xl font-black text-indigo-600 dark:text-indigo-400 font-mono tracking-tighter" x-text="strongDef?.number"></span>
                            </div>

                            <h3 class="text-4xl font-serif font-medium text-gray-900 dark:text-white mb-2" x-text="strongDef?.lemma"></h3>

                            <div class="flex flex-wrap gap-2 text-sm">
                                <span class="text-gray-500 dark:text-slate-400 italic" x-text="strongDef?.xlit"></span>
                                <span class="text-gray-300 dark:text-slate-600">•</span>
                                <span class="text-gray-600 dark:text-slate-300 font-medium" x-text="strongDef?.pronounce"></span>
                            </div>
                        </div>

                        <!-- Data Grid -->
                        <div class="space-y-4">

                            <!-- BSRTB Definition -->
                            <template x-if="lemmaBr">
                                <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-gray-100 dark:border-slate-800 shadow-sm relative overflow-hidden group">
                                    <div class="absolute top-0 right-0 w-16 h-16 bg-indigo-500/10 rounded-bl-full -mr-8 -mt-8 pointer-events-none group-hover:scale-110 transition-transform"></div>
                                    <div class="flex items-center gap-2 mb-2">
                                        <div class="w-1.5 h-1.5 rounded-full bg-indigo-500"></div>
                                        <h4 class="text-[10px] font-black text-gray-400 dark:text-slate-500 uppercase tracking-widest">Equivalente Semântico</h4>
                                    </div>
                                    <p class="text-xl font-black text-gray-900 dark:text-white leading-tight" x-text="lemmaBr"></p>
                                </div>
                            </template>

                            <!-- Usage in Context -->
                            <template x-if="strongPtSuggested">
                                <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-gray-100 dark:border-slate-800 shadow-sm relative overflow-hidden group">
                                    <div class="absolute top-0 right-0 w-16 h-16 bg-emerald-500/10 rounded-bl-full -mr-8 -mt-8 pointer-events-none group-hover:scale-110 transition-transform"></div>
                                    <div class="flex items-center gap-2 mb-2">
                                        <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div>
                                        <h4 class="text-[10px] font-black text-gray-400 dark:text-slate-500 uppercase tracking-widest">Uso no Contexto (KJF)</h4>
                                    </div>
                                    <p class="text-lg font-bold text-gray-800 dark:text-slate-200" x-text="strongPtSuggested"></p>
                                </div>
                            </template>
                        </div>

                        <!-- Full Definition -->
                        <div class="mt-8">
                             <h4 class="text-[10px] font-black text-gray-400 dark:text-slate-500 uppercase tracking-widest mb-4 flex items-center gap-3">
                                <span class="flex-1 h-px bg-gray-100 dark:bg-slate-800"></span>
                                Significado e Uso
                                <span class="flex-1 h-px bg-gray-100 dark:bg-slate-800"></span>
                            </h4>
                            <div class="prose prose-sm prose-indigo dark:prose-invert max-w-none">
                                <p class="text-gray-600 dark:text-slate-300 leading-relaxed" x-html="strongDef ? formatDef(strongDef.description) : ''"></p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
window.interlinearApp = function () {
    return {
        selectedTestament: 'old',
        selectedBook: 'Genesis',
        selectedChapter: 1,
        books: [],
        allBooks: [],
        totalChapters: 50,
        data: { verses: [], translation: [] },
        loadingBooks: false,
        loadingData: false,
        showSidebar: false,
        loadingStrong: false,
        strongDef: null,
        strongPtSuggested: null,
        lemmaBr: null,
        isStudyMode: false,
        scrollProgress: 0,

        updateProgress() {
            const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
            const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            this.scrollProgress = (winScroll / height) * 100;
        },

        async init() {
            const params = new URLSearchParams(window.location.search);
            if (params.has('book')) this.selectedBook = params.get('book');
            if (params.has('chapter')) this.selectedChapter = parseInt(params.get('chapter'));

            await this.loadBooks();
            await this.loadChapters();

            // Keyboard Shortcuts
            window.addEventListener('keydown', (e) => {
                if (e.key === 'ArrowLeft') this.prevChapter();
                if (e.key === 'ArrowRight') this.nextChapter();
                if (e.key.toLowerCase() === 'f' && !['INPUT', 'SELECT', 'TEXTAREA'].includes(document.activeElement.tagName)) {
                    this.isStudyMode = !this.isStudyMode;
                }
            });
        },

        async loadBooks() {
            this.loadingBooks = true;
            try {
                const res = await fetch('/painel/biblia/interlinear/books');
                const allBooks = await res.json();
                if (Array.isArray(allBooks)) {
                    this.allBooks = allBooks;
                    this.books = allBooks.filter(b => b.testament === this.selectedTestament);
                    // Ensure selected book exists in current testament, else reset
                    if (!this.books.find(b => b.name === this.selectedBook)) {
                        this.selectedBook = this.books[0]?.name || 'Genesis';
                    }
                    this.updateChapterCount();
                }
            } catch (e) {
                console.error('Error loading books:', e);
            } finally {
                this.loadingBooks = false;
            }
        },

        updateChapterCount() {
            if (!this.allBooks) return;
            const book = this.allBooks.find(b => b.name === this.selectedBook);
            this.totalChapters = book ? book.total_chapters : 50;
            if (this.selectedChapter > this.totalChapters) this.selectedChapter = 1;
        },

        async loadChapters() {
            this.updateChapterCount();
            await this.loadData();
        },

        async loadData() {
            this.loadingData = true;
            window.scrollTo({ top: 0, behavior: 'smooth' });
            try {
                const url = `/painel/biblia/interlinear/data?book=${encodeURIComponent(this.selectedBook)}&chapter=${this.selectedChapter}&testament=${this.selectedTestament}`;
                const res = await fetch(url);
                const result = await res.json();
                this.data = result;
            } catch (e) {
                console.error('Error loading data:', e);
                this.data = { verses: [], translation: [] };
            } finally {
                this.loadingData = false;
            }
        },

        async showStrong(number, ptLemma = null, ptSuggestion = null) {
            if (!number) return;
            const cleanNum = this.cleanStrong(number);
            this.showSidebar = true;
            this.loadingStrong = true;
            this.lemmaBr = ptLemma;
            this.strongPtSuggested = ptSuggestion;
            this.strongDef = null; // Reset previous def

            try {
                const res = await fetch(`/painel/biblia/strong/${cleanNum}`);
                const data = await res.json();
                this.strongDef = data;
            } catch (e) {
                console.error('Error showing strong:', e);
            } finally {
                this.loadingStrong = false;
            }
        },

        cleanStrong(number) {
            if (!number) return '';
            const match = number.match(/([HG]\d+)/);
            return match ? match[1] : number;
        },

        formatDef(text) {
            if (!text) return '';
            return text.replace(/\(G(\d+)\)/g, '<button class="text-indigo-600 font-bold hover:underline" onclick="window.dispatchEvent(new CustomEvent(\'lookup-strong\', {detail: \'G$1\'}))">G$1</button>')
                .replace(/\(H(\d+)\)/g, '<button class="text-indigo-600 font-bold hover:underline" onclick="window.dispatchEvent(new CustomEvent(\'lookup-strong\', {detail: \'H$1\'}))">H$1</button>');
        },

        prevChapter() {
            if (this.selectedChapter > 1) {
                this.selectedChapter--;
                this.loadData();
            }
        },

        nextChapter() {
            if (this.selectedChapter < this.totalChapters) {
                this.selectedChapter++;
                this.loadData();
            }
        }
    }
}

window.addEventListener('lookup-strong', (e) => {
    const el = document.querySelector('[x-data]');
    if (el && window.Alpine) {
        window.Alpine.$data(el).showStrong(e.detail);
    }
});
</script>
@endpush

