<?php

namespace Modules\Bible\App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Bible\App\Models\BibleVersion;
use Modules\Bible\App\Models\Book;
use Modules\Bible\App\Models\Chapter;
use Modules\Bible\App\Models\Verse;

class ImportBibleJsonCommand extends Command
{
    protected $signature = 'bible:import-json {file : Caminho do arquivo JSON} {--name= : Nome da versão} {--abbreviation= : Abreviação} {--default : Definir como versão padrão}';

    protected $description = 'Importa uma versão da Bíblia a partir de um arquivo JSON';

    public function handle(): int
    {
        $filePath = $this->argument('file');
        $name = $this->option('name') ?: $this->ask('Nome da versão da Bíblia');
        $abbreviation = $this->option('abbreviation') ?: $this->ask('Abreviação (ex: ARA, ARC)');
        $isDefault = $this->option('default');

        if (! file_exists($filePath)) {
            $this->error("Arquivo não encontrado: {$filePath}");

            return 1;
        }

        $this->info("Iniciando importação de: {$name}");

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
            $this->info('Limpando dados antigos...');
            $version->books()->each(function ($book) {
                $book->chapters()->each(function ($chapter) {
                    $chapter->verses()->delete();
                });
                $book->chapters()->delete();
            });
            $version->books()->delete();

            // Processar livros
            $this->info('Processando livros...');
            $booksCount = 0;
            $chaptersCount = 0;
            $versesCount = 0;
            $bookNumber = 0;
            $now = now();

            foreach ($data as $bookData) {
                $bookNumber++;
                $bookName = $bookData['name'] ?? '';
                $bookAbbrev = $bookData['abbrev'] ?? '';
                $chapters = $bookData['chapters'] ?? [];

                if (empty($bookName) || empty($chapters)) {
                    $this->warn('Livro sem nome ou capítulos, pulando...');

                    continue;
                }

                // Determinar testamento (primeiros 39 livros = Antigo Testamento)
                $testament = $bookNumber <= 39 ? 'old' : 'new';

                // Pré-calcular totais para o livro
                $totalChaptersForBook = count($chapters);
                $totalVersesForBook = 0;
                foreach ($chapters as $chapterVerses) {
                    if (is_array($chapterVerses)) {
                        $totalVersesForBook += count($chapterVerses);
                    }
                }

                // Criar livro
                $book = Book::create([
                    'bible_version_id' => $version->id,
                    'name' => $bookName,
                    'book_number' => $bookNumber,
                    'abbreviation' => $bookAbbrev,
                    'testament' => $testament,
                    'order' => $bookNumber,
                    'total_chapters' => $totalChaptersForBook,
                    'total_verses' => $totalVersesForBook,
                ]);

                $booksCount++;
                $this->info("Processando livro: {$bookName} ({$totalChaptersForBook} capítulos)");

                // Preparar dados para inserção em lote
                $chaptersToInsert = [];
                $versesPayloads = []; // Mapeia numero_capitulo => array de versiculos

                $chapterNumber = 0;
                foreach ($chapters as $chapterVerses) {
                    $chapterNumber++;

                    if (! is_array($chapterVerses) || empty($chapterVerses)) {
                        continue;
                    }

                    $chaptersToInsert[] = [
                        'book_id' => $book->id,
                        'chapter_number' => $chapterNumber,
                        'total_verses' => count($chapterVerses),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                    $chaptersCount++;

                    $versesPayloads[$chapterNumber] = $chapterVerses;
                }

                // Inserir capítulos em lote
                foreach (array_chunk($chaptersToInsert, 500) as $chunk) {
                    Chapter::insert($chunk);
                }

                // Buscar capítulos inseridos para obter seus IDs
                // Precisamos mapear chapter_number -> id
                $chapterMap = Chapter::where('book_id', $book->id)
                    ->pluck('id', 'chapter_number');

                // Preparar dados de versículos
                $versesToInsert = [];
                foreach ($versesPayloads as $cNum => $versesList) {
                    if (! isset($chapterMap[$cNum])) {
                        continue;
                    }

                    $cId = $chapterMap[$cNum];
                    $verseNumber = 0;

                    foreach ($versesList as $verseText) {
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

                        $versesToInsert[] = [
                            'chapter_id' => $cId,
                            'verse_number' => $verseNumber,
                            'text' => trim($verseText),
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                        $versesCount++;
                    }
                }

                // Inserir versículos em lote
                foreach (array_chunk($versesToInsert, 1000) as $chunk) {
                    Verse::insert($chunk);
                }
            }

            // Atualizar contadores da versão
            $version->update([
                'total_books' => $booksCount,
                'total_chapters' => $chaptersCount,
                'total_verses' => $versesCount,
            ]);

            DB::commit();

            $this->info('✅ Importação concluída com sucesso!');
            $this->info("   - Livros: {$booksCount}");
            $this->info("   - Capítulos: {$chaptersCount}");
            $this->info("   - Versículos: {$versesCount}");

            return 0;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Erro na importação: '.$e->getMessage());
            $this->error($e->getTraceAsString());

            return 1;
        }
    }
}
