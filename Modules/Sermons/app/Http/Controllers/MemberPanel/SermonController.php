<?php

namespace Modules\Sermons\App\Http\Controllers\MemberPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Mpdf\Mpdf;
use Modules\Bible\App\Services\BibleApiService;
use Modules\Sermons\App\Models\Sermon;
use Modules\Sermons\App\Models\SermonCategory;
use Modules\Sermons\App\Models\SermonCollaborator;
use Modules\Sermons\App\Models\SermonFavorite;
use Modules\Sermons\App\Models\SermonTag;

class SermonController extends Controller
{
    public function __construct(
        private BibleApiService $bibleApi
    ) {}

    /**
     * Display a listing of sermons
     */
    public function index(Request $request): View
    {
        $query = Sermon::visible(auth()->user())
            ->with(['category', 'user', 'tags'])
            ->published();

        // Filter by category
        if ($request->has('category_id') && ! empty($request->category_id)) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by search
        if ($request->has('search') && ! empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('subtitle', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by tag
        if ($request->has('tag_id') && ! empty($request->tag_id)) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('sermon_tags.id', $request->tag_id);
            });
        }

        // Filter by featured
        if ($request->has('featured')) {
            $query->featured();
        }

        $sermons = $query->orderBy('published_at', 'desc')->paginate(12);
        $categories = SermonCategory::active()->ordered()->get();
        $tags = SermonTag::all();
        $featuredSermons = Sermon::visible(auth()->user())->featured()->limit(3)->get();

