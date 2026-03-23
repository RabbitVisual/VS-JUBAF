<?php

namespace Modules\EBD\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Modules\EBD\App\Models\EBDLesson;
use Modules\EBD\App\Models\EBDLessonMaterial;

class MaterialController extends Controller
{
    /**
     * Display a listing of materials for a lesson
     */
    public function index(EBDLesson $lesson): View
    {
        $lesson->load(['materials' => function ($query) {
            $query->orderBy('order', 'asc');
        }]);

        return view('ebd::admin.lessons.materials.index', compact('lesson'));
    }

    /**
     * Show the form for creating a new material
     */
    public function create(EBDLesson $lesson): View
    {
        return view('ebd::admin.lessons.materials.create', compact('lesson'));
    }

    /**
     * Store a newly created material
     */
    public function store(Request $request, EBDLesson $lesson): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:pdf,image,video,link,document,presentation',
            'file' => 'nullable|file|max:102400', // 100MB max
            'url' => 'nullable|url|max:500',
            'description' => 'nullable|string',
            'is_required' => 'boolean',
            'is_public' => 'boolean',
        ]);

        // Validate that either file or URL is provided
        if (empty($request->file('file')) && empty($validated['url'])) {
            return redirect()->back()
                ->withErrors(['file' => 'Você deve fornecer um arquivo ou uma URL.'])
                ->withInput();
        }

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time().'_'.$file->getClientOriginalName();
            $path = $file->storeAs('ebd/materials', $filename, 'public');
            $validated['file_path'] = $path;
        }

        // Set order to last
        $maxOrder = $lesson->materials()->max('order') ?? 0;
        $validated['order'] = $maxOrder + 1;

        // Set defaults
        $validated['is_required'] = $request->has('is_required');
        $validated['is_public'] = $request->has('is_public');

        $lesson->materials()->create($validated);

        return redirect()->route('admin.ebd.lessons.show', $lesson)
            ->with('success', 'Material adicionado com sucesso!');
    }

    /**
     * Show the form for editing a material
     */
    public function edit(EBDLesson $lesson, EBDLessonMaterial $material): View
    {
        // Ensure material belongs to lesson
        if ($material->lesson_id !== $lesson->id) {
            abort(404);
        }

        return view('ebd::admin.lessons.materials.edit', compact('lesson', 'material'));
    }

    /**
     * Update the specified material
     */
    public function update(Request $request, EBDLesson $lesson, EBDLessonMaterial $material): RedirectResponse
    {
        // Ensure material belongs to lesson
        if ($material->lesson_id !== $lesson->id) {
            abort(404);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:pdf,image,video,link,document,presentation',
            'file' => 'nullable|file|max:102400', // 100MB max
            'url' => 'nullable|url|max:500',
            'description' => 'nullable|string',
            'is_required' => 'boolean',
            'is_public' => 'boolean',
        ]);

        // Handle file upload (replace existing if new file provided)
        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($material->file_path && Storage::disk('public')->exists($material->file_path)) {
                Storage::disk('public')->delete($material->file_path);
            }

            $file = $request->file('file');
            $filename = time().'_'.$file->getClientOriginalName();
            $path = $file->storeAs('ebd/materials', $filename, 'public');
            $validated['file_path'] = $path;
        }

        $validated['is_required'] = $request->has('is_required');
        $validated['is_public'] = $request->has('is_public');

        $material->update($validated);

        return redirect()->route('admin.ebd.lessons.show', $lesson)
            ->with('success', 'Material atualizado com sucesso!');
    }

    /**
     * Remove the specified material
     */
    public function destroy(EBDLesson $lesson, EBDLessonMaterial $material): RedirectResponse
    {
        // Ensure material belongs to lesson
        if ($material->lesson_id !== $lesson->id) {
            abort(404);
        }

        // Delete file if exists
        if ($material->file_path && Storage::disk('public')->exists($material->file_path)) {
            Storage::disk('public')->delete($material->file_path);
        }

        $material->delete();

        return redirect()->route('admin.ebd.lessons.show', $lesson)
            ->with('success', 'Material removido com sucesso!');
    }

    /**
     * Reorder materials
     */
    public function reorder(Request $request, EBDLesson $lesson): RedirectResponse
    {
        $validated = $request->validate([
            'materials' => 'required|array',
            'materials.*' => 'required|exists:ebd_lesson_materials,id',
        ]);

        foreach ($validated['materials'] as $index => $materialId) {
            EBDLessonMaterial::where('id', $materialId)
                ->where('lesson_id', $lesson->id)
                ->update(['order' => $index + 1]);
        }

        return redirect()->route('admin.ebd.lessons.show', $lesson)
            ->with('success', 'Materiais reordenados com sucesso!');
    }
}
