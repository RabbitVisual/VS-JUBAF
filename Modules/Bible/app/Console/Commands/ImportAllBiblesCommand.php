<?php

namespace Modules\Bible\App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Bible\App\Models\BibleVersion;
use Modules\Bible\App\Models\Book;
use Modules\Bible\App\Models\Chapter;
use Modules\Bible\App\Models\Verse;

class ImportAllBiblesCommand extends Command
{
    protected $signature = 'bible:import-all {--default= : Abreviação da versão padrão (ex: ARA)}';

    protected $description = 'Importa todas as versões da Bíblia disponíveis no index.json';

    public function handle(): int
    {
        $indexPath = storage_path('app/private/bible/offline/index.json');

        if (! file_exists($indexPath)) {
            $this->error("Arquivo index.json não encontrado em: {$indexPath}");

            return 1;
        }

        $indexContent = file_get_contents($indexPath);
        $indexData = json_decode($indexContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('Erro ao decodificar index.json: '.json_last_error_msg());

            return 1;
        }

        if (! isset($indexData['versions']) || empty($indexData['versions'])) {
            $this->error('Nenhuma versão encontrada no index.json');

            return 1;
        }

        $versions = $indexData['versions'];
        $defaultAbbreviation = $this->option('default');
        $totalVersions = count($versions);
        $importedCount = 0;
        $failedCount = 0;

        $this->info("Encontradas {$totalVersions} versões para importar");
        $this->newLine();

        foreach ($versions as $key => $versionInfo) {
            $fileName = $versionInfo['file'] ?? null;
            $name = $versionInfo['name'] ?? '';
            $abbreviation = $versionInfo['abbreviation'] ?? strtoupper($key);

            if (! $fileName) {
                $this->warn("Versão '{$name}' não tem arquivo definido, pulando...");
                $failedCount++;

                continue;
            }

            $filePath = storage_path('app/private/bible/offline/'.$fileName);

            if (! file_exists($filePath)) {
                $this->warn("Arquivo não encontrado: {$fileName}, pulando versão '{$name}'...");
                $failedCount++;

                continue;
            }

            // Determinar se é a versão padrão
            $isDefault = ($defaultAbbreviation && strtoupper($abbreviation) === strtoupper($defaultAbbreviation)) ||
                         (! $defaultAbbreviation && $key === array_key_first($versions));

            $this->info("Importando: {$name} ({$abbreviation})...");

            try {
                // Importar diretamente usando a mesma lógica do ImportBibleJsonCommand
                $exitCode = $this->importBibleVersion($filePath, $name, $abbreviation, $isDefault);

                if ($exitCode === 0) {
                    $this->info("✅ {$name} importada com sucesso!");
                    $importedCount++;
                } else {
                    $this->error("❌ Falha ao importar {$name}");
                    $failedCount++;
                }
            } catch (\Exception $e) {
                $this->error("❌ Erro ao importar '{$name}': ".$e->getMessage());
                $failedCount++;
            }

            $this->newLine();
        }

        $this->info('✅ Importação concluída!');
        $this->info("   - Versões importadas: {$importedCount}");

        if ($failedCount > 0) {
            $this->warn("   - Versões com erro: {$failedCount}");
        }

        return 0;
    }

    /**
     * Importa uma versão da Bíblia diretamente
     */
    private function importBibleVersion(string $filePath, string $name, string $abbreviation, bool $isDefault): int
    {
        if (! file_exists($filePath)) {
            $this->error("Arquivo não encontrado: {$filePath}");

            return 1;
        }

        DB::beginTransaction();

        try {
            // Ler arquivo JSON
            $jsonContent = file_get_contents($filePath);
            $data = json_decode($jsonContent, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Erro ao decodificar JSON: '.json_last_error_msg());
            }

            if (! is_array($data) || empty($data)) {
                throw new \Exception('Arquivo JSON inválido ou vazio');
            }

            // Criar ou atualizar versão
            $version = BibleVersion::updateOrCreate(
                ['abbreviation' => $abbreviation],
                [
                    'name' => $name,
                    'abbreviation' => $abbreviation,
                    'file_name' => basename($filePath),
                    'is_active' => true,
                    'is_default' => $isDefault,
                    'imported_at' => now(),
                ]
            );

            // Se esta for a versão padrão, remover padrão das outras
            if ($isDefault) {
                BibleVersion::where('id', '!=', $version->id)->update(['is_default' => false]);
            }

            // Limpar dados antigos se já existir
            $version->books()->each(function ($book) {
                $book->chapters()->each(function ($chapter) {
                    $chapter->verses()->delete();
                });
                $book->chapters()->delete();
            });
            $version->books()->delete();

            // Processar livros
            $booksCount = 0;
            $chaptersCount = 0;
            $versesCount = 0;
            $bookNumber = 0;

            foreach ($data as $bookData) {
                $bookNumber++;
                $bookName = $bookData['name'] ?? '';
                $bookAbbrev = $bookData['abbrev'] ?? '';
                $chapters = $bookData['chapters'] ?? [];

                if (empty($bookName) || empty($chapters)) {
                    continue;
                }

                // Determinar testamento (primeiros 39 livros = Antigo Testamento)
                $testament = $bookNumber <= 39 ? 'old' : 'new';

                // Criar livro
                $book = Book::create([
                    'bible_version_id' => $version->id,
                    'name' => $bookName,
                    'book_number' => $bookNumber,
                    'abbreviation' => $bookAbbrev,
                    'testament' => $testament,
                    'order' => $bookNumber,
                ]);

                $booksCount++;

                // Processar capítulos
                $chapterNumber = 0;
                foreach ($chapters as $chapterVerses) {
                    $chapterNumber++;

                    if (! is_array($chapterVerses) || empty($chapterVerses)) {
                        continue;
                    }

                    // Criar capítulo
                    $chapter = Chapter::create([
                        'book_id' => $book->id,
                        'chapter_number' => $chapterNumber,
                        'total_verses' => count($chapterVerses),
                    ]);

                    $chaptersCount++;

                    // Processar versículos
                    $verseNumber = 0;
                    foreach ($chapterVerses as $verseText) {
                        $verseNumber++;

                        // Se o versículo for um array, converter para string
                        if (is_array($verseText)) {
                            $verseText = implode(' ', $verseText);
                        }

                        // Garantir que é string
                        $verseText = (string) $verseText;

                        if (empty(trim($verseText))) {
                            continue;
                        }

                        // Criar versículo
                        Verse::create([
                            'chapter_id' => $chapter->id,
                            'verse_number' => $verseNumber,
                            'text' => trim($verseText),
                        ]);

                        $versesCount++;
                    }
                }

                // Atualizar contadores do livro
                $book->update([
                    'total_chapters' => $chapterNumber,
                    'total_verses' => $book->chapters()->withCount('verses')->get()->sum('verses_count'),
                ]);
            }

            // Atualizar contadores da versão
            $version->update([
                'total_books' => $booksCount,
                'total_chapters' => $chaptersCount,
                'total_verses' => $versesCount,
            ]);

            DB::commit();

            return 0;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Erro na importação: '.$e->getMessage());

            return 1;
        }
    }
}
