<?php

namespace Modules\Sermons\App\Http\Controllers\MemberPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Bible\App\Services\BibleApiService;
use Modules\Sermons\App\Models\BibleCommentary;

class BibleCommentaryController extends Controller
{
    public function __construct(
        private BibleApiService $bibleApi
    ) {}

    public function index(Request $request): View
    {
        $query = BibleCommentary::where('status', 'published')
            ->with('user');

        // Handle book_id to name conversion for filtering
        if ($request->has('book_id') && ! empty($request->book_id)) {
            $bookModel = \Modules\Bible\App\Models\Book::find($request->book_id);
            if ($bookModel) {
                $query->where('book', $bookModel->name);
            }
        } elseif ($request->has('book') && ! empty($request->book)) {
            $query->where('book', $request->book);
        }

        if ($request->has('chapter') && ! empty($request->chapter)) {
            // If chapter came from select (might be an ID if relying on JS defaults, but wait, JS loads chapters with IDs?)
            // JS: option.value = chapter.id;
            // So chapter filter will likely receive an ID if using the selector.
            // But commentary table stores `chapter` as INT (chapter number).
            // So if I receive chapter_id, I need to look up the chapter number.

            if (is_numeric($request->chapter) && $request->chapter > 1000) {
                // Heuristic: IDs are usually large? Or just look up Chapter model.
                // Better: check if input name is 'chapter_id'.
            }
            $query->where('chapter', $request->chapter);
        }

        // Let's support chapter_id param if my view uses it
        if ($request->has('chapter_id') && ! empty($request->chapter_id)) {
            $chapterModel = \Modules\Bible\App\Models\Chapter::find($request->chapter_id);
            if ($chapterModel) {
                $query->where('chapter', $chapterModel->chapter_number);
            }
        }

        if ($request->filled('verse_number') && $request->verse_number !== '') {
            $verseNum = (int) $request->verse_number;
            $query->where('verse_start', '<=', $verseNum)->where('verse_end', '>=', $verseNum);
        }

        // Default sort: Canonical order
        $commentaries = $query->orderBy('book')
            ->orderBy('chapter')
            ->orderBy('verse_start')
            ->paginate(20);

        // Get available books distinct names (legacy/fallback)
        $books = BibleCommentary::where('status', 'published')
            ->select('book')
            ->distinct()
            ->orderBy('book')
            ->pluck('book');

        $bibleVersions = $this->bibleApi->getVersions();

        return view('sermons::memberpanel.commentaries.index', compact('commentaries', 'books', 'bibleVersions'));
    }

    public function show(BibleCommentary $commentary): View
    {
        // Only show published commentaries
        if ($commentary->status !== 'published') {
            abort(404);
        }

        return view('sermons::memberpanel.commentaries.show', compact('commentary'));
    }
}
