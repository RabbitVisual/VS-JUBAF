<?php

namespace Modules\Bible\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Bible\App\Models\BibleVersion;
use Modules\Bible\App\Models\Book;
use Modules\Bible\App\Models\Chapter;
use Modules\Bible\App\Models\Verse;

class BibleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // 1. Fetch Active Versions
        $versions = BibleVersion::where('is_active', true)
            ->orderBy('is_default', 'desc')
            ->orderBy('name')
            ->get();

        // 2. Determine Selected Version
        $versionAbbr = $request->query('version');
        $selectedVersion = null;
        if ($versionAbbr) {
            $selectedVersion = BibleVersion::where('abbreviation', 'like', $versionAbbr)->first();
        }
        if (! $selectedVersion) {
            $globalAbbr = \App\Models\Settings::get('default_bible_version_abbreviation', '');
            if ($globalAbbr !== '') {
                $selectedVersion = $versions->firstWhere('abbreviation', $globalAbbr);
            }
            if (! $selectedVersion) {
                $selectedVersion = $versions->where('is_default', true)->first() ?? $versions->first();
            }
        }

        // 3. Fetch Books for Version
        $books = Book::where('bible_version_id', $selectedVersion->id)
            ->orderBy('book_number')
            ->get();

        // 4. Determine Selected Book & Chapter
        $bookId = $request->query('book');
        $selectedBook = null;
        if ($bookId) {
            $selectedBook = $books->where('id', $bookId)->first() ?? $books->where('book_number', $bookId)->first();
        }
        if (! $selectedBook) {
            $selectedBook = $books->first(); // Default to Genesis
        }

        $chapterNumber = (int) $request->query('chapter', 1);

        // 5. Fetch Chapter and Verses
        $chapter = Chapter::where('book_id', $selectedBook->id)
            ->where('chapter_number', $chapterNumber)
            ->with(['verses' => function ($q) {
                $q->orderBy('verse_number');
            }])
            ->first();

        // 6. Navigation Logic
        $prevChapter = null;
        $nextChapter = null;

        if ($chapterNumber > 1) {
            $prevChapter = $chapterNumber - 1;
        }

        if ($chapterNumber < $selectedBook->total_chapters) {
            $nextChapter = $chapterNumber + 1;
        }

        return view('bible::index', compact(
            'versions',
            'selectedVersion',
            'books',
            'selectedBook',
            'chapter',
            'chapterNumber',
            'prevChapter',
            'nextChapter'
        ));
    }

    /**
     * Compare specific verse or chapter with another version (AJAX)
     */
    public function compare(Request $request)
    {
        $v1Abbr = $request->query('v1');
        $v2Abbr = $request->query('v2');
        $bookNumber = $request->query('book_number');
        $chapterNumber = (int) $request->query('chapter');
        $verseNumber = $request->query('verse');

        $v1 = BibleVersion::where('abbreviation', $v1Abbr)->first();
        $v2 = BibleVersion::where('abbreviation', $v2Abbr)->first();

        if (! $v1 || ! $v2) {
            return response()->json(['error' => 'Versões não encontradas.'], 404);
        }

        // Find books by number in target versions
        $book1 = Book::where('bible_version_id', $v1->id)->where('book_number', $bookNumber)->first();
        $book2 = Book::where('bible_version_id', $v2->id)->where('book_number', $bookNumber)->first();

        if (! $book1 || ! $book2) {
            return response()->json(['error' => 'Livro não traduzido nesta versão.'], 404);
        }

        $query1 = Verse::select('verses.*')
            ->join('chapters', 'verses.chapter_id', '=', 'chapters.id')
            ->where('chapters.book_id', $book1->id)
            ->where('chapters.chapter_number', $chapterNumber);

        $query2 = Verse::select('verses.*')
            ->join('chapters', 'verses.chapter_id', '=', 'chapters.id')
            ->where('chapters.book_id', $book2->id)
            ->where('chapters.chapter_number', $chapterNumber);

        if ($verseNumber) {
            $verses1 = $query1->where('verses.verse_number', $verseNumber)->get();
            $verses2 = $query2->where('verses.verse_number', $verseNumber)->get();
        } else {
            $verses1 = $query1->orderBy('verses.verse_number')->get();
            $verses2 = $query2->orderBy('verses.verse_number')->get();
        }

        return response()->json([
            'v1' => [
                'abbreviation' => $v1->abbreviation,
                'name' => $v1->name,
                'verses' => $verses1,
            ],
            'v2' => [
                'abbreviation' => $v2->abbreviation,
                'name' => $v2->name,
                'verses' => $verses2,
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('bible::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {}

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('bible::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('bible::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) {}

    /**
     * Remove the specified resource from storage.
     */
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) {}

    public function import()
    {
        // Mock data for available files if needed, or scan directory
        $availableFiles = [];

        return view('admin::bible.import', compact('availableFiles'));
    }

    public function storeImport(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'abbreviation' => 'required|string|max:10',
            'language' => 'required|string|max:10',
            'file' => 'required|file|mimes:json',
        ]);

        // Logic to parse JSON and store Bible version would go here.
        // For now, redirect with success.

        return redirect()->route('admin.bible.index')->with('success', 'Versão importada com sucesso (Simulação).');
    }
}
