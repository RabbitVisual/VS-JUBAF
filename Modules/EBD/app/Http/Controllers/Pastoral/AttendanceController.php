<?php

namespace Modules\EBD\App\Http\Controllers\Pastoral;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\EBD\App\Models\EBDAttendance;
use Modules\EBD\App\Models\EBDLesson;

class AttendanceController extends Controller
{
    public function index(Request $request): View
    {
        $query = EBDAttendance::with(['lesson.ebdClass', 'student.user', 'registeredBy']);

        if ($request->filled('lesson_id')) {
            $query->where('lesson_id', $request->lesson_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date_from')) {
            $query->whereHas('lesson', fn ($q) => $q->where('lesson_date', '>=', $request->date_from));
        }
        if ($request->filled('date_to')) {
            $query->whereHas('lesson', fn ($q) => $q->where('lesson_date', '<=', $request->date_to));
        }

        $stats = [
            'total' => (clone $query)->count(),
            'present' => (clone $query)->where('status', 'present')->count(),
        ];
        $attendance = $query->orderBy('created_at', 'desc')->paginate(15);
        $lessons = EBDLesson::with('ebdClass')->orderBy('lesson_date', 'desc')->limit(50)->get();

        return view('ebd::pastoralpanel.attendance.index', compact('attendance', 'lessons', 'stats'));
    }

    public function show(EBDLesson $lesson): View
    {
        $lesson->load(['ebdClass', 'attendance.student.user', 'registeredBy']);
        $attendance = $lesson->attendance;

        return view('ebd::pastoralpanel.attendance.show', compact('lesson', 'attendance'));
    }
}
