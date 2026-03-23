export default function projectionConsole() {
    return {
        // Init properties from DOM data attributes
        userId: null,
        apiToken: null,
        syncUrl: null,
        setlistId: null,

        // Status
        isConnected: false,
        pendingSyncs: 0,

        // Data Models
        themes: [],
        setlist: null,
        timeline: [],
        searchTimeline: '',

        // UI State
        currentTimelineId: null, // Selected but not necessarily live
        currentSlideIndex: -1,   // Selected slide

        liveTimelineId: null, // Actually live

        // Projection Target State (The Payload)
        state: {
            theme: 'default',
            isBlackout: false,
            content: '',
            footer: '',
            bgUrl: '',
            media_url: '',
            type: 'clear', // 'slide', 'video', 'image', 'countdown', 'alert', 'clear', 'logo'
        },

        // Custom Inputs
        customAlertText: '',

        init() {
            const container = document.getElementById('projection-app') || document.body;
            this.userId = container.dataset.userId;
            this.apiToken = container.dataset.apiToken;
            this.syncUrl = container.dataset.syncUrl;
            this.setlistId = container.dataset.setlistId;

            this.setupHotkeys();
            this.loadInitialData();

            // Starts continuous loop to sync and check connection
            this.pollConnection();
        },

        get filteredTimeline() {
            if (!this.searchTimeline) return this.timeline;
            const term = this.searchTimeline.toLowerCase();
            return this.timeline.filter(i =>
                (i.title && i.title.toLowerCase().includes(term)) ||
                (i.artist && i.artist.toLowerCase().includes(term))
            );
        },

        get currentItem() {
            return this.timeline.find(i => i.id === this.currentTimelineId) || null;
        },

        get slides() {
            return this.currentItem?.slides || [];
        },

        get currentThemeName() {
            const theme = this.themes.find(t => t.slug === this.state.theme);
            return theme ? theme.name : 'Padrão';
        },

        // Setup Keyboard Navigation
        setupHotkeys() {
            window.addEventListener('keydown', (e) => {
                // Ignore if typing in inputs
                if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;

                if (e.key === 'ArrowRight' || e.key === 'ArrowDown') {
                    e.preventDefault();
                    this.navigateSlide(1);
                } else if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') {
                    e.preventDefault();
                    this.navigateSlide(-1);
                } else if (e.key === ' ') {
                    e.preventDefault();
                    this.navigateSlide(1);
                } else if (e.key === 'Escape') {
                    e.preventDefault();
                    this.clearLayer('all');
                }
            });
        },

        // API Calls
        async loadInitialData() {
            try {
                // Determine API endpoint base manually or use window.location
                const baseUrl = window.location.origin;

                // Fetch Setlist Data
                if (this.setlistId) {
                    const res = await fetch(`${baseUrl}/api/v1/projection/setlists/${this.setlistId}`, {
                        headers: { 'Authorization': `Bearer ${this.apiToken}`, 'Accept': 'application/json' }
                    });
                    if (res.ok) {
                        const json = await res.json();
                        this.setlist = json.data;
                        this.timeline = this.extractTimeline(json.data);
                        if (this.timeline.length > 0) {
                            this.selectTimelineItem(this.timeline[0]);
                        }
                    }
                }

                // Fetch Themes
                const themeRes = await fetch(`${baseUrl}/api/v1/projection/themes`, {
                    headers: { 'Authorization': `Bearer ${this.apiToken}`, 'Accept': 'application/json' }
                });
                if (themeRes.ok) {
                    const themeJson = await themeRes.json();
                    this.themes = themeJson.data || [];
                }

                // Initial State Sync Pull
                await this.pullState();

            } catch (error) {
                console.error('Failed to load initial data:', error);
            }
        },

        extractTimeline(setlist) {
            if (!setlist || !setlist.items) return [];
            return setlist.items.map(i => {
                let title = i.title || 'Item';
                let artist = i.notes || '';
                let type = 'song';
                let slides = [];

                if (i.song) {
                    title = i.song.title;
                    artist = i.song.artist || '';
                    slides = this.parseSongSlides(i.song.lyrics);
                } else if (i.type === 'media') {
                    type = 'media';
                    artist = 'Mídia / Vídeo';
                    slides = [{ bg_url: i.media_url || i.url, label: 'Mídia' }];
                } else {
                    type = i.type || 'text';
                    slides = [{ html: i.title, label: 'Slide Único' }];
                }

                return {
                    id: i.id,
                    title,
                    artist,
                    type,
                    type_label: i.type_label || 'Louvor',
                    slides
                };
            });
        },

        parseSongSlides(lyrics) {
            if (!lyrics) return [];
            // Parse chordpro style or simple blank line sections
            const sections = lyrics.split(/\n\s*\n/);
            return sections.map((sec, idx) => {
                // Strip chords [A]
                let text = sec.replace(/\[.*?\]/g, '');
                // Convert newlines to bl tags for preview
                let html = text.replace(/\n/g, '<br>');
                return { text, html, label: `Slide ${idx + 1}` };
            }).filter(s => s.text.trim() !== '');
        },

        // Polling loop to ensure connection and fetch changes from other remotes
        async pollConnection() {
            setInterval(async () => {
                if (this.pendingSyncs > 0) return; // Don't pull while pushing
                await this.pullState();
            }, 3000); // Poll slower, push instantly. Desktop app does the heavy lifting.
        },

        async pullState() {
            if (!this.syncUrl) return;
            try {
                const res = await fetch(this.syncUrl, {
                    headers: { 'Authorization': `Bearer ${this.apiToken}`, 'Accept': 'application/json' }
                });
                if (res.ok) {
                    const json = await res.json();
                    if (json && json.state) {
                        this.state = Object.assign({}, this.state, json.state);
                    }
                    this.isConnected = true;
                } else {
                    this.isConnected = false;
                }
            } catch (e) {
                this.isConnected = false;
            }
        },

        // 0-Delay Push Engine
        async pushState() {
            if (!this.syncUrl) return;
            this.pendingSyncs++;
            try {
                const res = await fetch(this.syncUrl, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${this.apiToken}`,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ state: this.state })
                });
                if (res.ok) {
                    this.isConnected = true;
                } else {
                    this.isConnected = false;
                }
            } catch (e) {
                this.isConnected = false;
            } finally {
                this.pendingSyncs--;
            }
        },

        // Actions
        selectTimelineItem(item) {
            this.currentTimelineId = item.id;
            this.currentSlideIndex = -1;
            // Does not auto-trigger slide unless settings say so.
        },

        triggerSlide(index) {
            if (!this.slides || index < 0 || index >= this.slides.length) return;

            this.currentSlideIndex = index;
            this.liveTimelineId = this.currentTimelineId;

            const slide = this.slides[index];
            this.state.type = slide.bg_url ? 'media' : 'slide';
            this.state.content = slide.html || slide.text || '';

            if (slide.bg_url) {
                this.state.bgUrl = slide.bg_url;
            }

            // Un-blackout automatically on slide fire
            this.state.isBlackout = false;

            this.pushState();
        },

        navigateSlide(dir) {
            if (!this.slides || this.slides.length === 0) return;
            let next = this.currentSlideIndex + dir;

            // Loop array or stop at edges
            if (next >= this.slides.length) {
                // Optionally move to next timeline item
                const idx = this.timeline.findIndex(i => i.id === this.currentTimelineId);
                if (idx !== -1 && idx < this.timeline.length - 1) {
                    this.selectTimelineItem(this.timeline[idx + 1]);
                    this.currentSlideIndex = -1;
                    return;
                }
                next = this.slides.length - 1;
            }
            if (next < 0) {
                // Optionally move to previous timeline item
                const idx = this.timeline.findIndex(i => i.id === this.currentTimelineId);
                if (idx > 0) {
                    this.selectTimelineItem(this.timeline[idx - 1]);
                    this.currentSlideIndex = -1;
                    return; // Wait for user to trigger slide manually from prev song
                }
                next = 0;
            }

            this.triggerSlide(next);

            // Scroll to view
            setTimeout(() => {
                const grid = document.getElementById('slides-grid');
                if (grid && grid.children[next]) {
                    grid.children[next].scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
            }, 50);
        },

        toggleBlackout() {
            this.state.isBlackout = !this.state.isBlackout;
            this.pushState();
        },

        clearLayer(layer) {
            if (layer === 'all') {
                this.state.type = 'clear';
                this.state.content = '';
                this.state.footer = '';
                this.state.bgUrl = '';
                this.state.isBlackout = false;
                this.currentSlideIndex = -1;
                this.liveTimelineId = null;
            } else if (layer === 'text') {
                this.state.content = '';
                this.currentSlideIndex = -1;
            } else if (layer === 'alert') {
                this.state.type = 'slide';
                this.customAlertText = '';
            }
            this.pushState();
        },

        triggerLogo() {
            this.state.type = 'logo';
            this.state.content = '';
            this.state.bgUrl = '';
            this.state.isBlackout = false;
            this.currentSlideIndex = -1;
            this.liveTimelineId = null;
            this.pushState();
        },

        triggerCountdown(seconds, text) {
            this.state.type = 'countdown';
            // Mock countdown handling for now. In real projection, we send start time.
            this.state.content = String(seconds);
            this.state.footer = text;
            this.state.isBlackout = false;
            this.pushState();
        },

        sendAlert() {
            if (!this.customAlertText.trim()) return;
            this.state.type = 'alert';
            this.state.content = this.customAlertText;
            this.pushState();
        },

        applyTheme(themeSlug) {
            this.state.theme = themeSlug;
            this.pushState();
        },

        // Helpers
        getTimelineIcon(type) {
            switch (type) {
                case 'media': return 'fa-solid fa-play-circle';
                case 'song': return 'fa-solid fa-music';
                case 'sermon': return 'fa-solid fa-book-bible';
                case 'prayer': return 'fa-solid fa-hands-praying';
                case 'announcement': return 'fa-solid fa-bullhorn';
                default: return 'fa-solid fa-bars-staggered';
            }
        },

        isVideo(url) {
            if (!url) return false;
            return !!url.match(/\.(mp4|webm|mov)$/i);
        }
    }
}

document.addEventListener('alpine:init', () => {
    Alpine.data('projectionConsole', projectionConsole);
});
