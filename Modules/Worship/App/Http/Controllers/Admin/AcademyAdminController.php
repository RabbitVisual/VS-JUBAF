<?php

namespace Modules\Worship\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Worship\App\Models\AcademyCourse;
use Modules\Worship\App\Models\AcademyModule;
use Modules\Worship\App\Models\AcademyEnrollment;
use Modules\Worship\App\Models\AcademyLesson;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class AcademyAdminController extends Controller
{
    /**
     * Display the main admin view.
     */
    public function index()
    {
        // Stats
        $totalStudents = \Modules\Worship\App\Models\AcademyEnrollment::distinct('user_id')->count();
        $totalCourses = AcademyCourse::count();
        $totalLessons = AcademyLesson::count();
        $totalCompletions = \Modules\Worship\App\Models\AcademyProgress::count();

        // Recent Activity
        $recentProgress = \Modules\Worship\App\Models\AcademyProgress::with(['user', 'lesson.module.course'])
            ->latest('completed_at')
            ->take(5)
            ->get();

        // Leaderboard
        $leaderboard = \App\Models\User::query()
            ->join('worship_academy_progress', 'users.id', '=', 'worship_academy_progress.user_id')
            ->select('users.id', 'users.name', 'users.email', 'users.photo')
            ->selectRaw('count(worship_academy_progress.id) as lessons_completed')
            ->groupBy('users.id', 'users.name', 'users.email', 'users.photo')
            ->orderByDesc('lessons_completed')
            ->take(5)
            ->get();

        // Popular Courses
        $popularCourses = AcademyCourse::withCount('enrollments')
            ->orderByDesc('enrollments_count')
            ->take(3)
            ->get();

        // All Courses (for the list)
        $courses = AcademyCourse::latest()->get();

        return view('worship::admin.academy.dashboard', compact(
            'totalStudents', 'totalCourses', 'totalLessons', 'totalCompletions',
            'recentProgress', 'leaderboard', 'popularCourses', 'courses'
        ));
    }

    /**
     * Web: List all courses.
     */
    public function coursesIndex()
    {
        $courses = AcademyCourse::with(['instrument', 'lessons'])->latest()->paginate(12);
        return view('worship::admin.academy.courses.index', compact('courses'));
    }

    /**
     * Web: Create a new course view.
     */
    public function coursesCreate()
    {
        $instruments = \Illuminate\Support\Facades\DB::table('worship_instruments')->get();
        return view('worship::admin.academy.courses.create', compact('instruments'));
    }

    /**
     * Web: Store a new course (Web Form).
     */
    public function coursesStore(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'instrument_id' => 'required|exists:worship_instruments,id',
            'level' => 'required|string',
            'difficulty_level' => 'nullable|string',
            'category' => 'nullable|string|in:vocal,instrumental,teoria,espiritualidade',
            'description' => 'nullable|string',
            'biblical_reflection' => 'nullable|string',
            'status' => 'required|in:draft,published',
            'cover_image' => 'nullable|url',
        ]);

        $validated['slug'] = Str::slug($validated['title']) . '-' . uniqid();
        $validated['instructor_id'] = auth()->id();

        $course = AcademyCourse::create($validated);

        return redirect()->route('worship.admin.academy.courses.wizard', $course->id)->with('success', 'Curso criado. Agora adicione os módulos.');
    }

    /**
     * Web: Show course details.
     */
    public function coursesShow($id)
    {
        $course = AcademyCourse::with(['modules.lessons', 'instrument'])->findOrFail($id);
        return view('worship::admin.academy.courses.show', compact('course'));
    }

    /**
     * Web: Edit course view.
     */
    public function coursesEdit($id)
    {
        $course = AcademyCourse::findOrFail($id);
        $instruments = \Illuminate\Support\Facades\DB::table('worship_instruments')->get();
        return view('worship::admin.academy.courses.edit', compact('course', 'instruments'));
    }

    /**
     * Web: Update course (form submit).
     */
    public function coursesUpdate(Request $request, $id)
    {
        $course = AcademyCourse::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'instrument_id' => 'required|exists:worship_instruments,id',
            'level' => 'required|string',
            'difficulty_level' => 'nullable|string',
            'category' => 'nullable|string|in:vocal,instrumental,teoria,espiritualidade',
            'description' => 'nullable|string',
            'biblical_reflection' => 'nullable|string',
            'status' => 'required|in:draft,published',
            'cover_image' => 'nullable|url',
        ]);

        if ($course->title !== $validated['title']) {
            $validated['slug'] = Str::slug($validated['title']) . '-' . uniqid();
        }

        $course->update($validated);

        return redirect()->route('worship.admin.academy.courses.show', $course->id)->with('success', 'Curso atualizado com sucesso!');
    }

    /**
     * Dashboard de alunos (inscritos por curso, conclusões, nível técnico).
     */
    public function students(Request $request)
    {
        $courseId = $request->query('course_id');
        $query = AcademyEnrollment::with(['user', 'course']);

        if ($courseId) {
            $query->where('course_id', $courseId);
        }

        $enrollments = $query->latest()->paginate(20);
        $courses = AcademyCourse::where('status', 'published')->orderBy('title')->get();

        return view('worship::admin.academy.students', compact('enrollments', 'courses'));
    }

    /**
     * Wizard passo 2: organização de módulos (após criar curso).
     */
    public function wizardStep2($id)
    {
        $course = AcademyCourse::with('modules')->findOrFail($id);

        return view('worship::admin.academy.wizard-step2', compact('course'));
    }

    /**
     * Wizard passo 2: salvar módulos e redirecionar para o builder (passo 3).
     */
    public function wizardStep2Store(Request $request, $id)
    {
        $course = AcademyCourse::findOrFail($id);

        $validated = $request->validate([
            'modules' => 'required|array',
            'modules.*.title' => 'required|string|max:255',
            'modules.*.id' => 'nullable',
        ]);

        $keptIds = [];
        foreach ($validated['modules'] as $index => $mod) {
            if (! empty($mod['id'])) {
                $module = $course->modules()->find($mod['id']);
                if ($module) {
                    $module->update(['title' => $mod['title'], 'order' => $index]);
                    $keptIds[] = $module->id;
                }
            } else {
                $module = $course->modules()->create(['title' => $mod['title'], 'order' => $index]);
                $keptIds[] = $module->id;
            }
        }
        $course->modules()->whereNotIn('id', $keptIds)->delete();

        return redirect()->route('worship.admin.academy.builder', $course->id)->with('success', 'Módulos salvos. Agora adicione as lições.');
    }

    /**
     * Web: Destroy course and related data.
     */
    public function coursesDestroy($id)
    {
        $course = AcademyCourse::findOrFail($id);
        $course->delete();

        return redirect()->route('worship.admin.academy.courses.index')->with('success', 'Curso excluído com sucesso!');
    }

    /**
     * Web: Store a new lesson for a course.
     */
    public function storeLesson(Request $request, $courseId)
    {
        $course = AcademyCourse::findOrFail($courseId);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'nullable|string|in:video,chordpro,material,devotional',
            'video_url' => 'nullable|url',
            'duration_minutes' => 'nullable|integer',
            'content' => 'nullable|string',
            'content_chordpro' => 'nullable|string',
            'teacher_tips' => 'nullable|string',
            'multicam_video_url' => 'nullable|url',
            'pdf_path' => 'nullable|string|max:500',
            'sheet_music_pdf' => 'nullable|string|max:500',
            'bible_reference' => 'nullable|string|max:255',
        ]);

        $type = $validated['type'] ?? 'video';
        $content = $type === 'chordpro' && $request->filled('content_chordpro')
            ? $request->content_chordpro
            : ($validated['content'] ?? null);

        // Find or create a default module for the course
        $module = $course->modules()->firstOrCreate(
            ['title' => 'Módulo Geral'],
            ['order' => 0]
        );

        $module->lessons()->create([
            'title' => $validated['title'],
            'type' => $type,
            'video_url' => $validated['video_url'] ?? null,
            'duration_minutes' => $validated['duration_minutes'] ?? null,
            'content' => $content,
            'teacher_tips' => $validated['teacher_tips'] ?? null,
            'multicam_video_url' => $validated['multicam_video_url'] ?? null,
            'pdf_path' => $validated['pdf_path'] ?? null,
            'sheet_music_pdf' => $validated['sheet_music_pdf'] ?? null,
            'bible_reference' => $validated['bible_reference'] ?? null,
            'order' => $module->lessons()->count() + 1,
            'slug' => Str::slug($validated['title']) . '-' . uniqid(),
        ]);

        return redirect()->back()->with('success', 'Lição adicionada com sucesso!');
    }

    /**
     * Web: Update a lesson.
     */
    public function updateLesson(Request $request, $courseId, $lessonId)
    {
        $lesson = AcademyLesson::findOrFail($lessonId);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'nullable|string|in:video,chordpro,material,devotional',
            'video_url' => 'nullable|url',
            'duration_minutes' => 'nullable|integer',
            'content' => 'nullable|string',
            'content_chordpro' => 'nullable|string',
            'teacher_tips' => 'nullable|string',
            'multicam_video_url' => 'nullable|url',
            'pdf_path' => 'nullable|string|max:500',
            'sheet_music_pdf' => 'nullable|string|max:500',
            'bible_reference' => 'nullable|string|max:255',
        ]);

        $type = $validated['type'] ?? $lesson->type;
        if ($type === 'chordpro' && $request->filled('content_chordpro')) {
            $validated['content'] = $request->content_chordpro;
        }
        $lesson->update($validated);

        return redirect()->back()->with('success', 'Lição atualizada com sucesso!');
    }

    /**
     * Web: Destroy a lesson.
     */
    public function destroyLesson($courseId, $lessonId)
    {
        $lesson = AcademyLesson::findOrFail($lessonId);
        $lesson->delete();

        return redirect()->back()->with('success', 'Lição excluída com sucesso!');
    }

    /**
     * API: List all courses.
     */
    public function apiIndex()
    {
        return response()->json(AcademyCourse::with('instructor')->latest()->get());
    }

    /**
     * API: Create a new course.
     */
    public function apiStore(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'instrument_id' => 'required|exists:worship_instruments,id',
            'level' => 'required|string',
            'description' => 'nullable|string',
            'status' => 'nullable|string|in:draft,published',
        ]);

        $validated['status'] = $validated['status'] ?? 'draft';
        $validated['slug'] = Str::slug($validated['title']) . '-' . uniqid();
        $validated['instructor_id'] = auth()->id();

        $course = AcademyCourse::create($validated);

        return response()->json($course);
    }

    /**
     * API: Get full course details with structure.
     */
    public function apiShow($id)
    {
        $course = AcademyCourse::with(['modules.lessons'])->findOrFail($id);
        return response()->json($course);
    }

    /**
     * API: Update Course Basic Info.
     */
    public function apiUpdate(Request $request, $id)
    {
        $course = AcademyCourse::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'level' => 'required|string',
            'description' => 'nullable|string',
            'status' => 'required|string',
            'cover_image' => 'nullable|string',
        ]);

        if ($course->title !== $validated['title']) {
            $validated['slug'] = Str::slug($validated['title']) . '-' . uniqid();
        }

        $course->update($validated);
        return response()->json($course);
    }

    /**
     * API: Sync the entire course structure (Modules & Lessons order).
     * Expects a JSON tree: modules: [ { id, title, lessons: [ { id, title, ... } ] } ]
     */
    public function apiUpdateStructure(Request $request, $id)
    {
        $course = AcademyCourse::findOrFail($id);
        $data = $request->validate([
            'modules' => 'present|array',
            'modules.*.title' => 'required|string',
            'modules.*.lessons' => 'present|array',
        ]);

        DB::transaction(function () use ($course, $data) {
            $existingModuleIds = [];
            $allActiveLessonIds = [];

            foreach ($data['modules'] as $moduleIndex => $moduleData) {
                // Find or Create Module
                if (isset($moduleData['id']) && $moduleData['id']) {
                    $module = AcademyModule::where('course_id', $course->id)->find($moduleData['id']);
                    if ($module) {
                        $module->update([
                            'title' => $moduleData['title'],
                            'order' => $moduleIndex
                        ]);
                    } else {
                        // Create if ID provided but not found (unlikely, but safe)
                        $module = $course->modules()->create([
                            'title' => $moduleData['title'],
                            'order' => $moduleIndex
                        ]);
                    }
                } else {
                    $module = $course->modules()->create([
                        'title' => $moduleData['title'],
                        'order' => $moduleIndex
                    ]);
                }
                $existingModuleIds[] = $module->id;

                // Process Lessons for this Module
                foreach ($moduleData['lessons'] as $lessonIndex => $lessonData) {
                    $lessonPayload = [
                        'module_id' => $module->id,
                        'title' => $lessonData['title'] ?? 'Nova Aula',
                        'order' => $lessonIndex,
                        'type' => $lessonData['type'] ?? 'video',
                        'content' => $lessonData['content'] ?? null,
                        'video_url' => $lessonData['video_url'] ?? null,
                        'duration_minutes' => isset($lessonData['duration_minutes']) ? (int) $lessonData['duration_minutes'] : null,
                        'teacher_tips' => $lessonData['teacher_tips'] ?? null,
                        'pdf_path' => $lessonData['pdf_path'] ?? null,
                        'sheet_music_pdf' => $lessonData['sheet_music_pdf'] ?? null,
                        'bible_reference' => $lessonData['bible_reference'] ?? null,
                    ];

                    if (isset($lessonData['id']) && $lessonData['id']) {
                        $lesson = AcademyLesson::find($lessonData['id']);
                        if ($lesson) {
                            $lesson->update($lessonPayload);
                            $allActiveLessonIds[] = $lesson->id;
                        }
                    } else {
                        $lessonPayload['slug'] = Str::slug($lessonPayload['title']) . '-' . uniqid();
                        $lesson = $module->lessons()->create($lessonPayload);
                        $allActiveLessonIds[] = $lesson->id;
                    }
                }
            }

            // Delete modules not in the list
            $course->modules()->whereNotIn('id', $existingModuleIds)->delete();

            // Delete lessons not in the list (Orphans)
            // We use whereIn module_id to limit scope to remaining modules, or better, scope to course via relationship
            // But AcademyLesson belongs to Module.
            // We can delete lessons where module_id is in existingModuleIds AND id NOT IN allActiveLessonIds
            AcademyLesson::whereIn('module_id', $existingModuleIds)->whereNotIn('id', $allActiveLessonIds)->delete();
        });

        return response()->json(['message' => 'Structure saved', 'course' => $course->fresh('modules.lessons')]);
    }

    /**
     * API: Get or Create Lesson Details
     */
    public function apiGetLesson($id)
    {
        return response()->json(AcademyLesson::findOrFail($id));
    }

    /**
     * API: Update Lesson Content
     */
    public function apiUpdateLesson(Request $request, $id)
    {
        $lesson = AcademyLesson::findOrFail($id);
        $validated = $request->validate([
            'title' => 'required|string',
            'type' => 'required|in:video,chordpro,material',
            'content' => 'nullable|string',
            'video_url' => 'nullable|string',
            'duration_minutes' => 'nullable|integer'
        ]);

        $lesson->update($validated);
        return response()->json($lesson);
    }

    /**
     * API: Delete Course
     */
    public function apiDestroy($id)
    {
        $course = AcademyCourse::findOrFail($id);
        $course->delete();
        return response()->json(['message' => 'Course deleted']);
    }

    /**
     * API: Delete Lesson
     */
    public function apiDestroyLesson($id)
    {
        $lesson = AcademyLesson::findOrFail($id);
        $lesson->delete();
        return response()->json(['message' => 'Lesson deleted']);
    }
}
