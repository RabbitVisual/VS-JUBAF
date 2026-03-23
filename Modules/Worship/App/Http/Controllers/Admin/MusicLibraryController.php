<?php

namespace Modules\Worship\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Worship\App\Models\WorshipSong;

class MusicLibraryController extends Controller
{
    public function index()
    {
        $songs = WorshipSong::orderBy('title')->paginate(15);

        return view('worship::admin.songs.index', compact('songs'));
    }

    public function create()
    {
        return view('worship::admin.songs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content_chordpro' => 'required|string',
        ]);

        $song = WorshipSong::create($request->all());
        $song->regenerateLyrics();

        return redirect()->route('worship.admin.songs.index')->with('success', 'Música cadastrada com sucesso!');
    }

    public function edit(WorshipSong $song)
    {
        return view('worship::admin.songs.edit-chordpro', compact('song'));
    }

    public function update(Request $request, WorshipSong $song)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content_chordpro' => 'required|string',
        ]);

        $song->update($request->all());
        $song->regenerateLyrics();

        return redirect()->route('worship.admin.songs.show', $song->id)->with('success', 'Música atualizada com sucesso!');
    }

    public function show(WorshipSong $song)
    {
        return view('worship::admin.songs.show', compact('song'));
    }

    public function destroy(WorshipSong $song)
    {
        $song->delete();

        return redirect()->route('worship.admin.songs.index')->with('success', 'Música removida com sucesso!');
    }
}
