<?php

namespace Modules\Worship\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Modules\Worship\App\Enums\MusicalKey;
use Modules\Worship\App\Models\WorshipSong;
use Modules\Worship\App\Services\ChordProImporter;
use Modules\Worship\App\Services\OpenSongImporter;

class SongImportController extends Controller
{
    public function __construct(
        private ChordProImporter $chordProImporter,
        private OpenSongImporter $openSongImporter
    ) {}

    public function showImportForm(): View
    {
        return view('worship::admin.songs.import');
    }

    /** Maximum files to import from a single ZIP (guarantees bulk without PHP file limit). */
    private const CHORDPRO_ZIP_MAX_FILES = 300;
    /** Até 1500 músicas por ZIP (XML OpenSong/OpenLyrics). */
    private const OPENSONG_ZIP_MAX_FILES = 1500;

    public function importChordPro(Request $request): RedirectResponse
    {
        $zipFile = $request->file('zip_file');
        $files = $request->file('files');
        $single = $request->file('file');

        if ($zipFile?->isValid() && $zipFile->getClientOriginalExtension() === 'zip') {
            $request->validate(['zip_file' => 'required|file|mimes:zip|max:52428800']); // 50 MB
            return $this->importChordProFromZip($zipFile);
        }

        if ($files && is_array($files)) {
            $files = array_filter($files);
        } elseif ($single) {
            $files = [$single];
        } else {
            $files = [];
        }

        if (empty($files)) {
            return redirect()->route('worship.admin.songs.import')
                ->with('error', 'Selecione arquivos ChordPro (.cho, .pro) ou um arquivo .zip com até '.self::CHORDPRO_ZIP_MAX_FILES.' músicas.');
        }

        $request->validate([
            'files' => 'required_without:file|nullable|array|max:'.self::CHORDPRO_ZIP_MAX_FILES,
            'files.*' => 'file|max:2048',
            'file' => 'required_without:files|nullable|file|max:2048',
        ]);

        $imported = 0;
        $failed = 0;
        $skipped = 0;
        $lastSong = null;

        foreach ($files as $file) {
            if (! $file->isValid()) {
                $failed++;
                continue;
            }
            $result = $this->parseAndCreateChordProSongIfNew($file->getRealPath());
            if ($result === 'created') {
                $lastSong = $this->lastCreatedSong ?? null;
                $imported++;
            } elseif ($result === 'skipped') {
                $skipped++;
            } else {
                $failed++;
            }
        }

        return $this->chordProImportResult($imported, $failed, $skipped, $lastSong);
    }

    /**
     * Import from ZIP: read in memory only, no extraction to disk. Laravel removes the
     * uploaded temp file after the request — no ZIP is persisted in the project.
     */
    private function importChordProFromZip(\Illuminate\Http\UploadedFile $zipFile): RedirectResponse
    {
        $zip = new \ZipArchive;
        $path = $zipFile->getRealPath();
        if ($zip->open($path, \ZipArchive::RDONLY) !== true) {
            return redirect()->route('worship.admin.songs.import')->with('error', 'Arquivo ZIP inválido ou corrompido.');
        }

        $allowedExtensions = ['cho', 'pro', 'txt'];
        $entries = [];
        for ($i = 0; $i < $zip->numFiles && count($entries) < self::CHORDPRO_ZIP_MAX_FILES; $i++) {
            $name = $zip->getNameIndex($i);
            if (str_contains($name, '\\')) {
                $name = str_replace('\\', '/', $name);
            }
            if (str_contains($name, '../')) {
                continue;
            }
            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            if (! in_array($ext, $allowedExtensions, true)) {
                continue;
            }
            $entries[] = $name;
        }

        $imported = 0;
        $failed = 0;
        $skipped = 0;
        $lastSong = null;

        foreach ($entries as $entryName) {
            $content = $zip->getFromName($entryName);
            if ($content === false || trim($content) === '') {
                $failed++;
                continue;
            }
            $encoding = mb_detect_encoding($content, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);
            if ($encoding && $encoding !== 'UTF-8') {
                $content = mb_convert_encoding($content, 'UTF-8', $encoding);
            }
            $data = $this->chordProImporter->parse($content);
            if (! $data) {
                $failed++;
                continue;
            }
            if ($this->chordProSongExists($data['title'], $data['artist'])) {
                $skipped++;
                continue;
            }
            $lastSong = WorshipSong::create([
                'title' => $data['title'],
                'artist' => $data['artist'],
                'content_chordpro' => $data['content_chordpro'],
                'lyrics_only' => $data['lyrics_only'],
                'original_key' => $data['original_key'] ?? MusicalKey::C,
            ]);
            $imported++;
        }
        $zip->close();

        return $this->chordProImportResult($imported, $failed, $skipped, $lastSong);
    }

