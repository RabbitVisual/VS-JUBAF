{{-- Painel lateral "Contexto Bíblico" (Panorama AT/NT). Requer $bibleBooks com book_number. --}}
<div x-data="contextoBiblico()" class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 overflow-hidden shadow-sm">
    <button @click="open = !open" class="w-full px-4 py-3 flex items-center justify-between text-left font-medium text-gray-900 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
        <span class="flex items-center gap-2">
            <x-icon name="scroll" class="w-5 h-5 text-amber-500" />
            Contexto Bíblico
            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-gray-200 dark:bg-gray-600 text-gray-500 dark:text-gray-400 text-xs font-bold cursor-help" title="Autor, data, tema e destinatários do livro selecionado (Panorama)." aria-label="Ajuda">?</span>
        </span>
        <x-icon name="chevron-down" class="w-4 h-4 transition-transform duration-200" :class="'open ? \'rotate-180\' : \'\''" />
    </button>
    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="border-t border-gray-200 dark:border-gray-700">
        <div class="p-4 space-y-4">
            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Livro</label>
            <select x-model="selectedBookNumber" @change="fetchPanorama()"
                class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-amber-500 focus:border-amber-500 text-sm">
                <option value="">Selecione um livro...</option>
                @foreach($bibleBooks as $book)
                    <option value="{{ $book->book_number }}">{{ $book->name }}</option>
                @endforeach
            </select>
            <template x-if="loading">
                <div class="space-y-2 py-2" role="status" aria-label="Carregando">
                    <div class="animate-pulse h-3 bg-gray-200 dark:bg-gray-600 rounded w-3/4"></div>
                    <div class="animate-pulse h-3 bg-gray-200 dark:bg-gray-600 rounded w-1/2"></div>
                    <div class="animate-pulse h-3 bg-gray-200 dark:bg-gray-600 rounded w-full"></div>
                    <div class="animate-pulse h-3 bg-gray-200 dark:bg-gray-600 rounded w-5/6"></div>
                    <span class="sr-only">Carregando panorama...</span>
                </div>
            </template>
            <template x-if="!loading && panorama">
                <div class="space-y-3 text-sm font-serif">
                    <p class="leading-relaxed"><span class="font-sans font-bold text-gray-600 dark:text-gray-400 text-xs uppercase tracking-wide">Autor</span><br><span class="text-gray-800 dark:text-gray-200" x-text="panorama.author || '—'"></span></p>
                    <p class="leading-relaxed"><span class="font-sans font-bold text-gray-600 dark:text-gray-400 text-xs uppercase tracking-wide">Data</span><br><span class="text-gray-800 dark:text-gray-200" x-text="panorama.date_written || '—'"></span></p>
                    <p class="leading-relaxed"><span class="font-sans font-bold text-gray-600 dark:text-gray-400 text-xs uppercase tracking-wide">Tema central</span><br><span class="text-gray-800 dark:text-gray-200 block mt-0.5" x-text="panorama.theme_central || '—'"></span></p>
                    <p class="leading-relaxed"><span class="font-sans font-bold text-gray-600 dark:text-gray-400 text-xs uppercase tracking-wide">Destinatários</span><br><span class="text-gray-800 dark:text-gray-200 block mt-0.5" x-text="panorama.recipients || '—'"></span></p>
                </div>
            </template>
            <template x-if="!loading && selectedBookNumber && !panorama">
                <p class="text-sm text-gray-500 dark:text-gray-400">Nenhum panorama disponível para este livro.</p>
            </template>
        </div>
    </div>
</div>

<script>
    function contextoBiblico() {
        return {
            open: true,
            selectedBookNumber: '',
            panorama: null,
            loading: false,
            async fetchPanorama() {
                if (!this.selectedBookNumber) { this.panorama = null; return; }
                this.loading = true;
                this.panorama = null;
                try {
                    const res = await fetch('/api/v1/bible/panorama?book_number=' + encodeURIComponent(this.selectedBookNumber));
                    const json = await res.json();
                    if (res.ok && json.data) this.panorama = json.data;
                } catch (e) {}
                this.loading = false;
            }
        };
    }
</script>
