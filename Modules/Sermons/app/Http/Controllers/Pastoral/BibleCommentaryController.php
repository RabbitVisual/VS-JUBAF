<?php

namespace Modules\Sermons\App\Http\Controllers\liderancaal;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
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
        $query = BibleCommentary::with('user');
        if ($request->filled('book')) $query->where('book', $request->book);
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(fn ($q) => $q->where('content', 'like', "%{$search}%")->orWhere('title', 'like', "%{$search}%"));
        }
        $commentaries = $query->orderBy('book')->orderBy('chapter')->orderBy('verse_start')->paginate(20);
        return view('sermons::liderancapanel.commentaries.index', compact('commentaries'));
    }

    public function create(): View
    {
        $bibleVersions = $this->bibleApi->getVersions();
        return view('sermons::liderancapanel.commentaries.create', compact('bibleVersions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'book_id' => 'nullable|integer',
            'book' => 'required_without:book_id|string',
            'chapter_id' => 'nullable|integer',
            'chapter' => 'required_without:chapter_id|integer|min:1',
            'verses' => 'nullable|string',
            'verse_start' => 'required_without:verses|integer|min:1',
            'verse_end' => 'nullable|integer|gte:verse_start',
            'title' => 'nullable|string|max:255',
            'content' => 'required|string',
            'audio_url' => 'nullable|url',
            'status' => 'required|in:draft,published',
            'is_official' => 'boolean',
            'cover_image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:15360',
        ]);
        if ($request->filled('book_id')) {
            $bookModel = \Modules\Bible\App\Models\Book::find($request->book_id);
            if ($bookModel) $validated['book'] = $bookModel->name;
        }
        if ($request->filled('chapter_id')) {
            $chapterModel = \Modules\Bible\App\Models\Chapter::find($request->chapter_id);
            if ($chapterModel) $validated['chapter'] = $chapterModel->chapter_number;
        }
        if ($request->filled('verses')) {
            preg_match_all('/\d+/', $request->verses, $matches);
            if (!empty($matches[0])) {
                $numbers = array_map('intval', $matches[0]);
                $validated['verse_start'] = min($numbers);
                $validated['verse_end'] = max($numbers) > min($numbers) ? max($numbers) : null;
            }
        }
        $validated['user_id'] = auth()->id();
        if ($request->hasFile('cover_image_file')) {
            $validated['cover_image'] = $request->file('cover_image_file')->store('commentaries/covers', 'public');
        }
        if ($request->hasFile('audio')) {
            $request->validate(['audio' => 'nullable|file|mimes:mp3,wav,ogg,m4a,aac|max:51200']);
            $validated['audio_path'] = $request->file('audio')->store('commentaries/audio', 'public');
            $validated['audio_url'] = null;
        } elseif (!empty($validated['audio_url'] ?? null)) {
            $validated['audio_path'] = null;
        }
        BibleCommentary::create($validated);
        return redirect()->route('lideranca.sermoes.commentaries.index')->with('success', 'Comentário Bíblico criado com sucesso!');
    }

    public function edit(BibleCommentary $commentary): View
    {
        $bibleVersions = $this->bibleApi->getVersions();
        $defaultVersion = $bibleVersions->first();
        $selectedVersionId = $defaultVersion?->id;
        $bibleBooks = $selectedVersionId ? $this->bibleApi->getBooks($selectedVersionId) : collect();
        $selectedBookId = $bibleBooks->firstWhere('name', $commentary->book)?->id;
        $selectedChapterId = null;
        if ($selectedBookId) {
            $chapters = $this->bibleApi->getChapters($selectedBookId, null, null);
            $selectedChapterId = $chapters->firstWhere('chapter_number', (int) $commentary->chapter)?->id;
        }
        $versesString = $commentary->verse_start . ($commentary->verse_end && $commentary->verse_end != $commentary->verse_start ? '-' . $commentary->verse_end : '');
        return view('sermons::liderancapanel.commentaries.edit', compact(
            'commentary', 'bibleVersions', 'bibleBooks', 'selectedVersionId', 'selectedBookId', 'selectedChapterId', 'versesString'
        ));
    }

    public function update(Request $request, BibleCommentary $commentary): RedirectResponse
    {
        $validated = $request->validate([
            'book_id' => 'nullable|integer',
            'book' => 'required_without:book_id|string',
            'chapter_id' => 'nullable|integer',
            'chapter' => 'required_without:chapter_id|integer|min:1',
            'verses' => 'nullable|string',
            'verse_start' => 'required_without:verses|integer|min:1',
            'verse_end' => 'nullable|integer|gte:verse_start',
            'title' => 'nullable|string|max:255',
            'content' => 'required|string',
            'audio_url' => 'nullable|url',
            'status' => 'required|in:draft,published',
            'is_official' => 'boolean',
            'cover_image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:15360',
        ]);
        if ($request->filled('book_id')) {
            $bookModel = \Modules\Bible\App\Models\Book::find($request->book_id);
            if ($bookModel) $validated['book'] = $bookModel->name;
        }
        if ($request->filled('chapter_id')) {
            $chapterModel = \Modules\Bible\App\Models\Chapter::find($request->chapter_id);
            if ($chapterModel) $validated['chapter'] = $chapterModel->chapter_number;
        }
        if ($request->filled('verses')) {
            preg_match_all('/\d+/', $request->verses, $matches);
            if (!empty($matches[0])) {
                $numbers = array_map('intval', $matches[0]);
                $validated['verse_start'] = min($numbers);
                $validated['verse_end'] = max($numbers) > min($numbers) ? max($numbers) : null;
            }
        }
        if ($request->input('remove_cover') == '1') {
            if ($commentary->cover_image && \Storage::disk('public')->exists($commentary->cover_image)) \Storage::disk('public')->delete($commentary->cover_image);
            $validated['cover_image'] = null;
        } elseif ($request->hasFile('cover_image_file')) {
            if ($commentary->cover_image && \Storage::disk('public')->exists($commentary->cover_image)) \Storage::disk('public')->delete($commentary->cover_image);
            $validated['cover_image'] = $request->file('cover_image_file')->store('commentaries/covers', 'public');
        }
        if ($request->input('remove_audio') == '1') {
            if ($commentary->audio_path && \Storage::disk('public')->exists($commentary->audio_path)) \Storage::disk('public')->delete($commentary->audio_path);
            $validated['audio_path'] = null;
            $validated['audio_url'] = null;
        } elseif ($request->hasFile('audio')) {
            if ($commentary->audio_path && \Storage::disk('public')->exists($commentary->audio_path)) \Storage::disk('public')->delete($commentary->audio_path);
            $request->validate(['audio' => 'nullable|file|mimes:mp3,wav,ogg,m4a,aac|max:40960']);
            $validated['audio_path'] = $request->file('audio')->store('commentaries/audio', 'public');
            $validated['audio_url'] = null;
        } elseif (!empty($validated['audio_url'] ?? null)) {
            if ($commentary->audio_path && \Storage::disk('public')->exists($commentary->audio_path)) \Storage::disk('public')->delete($commentary->audio_path);
            $validated['audio_path'] = null;
        }
        $commentary->update($validated);
        return redirect()->route('lideranca.sermoes.commentaries.index')->with('success', 'Comentário atualizado com sucesso!');
    }

    public function destroy(BibleCommentary $commentary): RedirectResponse
    {
        $commentary->delete();
        return redirect()->route('lideranca.sermoes.commentaries.index')->with('success', 'Comentário removido com sucesso!');
    }
}
