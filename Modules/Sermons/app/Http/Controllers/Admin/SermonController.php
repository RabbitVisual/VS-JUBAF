<?php

namespace Modules\Sermons\App\Http\Controllers\Admin;

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
use Modules\Sermons\App\Models\SermonTag;
use Modules\Sermons\App\Jobs\NotifyMembersOfNewContent;

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
        $query = Sermon::with(['category', 'user', 'tags']);

        // Filter by category
        if ($request->has('category_id') && ! empty($request->category_id)) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by status
        if ($request->has('status') && ! empty($request->status)) {
            $query->where('status', $request->status);
        }

        // Filter by visibility
        if ($request->has('visibility') && ! empty($request->visibility)) {
            $query->where('visibility', $request->visibility);
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

        $sermons = $query->orderBy('created_at', 'desc')->paginate(15);
        $categories = SermonCategory::active()->ordered()->get();
        $tags = SermonTag::all();

        return view('sermons::admin.sermons.index', compact('sermons', 'categories', 'tags'));
    }

    /**
     * Show the form for creating a new sermon
     */
    public function create(): View
    {
        $categories = SermonCategory::active()->ordered()->get();
        $tags = SermonTag::all();
        $series = \Modules\Sermons\App\Models\BibleSeries::where('status', 'published')->orderBy('title')->get();
        $worshipSongs = \Modules\Worship\App\Models\WorshipSong::orderBy('title')->get(['id', 'title']);

        $bibleVersions = $this->bibleApi->getVersions();
        $defaultVersion = $bibleVersions->first();
        $bibleBooks = $defaultVersion ? $this->bibleApi->getBooks($defaultVersion->id) : collect();

        return view('sermons::admin.sermons.create', compact('categories', 'tags', 'bibleVersions', 'bibleBooks', 'series', 'worshipSongs'));
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
            'sermon_structure_type' => 'nullable|string|in:expositivo,temático,textual',
            'structure_meta' => 'nullable|array',
            'full_content' => 'nullable|string',
            'cover_image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:15360',
            'status' => 'required|in:draft,published,archived',
            'visibility' => 'required|in:public,members,private',
            'is_collaborative' => 'boolean',
            'is_featured' => 'boolean',
            'tags' => 'nullable|array',
            'bible_references' => 'nullable|array',
            'sermon_date' => 'nullable|date',
            'attachments.*' => 'file|mimes:pdf,doc,docx,txt|max:10240',
            'worship_suggestion_id' => 'nullable|exists:worship_songs,id',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['slug'] = Str::slug($validated['title']).'-'.Str::random(6);
        $validated['visibility'] = $validated['visibility'] ?? Sermon::VISIBILITY_PRIVATE;
        $validated['status'] = $validated['status'] ?? Sermon::STATUS_DRAFT;

        if ($request->hasFile('cover_image_file')) {
            $path = $request->file('cover_image_file')->store('sermons/covers', 'public');
            $validated['cover_image'] = $path;
        }

        if ($request->hasFile('attachments')) {
            $attachments = [];
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('sermons/attachments', 'public');
                $attachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'mime' => $file->getMimeType(),
                ];
            }
            $validated['attachments'] = $attachments;
        }

        if (isset($validated['status']) && $validated['status'] === 'published') {
            $validated['published_at'] = now();
        }

        $sermon = Sermon::create($validated);

        if ($sermon->status === 'published' && $sermon->visibility === 'members') {
            NotifyMembersOfNewContent::dispatch($sermon);
        }

        // Attach tags (Find or Create)
        if ($request->has('tags')) {
            $tagIds = [];
            foreach ($request->tags as $tagName) {
                // If it's numeric, assume it's an ID (if passed from existing)
                if (is_numeric($tagName)) {
                    $tag = SermonTag::find($tagName);
                    if ($tag) {
                        $tagIds[] = $tag->id;

                        continue;
                    }
                }

                // Otherwise treat as name
                $tag = SermonTag::firstOrCreate(['name' => $tagName], ['slug' => Str::slug($tagName)]);
                $tagIds[] = $tag->id;
            }
            $sermon->tags()->sync($tagIds);
        }

        // Create bible references
        if ($request->has('bible_references') && is_array($request->bible_references)) {
            $bookIds = collect($request->bible_references)->pluck('book_id')->filter()->unique();
            $chapterIds = collect($request->bible_references)->pluck('chapter_id')->filter()->unique();

            $booksMap = $bookIds->isNotEmpty()
                ? \Modules\Bible\App\Models\Book::whereIn('id', $bookIds)->pluck('name', 'id')
                : collect();

            $chaptersMap = $chapterIds->isNotEmpty()
                ? \Modules\Bible\App\Models\Chapter::whereIn('id', $chapterIds)->pluck('chapter_number', 'id')
                : collect();

            foreach ($request->bible_references as $index => $ref) {
                // If book_id provided, lookup name
                if (! empty($ref['book_id']) && $booksMap->has($ref['book_id'])) {
                    $ref['book'] = $booksMap[$ref['book_id']];
                }

                // If chapter_id provided, lookup number
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

        return redirect()->route('admin.sermons.sermons.show', $sermon)
            ->with('success', 'Sermão criado com sucesso!');
    }

    /**
     * Display the specified sermon
     */
    public function show(Sermon $sermon): View
    {
        $this->authorize('view', $sermon);
        $sermon->load(['category', 'user', 'tags', 'bibleReferences', 'collaborators.user', 'comments.user']);
        $sermon->incrementViews();

        return view('sermons::admin.sermons.show', compact('sermon'));
    }

    /**
     * Show the form for editing the specified sermon
     */
    public function edit(Sermon $sermon): View
    {
        $this->authorize('update', $sermon);
        $categories = SermonCategory::active()->ordered()->get();
        $tags = SermonTag::all();
        $series = \Modules\Sermons\App\Models\BibleSeries::where('status', 'published')->orderBy('title')->get();
        $worshipSongs = \Modules\Worship\App\Models\WorshipSong::orderBy('title')->get(['id', 'title']);
        $sermon->load(['tags', 'bibleReferences', 'collaborators.user']);

        $bibleVersions = $this->bibleApi->getVersions();
        $defaultVersion = $bibleVersions->first();
        $bibleBooks = $defaultVersion ? $this->bibleApi->getBooks($defaultVersion->id) : collect();

        return view('sermons::admin.sermons.edit', compact('sermon', 'categories', 'tags', 'bibleVersions', 'bibleBooks', 'series', 'worshipSongs'));
    }

    /**
     * Update the specified sermon
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
            'structure_meta' => 'nullable|array',
            'full_content' => 'nullable|string',
            'cover_image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:15360',
            'status' => 'required|in:draft,published,archived',
            'visibility' => 'required|in:public,members,private',
            'is_collaborative' => 'boolean',
            'is_featured' => 'boolean',
            'tags' => 'nullable|array',
            'bible_references' => 'nullable|array',
            'sermon_date' => 'nullable|date',
            'attachments.*' => 'file|mimes:pdf,doc,docx,txt|max:10240',
            'worship_suggestion_id' => 'nullable|exists:worship_songs,id',
        ]);

        if (isset($validated['status']) && $validated['status'] === 'published' && ! $sermon->published_at) {
            $validated['published_at'] = now();
        }

        if ($request->hasFile('cover_image_file')) {
            // Delete old image
            if ($sermon->cover_image && \Storage::disk('public')->exists($sermon->cover_image)) {
                \Storage::disk('public')->delete($sermon->cover_image);
            }
            $path = $request->file('cover_image_file')->store('sermons/covers', 'public');
            $validated['cover_image'] = $path;
        }

        if ($request->hasFile('attachments')) {
            $currentAttachments = $sermon->attachments ?? [];
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('sermons/attachments', 'public');
                $currentAttachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'mime' => $file->getMimeType(),
                ];
            }
            $validated['attachments'] = $currentAttachments;
        }

        $sermon->update($validated);

        // Sync tags (Find or Create)
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

        // Update bible references
        if ($request->has('bible_references') && is_array($request->bible_references)) {
            $sermon->bibleReferences()->delete();

            $bookIds = collect($request->bible_references)->pluck('book_id')->filter()->unique();
            $chapterIds = collect($request->bible_references)->pluck('chapter_id')->filter()->unique();

            $booksMap = $bookIds->isNotEmpty()
                ? \Modules\Bible\App\Models\Book::whereIn('id', $bookIds)->pluck('name', 'id')
                : collect();

            $chaptersMap = $chapterIds->isNotEmpty()
                ? \Modules\Bible\App\Models\Chapter::whereIn('id', $chapterIds)->pluck('chapter_number', 'id')
                : collect();

            foreach ($request->bible_references as $index => $ref) {
                // If book_id provided, lookup name (Duplicate logic, could isolate but inline is fine for now)
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

        return redirect()->route('admin.sermons.sermons.show', $sermon)
            ->with('success', 'Sermão atualizado com sucesso!');
    }

    /**
     * Remove the specified sermon
     */
    public function destroy(Sermon $sermon): RedirectResponse
    {
        $this->authorize('delete', $sermon);

        $sermon->delete();

        return redirect()->route('admin.sermons.sermons.index')
            ->with('success', 'Sermão removido com sucesso!');
    }

    /**
     * Invite collaborator by email (owner only).
     */
    public function inviteCollaborator(Request $request, Sermon $sermon): RedirectResponse
    {
        $this->authorize('update', $sermon);
        if ($sermon->user_id !== auth()->id()) {
            abort(403, 'Apenas o autor pode convidar co-autores.');
        }

        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = \App\Models\User::where('email', $validated['email'])->first();
        if ($user->id === $sermon->user_id) {
            return redirect()->back()->with('error', 'Não é possível convidar o próprio autor.');
        }

        $existing = $sermon->collaborators()->where('user_id', $user->id)->first();
        if ($existing) {
            return redirect()->back()->with('error', 'Este usuário já foi convidado ou já é colaborador.');
        }

        $collab = $sermon->collaborators()->create([
            'user_id' => $user->id,
            'role' => \Modules\Sermons\App\Models\SermonCollaborator::ROLE_EDITOR,
            'can_edit' => true,
            'status' => \Modules\Sermons\App\Models\SermonCollaborator::STATUS_PENDING,
            'invited_at' => now(),
        ]);

        if (class_exists(\Modules\Notifications\App\Services\InAppNotificationService::class)) {
            app(\Modules\Notifications\App\Services\InAppNotificationService::class)->sendToUser(
                $user,
                'Convite para co-autoria',
                'Você foi convidado a colaborar no sermão "' . $sermon->title . '".',
                [
                    'action_url' => route('memberpanel.sermons.collaborator.invite', $collab),
                    'action_text' => 'Ver convite',
                    'notification_type' => 'sermon_collaboration',
                ]
            );
        }

        return redirect()->back()->with('success', 'Convite enviado para ' . $user->email);
    }

    /**
     * Export sermon to PDF (full or topics, A4 or A5). Marcadores [TRANSIÇÃO] e [APELO] destacados.
     */
    public function exportPdf(Request $request, Sermon $sermon): Response
    {
        $this->authorize('view', $sermon);

        $format = $request->input('format', 'full'); // full | topics
        $size = $request->input('size', 'a5'); // a4 | a5

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

    /**
     * Extrai tópicos (títulos e blocos principais) do HTML do full_content para o PDF "apenas tópicos".
     */
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
