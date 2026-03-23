<?php

namespace Modules\Diretoria\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Diretoria\App\Models\diretoriaProject;

class diretoriaProjectController extends Controller
{
    /**
     * Display a listing of projects.
     */
    public function index(Request $request): View
    {
        $query = diretoriaProject::with(['proposer', 'reviewer', 'ministry']);

        if ($request->has('status') && ! empty($request->status)) {
            $query->where('status', $request->status);
        }

        $projects = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('Diretoria::admin.projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new project.
     */
    public function create(): View
    {
        $ministries = \Modules\Ministries\App\Models\Ministry::active()->orderBy('name')->get();

        return view('Diretoria::admin.projects.create', compact('ministries'));
    }

    /**
     * Store a newly created project.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'justification' => 'nullable|string',
            'goals' => 'nullable|string',
            'department' => 'nullable|string',
            'ministry_id' => 'nullable|exists:ministries,id',
            'estimated_cost' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $project = diretoriaProject::create([
            ...$validated,
            'proposer_id' => auth()->id(),
            'status' => diretoriaProject::STATUS_SUBMITTED,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Projeto submetido com sucesso!',
            'redirect' => route('admin.Diretoria.projects.index'),
        ]);
    }

    /**
     * Display the specified project.
     */
    public function show(diretoriaProject $project): View
    {
        $project->load(['proposer', 'reviewer', 'ministry']);

        return view('Diretoria::admin.projects.show', compact('project'));
    }

    /**
     * Review the project (Approve/Reject/Comment).
     */
    public function review(Request $request, diretoriaProject $project): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected,under_review',
            'comments' => 'nullable|string',
        ]);

        $diretoriaMember = auth()->user()->diretoriaMember;

        if (! $diretoriaMember) {
            return response()->json(['message' => 'Apenas membros da diretoria podem revisar projetos.'], 403);
        }

        $project->update([
            'status' => $validated['status'],
            'diretoria_comments' => $validated['comments'],
            'reviewed_by' => $diretoriaMember->id,
            'reviewed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Revisão do projeto registrada com sucesso!',
            'redirect' => route('admin.Diretoria.projects.show', $project),
        ]);
    }

    /**
     * Update project (edit proposal).
     */
    public function update(Request $request, diretoriaProject $project): JsonResponse
    {
        // Only allow edits if draft or requested info
        if (! in_array($project->status, ['draft', 'submitted', 'under_review'])) {
            return response()->json(['message' => 'Não é possível editar este projeto neste status.'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'justification' => 'nullable|string',
            'goals' => 'nullable|string',
            'ministry_id' => 'nullable|exists:ministries,id',
            'estimated_cost' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $project->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Projeto atualizado com sucesso!',
            'redirect' => route('admin.Diretoria.projects.show', $project),
        ]);
    }

    /**
     * Remove the specified project.
     */
    public function destroy(diretoriaProject $project): JsonResponse
    {
        $project->delete();

        return response()->json([
            'success' => true,
            'message' => 'Projeto removido com sucesso!',
        ]);
    }
}
