<?php

namespace Modules\Worship\App\Services;

use Illuminate\Support\Str;

/**
 * Import OpenSong / OpenLyrics XML into WorshipSong data.
 *
 * Supported formats:
 * - OpenLyrics (OpenLP): <song xmlns="..."><properties><titles><title>, <authors><author>,
 *   <lyrics><verse name="v1"><lines>...</lines></verse>. Lines use <br/>.
 * - Legacy OpenSong: <song><title>, <author>, <lyrics> plain text with [v1], [v2], [C].
 *
 * Output is normalized to ChordPro-style content for ChordProEngine (section headers [Verse 1], [Chorus], etc.).
 */
class OpenSongImporter
{
    private const OPENLYRICS_NS = 'http://openlyrics.info/namespace/2009/song';

    /**
     * Parse OpenSong/OpenLyrics XML and return data suitable for WorshipSong.
     *
     * @return array{title: string, artist: string, content_chordpro: string, lyrics_only: string, original_key?: \Modules\Worship\App\Enums\MusicalKey}|null
     */
    public function parse(string $xmlContent): ?array
    {
        $xmlContent = trim($xmlContent);
        if ($xmlContent === '' || (! Str::startsWith($xmlContent, '<?xml') && ! Str::contains($xmlContent, '<song'))) {
            return null;
        }

        libxml_use_internal_errors(true);
        $doc = new \DOMDocument('1.0', 'UTF-8');
        if (! @$doc->loadXML($xmlContent)) {
            return null;
        }

        $root = $doc->documentElement;
        if (! $root || strtolower($root->localName) !== 'song') {
            return null;
        }

        $ns = $root->namespaceURI;
        $isOpenLyrics = $ns && Str::contains($ns, 'openlyrics');

        if ($isOpenLyrics) {
            return $this->parseOpenLyrics($doc, $root);
        }

        return $this->parseLegacyOpenSong($root);
    }

    private function parseOpenLyrics(\DOMDocument $doc, \DOMElement $root): ?array
    {
        $xpath = new \DOMXPath($doc);
        $xpath->registerNamespace('ol', self::OPENLYRICS_NS);

        $title = $this->xpathFirstText($xpath, $root, '//ol:properties/ol:titles/ol:title');
        if ($title === '') {
            $title = $this->xpathFirstText($xpath, $root, '//*[local-name()="titles"]/*[local-name()="title"]');
        }
        if ($title === '') {
            $title = 'Sem título';
        }

        $authors = $this->xpathAllText($xpath, $root, '//ol:properties/ol:authors/ol:author');
        if ($authors === []) {
            $authors = $this->xpathAllText($xpath, $root, '//*[local-name()="authors"]/*[local-name()="author"]');
        }
        $artist = $authors !== [] ? implode(', ', $authors) : 'Unknown';

        $verseOrder = $this->xpathFirstText($xpath, $root, '//ol:properties/ol:verseOrder');
        if ($verseOrder === '') {
            $verseOrder = $this->xpathFirstText($xpath, $root, '//*[local-name()="verseOrder"]');
        }
        $verseOrderList = $verseOrder !== '' ? preg_split('/\s+/', trim($verseOrder), -1, PREG_SPLIT_NO_EMPTY) : [];

        $verses = [];
        $verseNodes = $xpath->query('//ol:lyrics/ol:verse', $root);
        if ($verseNodes->length === 0) {
            $verseNodes = $xpath->query('//*[local-name()="lyrics"]/*[local-name()="verse"]', $root);
        }
        foreach ($verseNodes as $verseEl) {
            $name = $verseEl->getAttribute('name') ?: ('v' . (count($verses) + 1));
            $linesEl = $verseEl->getElementsByTagNameNS(self::OPENLYRICS_NS, 'lines')->item(0)
                ?? $verseEl->getElementsByTagName('lines')->item(0)
                ?? $this->firstChildByLocalName($verseEl, 'lines');
            $text = $linesEl ? $this->extractLinesWithBreaks($linesEl) : '';
            $verses[] = ['name' => $name, 'content' => $text];
        }

        $contentChordpro = $this->buildChordProFromVerses($verses, $verseOrderList);
        $engine = app(ChordProEngine::class);
        $lyricsOnly = $engine->extractLyrics($contentChordpro);
        $lyricsOnly = preg_replace('/\s+/', ' ', trim($lyricsOnly));

        return [
            'title' => $title,
            'artist' => $artist,
            'content_chordpro' => $contentChordpro,
            'lyrics_only' => $lyricsOnly,
        ];
    }

    private function xpathFirstText(\DOMXPath $xpath, \DOMElement $root, string $query): string
    {
        $nodes = $xpath->query($query, $root);
        if ($nodes->length > 0 && $nodes->item(0) !== null) {
            return trim($nodes->item(0)->textContent ?? '');
        }
        return '';
    }

