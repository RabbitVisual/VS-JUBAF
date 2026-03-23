<?php

namespace Modules\Bible\App\Http\Controllers\MemberPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Bible\App\Models\BibleVersion;
use Modules\Bible\App\Models\Verse;

class BibleController extends Controller
{
    public function index()
    {
        $defaultVersion = BibleVersion::default()->first()
            ?? BibleVersion::active()->first();

        if (! $defaultVersion) {
            return view('bible::memberpanel.bible.no-version');
        }

        return redirect()->route('memberpanel.bible.read', [
            'version' => $defaultVersion->abbreviation,
        ]);
    }

    public function read($versionAbbr = null)
    {
        if ($versionAbbr) {
            $version = BibleVersion::where('abbreviation', $versionAbbr)->firstOrFail();
        } else {
            $version = BibleVersion::default()->first() ?? BibleVersion::active()->first();
            if (! $version) {
                return view('bible::memberpanel.bible.no-version');
            }
        }

        $versions = BibleVersion::active()->orderBy('name')->get();
        $books = $version->books()->ordered()->get();
        $oldTestament = $books->where('testament', 'old');
        $newTestament = $books->where('testament', 'new');

        return view('bible::memberpanel.bible.read', compact('version', 'versions', 'oldTestament', 'newTestament'));
    }

    public function showBook($versionAbbr, $bookNumber)
    {
        $version = BibleVersion::where('abbreviation', $versionAbbr)->firstOrFail();
        $book = $version->books()->where('book_number', $bookNumber)->firstOrFail();
        $chapters = $book->chapters()->orderBy('chapter_number')->get();

        return view('bible::memberpanel.bible.book', compact('version', 'book', 'chapters'));
    }

    public function showChapter($versionAbbr, $bookNumber, $chapterNumber)
    {
        $version = BibleVersion::where('abbreviation', $versionAbbr)->firstOrFail();
        $book = $version->books()->where('book_number', $bookNumber)->firstOrFail();
        $chapter = $book->chapters()->where('chapter_number', $chapterNumber)->firstOrFail();
        $verses = $chapter->verses()->orderBy('verse_number')->get();

        $previousChapter = $book->chapters()
            ->where('chapter_number', '<', $chapterNumber)
            ->orderBy('chapter_number', 'desc')
            ->first();

        $nextChapter = $book->chapters()
            ->where('chapter_number', '>', $chapterNumber)
            ->orderBy('chapter_number')
            ->first();

        if (! $nextChapter) {
            $nextBook = $version->books()
                ->where('book_number', '>', $bookNumber)
                ->orderBy('book_number')
                ->first();
            if ($nextBook) {
                $nextChapter = $nextBook->chapters()->orderBy('chapter_number')->first();
            }
        }

        $chapterAudioUrl = $version->getChapterAudioUrl($book->book_number, $chapter->chapter_number);

        return view('bible::memberpanel.bible.chapter', compact(
            'version', 'book', 'chapter', 'verses', 'previousChapter', 'nextChapter', 'chapterAudioUrl'
        ));
    }

    // Exibe a página de busca
    public function search()
    {
        $versions = BibleVersion::active()->get();

        return view('bible::memberpanel.bible.search', compact('versions'));
    }

    // Processa a busca via AJAX (API interna)
    public function performSearch(Request $request)
    {
        $query = $request->get('q');

        // Se a busca for muito curta, não retorna nada
        if (strlen($query) < 3) {
            return response()->json([]);
        }

        // Busca versículos que contenham o texto (LIKE)
        // Limita a 50 resultados para performance
        $verses = Verse::where('text', 'LIKE', "%{$query}%")
            ->join('chapters', 'verses.chapter_id', '=', 'chapters.id')
            ->join('books', 'chapters.book_id', '=', 'books.id')
            ->select(
                'verses.id',
                'verses.text',
                'verses.verse_number',
                'chapters.chapter_number',
                'books.name as book_name',
                'books.book_number'
            )
            ->limit(50)
            ->get();

        return response()->json($verses);
    }

    public function favorites()
    {
        $user = Auth::user();

        $favorites = Verse::join('bible_favorites', 'verses.id', '=', 'bible_favorites.verse_id')
            ->where('bible_favorites.user_id', $user->id)
            ->with(['chapter.book.bibleVersion'])
            ->select('verses.*', 'bible_favorites.color as favorite_color')
            ->get();

        return view('bible::memberpanel.bible.favorites', compact('favorites'));
    }

    public function addFavorite(Request $request, Verse $verse)
    {
        $user = Auth::user();
        $color = $request->get('color', null);

        $exists = DB::table('bible_favorites')
            ->where('user_id', $user->id)
            ->where('verse_id', $verse->id)
            ->exists();

        if ($exists) {
            DB::table('bible_favorites')
                ->where('user_id', $user->id)
                ->where('verse_id', $verse->id)
                ->update([
                    'color' => $color,
                    'updated_at' => now(),
                ]);
        } else {
            DB::table('bible_favorites')->insert([
                'user_id' => $user->id,
                'verse_id' => $verse->id,
                'color' => $color,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function removeFavorite(Verse $verse)
    {
        $user = Auth::user();
        DB::table('bible_favorites')
            ->where('user_id', $user->id)
            ->where('verse_id', $verse->id)
            ->delete();

        return response()->json(['success' => true]);
    }

    public function verse(Verse $verse)
    {
        $verse->load(['chapter.book.bibleVersion']);

        return view('bible::memberpanel.bible.verse', compact('verse'));
    }
}
