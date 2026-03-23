<?php

namespace Modules\EBD\App\Http\Controllers\Pastoral;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\EBD\App\Models\EBDClass;
use Modules\EBD\App\Models\EBDStudent;

class StudentController extends Controller
{
    public function index(Request $request): View
    {
        $query = EBDStudent::with(['user', 'ebdClass']);

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
        }

        $students = $query->orderBy('enrollment_date', 'desc')->paginate(15);
        $classes = EBDClass::where('is_active', true)->orderBy('name')->get();

        return view('ebd::pastoralpanel.students.index', compact('students', 'classes'));
    }
}
