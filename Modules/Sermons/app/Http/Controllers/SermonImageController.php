<?php

namespace Modules\Sermons\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SermonImageController extends Controller
{
    /**
     * Upload an image from the rich text editor.
     */
    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:5120', // Max 5MB
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('sermons/content', 'public');
            $url = Storage::url($path);

            return response()->json([
                'url' => $url,
                'path' => $path,
            ]);
        }

        return response()->json(['error' => 'No image uploaded'], 400);
    }
}