    /** @return 'created'|'skipped'|'failed' */
    private function parseAndCreateChordProSongIfNew(string $path): string
    {
        $content = @file_get_contents($path);
        if ($content === false) {
            return 'failed';
        }
        $encoding = mb_detect_encoding($content, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);
        if ($encoding && $encoding !== 'UTF-8') {
            $content = mb_convert_encoding($content, 'UTF-8', $encoding);
        }
        $data = $this->chordProImporter->parse($content);
        if (! $data) {
            return 'failed';
        }
        if ($this->chordProSongExists($data['title'], $data['artist'])) {
            return 'skipped';
        }
        $this->lastCreatedSong = WorshipSong::create([
            'title' => $data['title'],
            'artist' => $data['artist'],
            'content_chordpro' => $data['content_chordpro'],
            'lyrics_only' => $data['lyrics_only'],
            'original_key' => $data['original_key'] ?? MusicalKey::C,
        ]);
        return 'created';
    }

    private ?WorshipSong $lastCreatedSong = null;

    private function chordProSongExists(string $title, string $artist): bool
    {
        return $this->findSongByTitleAndArtist($title, $artist) !== null;
    }

    private function findSongByTitleAndArtist(string $title, string $artist): ?WorshipSong
    {
        $t = mb_strtolower(trim($title));
        $a = mb_strtolower(trim($artist));
        if ($t === '' && $a === '') {
            return null;
        }
        return WorshipSong::query()
            ->whereRaw('LOWER(TRIM(title)) = ?', [$t])
            ->whereRaw('LOWER(TRIM(artist)) = ?', [$a])
            ->first();
    }

    private function chordProImportResult(int $imported, int $failed, int $skipped, ?WorshipSong $lastSong): RedirectResponse
    {
        if ($imported === 0 && $skipped === 0) {
            return redirect()->route('worship.admin.songs.import')
                ->with('error', $failed > 0 ? 'Nenhum arquivo válido. Verifique se os arquivos estão em formato ChordPro.' : 'Nenhum arquivo enviado.');
        }

        if ($imported === 0) {
            $message = $skipped > 0
                ? "Nenhuma música nova. {$skipped} já existiam na base (não duplicadas)."
                : 'Nenhuma música importada.';
            if ($failed > 0) {
                $message .= " {$failed} arquivo(s) inválido(s).";
            }
            return redirect()->route('worship.admin.songs.index')->with('success', $message);
        }

        $message = $imported === 1
            ? 'ChordPro importado com sucesso.'
            : "{$imported} músicas importadas com sucesso.";
        if ($skipped > 0) {
            $message .= " {$skipped} já existiam (não duplicadas).";
        }
        if ($failed > 0) {
            $message .= " {$failed} arquivo(s) inválido(s) ou ignorado(s).";
        }

        $redirect = $imported === 1 && $lastSong
            ? redirect()->route('worship.admin.songs.edit', $lastSong)
            : redirect()->route('worship.admin.songs.index');

        return $redirect->with('success', $message);
    }

    public function importOpenSong(Request $request): RedirectResponse
    {
        $zipFile = $request->file('opensong_zip');
        $files = $request->file('opensong_files');
        $single = $request->file('file');

        if ($zipFile?->isValid() && $zipFile->getClientOriginalExtension() === 'zip') {
            $request->validate(['opensong_zip' => 'required|file|mimes:zip|max:52428800']);
            return $this->importOpenSongFromZip($zipFile);
        }

        if ($files && is_array($files)) {
            $files = array_filter($files);
        } elseif ($single) {
            $files = [$single];
        } else {
            $files = [];
        }

        if (empty($files)) {
            return redirect()->route('worship.admin.songs.import')
                ->with('error', 'Selecione arquivos XML (OpenSong/OpenLyrics) ou um .zip com até '.self::OPENSONG_ZIP_MAX_FILES.' músicas.');
        }

        $request->validate([
            'opensong_files' => 'required_without:file|nullable|array|max:'.self::OPENSONG_ZIP_MAX_FILES,
            'opensong_files.*' => 'file|max:2048',
            'file' => 'required_without:opensong_files|nullable|file|max:2048',
        ]);

        $imported = 0;
        $failed = 0;
        $skipped = 0;
        $lastSong = null;

        foreach ($files as $file) {
            if (! $file->isValid()) {
                $failed++;
                continue;
            }
            $result = $this->parseAndCreateOpenSongIfNew($file->getRealPath());
            if ($result === 'created') {
                $lastSong = $this->lastCreatedOpenSong ?? null;
                $imported++;
            } elseif ($result === 'skipped') {
                $skipped++;
            } else {
                $failed++;
            }
        }

        return $this->openSongImportResult($imported, $failed, $skipped, $lastSong);
    }

