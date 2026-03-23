<?php

namespace Modules\PaymentGateway\App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\PaymentGateway\App\Services\PaymentGatewayApiService;

/**
 * API v1 central de PaymentGateway.
 * Respostas no padrão { data } para listagem de gateways e status de pagamento (polling).
 */
class PaymentGatewayController extends Controller
{
    public function __construct(
        private PaymentGatewayApiService $api
    ) {}

    /**
     * GET /api/v1/payment-gateways
     * Lista gateways ativos para o frontend (sem credenciais).
     */
    public function index(): JsonResponse
    {
        $gateways = $this->api->getActiveGatewaysForFrontend();

        return response()->json(['data' => $gateways]);
    }

    /**
     * GET /api/v1/payments/status?transaction_id=xxx
     * ou GET /api/v1/payments/{transactionId}/status
     * Retorna status do pagamento para polling.
     */
    public function paymentStatus(Request $request, ?string $transactionId = null): JsonResponse
    {
        $id = $transactionId ?? $request->query('transaction_id');
        if (empty($id) || ! is_string($id)) {
            return response()->json(['message' => 'transaction_id é obrigatório.'], 422);
        }

        $data = $this->api->getPaymentStatusByTransactionId($id);
        if ($data === null) {
            return response()->json(['message' => 'Pagamento não encontrado.'], 404);
        }

        return response()->json(['data' => $data]);
    }
}
