<?php

namespace Modules\Worship\App\Services;

use Modules\Worship\App\Enums\MusicalKey;

class KeyTransposer
{
    protected array $notes = ['C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B'];
    protected array $flatMap = ['Db' => 'C#', 'Eb' => 'D#', 'Gb' => 'F#', 'Ab' => 'G#', 'Bb' => 'A#'];

    /**
     * Transpose a chord by a specific number of semitones.
     */
    public function transposeChord(string $chord, int $semitones): string
    {
        if ($semitones === 0) return $chord;

        // Extract root and suffix (e.g., C#m7 -> root: C#, suffix: m7)
        if (!preg_match('/^([A-G][#b]?)(.*)$/', $chord, $matches)) {
            return $chord;
        }

        $root = $matches[1];
        $suffix = $matches[2];

        // Normalize flats
        if (isset($this->flatMap[$root])) {
            $root = $this->flatMap[$root];
        }

        $index = array_search($root, $this->notes);
        if ($index === false) return $chord;

        $newIndex = ($index + $semitones) % 12;
        if ($newIndex < 0) $newIndex += 12;

        return $this->notes[$newIndex] . $suffix;
    }

    /**
     * Calculate semitones between two keys.
     */
    public function getSemitonesDistance(MusicalKey $from, MusicalKey $to): int
    {
        return $to->getIndex() - $from->getIndex();
    }

    /**
     * Transpose whole ChordPro content.
     */
    public function transposeContent(string $content, MusicalKey $from, MusicalKey $to): string
    {
        $semitones = $this->getSemitonesDistance($from, $to);
        if ($semitones === 0) return $content;

        return preg_replace_callback('/\[([^\]]+)\]/', function($matches) use ($semitones) {
            $chord = $matches[1];
            // Skip section headers like [Chorus] or [Verse 1]
            // Heuristic: Chords usually start with A-G
            if (preg_match('/^[A-G]/', $chord)) {
                return '[' . $this->transposeChord($chord, $semitones) . ']';
            }
            return $matches[0];
        }, $content);
    }
}
