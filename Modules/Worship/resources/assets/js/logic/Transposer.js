export default class Transposer {
    constructor() {
        this.keys = ['C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B'];
        this.flatKeys = ['C', 'Db', 'D', 'Eb', 'E', 'F', 'Gb', 'G', 'Ab', 'A', 'Bb', 'B'];
    }

    calculateTransposedNote(note, steps) {
        if (!note) return '';
        let useFlats = note.includes('b');
        let baseKeys = useFlats ? this.flatKeys : this.keys;
        let index = baseKeys.indexOf(note);

        if (index === -1) {
             index = this.keys.indexOf(note);
             if(index === -1) index = this.flatKeys.indexOf(note);
        }

        if(index === -1) return note;

        let newIndex = (index + steps) % 12;
        if (newIndex < 0) newIndex += 12;

        return baseKeys[newIndex];
    }

    transposeChord(chord, steps) {
        // Handle slash chords internally if passed as full string?
        // RehearsalRoom splits slash chords before calling this, so this handles "Am7" or "C"
        const match = chord.match(/^([A-G][b#]?)(.*)/);
        if (match) {
            const newRoot = this.calculateTransposedNote(match[1], steps);
            return newRoot + match[2];
        }
        return chord;
    }

    transposeElement(element, steps) {
        if (!element.dataset.original) {
            element.dataset.original = element.innerText.trim();
        }

        const original = element.dataset.original;

        if (original.includes('/')) {
            const parts = original.split('/');
            const newRoot = this.transposeChord(parts[0], steps);
            const newBass = parts[1] ? this.transposeChord(parts[1], steps) : '';
            element.innerText = `${newRoot}/${newBass}`;
        } else {
            element.innerText = this.transposeChord(original, steps);
        }
    }

    transposeAll(selector, steps) {
        const chords = document.querySelectorAll(selector);
        chords.forEach(el => {
            this.transposeElement(el, steps);
        });
    }
}
