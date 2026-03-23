import Transposer from '../logic/Transposer.js';
import AutoScroller from '../logic/AutoScroller.js';

export default (initialBpm, setlistItems = []) => ({
    // State
    bpm: initialBpm || 120,
    isScrolling: false,
    metronomeActive: true,
    showStageMessage: false,
    stageMessage: '',
    currentTranspose: 0,

    // Song Context
    activeSongId: null,
    currentKey: '-',
    nextSong: '-',

    // Modules
    transposer: new Transposer(),
    autoScroller: null,

    // Metronome
    beatState: 0, // 0 to 3
    lastBeatTime: 0,
    metronomeFrameId: null,

    // Data
    setlistItems: setlistItems,

    init() {
        console.log('Stage Viewer Mounted. BPM:', this.bpm);

        // Initialize AutoScroller with dynamic BPM accessor
        this.autoScroller = new AutoScroller(() => this.bpm);

        // Keyboard Shortcuts
        window.addEventListener('keydown', (e) => {
            if (e.code === 'Space') {
                e.preventDefault();
                this.toggleAutoScroll();
            }
        });

        // Broadcast Channel
        const channel = new BroadcastChannel('worship_projection');
        channel.onmessage = (event) => {
            if (event.data.type === 'alert') {
                this.stageMessage = event.data.message;
                this.showStageMessage = true;
                setTimeout(() => { this.showStageMessage = false; }, 8000);
            }
        };

        // Intersection Observer for Active Song
        this.setupObserver();

        // Start Metronome Loop
        this.startMetronome();
    },

    setupObserver() {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && entry.intersectionRatio > 0.3) {
                    const songId = entry.target.id.replace('song-', '');
                    this.setActiveSong(songId);
                }
            });
        }, {
            threshold: [0.3, 0.6] // Trigger when 30% of song is visible
        });

        // Wait a tick for DOM to populate? Alpine init runs after DOM ready usually.
        // But sections might be rendered by blade so they exist.
        setTimeout(() => {
            document.querySelectorAll('.song-section').forEach(section => {
                observer.observe(section);
            });
        }, 100);
    },

    setActiveSong(songId) {
        if (this.activeSongId == songId) return;

        this.activeSongId = songId;
        const item = this.setlistItems.find(i => i.id == songId);

        if (item) {
            // Update Context
            this.currentKey = item.key;
            // Optionally update BPM if valid
            if (item.bpm && item.bpm > 0) {
                this.bpm = item.bpm;
            }

            // Determine Next Song
            const currentIndex = this.setlistItems.indexOf(item);
            const nextItem = this.setlistItems[currentIndex + 1];
            this.nextSong = nextItem ? nextItem.title : 'Fim do Setlist';
        }
    },

    toggleAutoScroll() {
        this.isScrolling = this.autoScroller.toggle();
    },

    adjustSpeed(delta) {
        this.bpm = Math.max(40, Math.min(250, parseInt(this.bpm) + delta));
    },

    transpose(direction) {
        this.currentTranspose += direction;
        this.transposer.transposeAll('.chord', this.currentTranspose);
    },

    startMetronome() {
        const loop = (timestamp) => {
            if (!this.lastBeatTime) this.lastBeatTime = timestamp;

            const beatDuration = 60000 / this.bpm;

            if (timestamp - this.lastBeatTime >= beatDuration) {
                this.beatState = (this.beatState + 1) % 4;
                this.lastBeatTime = timestamp;
            }

            this.metronomeFrameId = requestAnimationFrame(loop);
        };
        this.metronomeFrameId = requestAnimationFrame(loop);
    }
});
