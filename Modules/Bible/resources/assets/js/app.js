/* Bible Module Scripts - Interlinear Premium Suite */

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
        loadingChapters: false,
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

            // Initial books load
            await this.loadBooks();
            await this.loadChapters();
            await this.loadData();

            // Keyboard navigation
            window.addEventListener('keydown', (e) => {
                if (e.key === 'ArrowLeft') this.selectedTestament === 'old' ? this.nextChapter() : this.prevChapter();
                if (e.key === 'ArrowRight') this.selectedTestament === 'old' ? this.prevChapter() : this.nextChapter();
                if (e.key === 'f' && (e.ctrlKey || e.metaKey)) {
                    e.preventDefault();
                    this.isStudyMode = !this.isStudyMode;
                }
            });

            // Global scroll listener
            window.addEventListener('scroll', () => this.updateProgress());
        },

        async loadBooks() {
            this.loadingBooks = true;
            try {
                const res = await fetch('/painel/biblia/interlinear/books');
                const allBooks = await res.json();
                if (Array.isArray(allBooks)) {
                    this.allBooks = allBooks;
                    this.books = allBooks.filter(b => b.testament === this.selectedTestament);

                    if (!this.books.find(b => b.name === this.selectedBook)) {
                        this.selectedBook = this.books[0]?.name || 'Genesis';
                    }
                    this.updateChapterCount();
                }
            } catch (e) {
                console.error('Error loading books metadata:', e);
            } finally {
                this.loadingBooks = false;
            }
        },

        updateChapterCount() {
            if (!this.allBooks) return;
            const book = this.allBooks.find(b => b.name === this.selectedBook);
            this.totalChapters = book ? book.total_chapters : 50;
            if (this.selectedChapter > this.totalChapters) {
                this.selectedChapter = 1;
            }
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
                if (result.error) throw result.error;
                this.data = result;
            } catch (e) {
                console.error('Error loading interlinear data:', e);
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
            try {
                const res = await fetch(`/painel/biblia/strong/${cleanNum}`);
                const data = await res.json();
                this.strongDef = data;
            } catch (e) {
                console.error(e);
                this.strongDef = null;
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
            return text.replace(/\(G(\d+)\)/g, '<span class="text-purple-600 font-bold cursor-pointer" onclick="window.dispatchEvent(new CustomEvent(\'lookup-strong\', {detail: \'G$1\'}))">G$1</span>')
                .replace(/\(H(\d+)\)/g, '<span class="text-purple-600 font-bold cursor-pointer" onclick="window.dispatchEvent(new CustomEvent(\'lookup-strong\', {detail: \'H$1\'}))">H$1</span>');
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

// Global listener for cross-references
window.addEventListener('lookup-strong', (e) => {
    const el = document.querySelector('[x-data]');
    if (el && el.__x && el.__x.$data) {
        el.__x.$data.showStrong(e.detail);
    }
});
