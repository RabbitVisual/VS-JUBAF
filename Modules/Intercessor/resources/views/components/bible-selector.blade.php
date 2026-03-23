                        {{-- Smart Bible Selector --}}
                        <div class="mb-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-600 p-4 shadow-sm">
                            <div class="flex items-center gap-2 mb-3 pb-2 border-b border-gray-100 dark:border-gray-700">
                                <x-icon name="book-open" style="duotone" class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                                <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Citador Bíblico</span>
                            </div>

                            <div class="grid grid-cols-2 gap-3 mb-3">
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Versão</label>
                                    <select x-model="selected.versionId" @change="fetchBooks" class="w-full text-xs px-2 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-1 focus:ring-blue-500">
                                        <template x-for="version in versions" :key="'ver-' + version.id">
                                            <option :value="version.id" x-text="version.abbreviation || version.name"></option>
                                        </template>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Livro</label>
                                    <select x-model="selected.bookId" @change="fetchChapters" class="w-full text-xs px-2 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-1 focus:ring-blue-500" :disabled="!selected.versionId">
                                        <option value="">Selecione...</option>
                                        <template x-for="book in books" :key="'bk-' + book.id">
                                            <option :value="book.id" x-text="book.name"></option>
                                        </template>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3 mb-3">
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Capítulo</label>
                                    <select x-model="selected.chapterId" @change="fetchVerses" class="w-full text-xs px-2 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-1 focus:ring-blue-500" :disabled="!selected.bookId">
                                         <option value="">...</option>
                                         <template x-for="chapter in chapters" :key="'ch-' + chapter.id">
                                            <option :value="chapter.id" x-text="chapter.chapter_number"></option>
                                        </template>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Versículos (Ex: 1-5)</label>
                                    <input type="text" x-model="selected.verseRange" @blur="generatePreview" placeholder="Todos" class="w-full text-xs px-2 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-1 focus:ring-blue-500" :disabled="!selected.chapterId">
                                </div>
                            </div>

                             <div x-show="previewText" class="mt-3 bg-blue-50 dark:bg-blue-900/10 p-3 rounded-lg border border-blue-100 dark:border-blue-900/30" style="display: none;">
                                <p class="text-xs text-gray-600 dark:text-gray-300 italic font-serif mb-2" x-text="previewText"></p>
                                <div class="flex justify-between items-center border-t border-blue-200 dark:border-blue-800 pt-2">
                                    <span class="text-xs font-bold text-indigo-700 dark:text-indigo-400" x-text="referenceString"></span>
                                    <button type="button" @click="appendCitation" class="text-[10px] bg-indigo-600 text-white px-3 py-1 rounded hover:bg-indigo-700 font-bold uppercase tracking-wider transition-colors">
                                        Anexar
                                    </button>
                                </div>
                            </div>
                        </div>