        return view('sermons::memberpanel.sermons.index', compact('sermons', 'categories', 'tags', 'featuredSermons'));
    }

    /**
     * Show the form for creating a new sermon
     */
    public function create(): View
    {
        $categories = SermonCategory::active()->ordered()->get();
        $tags = SermonTag::all();

        $bibleVersions = $this->bibleApi->getVersions();
        $defaultVersion = $bibleVersions->first();
        $bibleBooks = $defaultVersion ? $this->bibleApi->getBooks($defaultVersion->id) : collect();

        return view('sermons::memberpanel.sermons.create', compact('categories', 'tags', 'bibleVersions', 'bibleBooks'));
    }

    /**
     * Store a newly created sermon
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:sermon_categories,id',
            'series_id' => 'nullable|exists:bible_series,id',
            'introduction' => 'nullable|string',
            'development' => 'nullable|string',
            'conclusion' => 'nullable|string',
            'application' => 'nullable|string',
            'full_content' => 'nullable|string',
            'status' => 'required|in:draft,published',
            'visibility' => 'required|in:public,members,private',
            'is_collaborative' => 'boolean',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:sermon_tags,id',
            'bible_references' => 'nullable|array',
            'sermon_date' => 'nullable|date',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['slug'] = Str::slug($validated['title']).'-'.Str::random(6);
        $validated['visibility'] = $validated['visibility'] ?? Sermon::VISIBILITY_PRIVATE;
        $validated['status'] = $validated['status'] ?? Sermon::STATUS_DRAFT;

        if (isset($validated['status']) && $validated['status'] === 'published') {
            $validated['published_at'] = now();
        }

        $sermon = Sermon::create($validated);

        // Attach tags
        if ($request->has('tags')) {
            $sermon->tags()->sync($request->tags);
        }

        // Create bible references
        if ($request->has('bible_references') && is_array($request->bible_references)) {
            foreach ($request->bible_references as $index => $ref) {
                if (! empty($ref['book'])) {
                    $sermon->bibleReferences()->create([
                        'book' => $ref['book'],
                        'chapter' => $ref['chapter'] ?? null,
                        'verses' => $ref['verses'] ?? null,
                        'reference_text' => $ref['reference_text'] ?? null,
                        'bible_version_id' => $ref['bible_version_id'] ?? null,
                        'book_id' => $ref['book_id'] ?? null,
                        'chapter_id' => $ref['chapter_id'] ?? null,
                        'type' => $ref['type'] ?? 'main',
                        'context' => $ref['context'] ?? null,
                        'order' => $index,
                    ]);
                }
            }
        }

        return redirect()->route('memberpanel.sermons.show', $sermon)
            ->with('success', 'Sermão criado com sucesso!');
    }

    /**
     * Show the form for editing the specified sermon (owner or co-author with can_edit).
     */
    public function edit(Sermon $sermon): View
    {
        $this->authorize('update', $sermon);

        $categories = SermonCategory::active()->ordered()->get();
        $tags = SermonTag::all();
        $series = \Modules\Sermons\App\Models\BibleSeries::where('status', 'published')->orderBy('title')->get();
        $sermon->load(['tags', 'bibleReferences', 'collaborators.user']);

        $bibleVersions = $this->bibleApi->getVersions();
        $defaultVersion = $bibleVersions->first();
        $bibleBooks = $defaultVersion ? $this->bibleApi->getBooks($defaultVersion->id) : collect();

        return view('sermons::memberpanel.sermons.edit', compact('sermon', 'categories', 'tags', 'bibleVersions', 'bibleBooks', 'series'));
    }

    /**
     * Update the specified sermon (owner or co-author with can_edit).
     */
    public function update(Request $request, Sermon $sermon): RedirectResponse
    {
        $this->authorize('update', $sermon);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:sermon_categories,id',
            'series_id' => 'nullable|exists:bible_series,id',
            'introduction' => 'nullable|string',
            'development' => 'nullable|string',
            'conclusion' => 'nullable|string',
            'application' => 'nullable|string',
            'sermon_structure_type' => 'nullable|string|in:expositivo,temático,textual',
            'full_content' => 'nullable|string',
            'status' => 'required|in:draft,published',
            'visibility' => 'required|in:public,members,private',
            'tags' => 'nullable|array',
            'bible_references' => 'nullable|array',
            'sermon_date' => 'nullable|date',
        ]);

        if (isset($validated['status']) && $validated['status'] === 'published' && ! $sermon->published_at) {
            $validated['published_at'] = now();
        }

        $sermon->update($validated);

        if ($request->has('tags')) {
            $tagIds = [];
            foreach ($request->tags as $tagName) {
                if (is_numeric($tagName)) {
                    $tag = SermonTag::find($tagName);
                    if ($tag) {
                        $tagIds[] = $tag->id;
                        continue;
                    }
                }
                $tag = SermonTag::firstOrCreate(['name' => $tagName], ['slug' => Str::slug($tagName)]);
                $tagIds[] = $tag->id;
            }
            $sermon->tags()->sync($tagIds);
        } else {
            $sermon->tags()->detach();
        }

        if ($request->has('bible_references') && is_array($request->bible_references)) {
            $sermon->bibleReferences()->delete();
            $bookIds = collect($request->bible_references)->pluck('book_id')->filter()->unique();
            $chapterIds = collect($request->bible_references)->pluck('chapter_id')->filter()->unique();
            $booksMap = $bookIds->isNotEmpty() ? \Modules\Bible\App\Models\Book::whereIn('id', $bookIds)->pluck('name', 'id') : collect();
            $chaptersMap = $chapterIds->isNotEmpty() ? \Modules\Bible\App\Models\Chapter::whereIn('id', $chapterIds)->pluck('chapter_number', 'id') : collect();
            foreach ($request->bible_references as $index => $ref) {
                if (! empty($ref['book_id']) && $booksMap->has($ref['book_id'])) {
                    $ref['book'] = $booksMap[$ref['book_id']];
                }
                if (! empty($ref['chapter_id']) && $chaptersMap->has($ref['chapter_id'])) {
                    $ref['chapter'] = $chaptersMap[$ref['chapter_id']];
                }
                if (! empty($ref['book'])) {
                    $sermon->bibleReferences()->create([
                        'book' => $ref['book'],
                        'chapter' => $ref['chapter'] ?? null,
                        'verses' => $ref['verses'] ?? null,
                        'reference_text' => $ref['reference_text'] ?? null,
                        'bible_version_id' => $ref['bible_version_id'] ?? null,
                        'book_id' => $ref['book_id'] ?? null,
                        'chapter_id' => $ref['chapter_id'] ?? null,
                        'type' => $ref['type'] ?? 'main',
                        'context' => $ref['context'] ?? null,
                        'order' => $index,
                    ]);
                }
            }
        }

        return redirect()->route('memberpanel.sermons.show', $sermon)->with('success', 'Sermão atualizado com sucesso!');
    }

    /**
     * Remove the specified sermon (only owner or policy allows delete).
     */
    public function destroy(Sermon $sermon): RedirectResponse
    {
        $this->authorize('delete', $sermon);
        $sermon->delete();
        return redirect()->route('memberpanel.sermons.my-sermons')->with('success', 'Sermão excluído.');
    }

    /**
     * Display the specified sermon
     */
    public function show(Sermon $sermon): View
    {
        $this->authorize('view', $sermon);

        $sermon->load(['category', 'user', 'tags', 'bibleReferences', 'comments.user', 'comments.replies.user']);
        $sermon->incrementViews();

        $isFavorite = $sermon->isFavoritedBy(auth()->user());
        $canEdit = $sermon->canEdit(auth()->user());

        return view('sermons::memberpanel.sermons.show', compact('sermon', 'isFavorite', 'canEdit'));
    }

    /**
     * Toggle favorite status
     */
    public function toggleFavorite(Sermon $sermon): RedirectResponse
    {
        $favorite = SermonFavorite::where('sermon_id', $sermon->id)
            ->where('user_id', auth()->id())
            ->first();

        if ($favorite) {
            $favorite->delete();
            $message = 'Sermão removido dos favoritos.';
        } else {
            SermonFavorite::create([
                'sermon_id' => $sermon->id,
                'user_id' => auth()->id(),
            ]);
            $message = 'Sermão adicionado aos favoritos.';
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * My sermons
     */
    public function mySermons(Request $request): View
    {
        $query = Sermon::where('user_id', auth()->id())
            ->with(['category', 'tags']);

        // Filter by status
        if ($request->has('status') && ! empty($request->status)) {
            $query->where('status', $request->status);
        }

        // Filter by search
        if ($request->has('search') && ! empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('subtitle', 'like', "%{$search}%");
            });
        }

        $sermons = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('sermons::memberpanel.sermons.my-sermons', compact('sermons'));
    }

    /**
     * My favorites
     */
    public function myFavorites(): View
    {
        $favorites = SermonFavorite::where('user_id', auth()->id())
            ->with(['sermon.category', 'sermon.user', 'sermon.tags'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('sermons::memberpanel.sermons.my-favorites', compact('favorites'));
    }

    /**
     * Store a comment
     */
    public function storeComment(Request $request, Sermon $sermon): RedirectResponse
    {
        $validated = $request->validate([
            'comment' => 'required|string|max:1000',
        ]);

        $sermon->comments()->create([
            'user_id' => auth()->id(),
            'comment' => $validated['comment'],
            'type' => 'comment', // Default type
        ]);

        return redirect()->back()->with('success', 'Comentário adicionado com sucesso!');
    }

    /**
     * Show collaborator invite page (accept/reject).
     */
    public function showCollaboratorInvite(SermonCollaborator $collaborator): View|RedirectResponse
    {
        if ($collaborator->user_id !== auth()->id()) {
            abort(403, 'Este convite não é para você.');
        }
        if ($collaborator->status !== SermonCollaborator::STATUS_PENDING) {
            return redirect()->route('memberpanel.sermons.show', $collaborator->sermon)
                ->with('info', 'Este convite já foi respondido.');
        }
        $collaborator->load('sermon.user');

        return view('sermons::memberpanel.sermons.collaborator-invite', compact('collaborator'));
    }

    /**
     * Accept or reject collaborator invite.
     */
    public function respondCollaborator(Request $request, SermonCollaborator $collaborator): RedirectResponse
    {
        if ($collaborator->user_id !== auth()->id()) {
            abort(403, 'Este convite não é para você.');
        }
        if ($collaborator->status !== SermonCollaborator::STATUS_PENDING) {
            return redirect()->route('memberpanel.sermons.index')->with('info', 'Convite já respondido.');
        }

        $action = $request->input('action', 'reject');
        if ($action === 'accept') {
            $collaborator->accept();
            $message = 'Você aceitou o convite para colaborar no sermão.';
        } else {
            $collaborator->reject();
            $message = 'Convite recusado.';
        }

        return redirect()->route('memberpanel.sermons.show', $collaborator->sermon)->with('success', $message);
    }

    /**
     * Export sermon to PDF for pulpit (full or topics, A4 or A5).
     */
    public function exportPdf(Request $request, Sermon $sermon): Response
    {
        $this->authorize('view', $sermon);

        $format = $request->input('format', 'full');
        $size = $request->input('size', 'a5');

        $mark = fn (string $html): string => str_replace(
            ['[TRANSIÇÃO]', '[APELO]'],
            ['<span class="transition">[TRANSIÇÃO]</span>', '<span class="apelo">[APELO]</span>'],
            $html
        );

        $introductionHtml = $sermon->introduction ? $mark($sermon->introduction) : '';
        $developmentHtml = $sermon->development ? $mark($sermon->development) : '';
        $conclusionHtml = $sermon->conclusion ? $mark($sermon->conclusion) : '';
        $applicationHtml = $sermon->application ? $mark($sermon->application) : '';
        $fullContentHtml = $sermon->full_content ? $mark($sermon->full_content) : '';

        $topicsFromFullContentHtml = '';
        if ($format === 'topics' && $sermon->full_content) {
            $topicsFromFullContentHtml = $this->extractTopicsFromHtml($sermon->full_content);
        }

        $html = view('sermons::admin.sermons.export-pdf', [
            'sermon' => $sermon,
            'format' => $format,
            'introductionHtml' => $introductionHtml,
            'developmentHtml' => $developmentHtml,
            'conclusionHtml' => $conclusionHtml,
            'applicationHtml' => $applicationHtml,
            'fullContentHtml' => $fullContentHtml,
            'topicsFromFullContentHtml' => $topicsFromFullContentHtml,
        ])->render();

        $mpdf = new Mpdf([
            'format' => $size === 'a4' ? 'A4' : 'A5',
            'margin_left' => 12,
            'margin_right' => 12,
            'margin_top' => 12,
            'margin_bottom' => 12,
        ]);
        $mpdf->WriteHTML($html);

        return response($mpdf->Output('', 'S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="esboco-' . Str::slug($sermon->title) . '.pdf"',
        ]);
    }

    private function extractTopicsFromHtml(string $html): string
    {
        $out = [];
        if (preg_match_all('/<h[1-6][^>]*>(.*?)<\/h[1-6]>/si', $html, $m)) {
            foreach ($m[1] as $heading) {
                $text = trim(strip_tags($heading));
                if ($text !== '') {
                    $out[] = '<p style="margin:0.3em 0;"><strong>' . htmlspecialchars($text) . '</strong></p>';
                }
            }
        }
        if (empty($out)) {
            $plain = trim(strip_tags($html));
            $plain = preg_replace('/\s+/', ' ', $plain);
            if ($plain !== '') {
                $out[] = '<p>' . htmlspecialchars(Str::limit($plain, 800)) . '</p>';
            }
        }
        return implode("\n", $out);
    }
}
