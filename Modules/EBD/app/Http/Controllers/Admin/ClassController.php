<?php

namespace Modules\EBD\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\EBD\App\Models\EBDClass;
use Modules\EBD\App\Models\EBDCourse;
use Modules\EBD\App\Services\EBDSettingsService;

class ClassController extends Controller
{
    /**
     * Display a listing of classes
     */
    public function index(Request $request): View
    {
        $query = EBDClass::with(['teachers.user', 'students.user']);

        // Filter by age group
        if ($request->filled('age_group')) {
            $query->where('age_group', $request->age_group);
        }

        // Filter by active status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $classes = $query->orderByRaw('COALESCE(`order`, 999) ASC')->orderBy('name')->paginate(15);

        return view('ebd::admin.classes.index', compact('classes'));
    }

    /**
     * Show the form for creating a new class
     */
    public function create(): View
    {
        $defaultLessonTime = EBDSettingsService::getDefaultLessonTime();
        $courses = EBDCourse::approved()->orderBy('name')->get();

        return view('ebd::admin.classes.create', compact('defaultLessonTime', 'courses'));
    }

    /**
     * Store a newly created class
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'age_group' => 'required|in:adult,youth,teen,children',
            'description' => 'nullable|string',
            'room' => 'nullable|string|max:255',
            'schedule_time' => 'required|date_format:H:i',
            'max_students' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'order' => 'nullable|integer',
            'course_id' => 'nullable|exists:ebd_courses,id',
            'ministry_id' => 'nullable|exists:ministries,id',
        ]);

        EBDClass::create($validated);

        return redirect()->route('admin.ebd.classes.index')
            ->with('success', 'Classe criada com sucesso!');
    }

    /**
     * Show the form for editing a class
     */
    public function edit(EBDClass $class): View
    {
        $courses = EBDCourse::approved()->orderBy('name')->get();

        return view('ebd::admin.classes.edit', compact('class', 'courses'));
    }

    /**
     * Update the specified class
     */
    public function update(Request $request, EBDClass $class): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'age_group' => 'required|in:adult,youth,teen,children',
            'description' => 'nullable|string',
            'room' => 'nullable|string|max:255',
            'schedule_time' => 'required|date_format:H:i',
            'max_students' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'order' => 'nullable|integer',
            'course_id' => 'nullable|exists:ebd_courses,id',
            'ministry_id' => 'nullable|exists:ministries,id',
        ]);

        $class->update($validated);

        return redirect()->route('admin.ebd.classes.index')
            ->with('success', 'Classe atualizada com sucesso!');
    }

    /**
     * Remove the specified class
     */
    public function destroy(EBDClass $class): RedirectResponse
    {
        $class->delete();

        return redirect()->route('admin.ebd.classes.index')
            ->with('success', 'Classe removida com sucesso!');
    }
}
