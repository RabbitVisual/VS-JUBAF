<?php

namespace Modules\ChurchCouncil\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Modules\ChurchCouncil\App\Models\TransferLetter;
use Modules\ChurchCouncil\App\Services\CouncilAuditService;

class TransferLetterController extends Controller
{
    public function __construct(
        private CouncilAuditService $audit
    ) {
    }

    public function index(Request $request): View
    {
        $query = TransferLetter::with('member');

        if ($request->filled('direction')) {
            $query->where('direction', $request->input('direction'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $letters = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return view('churchcouncil::admin.transfers.index', compact('letters'));
    }

    public function create(): View
    {
        $users = User::orderBy('name')->get();

        return view('churchcouncil::admin.transfers.create', compact('users'));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'direction' => 'required|in:outgoing,incoming',
            'from_church' => 'nullable|string|max:255',
            'to_church' => 'nullable|string|max:255',
        ]);

        $status = $validated['direction'] === TransferLetter::DIRECTION_OUTGOING
            ? TransferLetter::STATUS_PENDING_COUNCIL
            : TransferLetter::STATUS_DRAFT;

        $letter = TransferLetter::create([
            'user_id' => $validated['user_id'],
            'direction' => $validated['direction'],
            'from_church' => $validated['from_church'] ?? null,
            'to_church' => $validated['to_church'] ?? null,
            'status' => $status,
        ]);

        $this->audit->log('transfer_letter_created', $letter, [
            'direction' => $letter->direction,
            'status' => $letter->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Carta de transferência registrada com sucesso.',
            'redirect' => route('admin.churchcouncil.transfers.index'),
        ]);
    }

    public function show(TransferLetter $letter): View
    {
        $letter->load('member');

        return view('churchcouncil::admin.transfers.show', compact('letter'));
    }

    public function uploadDocument(Request $request, TransferLetter $letter): JsonResponse
    {
        $validated = $request->validate([
            'file' => 'required|file|max:5120|mimes:pdf',
        ]);

        $path = $validated['file']->store('churchcouncil/transfers', 'public');

        $letter->update([
            'file_path' => $path,
            'file_type' => $validated['file']->extension(),
            'file_size' => $validated['file']->getSize(),
        ]);

        $this->audit->log('transfer_letter_document_uploaded', $letter, [
            'file_path' => $letter->file_path,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Documento anexado com sucesso.',
        ]);
    }

    public function downloadDocument(TransferLetter $letter)
    {
        if (! $letter->file_path) {
            abort(404);
        }

        return Storage::disk('public')->download($letter->file_path, 'carta-transferencia-'.$letter->id.'.'.$letter->file_type);
    }
}

