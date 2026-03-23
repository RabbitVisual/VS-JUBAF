<?php

namespace Modules\ChurchCouncil\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Modules\ChurchCouncil\App\Models\CouncilDocument;
use Modules\ChurchCouncil\App\Models\CouncilMeeting;
use Modules\ChurchCouncil\App\Services\ChurchCouncilPdfService;

class CouncilDocumentController extends Controller
{
    public function __construct(
        private ChurchCouncilPdfService $councilPdf
    ) {
    }

    /**
     * Display a listing of documents.
     */
    public function index(Request $request): View
    {
        $query = CouncilDocument::with('uploader');

        if ($request->has('type') && ! empty($request->type)) {
            $query->where('document_type', $request->type);
        }

        $documents = $query->orderBy('document_date', 'desc')->paginate(15);

        return view('churchcouncil::admin.documents.index', compact('documents'));
    }

    /**
     * Show the form for creating a new document.
     */
    public function create(): View
    {
        $meetings = CouncilMeeting::orderBy('scheduled_date', 'desc')->limit(20)->get();

        return view('churchcouncil::admin.documents.create', compact('meetings'));
    }

    /**
     * Store a newly created document.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'document_type' => 'required|in:statute,regiment,minute,resolution,declaracao_doutrinaria,pacto_igrejas,regimento_interno,other',
            'document_date' => 'required|date',
            'file' => 'required|file|max:10240|mimes:pdf,doc,docx,xls,xlsx',
            'meeting_id' => 'nullable|exists:council_meetings,id',
            'is_public' => 'boolean',
        ]);

        $path = $request->file('file')->store('council/documents', 'public');

        CouncilDocument::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'document_type' => $validated['document_type'],
            'document_date' => $validated['document_date'],
            'file_path' => $path,
            'file_type' => $request->file('file')->extension(),
            'file_size' => $request->file('file')->getSize(),
            'uploaded_by' => auth()->id(),
            'meeting_id' => $validated['meeting_id'] ?? null,
            'is_public' => $request->boolean('is_public'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Documento enviado com sucesso!',
            'redirect' => route('admin.churchcouncil.documents.index'),
        ]);
    }

    /**
     * Display the specified document.
     */
    public function show(CouncilDocument $document)
    {
        return Storage::disk('public')->download($document->file_path, $document->title.'.'.$document->file_type);
    }

    /**
     * Show the form for editing the specified document.
     */
    public function edit(CouncilDocument $document): View
    {
        $meetings = CouncilMeeting::orderBy('scheduled_date', 'desc')->limit(20)->get();

        return view('churchcouncil::admin.documents.edit', compact('document', 'meetings'));
    }

    /**
     * Update the specified document.
     */
    public function update(Request $request, CouncilDocument $document): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'document_type' => 'required|in:statute,regiment,minute,resolution,declaracao_doutrinaria,pacto_igrejas,regimento_interno,other',
            'document_date' => 'required|date',
            'meeting_id' => 'nullable|exists:council_meetings,id',
            'is_public' => 'boolean',
        ]);

        $document->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'document_type' => $validated['document_type'],
            'document_date' => $validated['document_date'],
            'meeting_id' => $validated['meeting_id'] ?? null,
            'is_public' => $request->boolean('is_public'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Documento atualizado com sucesso!',
            'redirect' => route('admin.churchcouncil.documents.index'),
        ]);
    }

    /**
     * Remove the specified document.
     */
    public function destroy(CouncilDocument $document): JsonResponse
    {
        // Optional: Delete file from storage if hard delete
        // Storage::disk('public')->delete($document->file_path);

        $document->delete(); // Soft delete

        return response()->json([
            'success' => true,
            'message' => 'Documento removido com sucesso!',
        ]);
    }

    /**
     * Export meeting minutes (ata) as a professional PDF.
     */
    public function exportMinutesPdf(CouncilMeeting $meeting)
    {
        return $this->councilPdf->downloadMinutesPdf($meeting);
    }

    /**
     * Export convocation / edital PDF for an upcoming meeting.
     */
    public function exportConvocationPdf(CouncilMeeting $meeting)
    {
        return $this->councilPdf->downloadConvocationPdf($meeting);
    }
}
