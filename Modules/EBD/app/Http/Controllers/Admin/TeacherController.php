<?php

namespace Modules\EBD\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\EBD\App\Models\EBDClass;
use Modules\EBD\App\Models\EBDTeacher;

class TeacherController extends Controller
{
    /**
     * Display a listing of teachers
     */
    public function index(Request $request): View
    {
        $query = EBDTeacher::with(['user', 'ebdClass']);

        // Filter by class
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->input('class_id'));
        }

        // Filter by active status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->input('is_active') === '1');
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        // Search by user name
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $teachers = $query->orderBy('created_at', 'desc')->paginate(15);
        $classes = EBDClass::active()->orderBy('name')->get();

        return view('ebd::admin.teachers.index', compact('teachers', 'classes'));
    }

    /**
     * Show the form for creating a new teacher
     */
    public function create(): View
    {
        $classes = EBDClass::active()->orderBy('name')->get();
        $users = User::orderBy('name')->get();

        return view('ebd::admin.teachers.create', compact('classes', 'users'));
    }

    /**
     * Store a newly created teacher
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'class_id' => 'required|exists:ebd_classes,id',
            'role' => 'required|in:teacher,assistant,substitute',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        // Check if user is already a teacher for this class
        $existing = EBDTeacher::where('user_id', $validated['user_id'])
            ->where('class_id', $validated['class_id'])
            ->first();

        if ($existing) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['user_id' => 'Este usuário já é professor desta classe.']);
        }

        EBDTeacher::create($validated);

        return redirect()->route('admin.ebd.teachers.index')
            ->with('success', 'Professor adicionado com sucesso!');
    }

    /**
     * Show the form for editing a teacher
     */
    public function edit(EBDTeacher $teacher): View
    {
        $teacher->load(['user', 'ebdClass']);
        $classes = EBDClass::active()->orderBy('name')->get();
        $users = User::orderBy('name')->get();

        return view('ebd::admin.teachers.edit', compact('teacher', 'classes', 'users'));
    }

    /**
     * Update the specified teacher
     */
    public function update(Request $request, EBDTeacher $teacher): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'class_id' => 'required|exists:ebd_classes,id',
            'role' => 'required|in:teacher,assistant,substitute',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        // Check if user is already a teacher for this class (excluding current)
        $existing = EBDTeacher::where('user_id', $validated['user_id'])
            ->where('class_id', $validated['class_id'])
            ->where('id', '!=', $teacher->id)
            ->first();

        if ($existing) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['user_id' => 'Este usuário já é professor desta classe.']);
        }

        $teacher->update($validated);

        return redirect()->route('admin.ebd.teachers.index')
            ->with('success', 'Professor atualizado com sucesso!');
    }

    /**
     * Remove the specified teacher
     */
    public function destroy(EBDTeacher $teacher): RedirectResponse
    {
        $teacher->delete();

        return redirect()->route('admin.ebd.teachers.index')
            ->with('success', 'Professor removido com sucesso!');
    }
}