    /**
     * OpenSong from ZIP: read in memory only; no ZIP is persisted in the project.
     */
    private function importOpenSongFromZip(\Illuminate\Http\UploadedFile $zipFile): RedirectResponse
    {
        $zip = new \ZipArchive;
        if ($zip->open($zipFile->getRealPath(), \ZipArchive::RDONLY) !== true) {
            return redirect()->route('worship.admin.songs.import')->with('error', 'Arquivo ZIP inválido ou corrompido.');
        }

        $entries = [];
        for ($i = 0; $i < $zip->numFiles && count($entries) < self::OPENSONG_ZIP_MAX_FILES; $i++) {
            $name = $zip->getNameIndex($i);
            if (str_contains($name, '\\')) {
                $name = str_replace('\\', '/', $name);
            }
            if (str_contains($name, '../')) {
                continue;
            }
            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            if ($ext !== 'xml' && $ext !== 'txt') {
                continue;
            }
            $entries[] = $name;
        }

        $imported = 0;
        $failed = 0;
        $skipped = 0;
        $lastSong = null;

        foreach ($entries as $entryName) {
            $content = $zip->getFromName($entryName);
            if ($content === false || trim($content) === '') {
                $failed++;
                continue;
            }
            $encoding = mb_detect_encoding($content, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);
            if ($encoding && $encoding !== 'UTF-8') {
                $content = mb_convert_encoding($content, 'UTF-8', $encoding);
            }
            $data = $this->openSongImporter->parse($content);
            if (! $data) {
                $failed++;
                continue;
            }
            if ($this->chordProSongExists($data['title'], $data['artist'])) {
                $skipped++;
                continue;
            }
            $lastSong = WorshipSong::create([
                'title' => $data['title'],
                'artist' => $data['artist'],
                'content_chordpro' => $data['content_chordpro'],
                'lyrics_only' => $data['lyrics_only'],
                'original_key' => $data['original_key'] ?? MusicalKey::C,
            ]);
            $imported++;
        }
        $zip->close();

        return $this->openSongImportResult($imported, $failed, $skipped, $lastSong);
    }

    /** @return 'created'|'skipped'|'failed' */
    private function parseAndCreateOpenSongIfNew(string $path): string
    {
        $content = @file_get_contents($path);
        if ($content === false) {
            return 'failed';
        }
        $encoding = mb_detect_encoding($content, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);
        if ($encoding && $encoding !== 'UTF-8') {
            $content = mb_convert_encoding($content, 'UTF-8', $encoding);
        }
        $data = $this->openSongImporter->parse($content);
        if (! $data) {
            return 'failed';
        }
        if ($this->chordProSongExists($data['title'], $data['artist'])) {
            return 'skipped';
        }
        $this->lastCreatedOpenSong = WorshipSong::create([
            'title' => $data['title'],
            'artist' => $data['artist'],
            'content_chordpro' => $data['content_chordpro'],
            'lyrics_only' => $data['lyrics_only'],
            'original_key' => $data['original_key'] ?? MusicalKey::C,
        ]);
        return 'created';
    }

    private ?WorshipSong $lastCreatedOpenSong = null;

