<?php

namespace Modules\Bible\App\Http\Controllers\MemberPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Bible\App\Models\BibleFavorite;

class FavoriteController extends Controller
{
    /**
     * Store or update a favorite/highlight for a verse.
     */
    public function toggle(Request $request, $id)
    {
        $user = Auth::user();
        $color = $request->input('color');
        $note = $request->input('note');

        $favorite = BibleFavorite::updateOrCreate(
            [
                'user_id' => $user->id,
                'verse_id' => $id,
            ],
            [
                'color' => $color,
                'note' => $note,
            ]
        );

        return response()->json([
            'success' => true,
            'favorite' => $favorite,
            'message' => 'Versículo atualizado com sucesso.',
        ]);
    }

    /**
     * Remove a favorite/highlight.
     */
    public function destroy($id)
    {
        $user = Auth::user();

        BibleFavorite::where('user_id', $user->id)
            ->where('verse_id', $id)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Destaque removido.',
        ]);
    }

    /**
     * Batch update multiple verses.
     */
    public function batchUpdate(Request $request)
    {
        $data = $request->validate([
            'verses' => 'required|array',
            'verses.*' => 'integer',
            'type' => 'required|in:highlight,note',
            'color' => 'nullable|string',
            'note' => 'nullable|string',
        ]);

        $userId = Auth::id();

        DB::transaction(function () use ($data, $userId) {
            foreach ($data['verses'] as $verseId) {
                // Determine update data based on type
                $updateData = [];
                if ($data['type'] === 'highlight') {
                    $updateData['color'] = $data['color'];
                } else {
                    $updateData['color'] = $data['color'];
                    $updateData['note'] = $data['note'];
                }

                $fav = BibleFavorite::updateOrCreate(
                    [
                        'user_id' => $userId,
                        'verse_id' => $verseId,
                    ],
                    $updateData
                );

                // Garbage collection: delete if both are effectively empty
                if (! $fav->color && ! $fav->note) {
                    $fav->delete();
                }
            }
        });

        return response()->json(['status' => 'success', 'message' => 'Destaques atualizados com sucesso.']);
    }
}
