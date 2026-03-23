<?php

namespace Modules\Intercessor\App\Services;

class BibleParser
{
    /**
     * Parse text and expand @Reference citations into clickable links for Alpine modal.
     * Example: "@Salmos 23:1" -> "<a @click...>@Salmos 23:1</a>"
     */
    public static function parse(string $text): string
    {
        // Pattern to match @Book Chapter:Verse(-EndVerse)?
        // Handling spaces, portuguese names.

        return preg_replace_callback(
            '/@([\w\sáàâãéèêíïóôõöúçñÁÀÂÃÉÈÊÍÏÓÔÕÖÚÇÑ]+)\s+(\d+):(\d+)(?:-(\d+))?/u',
            function ($matches) {
                $bookName = trim($matches[1]);
                $chapterNum = $matches[2];
                $verseStart = $matches[3];
                $verseEnd = $matches[4] ?? null;

                $refString = "{$bookName} {$chapterNum}:{$verseStart}".($verseEnd ? "-{$verseEnd}" : '');

                // Return Alpine-compatible link
                // Uses dispatch to trigger the modal in the frontend
                return '<a href="#" @click.prevent.stop="$dispatch(\'open-bible-modal\', { ref: \''.e($refString).'\' })" class="text-amber-500 hover:text-amber-400 font-medium hover:underline transition-colors decoration-dotted" title="Ler '.e($refString).'">'.e($matches[0]).'</a>';
            },
            e($text)
        );
    }
}
