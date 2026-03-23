<?php

namespace Modules\MemberPanel\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Bible\App\Models\Verse;
use Modules\Bible\App\Services\BibleApiService;

class BibleController extends Controller
{
    public function __construct(
        private BibleApiService $bibleApi
    ) {}

    public function index()
    {
        $versions = $this->bibleApi->getVersions();
        $defaultVersion = $versions->first();
        if (! $defaultVersion) {
            return view('memberpanel::bible.no-version');
        }
        return redirect()->route('memberpanel.bible.read', [
            'version' => $defaultVersion->abbreviation,
        ]);
    }

    public function read($versionAbbr = null)
    {
        $versions = $this->bibleApi->getVersions();
        $version = $versionAbbr
            ? $versions->firstWhere('abbreviation', $versionAbbr)
            : $versions->first();
        if (! $version) {
            return view('memberpanel::bible.no-version');
        }
        $books = $this->bibleApi->getBooks($version->id);
        $oldTestament = $books->where('testament', 'old');
        $newTestament = $books->where('testament', 'new');
        return view('memberpanel::bible.read', compact('version', 'versions', 'oldTestament', 'newTestament'));
    }

    public function showBook($versionAbbr, $bookNumber)
    {
        $versions = $this->bibleApi->getVersions();
        $version = $versions->firstWhere('abbreviation', $versionAbbr);
        if (! $version) {
            abort(404);
        }
        $books = $this->bibleApi->getBooks($version->id);
        $book = $books->firstWhere('book_number', (int) $bookNumber);
        if (! $book) {
            abort(404);
        }
        $chapters = $this->bibleApi->getChapters($book->id, null, null);
        return view('memberpanel::bible.book', compact('version', 'book', 'chapters'));
    }

    public function showChapter($versionAbbr, $bookNumber, $chapterNumber)
    {
        $versions = $this->bibleApi->getVersions();
        $version = $versions->firstWhere('abbreviation', $versionAbbr);
        if (! $version) {
            abort(404);
        }
        $books = $this->bibleApi->getBooks($version->id);
        $book = $books->firstWhere('book_number', (int) $bookNumber);
        if (! $book) {
            abort(404);
        }
        $chapters = $this->bibleApi->getChapters($book->id, null, null);
        $chapter = $chapters->firstWhere('chapter_number', (int) $chapterNumber);
        if (! $chapter) {
            abort(404);
        }
        $verses = $this->bibleApi->getVerses($chapter->id, null, null, null);

        $previousChapter = $chapters->where('chapter_number', '<', (int) $chapterNumber)->sortByDesc('chapter_number')->first();
        $nextChapter = $chapters->where('chapter_number', '>', (int) $chapterNumber)->sortBy('chapter_number')->first();
        if (! $nextChapter) {
            $nextBook = $books->where('book_number', '>', (int) $bookNumber)->sortBy('book_number')->first();
            if ($nextBook) {
                $nextChapters = $this->bibleApi->getChapters($nextBook->id, null, null);
                $nextChapter = $nextChapters->sortBy('chapter_number')->first();
            }
        }

        return view('memberpanel::bible.chapter', compact(
            'version', 'book', 'chapter', 'verses', 'previousChapter', 'nextChapter'
        ));
    }

    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $versionId = $request->get('version_id');
        $versions = $this->bibleApi->getVersions();

        if (empty($query)) {
            return view('memberpanel::bible.search', [
                'query' => '',
                'results' => collect(),
                'versions' => $versions,
                'versionId' => $versionId,
            ]);
        }

        $verses = $this->bibleApi->search($query, 100);
        if ($versionId) {
            $verses = $verses->filter(fn ($v) => $v->chapter->book->bible_version_id == $versionId);
        }
        $results = $verses->load(['chapter.book.bibleVersion'])->groupBy(fn ($v) => $v->chapter->book->bibleVersion->abbreviation);

        return view('memberpanel::bible.search', compact('query', 'results', 'versions', 'versionId'));
    }

    public function favorites()
    {
        $user = Auth::user();
        $favorites = $user->bibleFavorites()->with(['chapter.book.bibleVersion'])->get();

        return view('memberpanel::bible.favorites', compact('favorites'));
    }

    public function addFavorite(Request $request, Verse $verse)
    {
        $user = Auth::user();
        $note = $request->get('note', '');
        $color = $request->get('color', null);

        // Check if exists
        $existing = $user->bibleFavorites()->where('verse_id', $verse->id)->first();

        if ($existing) {
            // Update existng or Toggle Off if same color/no note?
            // User requirement: "mark with color". If clicked again with different color -> update.
            // If same color -> maybe remove? Let's just update for now to be safe/simple "Highlight action".
            $user->bibleFavorites()->updateExistingPivot($verse->id, [
                'note' => $note, // Keep note or empty? Request usually sends it.
                'color' => $color,
                'updated_at' => now(),
            ]);
        } else {
            $user->bibleFavorites()->attach($verse->id, [
                'note' => $note,
                'color' => $color,
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function removeFavorite(Verse $verse)
    {
        $user = Auth::user();
        $user->bibleFavorites()->detach($verse->id);

        return response()->json(['success' => true]);
    }

    public function verse(Verse $verse)
    {
        $verse->load(['chapter.book.bibleVersion']);

        return view('memberpanel::bible.verse', compact('verse'));
    }
}
