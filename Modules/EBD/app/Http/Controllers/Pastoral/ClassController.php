<?php

namespace Modules\EBD\App\Http\Controllers\Pastoral;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\EBD\App\Models\EBDClass;

class ClassController extends Controller
{
    public function index(Request $request): View
    {
        $query = EBDClass::with(['teachers.user', 'students.user']);

        if ($request->filled('age_group')) {
            $query->where('age_group', $request->age_group);
        }
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $classes = $query->orderByRaw('COALESCE(`order`, 999) ASC')->orderBy('name')->paginate(15);

        return view('ebd::pastoralpanel.classes.index', compact('classes'));
    }

    public function show(EBDClass $class): View
    {
        $class->load(['teachers.user', 'activeStudents.user', 'lessons' => fn ($q) => $q->orderBy('lesson_date', 'desc')->limit(10)]);

        return view('ebd::pastoralpanel.classes.show', compact('class'));
    }
}
