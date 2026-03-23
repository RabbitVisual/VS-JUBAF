<?php

namespace Modules\Bible\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Modules\Bible\App\Models\Verse;
use Modules\Bible\App\Models\Book;
use Modules\Bible\App\Models\BibleVersion;

class InterlinearController extends Controller
{
    protected $storagePath;

    protected $bookMapping = [
        'Genesis' => 0, 'Exodus' => 1, 'Leviticus' => 2, 'Numbers' => 3, 'Deuteronomy' => 4,
        'Joshua' => 5, 'Judges' => 6, 'Ruth' => 7,
        'I Samuel' => 8, '1 Samuel' => 8, 'II Samuel' => 9, '2 Samuel' => 9,
        'I Kings' => 10, '1 Kings' => 10, 'II Kings' => 11, '2 Kings' => 11,
        'I Chronicles' => 12, '1 Chronicles' => 12, 'II Chronicles' => 13, '2 Chronicles' => 13,
        'Ezra' => 14, 'Nehemiah' => 15, 'Esther' => 16, 'Job' => 17, 'Psalms' => 18,
        'Proverbs' => 19, 'Ecclesiastes' => 20, 'Song of Solomon' => 21, 'Isaiah' => 22,
        'Jeremiah' => 23, 'Lamentations' => 24, 'Ezekiel' => 25, 'Daniel' => 26,
        'Hosea' => 27, 'Joel' => 28, 'Amos' => 29, 'Obadiah' => 30, 'Jonah' => 31,
        'Micah' => 32, 'Nahum' => 33, 'Habakkuk' => 34, 'Zephaniah' => 35, 'Haggai' => 36,
        'Zechariah' => 37, 'Malachi' => 38,
        'Matthew' => 39, 'Mark' => 40, 'Luke' => 41, 'John' => 42, 'Acts' => 43,
        'Romans' => 44, '1 Corinthians' => 45, '2 Corinthians' => 46, 'Galatians' => 47,
        'Ephesians' => 48, 'Philippians' => 49, 'Colossians' => 50, '1 Thessalonians' => 51,
        '2 Thessalonians' => 52, '1 Timothy' => 53, '2 Timothy' => 54, 'Titus' => 55,
        'Philemon' => 56, 'Hebreus' => 57, 'Hebrews' => 57, 'Tiago' => 58, 'James' => 58,
        '1 Pedro' => 59, '1 Peter' => 59, '2 Pedro' => 60, '2 Peter' => 60,
        '1 João' => 61, '1 John' => 61, '2 João' => 62, '2 John' => 62, '3 João' => 63, '3 John' => 63,
        'Judas' => 64, 'Jude' => 64, 'Apocalipse' => 65, 'Revelation' => 65,
    ];

    protected $semanticBridge = [
        'princípio' => ['começo', 'primeiro', 'primícia'],
        'criou' => ['criar', 'fez', 'formou', 'produziu'],
        'expansão' => ['firmamento', 'expanse', 'arco'],
        'firmamento' => ['expansão'],
        'fez' => ['fazê-la', 'realizou', 'preparou', 'fazer'],
        'separação' => ['separar', 'dividir', 'distinguir'],
        'Deus' => ['Elohim', 'Senhor', 'Divino'],
        'terra' => ['chão', 'mundo', 'país', 'região'],
        'céus' => ['céu', 'firmamento', 'altura', 'celestial'],
        'águas' => ['mar', 'rio', 'corrente'],
        'disse' => ['falou', 'respondeu', 'declarou'],
        'haja' => ['seja', 'exista', 'venha'],
        'meio' => ['centro', 'entre'],
        'entre' => ['meio', 'no meio'],
        'luz' => ['claridade', 'brilho'],
        'trevas' => ['escuridão', 'noite'],
        'noite' => ['trevas', 'escuro'],
        'dia' => ['manhã', 'período'],
        'espírito' => ['vento', 'sopro', 'fôlego'],
        'face' => ['superfície', 'presença'],
        'abismo' => ['profundo', 'profundezas'],
        'No' => ['começo', 'primeiro'],
        'Do' => ['origem'],
        'pela' => ['através'],
        'debaixo' => ['abaixo', 'sob', 'fundo'],
        'sobre' => ['cima', 'topo', 'acima'],
        'foi' => ['ser', 'tornar-se', 'acontecer', 'existir'],
        'era' => ['ser', 'existir'],
    ];

