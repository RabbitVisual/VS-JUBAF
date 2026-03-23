export default class AutoScroller {
    constructor(getBpmCallback) {
        this.isScrolling = false;
        this.scrollSpeed = 1.0;
        this.baseScrollSpeed = 0.8;
        this.animationFrameId = null;
        this.getBpm = getBpmCallback;
    }

    toggle() {
        this.isScrolling = !this.isScrolling;
        if (this.isScrolling) {
            this.scrollLoop();
        } else {
            this.stop();
        }
        return this.isScrolling;
    }

    start() {
        if (!this.isScrolling) {
            this.isScrolling = true;
            this.scrollLoop();
        }
    }

    stop() {
        this.isScrolling = false;
        if (this.animationFrameId) {
            cancelAnimationFrame(this.animationFrameId);
            this.animationFrameId = null;
        }
    }

    scrollLoop() {
        if (!this.isScrolling) return;

        const currentBpm = this.getBpm();
        const bpmFactor = currentBpm > 0 ? (currentBpm / 100) : 1;
        const amount = this.baseScrollSpeed * this.scrollSpeed * bpmFactor;

        window.scrollBy(0, amount);

        if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 10) {
            this.isScrolling = false;
            this.animationFrameId = null;
        } else {
            this.animationFrameId = requestAnimationFrame(() => this.scrollLoop());
        }
    }
}
