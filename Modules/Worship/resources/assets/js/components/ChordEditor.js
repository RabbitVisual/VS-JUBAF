export default (initialContent = '') => ({
    content: initialContent,
    previewHtml: '',

    init() {
        this.$watch('content', () => this.updatePreview());
        this.updatePreview();
    },

    async updatePreview() {
        if (!this.content) {
            this.previewHtml = '';
            return;
        }

        try {
            // Clientside basic highlighting logic
            // Using local parser
            this.previewHtml = this.parseChordPro(this.content);
        } catch (error) {
            console.error('Highlighting failed', error);
        }
    },

    parseChordPro(text) {
        if (!text) return '';

        const lines = text.split('\n');
        let html = '';

        lines.forEach(line => {
            line = line.trim();
            if (!line) {
                html += '<div class="h-4"></div>';
                return;
            }

            // Section Header (e.g., [Chorus])
            if (line.match(/^\[[^\]]+\]$/)) {
                html += `<div class="font-bold text-amber-500 uppercase text-xs mt-6 mb-2 tracking-widest border-b border-gray-800 pb-1">${line.replace(/[\[\]]/g, '')}</div>`;
                return;
            }

            // Lyrics + Chords processing
            if (line.includes('[')) {
                html += '<div class="relative flex flex-wrap mt-6 leading-loose min-h-[2.5rem]">';
                const parts = line.split(/(\[[^\]]+\])/).filter(Boolean);
                parts.forEach(part => {
                    if (part.startsWith('[')) {
                        const chord = part.replace(/[\[\]]/g, '');
                        html += `<span class="inline-flex flex-col relative mr-1"><span class="absolute -top-5 text-amber-400 font-bold text-sm tracking-tighter bg-black px-1 rounded-sm">${chord}</span>`;
                    } else {
                        // Avoid wrapping empty text
                        if (part.trim().length > 0 || part === ' ') {
                            html += `<span class="text-gray-300 font-medium">${part}</span></span>`;
                        } else {
                            html += '</span>';
                        }
                    }
                });
                html += '</div>';
            } else {
                html += `<div class="text-gray-400 font-medium mt-1">${line}</div>`;
            }
        });

        return html;
    }
});
