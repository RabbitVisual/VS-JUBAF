<?php

namespace Modules\Worship\App\Http\Controllers\MemberPanel;

use App\Http\Controllers\Controller;
use Modules\Worship\App\Models\AcademyCourse;
use Illuminate\Support\Facades\Auth;

class AcademyMemberController extends Controller
{
    /**
     * Display the list of available courses.
     */
    public function index()
    {
        $courses = AcademyCourse::with(['instrument', 'enrollments' => function($q) {
            $q->where('user_id', Auth::id());
        }])
        ->where('status', 'published') // Assuming we only show published courses
        ->latest()
        ->get();

        // Calculate progress for each course
        $courses->each(function($course) {
            $course->progress_percent = $course->enrollments->first()?->progress_percent ?? 0;
        });

        return view('worship::memberpanel.academy.index', compact('courses'));
    }

    /**
     * Display the classroom view (Alpine/Vue container). Data loaded via API v1.
     */
    public function classroom($courseId)
    {
        $course = AcademyCourse::findOrFail($courseId);

        return view('worship::memberpanel.academy.classroom', compact('course'));
    }
}