    private function openSongImportResult(int $imported, int $failed, int $skipped, ?WorshipSong $lastSong): RedirectResponse
    {
        if ($imported === 0 && $skipped === 0) {
            return redirect()->route('worship.admin.songs.import')
                ->with('error', $failed > 0 ? 'Nenhum arquivo XML válido (OpenSong/OpenLyrics).' : 'Nenhum arquivo enviado.');
        }

        if ($imported === 0) {
            $message = $skipped > 0
                ? "Nenhuma música nova. {$skipped} já existiam na base (não duplicadas)."
                : 'Nenhuma música importada.';
            if ($failed > 0) {
                $message .= " {$failed} arquivo(s) inválido(s).";
            }
            return redirect()->route('worship.admin.songs.index')->with('success', $message);
        }

        $message = $imported === 1
            ? 'OpenSong importado com sucesso.'
            : "{$imported} músicas importadas com sucesso.";
        if ($skipped > 0) {
            $message .= " {$skipped} já existiam (não duplicadas).";
        }
        if ($failed > 0) {
            $message .= " {$failed} arquivo(s) inválido(s) ou ignorado(s).";
        }

        $redirect = $imported === 1 && $lastSong
            ? redirect()->route('worship.admin.songs.edit', $lastSong)
            : redirect()->route('worship.admin.songs.index');

        return $redirect->with('success', $message);
    }

    /**
     * Reimportar música a partir de um arquivo ChordPro (.cho, .pro) ou OpenSong/OpenLyrics (XML).
     * Atualiza título, autor, letra e tom do registro existente (sem criar duplicata).
     */
    public function reimport(Request $request, WorshipSong $song): RedirectResponse
    {
        $request->validate([
            'reimport_file' => 'required|file|max:2048',
        ]);

        $file = $request->file('reimport_file');
        $ext = strtolower($file->getClientOriginalExtension());
        $content = file_get_contents($file->getRealPath());
        $encoding = mb_detect_encoding($content, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);
        if ($encoding && $encoding !== 'UTF-8') {
            $content = mb_convert_encoding($content, 'UTF-8', $encoding);
        }

        $isXml = $ext === 'xml' || (Str::startsWith(trim($content), '<?xml') && Str::contains($content, '<song'));
        $data = $isXml
            ? $this->openSongImporter->parse($content)
            : $this->chordProImporter->parse($content);

        if (! $data) {
            return redirect()->route('worship.admin.songs.edit', $song)
                ->with('error', $isXml ? 'Arquivo XML inválido (OpenSong/OpenLyrics).' : 'Arquivo ChordPro inválido ou vazio.');
        }

        $song->title = $data['title'];
        $song->artist = $data['artist'];
        $song->content_chordpro = $data['content_chordpro'];
        $song->lyrics_only = $data['lyrics_only'];
        if (isset($data['original_key'])) {
            $song->original_key = $data['original_key'];
        }
        $song->save();

        return redirect()->route('worship.admin.songs.edit', $song)
            ->with('success', 'Conteúdo reimportado com sucesso. Título, autor e letra foram atualizados.');
    }

    /** Máximo de arquivos em um ZIP de reimportação em massa (ChordPro + XML misto). */
    private const REIMPORT_BULK_MAX_FILES = 1500;

    /**
     * Reimportar em massa: atualiza músicas que já existem na base (título + autor).
     * Aceita ZIP ou vários arquivos (.cho, .pro, .xml). Não cria novas músicas.
     */
    public function reimportBulk(Request $request): RedirectResponse
    {
        $zipFile = $request->file('reimport_zip');
        $files = $request->file('reimport_files');

        if ($zipFile?->isValid() && $zipFile->getClientOriginalExtension() === 'zip') {
            $request->validate(['reimport_zip' => 'required|file|mimes:zip|max:52428800']);
            return $this->reimportBulkFromZip($zipFile);
        }

        if (! is_array($files)) {
            $files = [];
        }
        $files = array_filter($files);

        if (empty($files)) {
            return redirect()->route('worship.admin.songs.import')
                ->with('error', 'Selecione um arquivo .zip ou vários arquivos (.cho, .pro, .xml) para reimportar em massa.');
        }

        $request->validate([
            'reimport_files' => 'required|array|max:'.self::REIMPORT_BULK_MAX_FILES,
            'reimport_files.*' => 'file|max:2048',
        ]);

        $updated = 0;
        $notFound = 0;
        $failed = 0;

        foreach ($files as $file) {
            if (! $file->isValid()) {
                $failed++;
                continue;
            }
            $content = @file_get_contents($file->getRealPath());
            if ($content === false) {
                $failed++;
                continue;
            }
            $encoding = mb_detect_encoding($content, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);
            if ($encoding && $encoding !== 'UTF-8') {
                $content = mb_convert_encoding($content, 'UTF-8', $encoding);
            }
            $ext = strtolower($file->getClientOriginalExtension());
            $isXml = $ext === 'xml' || (Str::startsWith(trim($content), '<?xml') && Str::contains($content, '<song'));
            $data = $isXml ? $this->openSongImporter->parse($content) : $this->chordProImporter->parse($content);
            if (! $data) {
                $failed++;
                continue;
            }
            $existing = $this->findSongByTitleAndArtist($data['title'], $data['artist']);
            if (! $existing) {
                $notFound++;
                continue;
            }
            $existing->title = $data['title'];
            $existing->artist = $data['artist'];
            $existing->content_chordpro = $data['content_chordpro'];
            $existing->lyrics_only = $data['lyrics_only'];
            if (isset($data['original_key'])) {
                $existing->original_key = $data['original_key'];
            }
            $existing->save();
            $updated++;
        }

        return $this->reimportBulkResult($updated, $notFound, $failed);
    }

