<?php

namespace Modules\EBD\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\EBD\App\Models\EBDClass;
use Modules\EBD\App\Models\EBDStudent;
use Modules\EBD\App\Services\EnrollmentService;

class StudentController extends Controller
{
    public function __construct(
        protected EnrollmentService $enrollmentService
    ) {}
    /**
     * Display a listing of students
     */
    public function index(Request $request): View
    {
        $query = EBDStudent::with(['user', 'ebdClass']);

        // Filter by class
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->input('class_id'));
        }

        // Filter by active status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->input('is_active') === '1');
        }

        // Search by user name or email
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $students = $query->orderBy('enrollment_date', 'desc')->paginate(15);
        $classes = EBDClass::active()->orderBy('name')->get();

        return view('ebd::admin.students.index', compact('students', 'classes'));
    }

    /**
     * Show the form for creating a new student enrollment
     */
    public function create(): View
    {
        $classes = EBDClass::active()->orderBy('name')->get();
        $users = User::orderBy('name')->get();

        return view('ebd::admin.students.create', compact('classes', 'users'));
    }

    /**
     * Store a newly created student enrollment
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'class_id' => 'required|exists:ebd_classes,id',
            'enrollment_date' => 'nullable|date',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $class = EBDClass::findOrFail($validated['class_id']);
        $user = User::findOrFail($validated['user_id']);

        if ($this->enrollmentService->isEnrolled($user, $class)) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['user_id' => 'Este usuário já está matriculado nesta classe.']);
        }

        if ($class->max_students) {
            $currentCount = EBDStudent::where('class_id', $class->id)->where('is_active', true)->count();
            if ($currentCount >= $class->max_students) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['class_id' => 'Esta classe atingiu sua capacidade máxima de alunos.']);
            }
        }

        $this->enrollmentService->enroll($user, $class, [
            'enrollment_date' => $validated['enrollment_date'] ?? now(),
            'is_active' => $request->boolean('is_active', true),
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('admin.ebd.students.index')
            ->with('success', 'Aluno matriculado com sucesso!');
    }

    /**
     * Show the form for editing a student enrollment
     */
    public function edit(EBDStudent $student): View
    {
        $student->load(['user', 'ebdClass']);
        $classes = EBDClass::active()->orderBy('name')->get();
        $users = User::orderBy('name')->get();

        return view('ebd::admin.students.edit', compact('student', 'classes', 'users'));
    }

    /**
     * Update the specified student enrollment
     */
    public function update(Request $request, EBDStudent $student): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'class_id' => 'required|exists:ebd_classes,id',
            'enrollment_date' => 'nullable|date',
            'graduation_date' => 'nullable|date|after:enrollment_date',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        // Check if user is already enrolled in this class (excluding current)
        $existing = EBDStudent::where('user_id', $validated['user_id'])
            ->where('class_id', $validated['class_id'])
            ->where('id', '!=', $student->id)
            ->first();

        if ($existing) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['user_id' => 'Este usuário já está matriculado nesta classe.']);
        }

        // Check class capacity if changing class or activating
        if ($validated['class_id'] != $student->class_id || ($validated['is_active'] && ! $student->is_active)) {
            $class = EBDClass::find($validated['class_id']);
            if ($class->max_students) {
                $currentCount = EBDStudent::where('class_id', $validated['class_id'])
                    ->where('is_active', true)
                    ->where('id', '!=', $student->id)
                    ->count();

                if ($currentCount >= $class->max_students) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['class_id' => 'Esta classe atingiu sua capacidade máxima de alunos.']);
                }
            }
        }

        $student->update($validated);

        return redirect()->route('admin.ebd.students.index')
            ->with('success', 'Matrícula atualizada com sucesso!');
    }

    /**
     * Remove the specified student enrollment
     */
    public function destroy(EBDStudent $student): RedirectResponse
    {
        $student->delete();

        return redirect()->route('admin.ebd.students.index')
            ->with('success', 'Matrícula removida com sucesso!');
    }
}
