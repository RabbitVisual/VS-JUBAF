<?php

namespace Modules\Worship\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\PdfService;
use Illuminate\Http\Request;
use Modules\Worship\App\Models\WorshipSetlist;
use Modules\Worship\App\Models\WorshipSetlistItem;
use Modules\Worship\App\Models\WorshipSong;
use Modules\Worship\App\Services\SetlistManagerService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SetlistManagerController extends Controller
{
    public function __construct(
        protected PdfService $pdfService
    ) {}
    public function index()
    {
        $setlists = WorshipSetlist::with('leader')->orderBy('scheduled_at', 'desc')->paginate(10);

        return view('worship::admin.setlists.index', compact('setlists'));
    }

    public function create()
    {
        $leaders = User::all();

        return view('worship::admin.setlists.create', compact('leaders'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'scheduled_at' => 'required|date',
            'leader_id' => 'required|exists:users,id',
            'producer_notes' => 'nullable|string',
        ]);

        $setlist = WorshipSetlist::create($request->all());

        return redirect()->route('worship.admin.setlists.manage', $setlist->id)->with('success', 'Culto agendado com sucesso!');
    }

    public function update(Request $request, WorshipSetlist $setlist)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'scheduled_at' => 'required|date',
            'leader_id' => 'required|exists:users,id',
            'producer_notes' => 'nullable|string',
        ]);

        $setlist->update($request->all());

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Informações do culto atualizadas!']);
        }

        return redirect()->back()->with('success', 'Informações do culto atualizadas!');
    }

    public function updateStatus(Request $request, WorshipSetlist $setlist)
    {
        $request->validate([
            'status' => 'required|string',
        ]);

        $setlist->update(['status' => $request->status]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Status do culto atualizado!']);
        }

        return redirect()->back()->with('success', 'Status do culto atualizado!');
    }

    public function manage(WorshipSetlist $setlist)
    {
        $setlist->load(['items.song', 'roster.user', 'roster.instrument']);
        // Músicas são buscadas via API (GET /api/v1/worship/songs?q=) na view — evita carregar 2000+ no page load
        $instruments = \Modules\Worship\App\Models\WorshipInstrument::all();
        $users = User::orderBy('name')->get();

        return view('worship::admin.setlists.builder', compact('setlist', 'instruments', 'users'));
    }

    public function addSong(Request $request, WorshipSetlist $setlist)
    {
        $request->validate([
            'song_id' => 'required|exists:worship_songs,id',
        ]);

        $order = $setlist->items()->max('order') + 1;
        $song = WorshipSong::find($request->song_id);

        $setlist->items()->create([
            'song_id' => $request->song_id,
            'order' => $order,
            'override_key' => $song->original_key->value ?? 'C',
        ]);

        return redirect()->back()->with('success', 'Música adicionada ao repertório!');
    }

    public function removeSong(WorshipSetlistItem $item)
    {
        $item->delete();

        return redirect()->back()->with('success', 'Música removida do repertório!');
    }

    public function reorder(Request $request, WorshipSetlist $setlist, SetlistManagerService $service)
    {
        $request->validate([
            'items' => 'required|array',
        ]);

        $service->reorderItems($setlist, $request->items);

        return response()->json(['success' => true, 'message' => 'Ordem atualizada!']);
    }

    public function updateItem(Request $request, WorshipSetlistItem $item)
    {
        $item->update($request->only(['override_key', 'order', 'arrangement_note']));

        return response()->json(['success' => true]);
    }

    public function print(WorshipSetlist $setlist)
    {
        $setlist->load('items.song');

        return view('worship::admin.setlists.print-pdf', compact('setlist'));
    }

    public function exportPdf(WorshipSetlist $setlist): StreamedResponse
    {
        $setlist->load(['items.song', 'roster.user', 'roster.instrument', 'leader']);

        return $this->pdfService->downloadView(
            'worship::admin.setlists.print-pdf',
            compact('setlist'),
            'setlist-' . \Illuminate\Support\Str::slug($setlist->title) . '.pdf',
            'A4',
            'Portrait',
            [10, 10, 10, 10]
        );
    }

    public function destroy(WorshipSetlist $setlist)
    {
        $setlist->delete();

        return redirect()->route('worship.admin.setlists.index')->with('success', 'Culto excluído com sucesso!');
    }
}
