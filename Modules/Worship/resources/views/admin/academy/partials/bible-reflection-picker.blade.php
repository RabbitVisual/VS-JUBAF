{{-- Integração com Módulo Bible: Livro, Capítulo e Versículo(s) → texto local da API --}}
@php
    $textareaId = $textareaId ?? 'biblical_reflection';
@endphp
<div class="rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-900/50 p-4 sm:p-5" x-data="academyBiblePicker('{{ $textareaId }}')">
    <p class="text-[10px] font-bold uppercase tracking-widest text-indigo-600 dark:text-indigo-400 mb-3 flex items-center gap-2">
        <x-icon name="book-bible" class="w-4 h-4" />
        Inserir versículo da Bíblia (Módulo Bible)
    </p>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
        <div>
            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Versão</label>
            <select x-model="selected.versionId" @change="fetchBooks()" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Selecione...</option>
                <template x-for="v in versions" :key="v.id">
                    <option :value="v.id" x-text="v.name"></option>
                </template>
            </select>
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Livro</label>
            <select x-model="selected.bookId" @change="fetchChapters()" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Selecione...</option>
                <template x-for="b in books" :key="b.id">
                    <option :value="b.id" :data-name="b.name" x-text="b.name"></option>
                </template>
            </select>
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Capítulo</label>
            <select x-model="selected.chapterId" @change="fetchVerses()" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Selecione...</option>
                <template x-for="c in chapters" :key="c.id">
                    <option :value="c.id" :data-number="c.chapter_number" x-text="c.chapter_number"></option>
                </template>
            </select>
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Versículo(s)</label>
            <input type="text" x-model="selected.verseRange" placeholder="Ex: 1 ou 1-5" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500">
        </div>
    </div>
    <div class="mt-3 flex flex-wrap items-center gap-2">
        <button type="button" @click="fetchVerseText()" :disabled="loading || !canFetch"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-bold bg-indigo-600 text-white hover:bg-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
            <template x-if="loading">
                <span class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
            </template>
            <template x-if="!loading">
                <x-icon name="magnifying-glass" class="w-4 h-4" />
            </template>
            Buscar texto na Bíblia
        </button>
        <button type="button" x-show="verseText" @click="insertIntoReflection()"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-bold bg-amber-600 text-white hover:bg-amber-500 transition-colors">
            <x-icon name="plus" class="w-4 h-4" />
            Inserir na reflexão
        </button>
    </div>
    <p x-show="error" class="mt-2 text-sm text-red-600 dark:text-red-400" x-text="error"></p>
    <div x-show="verseText" class="mt-4 p-4 rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600">
        <p class="text-xs font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider mb-2" x-text="referenceString"></p>
        <p class="text-gray-700 dark:text-gray-300 font-serif leading-relaxed italic" x-html="verseText"></p>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('academyBiblePicker', (textareaId) => ({
        textareaId,
        versions: [],
        books: [],
        chapters: [],
        selected: {
            versionId: '',
            bookId: '',
            chapterId: '',
            chapterNumber: '',
            verseRange: '1'
        },
        allChapterVerses: [],
        verseText: '',
        referenceString: '',
        loading: false,
        error: '',

        get canFetch() {
            return this.selected.bookId && this.selected.chapterId && this.selected.verseRange;
        },

        init() {
            fetch('/api/v1/bible/versions', { headers: { 'Accept': 'application/json' } })
                .then(r => r.json())
                .then(resp => {
                    this.versions = resp.data || [];
                    if (this.versions.length > 0) {
                        this.selected.versionId = String(this.versions[0].id);
                        this.fetchBooks();
                    }
                })
                .catch(() => { this.versions = []; });
        },

        fetchBooks() {
            if (!this.selected.versionId) return;
            this.books = [];
            this.chapters = [];
            this.selected.bookId = '';
            this.selected.chapterId = '';
            fetch(`/api/v1/bible/books?version_id=${this.selected.versionId}`)
                .then(r => r.json())
                .then(resp => { this.books = resp.data || []; })
                .catch(() => { this.books = []; });
        },

        fetchChapters() {
            if (!this.selected.bookId) return;
            this.chapters = [];
            this.selected.chapterId = '';
            this.allChapterVerses = [];
            fetch(`/api/v1/bible/chapters?book_id=${this.selected.bookId}`)
                .then(r => r.json())
                .then(resp => { this.chapters = resp.data || []; })
                .catch(() => { this.chapters = []; });
        },

        fetchVerses() {
            if (!this.selected.chapterId) return;
            const ch = this.chapters.find(c => String(c.id) === String(this.selected.chapterId));
            this.selected.chapterNumber = ch ? ch.chapter_number : '';
            fetch(`/api/v1/bible/verses?chapter_id=${this.selected.chapterId}`)
                .then(r => r.json())
                .then(resp => { this.allChapterVerses = resp.data || []; })
                .catch(() => { this.allChapterVerses = []; });
        },

        async fetchVerseText() {
            if (!this.canFetch) return;
            const book = this.books.find(b => String(b.id) === String(this.selected.bookId));
            const bookName = book ? book.name : '';
            const ref = `${bookName} ${this.selected.chapterNumber}:${this.selected.verseRange}`;
            this.loading = true;
            this.error = '';
            this.verseText = '';
            this.referenceString = ref;
            try {
                const res = await fetch(`/api/v1/bible/find?ref=${encodeURIComponent(ref)}`);
                const json = await res.json();
                if (json.data && json.data.verses && json.data.verses.length > 0) {
                    this.referenceString = json.data.reference || ref;
                    this.verseText = json.data.verses.map(v => `<span class="font-bold text-gray-500 dark:text-gray-400 text-xs align-top mr-1">${v.verse_number}</span>${(v.text || '').replace(/</g, '&lt;')}`).join(' ');
                } else {
                    this.error = json.message || 'Referência não encontrada.';
                }
            } catch (e) {
                this.error = 'Erro ao buscar na Bíblia. Tente novamente.';
            } finally {
                this.loading = false;
            }
        },

        insertIntoReflection() {
            const ta = document.getElementById(this.textareaId);
            if (!ta) return;
            const plain = (this.verseText || '').replace(/<[^>]+>/g, '').replace(/&\w+;|&#\d+;/g, ' ').trim();
            const line = `${this.referenceString} — ${plain}`;
            const current = (ta.value || '').trim();
            ta.value = current ? current + "\n\n" + line : line;
            ta.dispatchEvent(new Event('input', { bubbles: true }));
        }
    }));
});
</script>
