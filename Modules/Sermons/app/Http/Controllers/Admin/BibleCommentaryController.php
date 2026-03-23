<?php

namespace Modules\Sermons\App\Http\Controllers\Admin;

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

        if ($request->has('book') && ! empty($request->book)) {
            $query->where('book', $request->book);
        }

        if ($request->has('search') && ! empty($request->search)) {
            $search = $request->search;
            $query->where('content', 'like', "%{$search}%")
                ->orWhere('title', 'like', "%{$search}%");
        }

        $commentaries = $query->orderBy('book')
            ->orderBy('chapter')
            ->orderBy('verse_start')
            ->paginate(20);

        return view('sermons::admin.commentaries.index', compact('commentaries'));
    }

    public function create(): View
    {
        $bibleVersions = $this->bibleApi->getVersions();
        return view('sermons::admin.commentaries.create', compact('bibleVersions'));
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

        // Handle book_id to name conversion if using the selector
        if ($request->has('book_id') && ! empty($request->book_id)) {
            $bookModel = \Modules\Bible\App\Models\Book::find($request->book_id);
            if ($bookModel) {
                $validated['book'] = $bookModel->name;
            }
        }

        // Handle chapter_id to number conversion
        if ($request->has('chapter_id') && ! empty($request->chapter_id)) {
            $chapterModel = \Modules\Bible\App\Models\Chapter::find($request->chapter_id);
            if ($chapterModel) {
                $validated['chapter'] = $chapterModel->chapter_number;
            }
        }

        // Handle verses string parsing
        if ($request->has('verses') && ! empty($request->verses)) {
            $verses = $request->verses;
            preg_match_all('/\d+/', $verses, $matches);
            if (! empty($matches[0])) {
                $numbers = array_map('intval', $matches[0]);
                $validated['verse_start'] = min($numbers);
                if (max($numbers) > min($numbers)) {
                    $validated['verse_end'] = max($numbers);
                } else {
                    $validated['verse_end'] = null;
                }
            }
        }

        $validated['user_id'] = auth()->id();

        // Handle cover image upload
        if ($request->hasFile('cover_image_file')) {
            $path = $request->file('cover_image_file')->store('commentaries/covers', 'public');
            $validated['cover_image'] = $path;
        }

        // Handle Audio Upload
        if ($request->hasFile('audio')) {
            $request->validate([
                'audio' => 'nullable|file|mimes:mp3,wav,ogg,m4a,aac|max:51200', // Max 50MB
            ]);
            $path = $request->file('audio')->store('commentaries/audio', 'public');
            $validated['audio_path'] = $path;
            $validated['audio_url'] = null; // Clear URL when uploading file
        } elseif (! empty($validated['audio_url'])) {
            $validated['audio_path'] = null; // Clear file when using URL
        }

        BibleCommentary::create($validated);

        return redirect()->route('admin.sermons.commentaries.index')
            ->with('success', 'Comentário Bíblico criado com sucesso!');
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

        // Prepare verses string for data-selected-verses
        $versesString = $commentary->verse_start;
        if ($commentary->verse_end && $commentary->verse_end != $commentary->verse_start) {
            $versesString .= "-{$commentary->verse_end}";
        }

        return view('sermons::admin.commentaries.edit', compact(
            'commentary',
            'bibleVersions',
            'bibleBooks',
            'selectedVersionId',
            'selectedBookId',
            'selectedChapterId',
            'versesString'
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

        // Handle book_id to name conversion if using the selector
        if ($request->has('book_id') && ! empty($request->book_id)) {
            $bookModel = \Modules\Bible\App\Models\Book::find($request->book_id);
            if ($bookModel) {
                $validated['book'] = $bookModel->name;
            }
        }

        // Handle chapter_id to number conversion
        if ($request->has('chapter_id') && ! empty($request->chapter_id)) {
            $chapterModel = \Modules\Bible\App\Models\Chapter::find($request->chapter_id);
            if ($chapterModel) {
                $validated['chapter'] = $chapterModel->chapter_number;
            }
        }

        // Handle verses string parsing
        if ($request->has('verses') && ! empty($request->verses)) {
            $verses = $request->verses;
            preg_match_all('/\d+/', $verses, $matches);
            if (! empty($matches[0])) {
                $numbers = array_map('intval', $matches[0]);
                $validated['verse_start'] = min($numbers);
                if (max($numbers) > min($numbers)) {
                    $validated['verse_end'] = max($numbers);
                } else {
                    $validated['verse_end'] = null;
                }
            }
        }

        // Handle cover image upload
        if ($request->input('remove_cover') == '1') {
            if ($commentary->cover_image && \Storage::disk('public')->exists($commentary->cover_image)) {
                \Storage::disk('public')->delete($commentary->cover_image);
            }
            $validated['cover_image'] = null;
        } elseif ($request->hasFile('cover_image_file')) {
            if ($commentary->cover_image && \Storage::disk('public')->exists($commentary->cover_image)) {
                \Storage::disk('public')->delete($commentary->cover_image);
            }
            $path = $request->file('cover_image_file')->store('commentaries/covers', 'public');
            $validated['cover_image'] = $path;
        }

        // Handle Audio Removal
        if ($request->input('remove_audio') == '1') {
            if ($commentary->audio_path && \Storage::disk('public')->exists($commentary->audio_path)) {
                \Storage::disk('public')->delete($commentary->audio_path);
            }
            $validated['audio_path'] = null;
            $validated['audio_url'] = null;
        }
        // Handle Audio Upload
        elseif ($request->hasFile('audio')) {
            // Delete old audio file if exists
            if ($commentary->audio_path && \Storage::disk('public')->exists($commentary->audio_path)) {
                \Storage::disk('public')->delete($commentary->audio_path);
            }

            $request->validate([
                'audio' => 'nullable|file|mimes:mp3,wav,ogg,m4a,aac|max:40960', // Max 40MB
            ]);
            $path = $request->file('audio')->store('commentaries/audio', 'public');
            $validated['audio_path'] = $path;
            $validated['audio_url'] = null; // Clear URL when uploading file
        } elseif (! empty($validated['audio_url'])) {
            // If audio_url is provided, clear audio_file
            if ($commentary->audio_path && \Storage::disk('public')->exists($commentary->audio_path)) {
                \Storage::disk('public')->delete($commentary->audio_path);
            }
            $validated['audio_path'] = null;
        }

        $commentary->update($validated);

        return redirect()->route('admin.sermons.commentaries.index')
            ->with('success', 'Comentário atualizado com sucesso!');
    }

    public function destroy(BibleCommentary $commentary): RedirectResponse
    {
        $commentary->delete();

        return redirect()->route('admin.sermons.commentaries.index')
            ->with('success', 'Comentário removido com sucesso!');
    }
}
