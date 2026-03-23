<?php

namespace Modules\Bible\App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Bible\App\Models\BibleVersion;
use Modules\Bible\App\Models\Book;
use Modules\Bible\App\Models\Chapter;
use Modules\Bible\App\Models\Verse;

class ImportBibleCommand extends Command
{
    protected $signature = 'bible:import {file : Caminho do arquivo CSV} {--name= : Nome da versão} {--abbreviation= : Abreviação} {--default : Definir como versão padrão}';

    protected $description = 'Importa uma versão da Bíblia a partir de um arquivo CSV';

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

            // Ler CSV
            $this->info('Lendo arquivo CSV...');

            // Detectar encoding do arquivo
            $fileContent = file_get_contents($filePath);
            $encoding = mb_detect_encoding($fileContent, ['UTF-8', 'ISO-8859-1', 'Windows-1252', 'ASCII'], true);

            if ($encoding && $encoding !== 'UTF-8') {
                $this->info("Convertendo encoding de {$encoding} para UTF-8...");
                $fileContent = mb_convert_encoding($fileContent, 'UTF-8', $encoding);
                // Salvar temporariamente o arquivo convertido
                $tempPath = sys_get_temp_dir().DIRECTORY_SEPARATOR.'bible_import_'.uniqid().'.csv';
                file_put_contents($tempPath, $fileContent);
                $originalFilePath = $filePath;
                $filePath = $tempPath;
            } else {
                $originalFilePath = null;
            }

            $handle = fopen($filePath, 'r');
            if (! $handle) {
                throw new \Exception('Não foi possível abrir o arquivo');
            }

            // Configurar para ler CSV corretamente
            // CSV pode ter campos com quebras de linha, então precisamos usar fgetcsv com delimitador correto
            $lineNumber = 0;
            $currentBook = null;
            $currentChapter = null;
            $books = [];
            $chapters = [];
            $versesCount = 0;
            $chaptersCount = 0;
            $booksCount = 0;

            // Ler CSV linha por linha, fgetcsv lida automaticamente com campos multilinha entre aspas
            while (($line = fgetcsv($handle, 0, ',', '"', '\\')) !== false) {
                $lineNumber++;

                // Pular linhas vazias ou de cabeçalho
                if ($lineNumber <= 1 || empty($line[0]) || ! is_numeric($line[0])) {
                    continue;
                }

                // Garantir que temos pelo menos 6 colunas
                if (count($line) < 6) {
                    $this->warn("Linha {$lineNumber} tem menos de 6 colunas, pulando...");

                    continue;
                }

                $verseId = (int) $line[0];
                $bookName = trim($line[1] ?? '');
                $bookNumber = (int) ($line[2] ?? 0);
                $chapterNumber = (int) ($line[3] ?? 0);
                $verseNumber = (int) ($line[4] ?? 0);
                $text = trim($line[5] ?? '');

                // Validar dados - não pular se o texto estiver vazio, pode ser um versículo válido
                if (empty($bookName) || $bookNumber === 0 || $chapterNumber === 0 || $verseNumber === 0) {
                    continue;
                }

                // Se o texto estiver vazio, usar um placeholder
                if (empty($text)) {
                    $text = '[Texto não disponível]';
                }

                // Criar ou obter livro
                if (! isset($books[$bookNumber])) {
                    $testament = $bookNumber <= 39 ? 'old' : 'new';
                    $book = Book::create([
                        'bible_version_id' => $version->id,
                        'name' => $bookName,
                        'book_number' => $bookNumber,
                        'testament' => $testament,
                        'order' => $bookNumber,
                    ]);
                    $books[$bookNumber] = $book;
                    $booksCount++;
                    $this->info("Processando livro: {$bookName}");
                } else {
                    $book = $books[$bookNumber];
                }

                // Criar ou obter capítulo
                $chapterKey = "{$bookNumber}-{$chapterNumber}";
                if (! isset($chapters[$chapterKey])) {
                    $chapter = Chapter::create([
                        'book_id' => $book->id,
                        'chapter_number' => $chapterNumber,
                    ]);
                    $chapters[$chapterKey] = $chapter;
                    $chaptersCount++;
                } else {
                    $chapter = $chapters[$chapterKey];
                }

                // Criar versículo (usar firstOrCreate e depois update para garantir que o texto seja atualizado)
                try {
                    $verse = Verse::firstOrCreate(
                        [
                            'chapter_id' => $chapter->id,
                            'verse_number' => $verseNumber,
                        ],
                        [
                            'text' => $text,
                            'original_verse_id' => $verseId,
                        ]
                    );

                    // Se o versículo já existia, atualizar o texto
                    if ($verse->wasRecentlyCreated === false) {
                        $verse->update([
                            'text' => $text,
                            'original_verse_id' => $verseId,
                        ]);
                    }

                    $versesCount++;
                } catch (\Exception $e) {
                    $this->warn("Erro ao criar versículo {$verseNumber} do capítulo {$chapterNumber} do livro {$bookName}: ".$e->getMessage());

                    continue;
                }

                // Atualizar contadores
                if ($versesCount % 1000 === 0) {
                    $this->info("Importados {$versesCount} versículos...");
                }
            }

            fclose($handle);

            // Limpar arquivo temporário se foi criado
            if (isset($originalFilePath) && file_exists($filePath)) {
                @unlink($filePath);
            }

            // Atualizar contadores nos livros e capítulos
            $this->info('Atualizando contadores...');
            foreach ($books as $book) {
                // Recarregar o livro do banco para ter os capítulos atualizados
                $book->refresh();
                $totalChapters = $book->chapters()->count();
                $totalVerses = DB::table('verses')
                    ->join('chapters', 'verses.chapter_id', '=', 'chapters.id')
                    ->where('chapters.book_id', $book->id)
                    ->count();
                $book->update([
                    'total_chapters' => $totalChapters,
                    'total_verses' => $totalVerses,
                ]);
            }

            foreach ($chapters as $chapter) {
                // Recarregar o capítulo do banco para ter os versículos atualizados
                $chapter->refresh();
                $verseCount = $chapter->verses()->count();
                $chapter->update([
                    'total_verses' => $verseCount,
                ]);
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
