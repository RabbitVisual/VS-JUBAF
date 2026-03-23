export default (initialBpm = 120) => ({
    bpm: initialBpm,
    isActive: false,
    audioContext: null,
    nextNoteTime: 0,
    timerID: null,

    init() {
        this.$watch('isActive', (val) => {
            if (val) this.start();
            else this.stop();
        });
    },

    start() {
        if (!this.audioContext) this.audioContext = new (window.AudioContext || window.webkitAudioContext)();
        this.nextNoteTime = this.audioContext.currentTime;
        this.scheduler();
    },

    stop() {
        clearTimeout(this.timerID);
    },

    scheduler() {
        while (this.nextNoteTime < this.audioContext.currentTime + 0.1) {
            this.playClick();
            this.nextNoteTime += 60.0 / this.bpm;
        }
        this.timerID = setTimeout(() => this.scheduler(), 25.0);
    },

    playClick() {
        const osc = this.audioContext.createOscillator();
        const envelope = this.audioContext.createGain();
        osc.frequency.value = 880;
        envelope.gain.value = 1;
        envelope.gain.exponentialRampToValueAtTime(1, this.nextNoteTime + 0.001);
        envelope.gain.exponentialRampToValueAtTime(0.001, this.nextNoteTime + 0.02);
        osc.connect(envelope);
        envelope.connect(this.audioContext.destination);
        osc.start(this.nextNoteTime);
        osc.stop(this.nextNoteTime + 0.03);
    }
});
