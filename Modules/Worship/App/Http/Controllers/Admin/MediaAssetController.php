<?php

namespace Modules\Worship\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Worship\App\Models\WorshipMediaAsset;

class MediaAssetController extends Controller
{
    public function index()
    {
        $assets = WorshipMediaAsset::latest()->get();
        return response()->json($assets);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'file' => 'required|file|mimes:jpeg,png,jpg,mp4,mov,pdf|max:51200', // 50MB, inclui PDF para materiais de aula
        ]);

        $file = $request->file('file');
        $mime = $file->getMimeType();
        $isPdf = $mime === 'application/pdf';

        $path = $file->store(
            $isPdf ? 'worship/materials' : 'worship/backgrounds',
            'public'
        );
        $type = str_contains($mime, 'video') ? 'video' : ($isPdf ? 'pdf' : 'image');

        $title = $request->filled('title')
            ? $request->title
            : pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        $asset = WorshipMediaAsset::create([
            'title' => $title,
            'type' => $type,
            'file_path' => '/storage/' . $path,
        ]);

        return response()->json([
            'id' => $asset->id,
            'title' => $asset->title,
            'type' => $asset->type,
            'file_path' => $asset->file_path,
            'path' => $asset->file_path,
            'url' => asset($asset->file_path),
        ]);
    }
}