    public function __construct()
    {
        $this->storagePath = storage_path('app/private/bible/offline');
    }

    public function index(Request $request)
    {
        return view('bible::interlinear');
    }

    public function getBooksMetadata()
    {
        try {
            $version = BibleVersion::where('is_active', true)->first();
            if (! $version) {
                Log::warning('Interlinear: No active bible version found.');

                return response()->json([]);
            }

            $books = Book::where('bible_version_id', $version->id)
                ->orderBy('book_number')
                ->select(['name', 'testament', 'total_chapters', 'book_number'])
                ->get();

            return response()->json($books);
        } catch (\Exception $e) {
            Log::error('Interlinear Error in getBooksMetadata: '.$e->getMessage());

            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    public function getData(Request $request)
    {
        $bookParam = $request->query('book', 'Genesis');
        $chapter = (int) $request->query('chapter', 1);
        $testament = $request->query('testament', 'old');

        // Resolve book name
        $book = $this->resolveBookName($bookParam);

        if ($testament === 'old') {
            return $this->getHebrewData($book, $chapter, $bookParam);
        } else {
            return $this->getGreekData($book, $chapter, $bookParam);
        }
    }

    public function getStrongDefinition($number)
    {
        $raw = $this->loadJson('strongs.json');
        $strongs = $raw['itens'] ?? $raw;
        $number = strtoupper($number);

        $definition = collect($strongs)->firstWhere('number', $number);

        if (! $definition) {
            return response()->json(['error' => 'Definition not found'], 404);
        }

        return response()->json($definition);
    }

    protected function getHebrewData($book, $chapter, $originalName = null)
    {
        $tagged = $this->loadJson('hebrew_tagged.json');

        if (! isset($tagged[$book])) {
            Log::error("Interlinear: Book '{$book}' (from '{$originalName}') not found in hebrew_tagged.json.");
            return response()->json(['error' => "Book {$book} not found in tagged data", 'resolved' => $book, 'original' => $originalName], 404);
        }

        $chapterData = $tagged[$book][$chapter - 1] ?? null;
        if (! $chapterData) {
            return response()->json(['error' => 'Chapter not found'], 404);
        }

        // Load Portuguese KJF for context and matching
        $pt = $this->loadJson('KJF.json');

        // Use mapping to find the correct translation index
        $bookIndex = $this->bookMapping[$book] ?? ($originalName ? ($this->bookMapping[$originalName] ?? null) : null);

        $ptVerses = ($bookIndex !== null && isset($pt[$bookIndex])) ? $pt[$bookIndex]['chapters'][$chapter - 1] : [];

        // Load BSRTB Lexicon
        $strongsRawData = $this->loadJson('strongs.json');
        $strongsRaw = $strongsRawData['itens'] ?? $strongsRawData;
        $strongs = collect($strongsRaw)->keyBy('number');

        $enrichedVerses = array_map(function ($verse, $idx) use ($strongs, $ptVerses) {
            $translation = $ptVerses[$idx] ?? '';

            return array_map(function ($segment) use ($strongs, $translation) {
                $strongNum = $this->extractStrong($segment[1]);
                $def = $strongs->get($strongNum);

                return [
                    'word' => $segment[0],
                    'strong' => $segment[1],
                    'tag' => $segment[2],
                    'xlit' => $def['xlit'] ?? '',
                    'pronounce' => $def['pronounce'] ?? '',
                    'lemma_pt' => $def['lemma_br'] ?? null,
                    'pt_suggested' => $this->findSuggestedTranslation($strongNum, $translation, $strongs),
                ];
            }, $verse);
        }, $chapterData, array_keys($chapterData));

        return response()->json([
            'testament' => 'old',
            'book' => $book,
            'chapter' => $chapter,
            'verses' => $enrichedVerses,
            'translation' => $ptVerses,
        ]);
    }

    protected function getGreekData($book, $chapter, $originalName = null)
    {
        $tr = $this->loadJson('GRC-Κοινη/trparsed.json');

        $versesList = collect($tr['verses'] ?? [])
            ->where('book_name', $book)
            ->where('chapter', $chapter);

        if ($versesList->isEmpty() && $originalName) {
            $versesList = collect($tr['verses'] ?? [])
                ->where('book_name', $originalName)
                ->where('chapter', $chapter);
        }

        // Load Portuguese KJF
        $pt = $this->loadJson('KJF.json');
        $bookIndex = $this->bookMapping[$book] ?? ($originalName ? ($this->bookMapping[$originalName] ?? null) : null);
        $ptVerses = ($bookIndex !== null && isset($pt[$bookIndex])) ? $pt[$bookIndex]['chapters'][$chapter - 1] : [];

        // Load BSRTB
        $strongsRawData = $this->loadJson('strongs.json');
        $strongsRaw = $strongsRawData['itens'] ?? $strongsRawData;
        $strongs = collect($strongsRaw)->keyBy('number');

        $versesWithAlignment = $versesList->map(function ($v, $idx) use ($strongs, $ptVerses) {
            $translation = $ptVerses[$idx] ?? '';
            $text = $v['text'] ?? '';
            preg_match_all('/([^\s]+)\s+(G\d+)\s+([^\s]+)/u', $text, $matches, PREG_SET_ORDER);

            return collect($matches)->map(function ($m) use ($strongs, $translation) {
                $strongNum = $m[2];
                $def = $strongs->get($strongNum);

                return [
                    'word' => $m[1],
                    'strong' => $strongNum,
                    'tag' => $m[3],
                    'xlit' => $def['xlit'] ?? '',
                    'pronounce' => $def['pronounce'] ?? '',
                    'lemma_pt' => $def['lemma_br'] ?? null,
                    'pt_suggested' => $this->findSuggestedTranslation($strongNum, $translation, $strongs),
                ];
            });
        })->values();

        return response()->json([
            'testament' => 'new',
            'book' => $book,
            'chapter' => $chapter,
            'verses' => $versesWithAlignment,
            'translation' => $ptVerses,
        ]);
    }

    protected function resolveBookName($name)
    {
        // Normalize input
        $name = trim(preg_replace('/\s+/', ' ', $name));

        // 1. Precise Portuguese to English Map
        $ptMap = [
            'Gênesis' => 'Genesis', 'Genesis' => 'Genesis',
            'Êxodo' => 'Exodus', 'Exodo' => 'Exodus',
            'Levítico' => 'Leviticus', 'Levitico' => 'Leviticus',
            'Números' => 'Numbers', 'Numeros' => 'Numbers',
            'Deuteronômio' => 'Deuteronomy', 'Deuteronomio' => 'Deuteronomy',
            'Josué' => 'Joshua', 'Josue' => 'Joshua',
            'Juízes' => 'Judges', 'Juizes' => 'Judges',
            'Rute' => 'Ruth',
            '1 Samuel' => 'I Samuel', 'I Samuel' => 'I Samuel',
            '2 Samuel' => 'II Samuel', 'II Samuel' => 'II Samuel',
            '1 Reis' => 'I Kings', '2 Reis' => 'II Kings',
            '1 Crônicas' => 'I Chronicles', '2 Crônicas' => 'II Chronicles',
            'Esdras' => 'Ezra', 'Neemias' => 'Nehemiah', 'Ester' => 'Esther', 'Jó' => 'Job', 'Jo' => 'Job',
            'Salmos' => 'Psalms', 'Provérbios' => 'Proverbs', 'Proverbios' => 'Proverbs',
            'Eclesiastes' => 'Ecclesiastes', 'Cânticos' => 'Song of Solomon', 'Canticos' => 'Song of Solomon',
            'Isaías' => 'Isaiah', 'Isaias' => 'Isaiah',
            'Jeremias' => 'Jeremiah', 'Lamentações' => 'Lamentations', 'Lamentacoes' => 'Lamentations',
            'Ezequiel' => 'Ezekiel', 'Daniel' => 'Daniel',
            'Oseias' => 'Hosea', 'Joel' => 'Joel', 'Amós' => 'Amos', 'Amos' => 'Amos',
            'Obadias' => 'Obadiah', 'Jonas' => 'Jonah', 'Miqueias' => 'Micah',
            'Naum' => 'Nahum', 'Habacuque' => 'Habakkuk', 'Sofonias' => 'Zephaniah',
            'Ageu' => 'Haggai', 'Zacarias' => 'Zechariah', 'Malaquias' => 'Malachi',

            'Mateus' => 'Matthew', 'Marcos' => 'Mark', 'Lucas' => 'Luke', 'João' => 'John', 'Joao' => 'John',
            'Atos' => 'Acts', 'Romanos' => 'Romans',
            '1 Coríntios' => '1 Corinthians', '2 Coríntios' => '2 Corinthians',
            'Gálatas' => 'Galatians', 'Efésios' => 'Ephesians',
            'Filipenses' => 'Philippians', 'Colossenses' => 'Colossians',
            '1 Tessalonicenses' => '1 Thessalonians', '2 Tessalonicenses' => '2 Thessalonians',
            '1 Timóteo' => '1 Timothy', '2 Timóteo' => '2 Timothy',
            'Tito' => 'Titus', 'Filemom' => 'Philemon', 'Hebreus' => 'Hebrews',
            'Tiago' => 'James', '1 Pedro' => '1 Peter', '2 Pedro' => '2 Peter',
            '1 João' => '1 John', '2 João' => '2 John', '3 João' => '3 John',
            'Judas' => 'Jude', 'Apocalipse' => 'Revelation',
        ];

        if (isset($ptMap[$name])) {
            $result = $ptMap[$name];
            Log::info("Interlinear Resolution: '{$name}' -> '{$result}'");
            return $result;
        }

        // 2. Exact English Key Match
        if (isset($this->bookMapping[$name])) {
            return $name;
        }

        // 3. Database Fallback
        $book = Book::where('name', $name)->first();
        if ($book) {
            $index = $book->book_number - 1;
            // Search for the English/canonical name in our mapping
            $canonical = array_search($index, $this->bookMapping);
            if ($canonical) {
                Log::info("Interlinear Resolution (DB): '{$name}' -> '{$canonical}'");
                return $canonical;
            }
        }

        return $name;
    }

    protected function extractStrong($raw)
    {
        if (preg_match('/([HG]\d+)/', $raw, $m)) {
            return $m[1];
        }

        return $raw;
    }

    protected function findSuggestedTranslation($strongNum, $verseText, $strongsCollection = null)
    {
        if (! $strongNum) {
            return null;
        }

        $def = $strongsCollection ? $strongsCollection->get($strongNum) : null;
        if (! $def) {
            $def = collect($this->loadJson('strongs.json'))->where('number', $strongNum)->first();
        }
        if (! $def) {
            return null;
        }

        $desc = $def['description'] ?? '';
        $cleanDesc = $desc;
        if (str_contains($desc, '--')) {
            $parts = explode('--', $desc);
            $cleanDesc = end($parts);
        }

        $candidates = preg_split('/[,;:\.]/', $cleanDesc);
        $originalWords = array_filter(explode(' ', $verseText));

        // Prepare clean candidates
        $preparedCands = [];
        foreach ($candidates as $cand) {
            $c = trim(mb_strtolower(preg_replace('/\([^\)]+\)/u', '', $cand)));
            $c = trim(preg_replace('/[^\p{L}\s]/u', '', $c));
            if (! empty($c) && mb_strlen($c) < 35) {
                $preparedCands[] = $c;
            }
        }

        // Prepare clean verse words
        $preparedWords = [];
        foreach ($originalWords as $idx => $vw) {
            $raw = preg_replace('/[^\p{L}]/u', '', $vw);
            $clean = mb_strtolower($raw);
            if (! empty($clean)) {
                $preparedWords[] = ['raw' => $raw, 'clean' => $clean, 'original_idx' => $idx];
            }
        }

        // Priority 1: Exact Match
        foreach ($preparedWords as $pw) {
            foreach ($preparedCands as $pc) {
                if ($pw['clean'] === $pc) {
                    return $pw['raw'];
                }
            }
        }

        // Priority 2: Semantic Bridge
        foreach ($preparedWords as $pw) {
            if (isset($this->semanticBridge[$pw['raw']])) {
                foreach ($this->semanticBridge[$pw['raw']] as $synonym) {
                    $cleanSyn = mb_strtolower($synonym);
                    foreach ($preparedCands as $pc) {
                        if ($pc === $cleanSyn) {
                            return $pw['raw'];
                        }
                    }
                }
            }
        }

        // Priority 3: Substring
        foreach ($preparedWords as $pw) {
            foreach ($preparedCands as $pc) {
                if (mb_strlen($pw['clean']) >= 4 && mb_strlen($pc) >= 4) {
                    if (mb_stripos($pw['clean'], $pc) !== false || mb_stripos($pc, $pw['clean']) !== false) {
                        return $pw['raw'];
                    }
                }
            }
        }

        return null;
    }

    protected function loadJson($filename)
    {
        $path = $this->storagePath.'/'.$filename;
        if (! File::exists($path)) {
            Log::error("Bible Interlinear: File not found at {$path}");

            return [];
        }

        return json_decode(File::get($path), true);
    }
}
