<?php

namespace Modules\Worship\App\Services;

class ChordProEngine
{
    /**
     * Parse ChordPro content into structured HTML.
     */
    public function toHtml(string $content): string
    {
        $lines = explode("\n", $content);
        $html = '';

        foreach ($lines as $line) {
            $line = trim($line);

            if (empty($line)) {
                $html .= '<div class="h-4"></div>';
                continue;
            }

            // Headers [Chorus], [Verse 1], [Refrão], etc.
            if (preg_match('/^\[([^\]]+)\]$/', $line, $matches)) {
                $title = htmlspecialchars($matches[1], ENT_QUOTES, 'UTF-8');
                $html .= "<div class=\"section-header\">{$title}</div>";
                continue;
            }

            // Directives {title: ...}, {key: ...} - mostly handled by metadata, but handle comments inline
            if (str_starts_with($line, '{') && str_ends_with($line, '}')) {
                // Parse Directive
                $content = trim($line, '{}');
                $parts = explode(':', $content, 2);
                $name = strtolower(trim($parts[0]));
                $value = isset($parts[1]) ? trim($parts[1]) : '';

                // Handle comments specifically
                if ($name === 'c' || $name === 'comment' || $name === 'comment_italic' || $name === 'ci') {
                    $html .= '<div class="chordpro-comment">' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '</div>';
                }

                // Other directives (soc, eoc, etc) could be handled here or pre-processed
                // For now, we strip others to prevent clutter
                continue;
            }

            // Lyrics with Chords
            if (str_contains($line, '[')) {
                $html .= '<div class="lyric-line flex flex-wrap items-end gap-x-1">'; // Flex container

                // Split by chords [CHORD]
                $parts = preg_split('/(\\[[^\\]]+\\])/', $line, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

                $pendingChord = null;

                foreach ($parts as $part) {
                    if (str_starts_with($part, '[')) {
                        // If we already have a pending chord (consecutive chords), flush it with empty lyric
                        if ($pendingChord) {
                            $cleanChord = htmlspecialchars(trim($pendingChord, '[]'), ENT_QUOTES, 'UTF-8');
                            $html .= '<div class="chord-group"><span class="chord">' . $cleanChord . '</span><span class="lyric">&nbsp;</span></div>';
                        }
                        $pendingChord = $part;
                    } else {
                        // It's a text part
                        if ($pendingChord) {
                            // Render chord + text group
                            $cleanChord = htmlspecialchars(trim($pendingChord, '[]'), ENT_QUOTES, 'UTF-8');
                            $html .= '<div class="chord-group">
                                        <span class="chord">' . $cleanChord . '</span>
                                        <span class="lyric">' . htmlspecialchars($part, ENT_QUOTES, 'UTF-8') . '</span>
                                      </div>';
                            $pendingChord = null;
                        } else {
                            // Standalone text (before any chord, or between chord groups)
                            $html .= '<span class="lyric-only">' . htmlspecialchars($part, ENT_QUOTES, 'UTF-8') . '</span>';
                        }
                    }
                }

                // Flush trailing chord if exists
                if ($pendingChord) {
                    $cleanChord = htmlspecialchars(trim($pendingChord, '[]'), ENT_QUOTES, 'UTF-8');
                    $html .= '<div class="chord-group"><span class="chord">' . $cleanChord . '</span><span class="lyric">&nbsp;</span></div>';
                }

                $html .= '</div>';
            } else {
                // Just lyrics
                $html .= '<div class="lyric-line lyric-only">' . htmlspecialchars($line, ENT_QUOTES, 'UTF-8') . '</div>';
            }
        }

        return $html;
    }

    /**
     * Extract plain lyrics from ChordPro content.
     */
    public function extractLyrics(string $content): string
    {
        // 1. Remove bracketed chords [C] and directives {key: C}
        $lyrics = preg_replace('/\[[^\]]+\]/', '', $content);
        $lyrics = preg_replace('/\{[^\}]+\}/', '', $lyrics);

        // 2. Remove lines that look like section headers if they are alone
        // (Headers are usually handled by getSections, but just in case)

        $lines = explode("\n", $lyrics);
        $filteredLines = [];

        foreach ($lines as $line) {
            $trimmed = trim($line);

            if ($trimmed === '') {
                continue;
            }

            // 3. Identify and remove lines that are purely chords (e.g., "A B/C# D9")
            // Regex explain:
            // ^ indicates start of line
            // [A-G] matches chord roots
            // [b#]? matches optional accidental
            // [m0-9\/\(\)\+\-\sSsMdaimugj]* matches chord qualities, extensions, separators, whitespace
            // $ indicates end of line
            // We use a fairly generous regex to catch most loose chord lines
            if (preg_match('/^([A-G][b#]?[m0-9\/\(\)\+\-\sSsMdaimugj]*)+$/', $trimmed)) {
                // If the line is ONLY valid chord characters, we treat it as a chord line and skip it
                // We add a length check to avoid false positives on very short words like "A" or "Am" which validly exist in Portuguese ("A casa", "Amor"),
                // BUT "A" on its own line is likely a chord. "Am" on its own line is likely a chord.
                // Portuguese words usually aren't single letters except 'A', 'E', 'O'. Context matters, but for projection, safe to skip single-letter lines if they look like chords.
                continue;
            }

            // 4. Remove section headers usually found in square brackets but logic might leave them if malformed
            if (str_starts_with($trimmed, '[') && str_ends_with($trimmed, ']')) {
                continue;
            }

            $filteredLines[] = $trimmed;
        }

        return implode("\n", $filteredLines);
    }

    /**
     * Parse content into sections for the projection console.
     */
    public function getSections(string $content): array
    {
        // Remove NO_EMPTY to ensure consistent indexing
        // [0] = Preamble (or empty), [1] = Title, [2] = Content, [3] = Title, [4] = Content...
        $sections = preg_split('/^\[([^\]]+)\]/m', $content, -1, PREG_SPLIT_DELIM_CAPTURE);
        $result = [];
        $count = count($sections);

        // Start at 1 because 0 is always content-before-first-header (preamble or empty)
        for ($i = 1; $i < $count; $i += 2) {
            $title = isset($sections[$i]) ? trim($sections[$i]) : '';
            $rawContent = isset($sections[$i + 1]) ? $sections[$i + 1] : '';

            // Skip if somehow we have a title but it's empty (shouldn't happen with regex capture but safety first)
            if ($title === '') continue;

            $result[] = [
                'title' => $title,
                'content' => trim($rawContent),
                'lyrics' => $this->extractLyrics($rawContent)
            ];
        }

        return $result;
    }
}
