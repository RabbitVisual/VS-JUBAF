<?php

namespace Modules\EBD\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\ChurchCouncil\App\Models\CouncilApproval;
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

        return view('ebd::admin.courses.index', compact('courses'));
    }

    public function create(): View
    {
        return view('ebd::admin.courses.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:ebd_courses,slug',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['order'] = $validated['order'] ?? 0;
        if (empty($validated['slug'])) {
            $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);
        }

        EBDCourse::create($validated);

        return redirect()->route('admin.ebd.courses.index')
            ->with('success', 'Curso criado com sucesso!');
    }

    public function show(EBDCourse $course): View
    {
        $course->load(['lessons' => fn ($q) => $q->orderBy('order')]);
        return view('ebd::admin.courses.show', compact('course'));
    }

    public function edit(EBDCourse $course): View
    {
        return view('ebd::admin.courses.edit', compact('course'));
    }

    public function update(Request $request, EBDCourse $course): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:ebd_courses,slug,'.$course->id,
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['order'] = $validated['order'] ?? 0;

        $course->update($validated);

        return redirect()->route('admin.ebd.courses.index')
            ->with('success', 'Curso atualizado com sucesso!');
    }

    public function destroy(EBDCourse $course): RedirectResponse
    {
        if ($course->classes()->exists()) {
            return redirect()->back()
                ->with('error', 'Não é possível excluir um curso vinculado a turmas. Desvincule as turmas primeiro.');
        }
        $course->delete();
        return redirect()->route('admin.ebd.courses.index')
            ->with('success', 'Curso removido com sucesso!');
    }

    public function submitForHomologation(EBDCourse $course): RedirectResponse
    {
        if ($course->homologation_status === EBDCourse::HOMOLOGATION_APPROVED) {
            return redirect()->back()->with('info', 'Este curso já está homologado.');
        }
        if ($course->homologation_status === EBDCourse::HOMOLOGATION_PENDING) {
            return redirect()->back()->with('info', 'Este curso já está aguardando homologação do conselho.');
        }

        $course->update(['homologation_status' => EBDCourse::HOMOLOGATION_PENDING]);

        CouncilApproval::create([
            'approvable_type' => EBDCourse::class,
            'approvable_id' => $course->id,
            'approval_type' => CouncilApproval::TYPE_EBD_CURRICULUM,
            'status' => CouncilApproval::STATUS_PENDING,
            'request_details' => "Currículo EBD: {$course->name}. " . ($course->description ?: ''),
            'requested_by' => auth()->id(),
            'submitted_at' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'Curso enviado para homologação do conselho. Acompanhe em Admin > Conselho > Aprovações.');
    }
}
