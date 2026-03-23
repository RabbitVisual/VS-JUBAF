<?php

namespace Modules\MemberPanel\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Modules\ChurchCouncil\App\Models\CouncilApproval;
use Modules\ChurchCouncil\App\Models\TransferLetter;
use Modules\ChurchCouncil\App\Services\CouncilAuditService;
use Modules\Notifications\App\Services\InAppNotificationService;

class TransferController extends Controller
{
    public function __construct(
        private CouncilAuditService $audit,
        private InAppNotificationService $inApp,
    ) {
    }

    /**
     * Show current member transfer letters.
     */
    public function index(): View
    {
        $user = Auth::user();

        $letters = TransferLetter::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('memberpanel::transfers.index', compact('user', 'letters'));
    }

    /**
     * Show form to request an outgoing transfer letter.
     */
    public function create(): View
    {
        $user = Auth::user();

        return view('memberpanel::transfers.request', compact('user'));
    }

    /**
     * Store a new outgoing transfer request and open a council approval.
     */
    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'to_church' => 'required|string|max:255',
        ]);

        $fromChurch = \App\Models\Settings::get('church_name', config('app.name'));

        $letter = TransferLetter::create([
            'user_id' => $user->id,
            'direction' => TransferLetter::DIRECTION_OUTGOING,
            'from_church' => $fromChurch,
            'to_church' => $validated['to_church'],
            'status' => TransferLetter::STATUS_PENDING_COUNCIL,
        ]);

        CouncilApproval::create([
            'approvable_type' => TransferLetter::class,
            'approvable_id' => $letter->id,
            'approval_type' => CouncilApproval::TYPE_MEMBERSHIP_TRANSFER_OUT,
            'status' => CouncilApproval::STATUS_PENDING,
            'request_details' => 'Pedido de carta de transferência para: '.$validated['to_church'],
            'requested_by' => $user->id,
            'submitted_at' => now(),
            'metadata' => [
                'direction' => $letter->direction,
            ],
        ]);

        $this->audit->log('transfer_letter_requested', $letter, [
            'to_church' => $letter->to_church,
        ]);

        $this->inApp->sendToUser(
            $user,
            'Pedido de carta de transferência registrado',
            'Seu pedido de carta de transferência foi enviado ao conselho e aguarda análise.',
            [
                'type' => 'info',
                'priority' => 'normal',
                'action_url' => route('memberpanel.transfers.index'),
                'action_text' => 'Ver pedidos',
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Seu pedido de carta de transferência foi enviado ao conselho.',
            'redirect' => route('memberpanel.transfers.index'),
        ]);
    }
}

