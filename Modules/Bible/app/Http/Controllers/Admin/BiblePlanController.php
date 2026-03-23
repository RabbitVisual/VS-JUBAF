<?php

namespace Modules\Bible\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Bible\App\Models\BiblePlan;
use Modules\Bible\App\Models\Book;
use Modules\Bible\App\Services\PlanGeneratorEngine;

class BiblePlanController extends Controller
{
    protected $generator;

    public function __construct(PlanGeneratorEngine $generator)
    {
        $this->generator = $generator;
    }

    public function index()
    {
        $plans = BiblePlan::withCount('days')->latest()->paginate(10);

        return view('bible::admin.plans.index', compact('plans'));
    }

    public function create()
    {
        return view('bible::admin.plans.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'duration_days' => 'required|integer|min:1',
            'type' => 'required|in:manual,sequential,chronological',
            'reading_mode' => 'required|in:digital,physical_timer',
            'cover_image' => 'nullable|image|max:2048',
        ]);

        $coverPath = null;
        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')->store('bible/plans', 'public');
        }

        $slug = str()->slug($request->title);
        $originalSlug = $slug;
        $count = 1;
        while (BiblePlan::withTrashed()->where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }

        $plan = BiblePlan::create([
            'title' => $request->title,
            'slug' => $slug,
            'description' => $request->description,
            'type' => $request->type,
            'reading_mode' => $request->reading_mode,
            'duration_days' => $request->duration_days,
            'cover_image' => $coverPath,
            'allow_back_tracking' => $request->has('allow_back_tracking'),
            'is_active' => false, // Draft until generated/published
        ]);

        if ($request->type === 'sequential') {
            return redirect()->route('admin.bible.plans.generate', $plan->id);
        }

        return redirect()->route('admin.bible.plans.show', $plan->id);
    }

    public function show($id)
    {
        $plan = BiblePlan::with(['days.contents.book'])->findOrFail($id);
        $books = Book::orderBy('order')->get();

        return view('bible::admin.plans.show', compact('plan', 'books'));
    }

    public function edit($id)
    {
        $plan = BiblePlan::findOrFail($id);

        return view('bible::admin.plans.edit', compact('plan'));
    }

    public function update(Request $request, $id)
    {
        $plan = BiblePlan::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            // 'duration_days' => 'required|integer|min:1', // Changing duration might be dangerous if days exist
            'cover_image' => 'nullable|image|max:2048',
        ]);

        $data = [
            'title' => $request->title,
            'description' => $request->description,
            'allow_back_tracking' => $request->has('allow_back_tracking'),
            'is_active' => $request->has('is_active'),
        ];

        if ($request->has('duration_days')) {
            $data['duration_days'] = $request->duration_days;
            // Note: Changing duration doesn't auto-add/remove days unless we trigger regeneration.
            // We'll leave it as a metadata update for now.
        }

        // Handle Type and Reading Mode only if no days exist? Or assume user knows what they are doing.
        // Usually these define the structure. I'll allow editing them if they are just metadata, but changing 'type' significantly breaks content structure.
        // For safe edits, I'll stick to Title, Description, Settings, Image.

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('bible/plans', 'public');
        }

        $plan->update($data);

        return redirect()->route('admin.bible.plans.show', $plan->id)->with('success', 'Plano atualizado com sucesso!');
    }

    // Show the generator configuration screen
    public function generator($id)
    {
        $plan = BiblePlan::findOrFail($id);
        $books = Book::with('bibleVersion')->orderBy('order')->get();
        $versions = \Modules\Bible\App\Models\BibleVersion::active()->get();

        return view('bible::admin.plans.generator', compact('plan', 'books', 'versions'));
    }

    public function processGeneration(Request $request, $id)
    {
        $plan = BiblePlan::findOrFail($id);

        // Manual plans don't strictly need a version for the structure, but it's good practice.
        // Sequential/Chronological definitely do.
        $request->validate([
            'bible_version_id' => 'required_unless:type,manual|exists:bible_versions,id',
        ]);

        $versionId = $request->input('bible_version_id');

        // Allow 'order_type' (new standard) or fallback to 'template_type' (legacy)
        $orderType = $request->input('order_type', $request->input('template_type'));

        try {
            if ($plan->type === 'manual') {
                // Just create the days in blank
                $plan->days()->delete(); // Clear existing
                for ($i = 1; $i <= $plan->duration_days; $i++) {
                    \Modules\Bible\App\Models\BiblePlanDay::create([
                        'plan_id' => $plan->id,
                        'day_number' => $i,
                        'title' => 'Dia '.$i,
                    ]);
                }
            } elseif ($orderType) {
                // Handle Template Based Generation (Canonical, Historical, Christ Centered)
                $this->generator->generateFromTemplate($plan, $orderType, $versionId);
            } elseif ($plan->type === 'chronological') {
                // Default fallback if no template selected but chronological type (should be covered by template now)
                $this->generator->generateFromTemplate($plan, 'canonical', $versionId);
            } else {
                // Sequential handling requires book_ids
                $bookIds = $request->input('book_ids', []);
                if (empty($bookIds)) {
                    return back()->with('error', 'Selecione pelo menos um livro.');
                }
                // Pass versionId to ensure we process the correct books
                $this->generator->generateSequential($plan, $bookIds, $plan->duration_days);
            }

            $plan->update(['is_active' => true]);

            return redirect()->route('admin.bible.plans.show', $plan->id)
                ->with('success', 'Plano gerado com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao gerar: '.$e->getMessage());
        }
    }

    public function destroy($id)
    {
        $plan = BiblePlan::withTrashed()->findOrFail($id);

        // Manual cleanup of relations to ensure db is clean
        $plan->days()->each(function ($day) {
            $day->contents()->delete();
            $day->delete();
        });

        $plan->forceDelete();

        return back()->with('success', 'Plano excluído permanentemente.');
    }

    // Content Management Page
    public function editDay($planId, $dayId)
    {
        $plan = BiblePlan::findOrFail($planId);
        $day = \Modules\Bible\App\Models\BiblePlanDay::with(['contents.book'])->where('plan_id', $planId)->findOrFail($dayId);

        // Load relationships for content editing
        $books = Book::orderBy('order')->get();
        // Versions for the selector
        $versions = \Modules\Bible\App\Models\BibleVersion::where('is_active', true)->get();

        return view('bible::admin.plans.edit-day', compact('plan', 'day', 'books', 'versions'));
    }

    public function storeContent(Request $request, $dayId)
    {
        $day = \Modules\Bible\App\Models\BiblePlanDay::findOrFail($dayId);

        $request->validate([
            'type' => 'required|in:scripture,devotional,video',
            'title' => 'nullable|string|max:255',
        ]);

        $data = [
            'plan_day_id' => $day->id,
            'type' => $request->type,
            'title' => $request->title,
            'order_index' => $day->contents()->count(),
        ];

        if ($request->type === 'scripture') {
            $request->validate([
                'book_id' => 'required|exists:books,id',
                'chapter_start' => 'required|integer',
            ]);
            $data['book_id'] = $request->book_id;
            $data['chapter_start'] = $request->chapter_start;
            $data['chapter_end'] = $request->chapter_end ?? $request->chapter_start;
            $data['verse_start'] = $request->verse_start;
            $data['verse_end'] = $request->verse_end;
        } elseif ($request->type === 'devotional') {
            $data['body'] = $request->body; // HTML from editor
        } elseif ($request->type === 'video') {
            $request->validate(['video_url' => 'required|url']);
            $data['body'] = $request->video_url;
        }

        \Modules\Bible\App\Models\BiblePlanContent::create($data);

        return back()->with('success', 'Conteúdo adicionado com sucesso!');
    }

    public function updateContent(Request $request, $contentId)
    {
        $content = \Modules\Bible\App\Models\BiblePlanContent::findOrFail($contentId);

        $request->validate([
            'type' => 'required|in:scripture,devotional,video',
            'title' => 'nullable|string|max:255',
        ]);

        $data = [
            'type' => $request->type,
            'title' => $request->title,
        ];

        if ($request->type === 'scripture') {
            $request->validate([
                'book_id' => 'required|exists:books,id',
                'chapter_start' => 'required|integer',
            ]);
            $data['book_id'] = $request->book_id;
            $data['chapter_start'] = $request->chapter_start;
            $data['chapter_end'] = $request->chapter_end ?? $request->chapter_start;
            $data['verse_start'] = $request->verse_start;
            $data['verse_end'] = $request->verse_end;
        } elseif ($request->type === 'devotional') {
            $data['body'] = $request->body;
        } elseif ($request->type === 'video') {
            $request->validate(['video_url' => 'required|url']);
            $data['body'] = $request->video_url;
        }

        $content->update($data);

        return back()->with('success', 'Conteúdo atualizado com sucesso!');
    }

    public function destroyContent($contentId)
    {
        \Modules\Bible\App\Models\BiblePlanContent::findOrFail($contentId)->delete();

        return back()->with('success', 'Conteúdo removido.');
    }
}
