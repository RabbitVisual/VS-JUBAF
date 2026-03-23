<?php

namespace Modules\EBD\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\EBD\App\Models\EbdLessonMedia;
use Modules\EBD\App\Models\EBDLesson;
use Illuminate\Support\Facades\Storage;

class LmsContentController extends Controller
{
    /**
     * Store media for a lesson.
     */
    public function storeMedia(Request $request, $lessonId)
    {
        $lesson = EBDLesson::findOrFail($lessonId);

        $validated = $request->validate([
            'type' => 'required|in:local_video,pdf,audio',
            'title' => 'required|string|max:255',
            'file' => 'required_without:path|file|max:51200', // 50MB max
            'path' => 'nullable|string',
            'duration_seconds' => 'nullable|integer',
        ]);

        $path = $validated['path'] ?? null;

        if ($request->hasFile('file')) {
             $path = $request->file('file')->store('ebd/lessons/media', 'public');
        }

        if (!$path) {
            return back()->withErrors(['file' => 'Arquivo ou caminho obrigatório.']);
        }

        EbdLessonMedia::create([
            'lesson_id' => $lessonId,
            'type' => $validated['type'],
            'title' => $validated['title'],
            'path' => $path,
            'duration_seconds' => $validated['duration_seconds'] ?? null,
        ]);

        return back()->with('success', 'Conteúdo adicionado com sucesso.');
    }

    /**
     * Remove media.
     */
    public function destroyMedia($id)
    {
        $media = EbdLessonMedia::findOrFail($id);

        if ($media->path && Storage::disk('public')->exists($media->path)) {
            Storage::disk('public')->delete($media->path);
        }

        $media->delete();

        return back()->with('success', 'Conteúdo removido.');
    }
}
