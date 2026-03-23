/**
 * Sermons Module JavaScript
 * Handles Bible reference selection, tag management, and sermon editor
 */

(function () {
    'use strict';

    /**
     * Bible Reference Manager
     */
    class BibleReferenceManager {
        constructor() {
            this.init();
        }

        init() {
            const addReferenceBtn = document.getElementById('add-bible-reference');
            const referencesContainer = document.getElementById('bible-references-container');

            if (addReferenceBtn && referencesContainer) {
                addReferenceBtn.addEventListener('click', () => this.addReference());
                // Load existing references logic if needed (e.g. edit mode)
                this.loadExistingReferences();
            }

            // Bind events for static elements (if any)
            this.bindEvents(document);
            // Populate book/chapter/verse when version (or book/chapter) already selected (e.g. commentaries index with form, or query string)
            document.querySelectorAll('[name*="bible_version_id"]').forEach(versionSelect => {
                const container = versionSelect.closest('.bible-reference-item') || versionSelect.closest('form');
                if (!container || !container.querySelector('[name*="book_id"]')) return;
                const form = container.tagName === 'FORM' ? container : null;
                const bookId = form?.dataset?.bookId;
                const chapterId = form?.dataset?.chapterId;
                const verseNumber = form?.dataset?.verseNumber;
                if (versionSelect.value) {
                    this.loadBooks(versionSelect).then(() => {
                        const bookSelect = container.querySelector('[name*="book_id"]');
                        if (bookSelect && (bookSelect.value || bookId)) {
                            if (bookId) bookSelect.value = bookId;
                            this.loadChapters(bookSelect).then(() => {
                                const chapterSelect = container.querySelector('[name*="chapter_id"]');
                                if (chapterSelect && (chapterId || chapterSelect.value)) {
                                    if (chapterId) chapterSelect.value = chapterId;
                                    const verseSelect = container.querySelector('[name="verse_number"]');
                                    if (verseSelect && (verseNumber || chapterSelect.value)) {
                                        this.loadVerseSelect(chapterSelect).then(() => {
                                            if (verseNumber) verseSelect.value = verseNumber;
                                        });
                                    }
                                }
                            });
                        }
                    });
                }
            });
        }

        bindEvents(root) {
            // Load books when version changes
            root.querySelectorAll('[name*="bible_version_id"]').forEach(select => {
                // Remove existing listeners to avoid duplicates if re-binding
                const newSelect = select.cloneNode(true);
                select.parentNode.replaceChild(newSelect, select);
                newSelect.addEventListener('change', (e) => this.loadBooks(e.target));
            });

            // Re-bind just in case, or use delegation.
            // Delegation is better for dynamic elements.
            document.addEventListener('change', (e) => {
                if (e.target.matches('[name*="bible_version_id"]')) {
                    this.loadBooks(e.target);
                }
                if (e.target.matches('[name*="book_id"]')) {
                    this.loadChapters(e.target);
                }
                if (e.target.matches('[name*="chapter_id"]')) {
                    this.loadVerses(e.target);
                    this.loadVerseSelect(e.target);
                }
                if (e.target.matches('[name*="chapter"]')) {
                    // Fallback for fields named just 'chapter' if they are selects
                    if (e.target.tagName === 'SELECT') {
                        // this.loadVerses(e.target); // Optional: if we want verse selection on these fields too
                    }
                }
            });
        }

        loadExistingReferences() {
            const existingRefs = document.querySelectorAll('.bible-reference-item');
            existingRefs.forEach(ref => {
                // Trigger loads if values exist
                const versionSelect = ref.querySelector('[name*="bible_version_id"]');
                const bookSelect = ref.querySelector('[name*="book_id"]');
                const chapterSelect = ref.querySelector('[name*="chapter_id"]') || ref.querySelector('[name*="chapter"]');

                if (versionSelect && versionSelect.value) {
                    this.loadBooks(versionSelect).then(() => {
                        if (bookSelect && bookSelect.getAttribute('data-selected')) {
                            bookSelect.value = bookSelect.getAttribute('data-selected');
                        }
                        if (bookSelect && bookSelect.value) {
                            this.loadChapters(bookSelect).then(() => {
                                if (chapterSelect && chapterSelect.getAttribute('data-selected')) {
                                    chapterSelect.value = chapterSelect.getAttribute('data-selected');
                                }
                                if (chapterSelect && chapterSelect.value) {
                                    this.loadVerses(chapterSelect);
                                }
                            });
                        }
                    });
                }
            });
        }

        addReference() {
            const container = document.getElementById('bible-references-container');
            if (!container) return;

            const index = container.children.length;
            const template = document.getElementById('bible-reference-template');
            if (!template) return;

            const clone = template.content.cloneNode(true);
            const item = clone.querySelector('.bible-reference-item');

            // Update field names
            item.querySelectorAll('[name]').forEach(field => {
                const name = field.getAttribute('name');
                const newName = name.replace(/\[\d+\]/, `[${index}]`).replace('bible_references[0]', `bible_references[${index}]`);
                field.setAttribute('name', newName);
                field.id = newName.replace(/[\[\]]/g, '_'); // Update ID to be unique
                const label = item.querySelector(`label[for="${name}"]`); // Try original name
                if (!label) {
                    // rough attempt to find label
                }
            });

            // Update label 'for' attributes
            item.querySelectorAll('label').forEach(label => {
                const forAttr = label.getAttribute('for');
                if (forAttr) {
                    label.setAttribute('for', forAttr.replace(/[\[\]]/g, '_').replace(/_\d+_/, `_${index}_`).replace(/_0_/, `_${index}_`)); // Heuristic replacement
                }
            });


            // Add remove button
            const removeBtn = item.querySelector('[data-remove-reference]');
            if (removeBtn) {
                removeBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    item.remove();
                    this.updateIndexes();
                });
            }

            container.appendChild(item);
        }

        async loadBooks(versionSelect) {
            const versionId = versionSelect.value;
            const container = versionSelect.closest('.bible-reference-item') || versionSelect.closest('form');
            if (!container) return;

            const bookSelect = container.querySelector('[name*="book_id"]');
            if (!bookSelect) return;

            bookSelect.innerHTML = '<option value="">Carregando...</option>';

            if (!versionId) {
                bookSelect.innerHTML = '<option value="">Selecione a versão</option>';
                return;
            }

            try {
                const response = await fetch(`/api/v1/bible/books?version_id=${versionId}`);
                if (!response.ok) throw new Error('Falha ao carregar livros');
                const resp = await response.json();
                const books = resp.data || [];

                bookSelect.innerHTML = '<option value="">Selecione o livro</option>';
                books.forEach(book => {
                    const option = document.createElement('option');
                    option.value = book.id;
                    option.textContent = book.name;
                    bookSelect.appendChild(option);
                });
            } catch (error) {
                console.error('Error loading books:', error);
                bookSelect.innerHTML = '<option value="">Erro ao carregar</option>';
            }
        }

        async loadChapters(bookSelect) {
            const bookId = bookSelect.value;
            const container = bookSelect.closest('.bible-reference-item') || bookSelect.closest('form');
            if (!container) return;

            const chapterSelect = container.querySelector('[name*="chapter_id"]') || container.querySelector('[name*="chapter"]');
            if (!chapterSelect || chapterSelect.tagName !== 'SELECT') return;

            chapterSelect.innerHTML = '<option value="">Carregando...</option>';

            if (!bookId) {
                chapterSelect.innerHTML = '<option value="">Selecione o livro</option>';
                return;
            }

            try {
                const response = await fetch(`/api/v1/bible/chapters?book_id=${bookId}`);
                if (!response.ok) throw new Error('Falha ao carregar capítulos');
                const resp = await response.json();
                const chapters = resp.data || [];
                const isCommentariesForm = container.querySelector('[name="verse_number"]');
                chapterSelect.innerHTML = isCommentariesForm ? '<option value="">Todos</option>' : '<option value="">Selecione o capítulo</option>';
                chapters.forEach(chapter => {
                    const option = document.createElement('option');
                    option.value = chapter.id;
                    option.textContent = chapter.chapter_number;
                    option.setAttribute('data-number', chapter.chapter_number);
                    chapterSelect.appendChild(option);
                });
                const verseSelect = container.querySelector('[name="verse_number"]');
                if (verseSelect) {
                    verseSelect.innerHTML = '<option value="">Todos</option>';
                }
            } catch (error) {
                console.error('Error loading chapters:', error);
                chapterSelect.innerHTML = '<option value="">Erro ao carregar</option>';
            }
        }

        async loadVerseSelect(chapterSelect) {
            const chapterId = chapterSelect.value;
            const container = chapterSelect.closest('.bible-reference-item') || chapterSelect.closest('form');
            if (!container) return;

            const verseSelect = container.querySelector('[name="verse_number"]');
            if (!verseSelect || verseSelect.tagName !== 'SELECT') return;

            verseSelect.innerHTML = '<option value="">Carregando...</option>';

            if (!chapterId) {
                verseSelect.innerHTML = '<option value="">Todos</option>';
                return;
            }

            try {
                const response = await fetch(`/api/v1/bible/verses?chapter_id=${chapterId}`);
                if (!response.ok) throw new Error('Falha ao carregar versículos');
                const resp = await response.json();
                const verses = resp.data || [];

                verseSelect.innerHTML = '<option value="">Todos</option>';
                verses.forEach(verse => {
                    const option = document.createElement('option');
                    option.value = verse.verse_number ?? verse.id;
                    option.textContent = verse.verse_number ?? verse.id;
                    verseSelect.appendChild(option);
                });
            } catch (error) {
                console.error('Error loading verses:', error);
                verseSelect.innerHTML = '<option value="">Todos</option>';
            }
        }

        async loadVerses(chapterSelect) {
            const chapterId = chapterSelect.value;
            const container = chapterSelect.closest('.bible-reference-item');
            if (!container) return;

            let versesInput = container.querySelector('[name*="verses"]');
            if (!versesInput) return;

            let versesContainer = container.querySelector('.verses-selector');
            if (!versesContainer) {
                versesInput.type = 'hidden';
                versesContainer = document.createElement('div');
                versesContainer.className = 'verses-selector mt-2 p-3 border rounded-xl shadow-sm bg-gray-50 dark:bg-gray-900/50 border-gray-200 dark:border-gray-700 transition-all';

                versesContainer.innerHTML = `
                    <div class="flex justify-between items-center mb-3 pb-2 border-b border-gray-200 dark:border-gray-700">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Versículos</span>
                        <div class="flex gap-3">
                            <button type="button" class="select-all-btn text-[10px] font-black text-blue-600 hover:text-blue-700 dark:text-blue-400 uppercase tracking-tighter transition-colors">Selecionar Tudo</button>
                            <button type="button" class="clear-all-btn text-[10px] font-black text-red-600 hover:text-red-700 dark:text-red-400 uppercase tracking-tighter transition-colors">Limpar</button>
                        </div>
                    </div>
                    <div class="verses-grid grid grid-cols-6 sm:grid-cols-8 md:grid-cols-10 gap-1 max-h-48 overflow-y-auto pr-1 custom-scrollbar">
                        <div class="col-span-full py-4 text-center">
                             <div class="inline-block animate-spin rounded-full h-4 w-4 border-2 border-blue-600 border-t-transparent"></div>
                        </div>
                    </div>
                    <div class="mt-2 text-[9px] text-gray-400 dark:text-gray-500 italic">
                        Dica: Use Shift + Clique para selecionar um intervalo.
                    </div>
                `;

                versesInput.parentNode.insertBefore(versesContainer, versesInput.nextSibling);

                versesContainer.querySelector('.select-all-btn').addEventListener('click', (e) => {
                    e.preventDefault();
                    versesContainer.querySelectorAll('.verse-btn').forEach(btn => {
                        btn.classList.add('bg-blue-600', 'text-white', 'border-blue-600');
                    });
                    this.updateVersesInput(versesContainer, versesInput);
                });

                versesContainer.querySelector('.clear-all-btn').addEventListener('click', (e) => {
                    e.preventDefault();
                    versesContainer.querySelectorAll('.verse-btn').forEach(btn => {
                        btn.classList.remove('bg-blue-600', 'text-white', 'border-blue-600');
                    });
                    this.updateVersesInput(versesContainer, versesInput);
                });
            }

            const grid = versesContainer.querySelector('.verses-grid');
            if (!chapterId) {
                grid.innerHTML = '<div class="col-span-full py-2 text-center text-xs text-gray-400">Selecione o capítulo</div>';
                return;
            }

            // Persistence: Get currently selected verses
            // If the chapter is different from what's in data-selected-verses, we should probably ignore it
            // But for simplicity, we just use the current value.
            const currentVerses = versesInput.value || versesInput.getAttribute('data-selected-verses') || '';

            try {
                const response = await fetch(`/api/v1/bible/verses?chapter_id=${chapterId}`);
                if (!response.ok) throw new Error('Falha ao carregar');
                const resp = await response.json();
                const verses = resp.data || [];

                grid.innerHTML = '';
                let lastClickedIndex = -1;

                verses.forEach((verse, index) => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'verse-btn px-1 py-1 text-[10px] border rounded hover:bg-blue-50 dark:hover:bg-blue-900 border-gray-200 dark:border-gray-700 transition-all font-bold';
                    btn.textContent = verse.verse_number;
                    btn.dataset.verseNum = verse.verse_number;
                    btn.dataset.index = index;

                    if (this.isVerseSelected(verse.verse_number, currentVerses)) {
                        btn.classList.add('bg-blue-600', 'text-white', 'border-blue-600');
                    }

                    btn.addEventListener('click', (e) => {
                        e.preventDefault();
                        const currentIndex = parseInt(btn.dataset.index);

                        if (e.shiftKey && lastClickedIndex !== -1) {
                            const start = Math.min(lastClickedIndex, currentIndex);
                            const end = Math.max(lastClickedIndex, currentIndex);
                            const buttons = grid.querySelectorAll('.verse-btn');
                            const selecting = !btn.classList.contains('bg-blue-600');

                            for (let i = start; i <= end; i++) {
                                if (selecting) {
                                    buttons[i].classList.add('bg-blue-600', 'text-white', 'border-blue-600');
                                } else {
                                    buttons[i].classList.remove('bg-blue-600', 'text-white', 'border-blue-600');
                                }
                            }
                        } else {
                            btn.classList.toggle('bg-blue-600');
                            btn.classList.toggle('text-white');
                            btn.classList.toggle('border-blue-600');
                        }

                        lastClickedIndex = currentIndex;
                        this.updateVersesInput(versesContainer, versesInput);
                    });

                    grid.appendChild(btn);
                });
            } catch (error) {
                console.error('Error:', error);
                grid.innerHTML = '<div class="col-span-full py-2 text-center text-xs text-red-500">Erro ao carregar versículos.</div>';
            }
        }

        isVerseSelected(verseNum, selectedString) {
            if (!selectedString) return false;

            // selectedString can be "1, 2, 5-10"
            const parts = selectedString.split(',').map(s => s.trim());
            const num = parseInt(verseNum);

            return parts.some(part => {
                if (part.includes('-')) {
                    const [start, end] = part.split('-').map(s => parseInt(s));
                    return num >= start && num <= end;
                }
                return parseInt(part) === num;
            });
        }

        updateVersesInput(container, input) {
            const selected = Array.from(container.querySelectorAll('.bg-blue-600')).map(btn => btn.dataset.verseNum);
            // Format ranges e.g. 1-3, 5, 8-10
            // Simple comma separated for now? "1, 2, 3, 5"
            // Or better: try to form ranges

            selected.sort((a, b) => parseInt(a) - parseInt(b));

            // Logic to rangeify
            let ranges = [];
            let start = null;
            let end = null;

            for (let i = 0; i < selected.length; i++) {
                let num = parseInt(selected[i]);
                if (start === null) {
                    start = num;
                    end = num;
                } else if (num === end + 1) {
                    end = num;
                } else {
                    ranges.push(start === end ? `${start}` : `${start}-${end}`);
                    start = num;
                    end = num;
                }
            }
            if (start !== null) {
                ranges.push(start === end ? `${start}` : `${start}-${end}`);
            }

            input.value = ranges.join(', ');
        }

        updateIndexes() {
            const container = document.getElementById('bible-references-container');
            if (!container) return;

            container.querySelectorAll('.bible-reference-item').forEach((item, index) => {
                item.querySelectorAll('[name]').forEach(field => {
                    const name = field.getAttribute('name');
                    if (name) {
                        const newName = name.replace(/\[\d+\]/, `[${index}]`);
                        field.setAttribute('name', newName);
                        field.id = newName.replace(/[\[\]]/g, '_');
                    }
                });
                // labels update omitted for brevity
            });
        }
    }

    /**
     * Tag Manager
     */
    class TagManager {
        constructor() {
            this.selectedTags = new Set();
            this.init();
        }

        init() {
            const tagInput = document.getElementById('tag-input');
            const addTagBtn = document.getElementById('add-tag');
            const tagsContainer = document.getElementById('selected-tags');

            if (tagInput && addTagBtn) {
                addTagBtn.addEventListener('click', () => this.addTag(tagInput.value));
                tagInput.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        this.addTag(tagInput.value);
                    }
                });
            }

            // Initialize existing tags
            document.querySelectorAll('[data-tag-id]').forEach(el => {
                const tagId = el.getAttribute('data-tag-id');
                if (tagId) this.selectedTags.add(tagId);
            });
        }

        addTag(tagName) {
            if (!tagName || this.selectedTags.has(tagName)) return;

            this.selectedTags.add(tagName);
            const container = document.getElementById('selected-tags');
            if (!container) return;

            const tagEl = document.createElement('span');
            tagEl.className = 'inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100 mr-2 mb-2';
            tagEl.innerHTML = `
                ${tagName}
                <button type="button" class="ml-2 text-blue-600 hover:text-blue-800" onclick="this.parentElement.remove();">
                    ×
                </button>
                <input type="hidden" name="tags[]" value="${tagName}">
            `;
            container.appendChild(tagEl);
        }
    }

    /**
     * Sermon Editor
     */
    class SermonEditor {
        constructor() {
            this.init();
        }

        init() {
            // Auto-save draft (optional)
            const form = document.getElementById('sermon-form');
            if (form) {
                let saveTimeout;
                form.addEventListener('input', () => {
                    clearTimeout(saveTimeout);
                    saveTimeout = setTimeout(() => {
                        // Auto-save logic can be implemented here
                    }, 5000);
                });
            }

            // Character counters
            const titleInput = document.getElementById('title');
            const descriptionTextarea = document.getElementById('description');

            if (titleInput) {
                titleInput.addEventListener('input', (e) => {
                    const counter = document.getElementById('title-counter');
                    if (counter) {
                        counter.textContent = `${e.target.value.length}/255`;
                    }
                });
            }

            if (descriptionTextarea) {
                descriptionTextarea.addEventListener('input', (e) => {
                    const counter = document.getElementById('description-counter');
                    if (counter) {
                        counter.textContent = `${e.target.value.length} caracteres`;
                    }
                });
            }
        }
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            new BibleReferenceManager();
            new TagManager();
            new SermonEditor();
        });
    } else {
        new BibleReferenceManager();
        new TagManager();
        new SermonEditor();
    }
})();
