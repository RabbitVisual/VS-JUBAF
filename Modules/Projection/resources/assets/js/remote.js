/**
 * Projection Remote — Alpine.js app (sem CDN, bundle local).
 */
import Alpine from 'alpinejs';

function projectionRemote() {
    const csrfToken = () => (document.querySelector('meta[name="csrf-token"]') || {}).content || '';
    const credentials = 'same-origin';

    return {
        state: {},
        setlistData: null,
        setlists: [],
        themes: [],
        loading: false,
        errorMessage: '',
        formatSetlistLabel(s) {
            if (!s) return '';
            const d = s.scheduled_at ? new Date(s.scheduled_at).toLocaleDateString('pt-BR') : '';
            return (d ? d + ' — ' : '') + (s.title || 'Setlist');
        },
        onSetlistSelect(id) {
            if (!id) return;
            this.fetchSetlist(id);
        },
        get liveTitle() { return this.state.current_item_title || null; },
        get currentItemId() { return this.state.current_item_id ?? null; },
        get currentSlideIndex() { return this.state.current_slide_index ?? 0; },
        get setlistItems() { return this.setlistData?.items ?? []; },
        get currentSlides() {
            if (!this.setlistData?.items) return [];
            const item = this.setlistData.items.find(i => i.id == this.state.current_item_id || String(i.id) === String(this.state.current_item_id));
            return item?.slides ?? [];
        },
        get currentItemIndex() {
            const items = this.setlistData?.items ?? [];
            const id = this.state.current_item_id;
            if (id == null) return -1;
            const idx = items.findIndex(i => Number(i.id) === Number(id));
            return idx >= 0 ? idx : -1;
        },
        get slideLabel() {
            const slides = this.currentSlides;
            const idx = this.currentSlideIndex;
            if (slides.length === 0) return null;
            return `Slide ${idx + 1}/${slides.length}`;
        },
        get itemLabel() {
            const items = this.setlistItems;
            const idx = this.currentItemIndex;
            if (items.length === 0) return null;
            if (idx < 0) return `Item —/${items.length}`;
            return `Item ${idx + 1}/${items.length}`;
        },
        async fetchState() {
            try {
                this.errorMessage = '';
                const r = await fetch('/api/v1/projection/state', { credentials, headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
                const j = await r.json();
                this.state = (j && j.data) ? j.data : j;
                if (this.state.current_setlist_id && (!this.setlistData || Number(this.setlistData.id) !== Number(this.state.current_setlist_id))) {
                    this.fetchSetlist(this.state.current_setlist_id);
                }
            } catch (e) {
                this.errorMessage = 'Falha ao carregar estado.';
                console.error(e);
            }
        },
        async fetchSetlist(id) {
            try {
                this.errorMessage = '';
                const r = await fetch('/api/v1/worship/setlists/' + id, { credentials, headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
                const j = await r.json();
                this.setlistData = (j && j.data) ? j.data : j;
            } catch (e) {
                this.setlistData = null;
                this.errorMessage = 'Falha ao carregar setlist.';
            }
        },
        async fetchSetlists() {
            try {
                this.errorMessage = '';
                const r = await fetch('/api/v1/worship/setlists?limit=50', { credentials, headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
                const j = await r.json();
                this.setlists = (j && j.data) ? j.data : (j ?? []);
                if (this.setlists.length && this.state.current_setlist_id && !this.setlistData) {
                    this.fetchSetlist(this.state.current_setlist_id);
                }
            } catch (e) {
                this.setlists = [];
                this.errorMessage = 'Falha ao carregar cultos.';
            }
        },
        async fetchThemes() {
            const builtin = [
                { id: 'black', slug: 'black', name: 'Original Black' },
                { id: 'dark', slug: 'dark', name: 'Soft Dark' },
                { id: 'modern-dark', slug: 'modern-dark', name: 'Slate Blue' },
                { id: 'paper', slug: 'paper', name: 'Sepia Paper' },
                { id: 'ethereal', slug: 'ethereal', name: 'Ethereal' },
                { id: 'vibrant-worship', slug: 'vibrant-worship', name: 'Worship Green' }
            ];
            try {
                const r = await fetch('/api/v1/projection/themes', { credentials, headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
                const j = await r.json();
                const api = j.data ?? j ?? [];
                this.themes = [...builtin, ...api];
            } catch (e) { this.themes = builtin; }
        },
        onThemeChange(e) {
            const val = e.target.value;
            const t = this.themes.find((x) => String(x.slug || x.id) === String(val));
            this.sendState({ theme: val, theme_id: t && typeof t.id === 'number' ? t.id : null });
        },
        async sendState(payload) {
            const body = { ...this.state, ...payload };
            this.loading = true;
            this.errorMessage = '';
            try {
                const r = await fetch('/api/v1/projection/state', {
                    method: 'POST',
                    credentials,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken(),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(body)
                });
                const j = await r.json();
                this.state = (j && j.data) ? j.data : j;
            } catch (e) {
                this.errorMessage = 'Falha ao enviar.';
                console.error(e);
            } finally {
                this.loading = false;
            }
        },
        async nextSlide() {
            this.loading = true;
            this.errorMessage = '';
            try {
                const r = await fetch('/api/v1/projection/state/next-slide', {
                    method: 'POST',
                    credentials,
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken(), 'X-Requested-With': 'XMLHttpRequest' }
                });
                const j = await r.json();
                this.state = (j && j.data) ? j.data : j;
            } catch (e) {
                this.errorMessage = 'Falha ao avançar.';
                console.error(e);
            } finally {
                this.loading = false;
            }
        },
        async prevSlide() {
            this.loading = true;
            this.errorMessage = '';
            try {
                const r = await fetch('/api/v1/projection/state/prev-slide', {
                    method: 'POST',
                    credentials,
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken(), 'X-Requested-With': 'XMLHttpRequest' }
                });
                const j = await r.json();
                this.state = (j && j.data) ? j.data : j;
            } catch (e) {
                this.errorMessage = 'Falha ao voltar.';
                console.error(e);
            } finally {
                this.loading = false;
            }
        },
        goToSlide(index) {
            const items = this.setlistData?.items ?? [];
            const item = items.find(i => i.id == this.state.current_item_id);
            if (!item || !item.slides || !item.slides[index]) return;
            const s = item.slides[index];
            let payload = { current_slide_index: index, current_item_id: item.id, current_setlist_id: this.setlistData.id, current_item_title: item.title, isClear: false, isBlackout: false };
            if (s.media_type && s.url) { payload.type = s.media_type; payload.url = s.url; payload.content = ''; payload.footer = item.title; }
            else if (s.countdown_seconds !== undefined) { payload.type = 'countdown'; payload.countdown_seconds = s.countdown_seconds; payload.footer = s.label || item.title; payload.content = ''; }
            else { payload.type = 'slide'; payload.content = s.html || ''; payload.footer = item.type === 'bible' ? item.title : null; }
            this.sendState(payload);
        },
        goToItem(item) {
            if (!this.setlistData || !this.setlistData.items) return;
            const slide = item.slides && item.slides[0] ? item.slides[0] : null;
            let payload = { current_slide_index: 0, current_item_id: item.id, current_setlist_id: this.setlistData.id, current_item_title: item.title, isClear: false, isBlackout: false };
            if (slide) {
                if (slide.media_type && slide.url) { payload.type = slide.media_type; payload.url = slide.url; payload.content = ''; payload.footer = item.title; }
                else if (slide.countdown_seconds !== undefined) { payload.type = 'countdown'; payload.countdown_seconds = slide.countdown_seconds; payload.footer = slide.label || item.title; payload.content = ''; }
                else { payload.type = 'slide'; payload.content = slide.html || ''; payload.footer = item.type === 'bible' ? item.title : null; }
            } else {
                payload.type = 'slide';
                payload.content = '';
                payload.footer = null;
            }
            this.sendState(payload);
        },
        init() {
            this.fetchState();
            this.fetchSetlists();
            this.fetchThemes();
            setInterval(() => this.fetchState(), 2000);
        }
    };
}

Alpine.data('projectionRemote', projectionRemote);
window.Alpine = Alpine;
Alpine.start();
