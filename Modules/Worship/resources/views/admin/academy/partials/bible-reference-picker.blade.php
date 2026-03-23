{{-- Seletor de referência bíblica para lições (Módulo Bible): define Livro, Capítulo, Versículo e preenche o campo. --}}
@php
    $inputId = $inputId ?? 'edit_bible_reference';
@endphp
<div class="mt-2 p-3 rounded-lg bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-600" x-data="academyBibleRefPicker('{{ $inputId }}')">
    <p class="text-[10px] font-bold uppercase tracking-widest text-indigo-600 dark:text-indigo-400 mb-2 flex items-center gap-1">
        <x-icon name="book-bible" class="w-3.5 h-3.5" />
        Buscar referência na Bíblia
    </p>
    <div class="flex flex-wrap gap-2 items-end">
        <div class="min-w-[100px]">
            <label class="block text-[10px] font-bold text-gray-500 dark:text-gray-400 mb-0.5">Versão</label>
            <select x-model="selected.versionId" @change="fetchBooks()" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-xs py-1.5 px-2">
                <option value="">—</option>
                <template x-for="v in versions" :key="v.id">
                    <option :value="v.id" x-text="v.name"></option>
                </template>
            </select>
        </div>
        <div class="min-w-[120px]">
            <label class="block text-[10px] font-bold text-gray-500 dark:text-gray-400 mb-0.5">Livro</label>
            <select x-model="selected.bookId" @change="fetchChapters()" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-xs py-1.5 px-2">
                <option value="">—</option>
                <template x-for="b in books" :key="b.id">
                    <option :value="b.id" x-text="b.name"></option>
                </template>
            </select>
        </div>
        <div class="min-w-[70px]">
            <label class="block text-[10px] font-bold text-gray-500 dark:text-gray-400 mb-0.5">Cap.</label>
            <select x-model="selected.chapterId" @change="fetchVerses()" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-xs py-1.5 px-2">
                <option value="">—</option>
                <template x-for="c in chapters" :key="c.id">
                    <option :value="c.id" :data-number="c.chapter_number" x-text="c.chapter_number"></option>
                </template>
            </select>
        </div>
        <div class="min-w-[80px]">
            <label class="block text-[10px] font-bold text-gray-500 dark:text-gray-400 mb-0.5">Vers.</label>
            <input type="text" x-model="selected.verseRange" placeholder="1 ou 1-5" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-xs py-1.5 px-2">
        </div>
        <button type="button" @click="fetchAndSet()" :disabled="loading || !selected.bookId || !selected.chapterId || !selected.verseRange"
            class="px-3 py-1.5 rounded-lg text-xs font-bold bg-indigo-600 text-white hover:bg-indigo-500 disabled:opacity-50">
            <span x-show="!loading">Buscar</span>
            <span x-show="loading" class="inline-block w-3 h-3 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
        </button>
    </div>
    <p x-show="error" class="mt-1.5 text-xs text-red-600 dark:text-red-400" x-text="error"></p>
    <div x-show="referenceString && versePreview" class="mt-2 pt-2 border-t border-gray-200 dark:border-gray-600">
        <p class="text-xs font-bold text-indigo-600 dark:text-indigo-400" x-text="referenceString"></p>
        <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5 italic line-clamp-2" x-text="versePreview"></p>
        <button type="button" @click="setInput()" class="mt-1.5 text-xs font-bold text-amber-600 hover:text-amber-500">
            Usar esta referência
        </button>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    if (window.__academyBibleRefPickerRegistered) {
        return;
    }
    window.__academyBibleRefPickerRegistered = true;
    Alpine.data('academyBibleRefPicker', (inputId) => ({
        inputId,
        versions: [],
        books: [],
        chapters: [],
        selected: { versionId: '', bookId: '', chapterId: '', chapterNumber: '', verseRange: '1' },
        referenceString: '',
        versePreview: '',
        loading: false,
        error: '',
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
                .catch(() => {});
        },
        fetchBooks() {
            if (!this.selected.versionId) return;
            this.books = []; this.chapters = []; this.selected.bookId = ''; this.selected.chapterId = '';
            fetch(`/api/v1/bible/books?version_id=${this.selected.versionId}`)
                .then(r => r.json())
                .then(resp => { this.books = resp.data || []; })
                .catch(() => {});
        },
        fetchChapters() {
            if (!this.selected.bookId) return;
            this.chapters = []; this.selected.chapterId = '';
            fetch(`/api/v1/bible/chapters?book_id=${this.selected.bookId}`)
                .then(r => r.json())
                .then(resp => { this.chapters = resp.data || []; })
                .catch(() => {});
        },
        fetchVerses() {
            if (!this.selected.chapterId) return;
            const ch = this.chapters.find(c => String(c.id) === String(this.selected.chapterId));
            this.selected.chapterNumber = ch ? ch.chapter_number : '';
        },
        async fetchAndSet() {
            const book = this.books.find(b => String(b.id) === String(this.selected.bookId));
            const bookName = book ? book.name : '';
            const ref = `${bookName} ${this.selected.chapterNumber}:${this.selected.verseRange}`;
            this.loading = true; this.error = ''; this.referenceString = ''; this.versePreview = '';
            try {
                const res = await fetch(`/api/v1/bible/find?ref=${encodeURIComponent(ref)}`);
                const json = await res.json();
                if (json.data && json.data.verses && json.data.verses.length > 0) {
                    this.referenceString = json.data.reference || ref;
                    this.versePreview = json.data.verses.map(v => v.text || '').join(' ').slice(0, 120) + (json.data.verses.reduce((a,v) => a + (v.text||'').length, 0) > 120 ? '…' : '');
                    this.setInput();
                } else {
                    this.error = json.message || 'Referência não encontrada.';
                }
            } catch (e) {
                this.error = 'Erro ao buscar.';
            } finally {
                this.loading = false;
            }
        },
        setInput() {
            const el = document.getElementById(this.inputId);
            if (el && this.referenceString) {
                el.value = this.referenceString;
                el.dispatchEvent(new Event('input', { bubbles: true }));
            }
        }
    }));
});
</script>
