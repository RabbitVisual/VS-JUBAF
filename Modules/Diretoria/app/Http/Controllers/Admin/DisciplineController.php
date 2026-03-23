<?php

namespace Modules\Diretoria\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Modules\Diretoria\App\Models\DisciplineAction;
use Modules\Diretoria\App\Models\DisciplineCase;
use Modules\Diretoria\App\Models\DisciplineCaseFile;
use Modules\Diretoria\App\Services\DiretoriaAuditService;
use Modules\Notifications\App\Services\InAppNotificationService;
use Modules\Diretoria\App\Models\DiretoriaMember;

class DisciplineController extends Controller
{
    public function __construct(
        private DiretoriaAuditService $audit,
        private InAppNotificationService $inApp
    ) {
    }
    /**
     * List discipline cases.
     */
    public function index(Request $request): View
    {
        $query = DisciplineCase::with(['member', 'openedBy']);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('case_type')) {
            $query->where('case_type', $request->input('case_type'));
        }

        $cases = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return view('Diretoria::admin.discipline.index', compact('cases'));
    }

    /**
     * Show form to open a new discipline case.
     */
    public function create(): View
    {
        $users = User::orderBy('name')->get();

        return view('Diretoria::admin.discipline.create', compact('users'));
    }

    /**
     * Store a new discipline case.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'case_type' => 'required|in:admonition,temporary_suspension,exclusion,restoration',
            'summary' => 'required|string',
        ]);

        $case = DisciplineCase::create([
            'user_id' => $validated['user_id'],
            'case_type' => $validated['case_type'],
            'status' => DisciplineCase::STATUS_OPENED,
            'current_stage' => 'inicial',
            'summary' => $validated['summary'],
            'opened_by' => auth()->id(),
        ]);

        DisciplineAction::create([
            'discipline_case_id' => $case->id,
            'stage' => 'abertura',
            'notes' => $validated['summary'],
            'performed_by' => auth()->id(),
            'performed_at' => now(),
        ]);

        $this->audit->log('discipline_case_opened', $case, [
            'case_type' => $case->case_type,
        ]);

        $diretoriaUsers = DiretoriaMember::active()->with('user')->get()->pluck('user')->filter();
        if ($diretoriaUsers->isNotEmpty()) {
            $this->inApp->sendToUsers(
                $diretoriaUsers,
                'Novo caso disciplinar aberto',
                'Um novo caso disciplinar foi registrado para avaliação liderancaal.',
                [
                    'type' => 'warning',
                    'priority' => 'high',
                    'action_url' => route('admin.Diretoria.discipline.show', $case),
                    'action_text' => 'Ver caso',
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Caso disciplinar aberto com sucesso.',
            'redirect' => route('admin.Diretoria.discipline.index'),
        ]);
    }

    /**
     * Show a discipline case details.
     */
    public function show(DisciplineCase $case): View
    {
        $case->load(['member', 'openedBy', 'actions.performer', 'files.uploader']);

        return view('Diretoria::admin.discipline.show', compact('case'));
    }

    /**
     * Register a new action in the discipline timeline.
     */
    public function storeAction(Request $request, DisciplineCase $case): JsonResponse
    {
        $validated = $request->validate([
            'stage' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'status' => 'nullable|in:opened,under_care,recommended_to_assembly,decided_by_assembly,closed',
        ]);

        $action = DisciplineAction::create([
            'discipline_case_id' => $case->id,
            'stage' => $validated['stage'],
            'notes' => $validated['notes'] ?? null,
            'performed_by' => auth()->id(),
            'performed_at' => now(),
        ]);

        if (! empty($validated['status'])) {
            $case->update([
                'status' => $validated['status'],
                'current_stage' => $validated['stage'],
                'closed_at' => $validated['status'] === DisciplineCase::STATUS_CLOSED ? now() : $case->closed_at,
            ]);
        } else {
            $case->update(['current_stage' => $validated['stage']]);
        }

        $this->audit->log('discipline_action_added', $case, [
            'stage' => $action->stage,
            'status' => $case->status,
        ]);

        if (! empty($validated['status'])) {
            $diretoriaUsers = DiretoriaMember::active()->with('user')->get()->pluck('user')->filter();
            if ($diretoriaUsers->isNotEmpty()) {
                $this->inApp->sendToUsers(
                    $diretoriaUsers,
                    'Atualização em caso disciplinar',
                    "O caso disciplinar de {$case->member->name} foi atualizado (estado: {$case->status}).",
                    [
                        'type' => 'info',
                        'priority' => 'normal',
                        'action_url' => route('admin.Diretoria.discipline.show', $case),
                        'action_text' => 'Ver detalhes',
                    ]
                );
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Ação registrada com sucesso.',
        ]);
    }

    /**
     * Upload de arquivo sensível vinculado ao caso disciplinar (storage protegido).
     */
    public function uploadFile(Request $request, DisciplineCase $case): JsonResponse
    {
        $user = auth()->user();
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Autenticação necessária.'], 401);
        }

        $DiretoriaMember = $user->DiretoriaMember;
        $isAdminOrlideranca = method_exists($user, 'hasRole')
            ? ($user->hasRole('admin') || $user->hasRole('lideranca'))
            : false;

        if (! $DiretoriaMember && ! $isAdminOrlideranca) {
            return response()->json([
                'success' => false,
                'message' => 'Apenas diretoria, lideranças ou admins podem anexar documentos disciplinares.',
            ], 403);
        }

        $validated = $request->validate([
            'file' => 'required|file|max:10240',
        ]);

        $file = $validated['file'];
        $path = $file->store('Diretoria/discipline', 'protected');

        $record = DisciplineCaseFile::create([
            'discipline_case_id' => $case->id,
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'uploaded_by' => $user->id,
        ]);

        $this->audit->log('discipline_file_uploaded', $case, [
            'file_id' => $record->id,
            'original_name' => $record->original_name,
            'size' => $record->size,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Documento anexado com sucesso.',
        ]);
    }

    /**
     * Download protegido de arquivo disciplinar.
     */
    public function downloadFile(DisciplineCase $case, DisciplineCaseFile $file)
    {
        $user = auth()->user();
        if (! $user) {
            abort(401);
        }

        if ($file->discipline_case_id !== $case->id) {
            abort(404);
        }

        $DiretoriaMember = $user->DiretoriaMember;
        $isAdminOrlideranca = method_exists($user, 'hasRole')
            ? ($user->hasRole('admin') || $user->hasRole('lideranca'))
            : false;

        if (! $DiretoriaMember && ! $isAdminOrlideranca && $user->id !== $case->user_id) {
            abort(403, 'Você não tem permissão para acessar este documento.');
        }

        if (! $file->path) {
            abort(404);
        }

        return Storage::disk('protected')->download($file->path, $file->original_name);
    }
}
