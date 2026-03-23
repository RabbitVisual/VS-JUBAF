<?php

namespace Modules\EBD\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\EBD\App\Models\EBDAttendance;
use Modules\EBD\App\Models\EBDLesson;
use Modules\EBD\App\Services\AttendanceService;

class AttendanceController extends Controller
{
    public function __construct(
        protected AttendanceService $attendanceService
    ) {}
    /**
     * Display a listing of attendance records
     */
    public function index(Request $request): View
    {
        $query = EBDAttendance::with(['lesson.ebdClass', 'student.user', 'registeredBy']);

        // Filter by lesson
        if ($request->filled('lesson_id')) {
            $query->where('lesson_id', $request->input('lesson_id'));
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereHas('lesson', function ($q) use ($request) {
                $q->where('lesson_date', '>=', $request->input('date_from'));
            });
        }
        if ($request->filled('date_to')) {
            $query->whereHas('lesson', function ($q) use ($request) {
                $q->where('lesson_date', '<=', $request->input('date_to'));
            });
        }

        // Stats for the current filtered query
        $stats = [
            'total' => (clone $query)->count(),
            'present' => (clone $query)->where('status', 'present')->count(),
        ];

        $attendance = $query->orderBy('created_at', 'desc')->paginate(15);
        $lessons = EBDLesson::with('ebdClass')->orderBy('lesson_date', 'desc')->limit(50)->get();

        return view('ebd::admin.attendance.index', compact('attendance', 'lessons', 'stats'));
    }

    /**
     * Show the form for taking attendance for a lesson
     */
    public function create(Request $request): View
    {
        $lessonId = $request->input('lesson_id');

        if (! $lessonId) {
            // If no lesson_id provided, show lesson selection
            $lessons = EBDLesson::with('ebdClass')
                ->orderBy('lesson_date', 'desc')
                ->limit(50)
                ->get();

            return view('ebd::admin.attendance.select-lesson', compact('lessons'));
        }

        $lesson = EBDLesson::with(['ebdClass.activeStudents.user'])->findOrFail($lessonId);

        // Get existing attendance
        $existingAttendance = EBDAttendance::where('lesson_id', $lessonId)
            ->pluck('status', 'student_id')
            ->toArray();

        return view('ebd::admin.attendance.create', compact('lesson', 'existingAttendance'));
    }

    /**
     * Store attendance for a lesson
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'lesson_id' => 'required|exists:ebd_lessons,id',
            'attendance' => 'required|array',
            'attendance.*.student_id' => 'required|exists:ebd_students,id',
            'attendance.*.status' => 'required|in:present,absent,late,excused',
            'attendance.*.arrival_time' => 'nullable|date_format:H:i',
            'attendance.*.notes' => 'nullable|string|max:500',
        ]);

        $lesson = EBDLesson::findOrFail($validated['lesson_id']);

        $this->attendanceService->bulkRecordFromRows($lesson, $validated['attendance'], auth()->user());

        return redirect()->route('admin.ebd.attendance.index', ['lesson_id' => $validated['lesson_id']])
            ->with('success', 'Presença registrada com sucesso!');
    }

    /**
     * Show attendance for a specific lesson
     */
    public function show(EBDLesson $lesson): View
    {
        $lesson->load(['ebdClass' => function ($query) {
            $query->with(['students' => function ($q) {
                $q->where('is_active', true)->with('user');
            }]);
        }, 'attendance.student.user']);

        $attendance = EBDAttendance::where('lesson_id', $lesson->id)
            ->with(['student.user', 'registeredBy'])
            ->get()
            ->keyBy('student_id');

        return view('ebd::admin.attendance.show', compact('lesson', 'attendance'));
    }

    /**
     * Update a single attendance record
     */
    public function update(Request $request, EBDAttendance $attendance): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:present,absent,late,excused',
            'arrival_time' => 'nullable|date_format:H:i',
            'notes' => 'nullable|string|max:500',
        ]);

        if (! empty($validated['arrival_time'])) {
            $lesson = $attendance->lesson;
            $validated['arrival_time'] = $lesson->lesson_date->format('Y-m-d').' '.$validated['arrival_time'].':00';
        }

        $attendance->update($validated);

        return redirect()->back()
            ->with('success', 'Presença atualizada com sucesso!');
    }

    /**
     * Remove the specified attendance record
     */
    public function destroy(EBDAttendance $attendance): RedirectResponse
    {
        $lessonId = $attendance->lesson_id;
        $attendance->delete();

        return redirect()->route('admin.ebd.attendance.show', $lessonId)
            ->with('success', 'Registro de presença removido com sucesso!');
    }
}
