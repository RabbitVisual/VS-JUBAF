<div x-data="biblePicker()"
     x-show="isOpen"
     @open-bible-picker.window="open()"
     x-transition
     style="display: none;"
     class="fixed inset-0 z-[60] flex items-center justify-center p-4">

    <!-- Backdrop -->
    <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="close()"></div>

    <!-- Modal -->
    <div class="relative w-full max-w-2xl bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden transform transition-all">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-900/50">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <x-icon name="book-open" class="w-5 h-5 text-amber-500" />
                Citar Bíblia
            </h3>
            <button @click="close()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <x-icon name="x" class="w-6 h-6" />
            </button>
        </div>

        <!-- Body -->
        <div class="p-6 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Livro</label>
                    <select x-model="selectedBook" @change="resetChapter()"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-amber-500 focus:border-amber-500">
                        <option value="">Selecione...</option>
                        @foreach($bibleBooks as $book)
                            <option value="{{ $book->id }}" data-name="{{ $book->name }}">{{ $book->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Capítulo</label>
                        <input type="number" x-model="chapter" min="1"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-amber-500 focus:border-amber-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Versículo(s)</label>
                        <input type="text" x-model="verses" placeholder="Ex: 1-5"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-amber-500 focus:border-amber-500">
                    </div>
                </div>
            </div>

            <div>
                <div class="flex justify-between items-center mb-1">
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Texto do Versículo</label>
                    <button type="button" @click="fetchText()" :disabled="fetching"
                        class="text-xs text-amber-600 hover:text-amber-700 font-bold flex items-center gap-1 disabled:opacity-50">
                        <template x-if="fetching">
                            <span class="flex items-center gap-1"><span class="animate-spin inline-block w-3 h-3 border border-amber-500 border-t-transparent rounded-full"></span> Buscando...</span>
                        </template>
                        <template x-if="!fetching">
                            <span class="flex items-center gap-1"><x-icon name="refresh" class="w-3 h-3" /> Buscar Texto</span>
                        </template>
                    </button>
                </div>
                <p x-show="fetchError" class="text-sm text-red-600 dark:text-red-400 mb-1" x-text="fetchError"></p>
                <textarea x-model="text" rows="4"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-amber-500 focus:border-amber-500 font-serif italic"
                    placeholder="O texto aparecerá aqui ou você pode digitar manualmente..."></textarea>
            </div>
        </div>

        <!-- Footer -->
        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 flex justify-end gap-3 border-t border-gray-200 dark:border-gray-700">
            <button @click="close()" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg">
                Cancelar
            </button>
            <button @click="insert()"
                class="px-4 py-2 text-sm font-bold text-white bg-amber-600 hover:bg-amber-700 rounded-lg shadow-md transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                :disabled="!text || !selectedBook">
                Inserir no Sermão
            </button>
        </div>
    </div>
</div>

<script>
    function biblePicker() {
        return {
            isOpen: false,
            selectedBook: '',
            chapter: '',
            verses: '',
            text: '',
            fetching: false,
            fetchError: '',

            open() {
                this.isOpen = true;
                this.fetchError = '';
            },

            close() {
                this.isOpen = false;
                this.reset();
                this.fetchError = '';
            },

            reset() {
                this.text = '';
            },

            resetChapter() {
                this.chapter = '';
                this.verses = '';
                this.text = '';
                this.fetchError = '';
            },

            async fetchText() {
                if (!this.selectedBook || !this.chapter) return;
                this.fetching = true;
                this.fetchError = '';
                const params = new URLSearchParams({
                    book_id: this.selectedBook,
                    chapter_number: this.chapter
                });
                if (this.verses) params.set('verse_range', this.verses);
                try {
                    const res = await fetch('/api/v1/bible/verses?' + params.toString());
                    const json = await res.json();
                    if (!res.ok) {
                        this.fetchError = json.message || 'Não foi possível buscar os versículos.';
                        this.text = '';
                        return;
                    }
                    const data = json.data || [];
                    this.text = Array.isArray(data) ? data.map(v => v.text || '').filter(Boolean).join(' ') : '';
                    if (!this.text) this.fetchError = 'Nenhum versículo encontrado.';
                } catch (e) {
                    this.fetchError = 'Erro ao conectar com a API. Tente novamente.';
                    this.text = '';
                } finally {
                    this.fetching = false;
                }
            },

            insert() {
                const bookName = document.querySelector(`option[value="${this.selectedBook}"]`)?.innerText;
                const reference = `${bookName} ${this.chapter}:${this.verses}`;

                // Dispatch event to Rich Editor
                window.dispatchEvent(new CustomEvent('insert-bible-text', {
                    detail: {
                        text: this.text,
                        reference: reference
                    }
                }));

                this.close();
            }
        }
    }
</script>