    private function reimportBulkFromZip(\Illuminate\Http\UploadedFile $zipFile): RedirectResponse
    {
        $zip = new \ZipArchive;
        if ($zip->open($zipFile->getRealPath(), \ZipArchive::RDONLY) !== true) {
            return redirect()->route('worship.admin.songs.import')->with('error', 'Arquivo ZIP inválido ou corrompido.');
        }

        $allowed = ['cho', 'pro', 'txt', 'xml'];
        $entries = [];
        for ($i = 0; $i < $zip->numFiles && count($entries) < self::REIMPORT_BULK_MAX_FILES; $i++) {
            $name = $zip->getNameIndex($i);
            if (str_contains($name, '\\')) {
                $name = str_replace('\\', '/', $name);
            }
            if (str_contains($name, '../')) {
                continue;
            }
            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            if (! in_array($ext, $allowed, true)) {
                continue;
            }
            $entries[] = $name;
        }

        $updated = 0;
        $notFound = 0;
        $failed = 0;

        foreach ($entries as $entryName) {
            $content = $zip->getFromName($entryName);
            if ($content === false || trim($content) === '') {
                $failed++;
                continue;
            }
            $encoding = mb_detect_encoding($content, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);
            if ($encoding && $encoding !== 'UTF-8') {
                $content = mb_convert_encoding($content, 'UTF-8', $encoding);
            }
            $ext = strtolower(pathinfo($entryName, PATHINFO_EXTENSION));
            $isXml = $ext === 'xml' || (Str::startsWith(trim($content), '<?xml') && Str::contains($content, '<song'));
            $data = $isXml ? $this->openSongImporter->parse($content) : $this->chordProImporter->parse($content);
            if (! $data) {
                $failed++;
                continue;
            }
            $existing = $this->findSongByTitleAndArtist($data['title'], $data['artist']);
            if (! $existing) {
                $notFound++;
                continue;
            }
            $existing->title = $data['title'];
            $existing->artist = $data['artist'];
            $existing->content_chordpro = $data['content_chordpro'];
            $existing->lyrics_only = $data['lyrics_only'];
            if (isset($data['original_key'])) {
                $existing->original_key = $data['original_key'];
            }
            $existing->save();
            $updated++;
        }
        $zip->close();

        return $this->reimportBulkResult($updated, $notFound, $failed);
    }

    private function reimportBulkResult(int $updated, int $notFound, int $failed): RedirectResponse
    {
        if ($updated === 0 && $notFound === 0 && $failed > 0) {
            return redirect()->route('worship.admin.songs.import')
                ->with('error', 'Nenhum arquivo válido. Envie .cho, .pro ou .xml (ChordPro/OpenLyrics).');
        }
        if ($updated === 0 && $notFound === 0) {
            return redirect()->route('worship.admin.songs.import')
                ->with('error', 'Nenhum arquivo enviado.');
        }

        $message = $updated > 0
            ? "{$updated} músicas atualizadas com sucesso."
            : 'Nenhuma música atualizada.';
        if ($notFound > 0) {
            $message .= " {$notFound} sem correspondência na base (título + autor).";
        }
        if ($failed > 0) {
            $message .= " {$failed} arquivo(s) inválido(s).";
        }

        return redirect()->route('worship.admin.songs.import')->with('success', $message);
    }
}