    private function xpathAllText(\DOMXPath $xpath, \DOMElement $root, string $query): array
    {
        $nodes = $xpath->query($query, $root);
        $out = [];
        foreach ($nodes as $node) {
            $t = trim($node->textContent ?? '');
            if ($t !== '') {
                $out[] = $t;
            }
        }
        return $out;
    }

    private function firstChildByLocalName(\DOMElement $parent, string $localName): ?\DOMElement
    {
        foreach ($parent->childNodes as $child) {
            if ($child instanceof \DOMElement && $child->localName === $localName) {
                return $child;
            }
        }
        return null;
    }

    /**
     * Extrai o texto de <lines> preservando quebras: cada <br/> vira \n.
     * textContent do DOM ignora elementos <br/>, então o texto ficaria colado sem isso.
     */
    private function extractLinesWithBreaks(\DOMElement $linesEl): string
    {
        $text = '';
        foreach ($linesEl->childNodes as $node) {
            if ($node->nodeType === XML_TEXT_NODE) {
                $text .= $node->textContent;
            } elseif ($node->nodeType === XML_ELEMENT_NODE) {
                $local = strtolower($node->localName ?? '');
                if ($local === 'br') {
                    $text .= "\n";
                } else {
                    $text .= $node->textContent;
                }
            }
        }
        return trim(preg_replace("/\n{3,}/", "\n\n", $text));
    }

    private function buildChordProFromVerses(array $verses, array $verseOrderList): string
    {
        $order = $verseOrderList;
        if ($order === []) {
            $order = array_map(fn ($v) => $v['name'], $verses);
        }
        $byName = [];
        foreach ($verses as $v) {
            $byName[$v['name']] = $v['content'];
        }
        $lines = [];
        foreach ($order as $name) {
            $content = $byName[$name] ?? null;
            if ($content === null) {
                continue;
            }
            $sectionLabel = $this->verseNameToSectionLabel($name);
            $lines[] = '[' . $sectionLabel . ']';
            $lines[] = $content;
        }
        return implode("\n", $lines);
    }

    private function verseNameToSectionLabel(string $name): string
    {
        $name = strtolower(trim($name));
        if (preg_match('/^v(\d+)$/', $name, $m)) {
            return 'Verse ' . $m[1];
        }
        if ($name === 'c' || $name === 'chorus') {
            return 'Chorus';
        }
        if ($name === 'b' || $name === 'bridge') {
            return 'Bridge';
        }
        if (preg_match('/^c(\d+)$/', $name, $m)) {
            return 'Chorus ' . $m[1];
        }
        return 'Verse ' . $name;
    }

    private function parseLegacyOpenSong(\DOMElement $root): ?array
    {
        $title = $this->getFirstText($root, ['title']);
        $artist = $this->getFirstText($root, ['author', 'artist']);
        $lyricsRaw = $this->getFirstText($root, ['lyrics']);

        if ($title === '' && $lyricsRaw === '') {
            return null;
        }
        if ($title === '') {
            $title = 'Sem título';
        }
        if ($artist === '') {
            $artist = 'Unknown';
        }

        $contentChordpro = $this->legacyLyricsToChordPro($lyricsRaw);
        $engine = app(ChordProEngine::class);
        $lyricsOnly = $engine->extractLyrics($contentChordpro);
        $lyricsOnly = preg_replace('/\s+/', ' ', trim($lyricsOnly));

        return [
            'title' => $title,
            'artist' => $artist,
            'content_chordpro' => $contentChordpro ?: $lyricsRaw,
            'lyrics_only' => $lyricsOnly ?: $lyricsRaw,
        ];
    }

    private function getFirstText(\DOMElement $root, array $tagNames): string
    {
        foreach ($tagNames as $name) {
            $list = $root->getElementsByTagName($name);
            if ($list->length > 0) {
                return trim($list->item(0)->textContent ?? '');
            }
        }
        return '';
    }

    private function legacyLyricsToChordPro(string $lyrics): string
    {
        $lines = explode("\n", str_replace(["\r\n", "\r"], "\n", $lyrics));
        $out = [];
        foreach ($lines as $line) {
            $line = rtrim($line);
            if (preg_match('/^\[([vcbVCB]\d*)\]\s*$/i', $line, $m)) {
                $out[] = '[' . $this->verseNameToSectionLabel($m[1]) . ']';
                continue;
            }
            if (preg_match('/^\[([^\]]+)\]\s*(.*)$/', $line, $m)) {
                $out[] = '[' . $this->verseNameToSectionLabel($m[1]) . ']';
                if ($m[2] !== '') {
                    $out[] = $m[2];
                }
                continue;
            }
            if ($line !== '') {
                $out[] = $line;
            }
        }
        return implode("\n", $out);
    }
}
