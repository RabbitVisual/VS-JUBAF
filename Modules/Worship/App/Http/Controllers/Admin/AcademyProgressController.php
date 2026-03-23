<?php

namespace Modules\Worship\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Worship\App\Models\AcademyCourse;
use Modules\Worship\App\Models\AcademyLesson;
use Modules\Worship\App\Models\AcademyProgress;

class AcademyProgressController extends Controller
{
    public function dashboard()
    {
        // Overall Stats (Academy v2)
        $totalCourses = AcademyCourse::count();
        $totalLessons = AcademyLesson::count();
        $totalStudents = AcademyProgress::distinct('user_id')->count('user_id');
        $totalCompletions = AcademyProgress::count();

        // Recent Activity
        $recentProgress = AcademyProgress::with(['user', 'lesson.module.course'])
            ->latest('completed_at')
            ->limit(10)
            ->get();

        // Popular Courses (by completion count)
        $popularCourses = AcademyCourse::withCount('lessons')
            ->withCount(['lessons as total_completions' => function ($q) {
                $q->join('worship_academy_progress', 'worship_academy_lessons.id', '=', 'worship_academy_progress.lesson_id');
            }])
            ->orderByDesc('total_completions')
            ->limit(5)
            ->get();

        // Leaderboard (Students with most completions)
        $leaderboard = \App\Models\User::whereHas('academyProgress')
            ->withCount('academyProgress as lessons_completed')
            ->orderByDesc('lessons_completed')
            ->limit(5)
            ->get();

        return view('worship::admin.academy.dashboard', compact(
            'totalCourses',
            'totalLessons',
            'totalStudents',
            'totalCompletions',
            'recentProgress',
            'popularCourses',
            'leaderboard'
        ));
    }

    public function feedback(Request $request, AcademyProgress $progress)
    {
        $request->validate(['feedback' => 'required|string']);

        $progress->update(['feedback' => $request->feedback]);

        return back()->with('success', 'Feedback enviado!');
    }
}
