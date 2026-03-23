<?php

namespace Modules\EBD\App\Http\Controllers\Pastoral;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\EBD\App\Models\EBDCourse;

class CourseController extends Controller
{
    public function index(Request $request): View
    {
        $query = EBDCourse::withCount(['lessons', 'classes']);

        if ($request->filled('homologation_status')) {
            $query->where('homologation_status', $request->homologation_status);
        }
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $courses = $query->orderBy('order')->orderBy('name')->paginate(15);

        return view('ebd::pastoralpanel.courses.index', compact('courses'));
    }

    public function show(EBDCourse $course): View
    {
        $course->load(['lessons' => fn ($q) => $q->orderBy('order')]);

        return view('ebd::pastoralpanel.courses.show', compact('course'));
    }
}
