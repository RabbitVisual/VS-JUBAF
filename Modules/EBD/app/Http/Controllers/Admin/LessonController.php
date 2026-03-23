<?php

namespace Modules\EBD\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Bible\App\Services\BibleApiService;
use Modules\EBD\App\Models\EBDClass;
use Modules\EBD\App\Models\EBDCourse;
use Modules\EBD\App\Models\EBDLesson;
use Modules\EBD\App\Services\EBDSettingsService;

class LessonController extends Controller
{
    public function __construct(
        private BibleApiService $bibleApi
    ) {}

    /**
     * Display a listing of lessons
     */
    public function index(Request $request): View
    {
        $query = EBDLesson::with(['ebdClass', 'course', 'creator']);

        // Filter by course
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        // Filter by class
        if ($request->has('class_id') && ! empty($request->class_id)) {
            $query->where('class_id', $request->class_id);
        }

        // Filter by status
        if ($request->has('status') && ! empty($request->status)) {
            $query->where('status', $request->status);
        }

        // Filter by Bible book
        if ($request->has('bible_book') && ! empty($request->bible_book)) {
            $query->where('bible_book', $request->bible_book);
        }

        // Filter by date range
        if ($request->has('date_from')) {
            $query->where('lesson_date', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->where('lesson_date', '<=', $request->date_to);
        }

        $lessons = $query->orderByRaw('COALESCE(`order`, 999) ASC')->orderBy('lesson_date', 'desc')->paginate(15);
        $classes = EBDClass::active()->orderBy('name')->get();
        $courses = EBDCourse::orderBy('name')->get();

        return view('ebd::admin.lessons.index', compact('lessons', 'classes', 'courses'));
    }

    /**
     * Show the form for creating a new lesson
     */
    public function create(Request $request): View
    {
        $classes = EBDClass::active()->orderBy('name')->get();
        $courses = EBDCourse::approved()->orderBy('name')->get();
        $selectedClass = $request->get('class_id');
        $selectedCourse = $request->get('course_id');

        $defaultBibleVersion = EBDSettingsService::getDefaultBibleVersion();
        $defaultLessonTime = EBDSettingsService::getDefaultLessonTime();

        $bibleVersions = $this->bibleApi->getVersions();
        $version = $bibleVersions->firstWhere('abbreviation', strtoupper($defaultBibleVersion))
            ?? $bibleVersions->firstWhere('abbreviation', $defaultBibleVersion)
            ?? $bibleVersions->first();
        $defaultVersionId = $version?->id;
        $bibleBooks = $version ? $this->bibleApi->getBooks($version->id) : collect();

        return view('ebd::admin.lessons.create', compact('classes', 'courses', 'selectedClass', 'selectedCourse', 'bibleVersions', 'bibleBooks', 'defaultBibleVersion', 'defaultLessonTime', 'defaultVersionId'));
    }

    /**
     * Store a newly created lesson
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:ebd_classes,id',
            'course_id' => 'nullable|exists:ebd_courses,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'video_url' => 'nullable|string|max:500',
            'lesson_date' => 'required|date',
            'lesson_time' => 'required|date_format:H:i',
            'bible_book' => 'nullable|string|max:255',
            'bible_chapter' => 'nullable|integer|min:1',
            'bible_verses' => 'nullable|string|max:255',
            'bible_version' => 'nullable|string|max:10',
            'objective' => 'nullable|string',
            'introduction' => 'nullable|string',
            'development' => 'nullable|string',
            'conclusion' => 'nullable|string',
            'application' => 'nullable|string',
            'status' => 'required|in:scheduled,in_progress,completed,cancelled',
        ]);

        if (empty($validated['bible_version'])) {
            $validated['bible_version'] = EBDSettingsService::getDefaultBibleVersion();
        }
        $validated['order'] = $validated['order'] ?? 0;
        $validated['created_by'] = auth()->id();

        $lesson = EBDLesson::create($validated);

        if (class_exists(\Modules\EBD\App\Events\NewLessonAvailable::class)) {
            event(new \Modules\EBD\App\Events\NewLessonAvailable($lesson));
        }

        return redirect()->route('admin.ebd.lessons.index')
            ->with('success', 'Lição criada com sucesso!');
    }

    /**
     * Show the form for editing a lesson
     */
    public function edit(EBDLesson $lesson): View
    {
        $classes = EBDClass::active()->orderBy('name')->get();
        $courses = EBDCourse::approved()->orderBy('name')->get();

        $bibleVersions = $this->bibleApi->getVersions();
        $version = $lesson->bible_version
            ? ($bibleVersions->firstWhere('abbreviation', strtoupper($lesson->bible_version)) ?? $bibleVersions->firstWhere('abbreviation', $lesson->bible_version))
            : $bibleVersions->first();
        $bibleBooks = $version ? $this->bibleApi->getBooks($version->id) : collect();

        return view('ebd::admin.lessons.edit', compact('lesson', 'classes', 'courses', 'bibleVersions', 'bibleBooks'));
    }

    /**
     * Update the specified lesson
     */
    public function update(Request $request, EBDLesson $lesson): RedirectResponse
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:ebd_classes,id',
            'course_id' => 'nullable|exists:ebd_courses,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'video_url' => 'nullable|string|max:500',
            'lesson_date' => 'required|date',
            'lesson_time' => 'required|date_format:H:i',
            'bible_book' => 'nullable|string|max:255',
            'bible_chapter' => 'nullable|integer|min:1',
            'bible_verses' => 'nullable|string|max:255',
            'bible_version' => 'nullable|string|max:10',
            'objective' => 'nullable|string',
            'introduction' => 'nullable|string',
            'development' => 'nullable|string',
            'conclusion' => 'nullable|string',
            'application' => 'nullable|string',
            'status' => 'required|in:scheduled,in_progress,completed,cancelled',
        ]);

        $validated['order'] = $validated['order'] ?? $lesson->order ?? 0;
        $lesson->update($validated);

        return redirect()->route('admin.ebd.lessons.index')
            ->with('success', 'Lição atualizada com sucesso!');
    }

    /**
     * Remove the specified lesson
     */
    public function destroy(EBDLesson $lesson): RedirectResponse
    {
        $lesson->delete();

        return redirect()->route('admin.ebd.lessons.index')
            ->with('success', 'Lição removida com sucesso!');
    }

    /**
     * Show lesson details with Bible integration
     */
    public function show(EBDLesson $lesson): View
    {
        $lesson->load(['ebdClass.activeStudents.user', 'creator', 'materials', 'questions', 'attendance.student.user', 'evaluations']);

        // Get Bible chapter content if Bible module is available
        $bibleService = app(\Modules\EBD\App\Services\BibleService::class);
        $bibleContent = null;
        if ($lesson->bible_book && $lesson->bible_chapter) {
            $bibleContent = collect($bibleService->getVerses(
                $lesson->bible_book,
                $lesson->bible_chapter,
                $lesson->bible_version ?? 'nvi',
                $lesson->bible_verses
            ));
        }

        return view('ebd::admin.lessons.show', compact('lesson', 'bibleContent'));
    }
}
