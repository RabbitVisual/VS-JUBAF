<?php

namespace Modules\Worship\App\Services;

use Modules\Worship\App\Enums\MusicalKey;

/**
 * Import ChordPro (.cho / .pro) content into WorshipSong data.
 * Preserves full ChordPro structure (chords, sections, line breaks) and normalizes
 * directives like {start_of_chorus}/{end_of_chorus} so ChordProEngine can render correctly.
 */
class ChordProImporter
{
    /** Map flat keys to MusicalKey (sharp) for enum. */
    private const KEY_ALIASES = [
        'C' => 'C', 'D' => 'D', 'E' => 'E', 'F' => 'F', 'G' => 'G', 'A' => 'A', 'B' => 'B',
        'Db' => 'C#', 'Eb' => 'D#', 'Gb' => 'F#', 'Ab' => 'G#', 'Bb' => 'A#',
        'C#' => 'C#', 'D#' => 'D#', 'F#' => 'F#', 'G#' => 'G#', 'A#' => 'A#',
    ];

    /**
     * Parse ChordPro file content and return data suitable for WorshipSong.
     * content_chordpro is the full file normalized for display (soc/eoc → section headers).
     *
     * @return array{title: string, artist: string, content_chordpro: string, lyrics_only: string, original_key?: MusicalKey}|null
     */
    public function parse(string $content): ?array
    {
        $content = str_replace(["\r\n", "\r"], "\n", trim($content));
        if ($content === '') {
            return null;
        }

        $title = '';
        $artist = 'Unknown';
        $keyRaw = null;
        $lines = explode("\n", $content);

        // First pass: extract metadata from directives
        foreach ($lines as $line) {
            $line = rtrim($line);
            if (preg_match('/^\{([^:}]+):?\s*(.*)\}\s*$/', $line, $m)) {
                $tag = strtolower(trim($m[1]));
                $value = trim($m[2] ?? '');
                if (in_array($tag, ['title', 't'], true)) {
                    $title = $value;
                } elseif (in_array($tag, ['author', 'artist', 'composer', 'lyricist'], true)) {
                    $artist = $value ?: $artist;
                } elseif (in_array($tag, ['key'], true) && $value !== '') {
                    $keyRaw = $value;
                }
            }
        }

        // Normalize content for ChordProEngine: preserve all lines but convert soc/eoc to [Chorus]/[Bridge] etc.
        $normalizedLines = [];
        foreach ($lines as $line) {
            $line = rtrim($line);
            if (preg_match('/^\{([^:}]+):?\s*(.*)\}\s*$/', $line, $m)) {
                $tag = strtolower(trim($m[1]));
                $value = trim($m[2] ?? '');
                if (in_array($tag, ['start_of_chorus', 'soc'], true)) {
                    $normalizedLines[] = '[Chorus]';
                    continue;
                }
                if (in_array($tag, ['end_of_chorus', 'eoc'], true)) {
                    $normalizedLines[] = '';
                    continue;
                }
                if (in_array($tag, ['start_of_bridge', 'sob'], true)) {
                    $normalizedLines[] = '[Bridge]';
                    continue;
                }
                if (in_array($tag, ['end_of_bridge', 'eob'], true)) {
                    $normalizedLines[] = '';
                    continue;
                }
                if (in_array($tag, ['start_of_verse', 'sov'], true)) {
                    $normalizedLines[] = '[Verse]';
                    continue;
                }
                if (in_array($tag, ['end_of_verse', 'eov'], true)) {
                    $normalizedLines[] = '';
                    continue;
                }
                // Keep comment directives so ChordProEngine can show them
                if (in_array($tag, ['c', 'comment', 'comment_italic', 'ci'], true)) {
                    $normalizedLines[] = $line;
                    continue;
                }
                // Drop other metadata directives (title, author, key) from body to avoid clutter; they're in sidebar
                if (in_array($tag, ['title', 't', 'author', 'artist', 'key', 'composer', 'lyricist'], true)) {
                    continue;
                }
                $normalizedLines[] = $line;
            } elseif (preg_match('/^#/', $line)) {
                continue;
            } else {
                $normalizedLines[] = $line;
            }
        }

        $contentChordpro = implode("\n", $normalizedLines);
        if (trim($contentChordpro) === '') {
            return null;
        }

        if ($title === '') {
            foreach ($normalizedLines as $l) {
                $t = trim($l);
                if ($t === '') continue;
                if (preg_match('/^\[([^\]]+)\]$/', $t, $m)) {
                    $title = $m[1];
                } else {
                    $title = mb_substr($t, 0, 80);
                }
                break;
            }
        }
        if ($title === '') {
            $title = 'Sem título';
        }

        $engine = app(ChordProEngine::class);
        $lyricsOnly = $engine->extractLyrics($contentChordpro);
        $lyricsOnly = preg_replace('/\s+/', ' ', trim($lyricsOnly));

        $originalKey = $this->parseKey($keyRaw);

        $result = [
            'title' => $title,
            'artist' => $artist,
            'content_chordpro' => $contentChordpro,
            'lyrics_only' => $lyricsOnly,
        ];
        if ($originalKey !== null) {
            $result['original_key'] = $originalKey;
        }
        return $result;
    }

    private function parseKey(?string $keyRaw): ?MusicalKey
    {
        if ($keyRaw === null || $keyRaw === '') {
            return null;
        }
        $key = trim($keyRaw);
        $key = preg_replace('/\s*maj(or)?$/i', '', $key);
        $key = preg_replace('/\s*min(or)?$/i', 'm', $key);
        $root = preg_replace('/m$/i', '', $key);
        $root = trim($root);
        $normalized = self::KEY_ALIASES[$root] ?? $root;
        return MusicalKey::tryFrom($normalized);
    }
}
