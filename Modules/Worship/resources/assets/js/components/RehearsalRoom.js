import Transposer from '../logic/Transposer';
import AutoScroller from '../logic/AutoScroller';

export default () => ({
    view: 'list',
    selectedSongIndex: 0,
    currentTransposition: 0,
    currentKey: '',
    selectedInstrument: 'all',
    showChords: true,
    zenMode: false,
    fontSize: 1.35,
    songs: [],
    scrolling: false,
    scrollSpeed: 1,
    metronomeActive: false,
    isBeat: false,

    transposer: null,
    autoScroller: null,

    init() {
        this.transposer = new Transposer();
        this.autoScroller = new AutoScroller(() => this.scrollSpeed * 100);
    },

    setSongs(songs) {
        this.songs = songs;
    },

    get selectedSong() {
        return this.songs[this.selectedSongIndex] || null;
    },

    toggleScroll() {
        this.scrolling = !this.scrolling;
        if (this.scrolling) {
            this.autoScroller.start();
        } else {
            this.autoScroller.stop();
        }
    },

    toggleMetronome() {
        this.metronomeActive = !this.metronomeActive;
        if (this.metronomeActive && this.selectedSong?.bpm) {
            const interval = (60 / this.selectedSong.bpm) * 1000;
            this.beatInterval = setInterval(() => {
                this.isBeat = true;
                setTimeout(() => { this.isBeat = false; }, 100);
            }, interval);
        } else {
            clearInterval(this.beatInterval);
            this.isBeat = false;
        }
    },

    get currentSongHtml() {
        if (!this.selectedSong) return '';
        return this.render(this.selectedSong.content, this.currentTransposition);
    },

    openStudy(index) {
        this.stopAll();
        this.selectedSongIndex = index;
        this.currentTransposition = 0;
        this.selectedInstrument = 'all';
        this.showChords = true;
        this.updateKey();
        this.view = 'study';
        window.scrollTo({ top: 0, behavior: 'smooth' });
    },

    backToList() {
        this.stopAll();
        this.view = 'list';
        setTimeout(() => window.scrollTo({ top: 0, behavior: 'smooth' }), 50);
    },

    stopAll() {
        this.autoScroller.stop();
        clearInterval(this.beatInterval);
        this.scrolling = false;
        this.metronomeActive = false;
        this.isBeat = false;
    },

    transpose(val) {
        this.currentTransposition += val;
        this.updateKey();
    },

    updateKey() {
        if (!this.selectedSong) return;
        const baseKey = this.selectedSong.plannedKey || this.selectedSong.original_key;
        this.currentKey = this.transposer.transposeChord(baseKey, this.currentTransposition);
    },

    render(content, transpose) {
        const lines = content.split('\n');
        let html = '';

        lines.forEach(line => {
            let text = line.trim();
            if (text.startsWith('{')) return;

            if (text.match(/^\[(Chorus|Verse|Bridge|Intro|Outro|Refrão|Verso|Ponte|Final|Solo|Interlúdio|Instrumental).*\]$/i)) {
                html += `<div class="section-header">${text.replace(/[\[\]]/g, '')}</div>`;
                return;
            }

            // Process instrument specific cues
            line = line.replace(/\[(VOC|GTR|BAS|DRM|KEY):([^\]]+)\]/g, (match, inst, cue) => {
                const colors = { 'VOC': 'voc', 'GTR': 'gtr', 'BAS': 'bas', 'DRM': 'drm', 'KEY': 'key' };
                let classes = `inst-cue cue-${colors[inst]}`;
                if (this.selectedInstrument !== 'all') {
                    classes += (this.selectedInstrument === inst) ? ' cue-active' : ' cue-dim';
                }
                return `<span class="${classes}">${cue.trim()}</span>`;
            });

            if (line.includes('[')) {
                html += '<div class="lyric-line">';
                let mappedLine = line.replace(/\[([^\]]+)\]/g, (match, chord) => {
                    if (chord.includes(':')) return match;
                    let tChord = chord.split('/').map(c => this.transposer.transposeChord(c, transpose)).join('/');
                    return `<span class="chord-wrapper"><span class="chord-text">${tChord}</span></span>`;
                });
                html += mappedLine;
                html += '</div>';
            } else if (text === '') {
                html += '<div style="height: 1.5rem"></div>';
            } else {
                html += `<div class="lyric-line">${line}</div>`;
            }
        });
        return html;
    }
});
