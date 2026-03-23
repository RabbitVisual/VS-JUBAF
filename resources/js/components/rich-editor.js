import Quill from 'quill';
import 'quill/dist/quill.snow.css';

export default () => ({
    editor: null,
    content: '',

    init() {
        // Prefer initial content from script template (reliable on edit); fallback to div innerHTML
        const fromTemplate = this.$refs.initialContent ? this.$refs.initialContent.innerHTML : '';
        this.content = (fromTemplate && fromTemplate.trim() !== '') ? fromTemplate : (this.$refs.editor ? this.$refs.editor.innerHTML : '');

        this.editor = new Quill(this.$refs.editor, {
            theme: 'snow',
            modules: {
                toolbar: {
                    container: [
                        [{ 'header': [1, 2, 3, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        ['blockquote', 'code-block'],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        [{ 'color': [] }, { 'background': [] }],
                        ['link', 'image', 'video'],
                        ['clean'],
                        ['bible-picker'],
                        ['bible-ref']
                    ],
                    handlers: {
                        'bible-picker': () => {
                            this.$dispatch('open-bible-picker');
                        },
                        'bible-ref': () => {
                            this.insertBibleRef();
                        },
                        'image': () => {
                            this.selectLocalImage();
                        }
                    }
                }
            },
            placeholder: 'Escreva seu sermão aqui...'
        });

        // Restore initial content into Quill (Quill replaces the div, so we re-apply)
        if (this.content) {
            this.editor.root.innerHTML = this.content;
        }

        // Sync content to hidden input for form submission
        this.editor.on('text-change', () => {
            this.content = this.editor.root.innerHTML;
            this.$dispatch('input', this.content);
        });

        // Add Bible Picker Icon to Toolbar
        const bibleBtn = this.$el.querySelector('.ql-bible-picker');
        if (bibleBtn) {
            bibleBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>';
        }
        const refBtn = this.$el.querySelector('.ql-bible-ref');
        if (refBtn) {
            refBtn.innerHTML = '<span class="ql-bold">@</span>';
            refBtn.title = 'Inserir referência (@ ex: João 3:16)';
        }

        // Remove duplicate toolbar(s): keep only the one with Book + @ (bible-picker)
        const removeDuplicateToolbars = () => {
            const all = document.querySelectorAll('.ql-toolbar');
            all.forEach((tb) => {
                if (!tb.querySelector('.ql-bible-picker')) tb.remove();
            });
        };
        removeDuplicateToolbars();
        requestAnimationFrame(removeDuplicateToolbars);
        setTimeout(removeDuplicateToolbars, 0);
        setTimeout(removeDuplicateToolbars, 150);

        // Listen for Bible Insert Events
        window.addEventListener('insert-bible-text', (event) => {
            const { text, reference } = event.detail;
            const range = this.getSafeRange();
            const html = `<blockquote class="bible-ref" data-bible-ref="${this.escapeHtml(reference)}" title="${this.escapeHtml(reference)}">${this.escapeHtml(text)} <strong>(${this.escapeHtml(reference)})</strong></blockquote><p><br></p>`;
            this.editor.clipboard.dangerouslyPasteHTML(range.index, html);
            this.editor.setSelection(range.index + 1, 0);
        });

        // Elias: aplicar formatação para o púlpito (substitui conteúdo do editor)
        window.addEventListener('elias-apply-format', (event) => {
            const html = event.detail?.html;
            if (this.editor && html != null) {
                this.editor.root.innerHTML = html;
                this.content = html;
            }
        });
    },

    getSafeRange() {
        if (!this.editor) return { index: 0, length: 0 };
        const r = this.editor.getSelection(true);
        if (r && typeof r.index === 'number' && r.index >= 0) return r;
        const len = this.editor.getLength();
        return { index: Math.max(0, len - 1), length: 0 };
    },

    escapeHtml(str) {
        if (str == null) return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    },

    async insertBibleRef() {
        const ref = window.prompt('Digite a referência (ex: João 3:16, Gênesis 1:1-3):');
        if (!ref || !ref.trim()) return;
        try {
            const res = await fetch('/api/v1/bible/find?ref=' + encodeURIComponent(ref.trim()));
            const json = await res.json();
            if (!res.ok || !json.data) {
                alert(json.message || 'Referência não encontrada.');
                return;
            }
            const d = json.data;
            const text = (d.verses || []).map(v => v.text || '').filter(Boolean).join(' ');
            const reference = d.reference || ref.trim();
            const range = this.getSafeRange();
            const html = `<blockquote class="bible-ref" data-bible-ref="${this.escapeHtml(reference)}" title="${this.escapeHtml(reference)}">${this.escapeHtml(text)} <strong>(${this.escapeHtml(reference)})</strong></blockquote><p><br></p>`;
            this.editor.clipboard.dangerouslyPasteHTML(range.index, html);
            this.editor.setSelection(range.index + 1, 0);
        } catch (e) {
            alert('Erro ao buscar referência.');
        }
    },

    selectLocalImage() {
        const input = document.createElement('input');
        input.setAttribute('type', 'file');
        input.setAttribute('accept', 'image/*');
        input.click();

        input.onchange = async () => {
            const file = input.files[0];
            if (/^image\//.test(file.type)) {
                this.uploadImage(file);
            } else {
                console.warn('You could only upload images.');
            }
        };
    },

    async uploadImage(file) {
        const formData = new FormData();
        formData.append('image', file);

        // Add CSRF token
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        try {
            const response = await fetch('/sermon-images/upload', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': token
                }
            });

            if (response.ok) {
                const data = await response.json();
                const range = this.getSafeRange();
                this.editor.insertEmbed(range.index, 'image', data.url);
                this.editor.setSelection(range.index + 1, 0);
            } else {
                console.error('Image upload failed');
                alert('Falha no upload da imagem.');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Erro ao enviar imagem.');
        }
    }
});
