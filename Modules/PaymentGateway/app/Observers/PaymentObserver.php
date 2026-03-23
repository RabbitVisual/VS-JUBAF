<?php

namespace Modules\PaymentGateway\App\Observers;

use Illuminate\Support\Facades\Event;
use Modules\PaymentGateway\App\Models\Payment;
use Modules\Treasury\App\Models\Campaign;
use Modules\Treasury\App\Models\FinancialEntry;
use Modules\Treasury\App\Models\FinancialGoal;

class PaymentObserver
{
    /**
     * Handle the Payment "updated" event.
     */
    public function updated(Payment $payment): void
    {
        // Verifica se o status mudou para 'completed'
        if ($payment->isDirty('status') && $payment->status === 'completed') {
            // Dispatch event for other modules to listen
            Event::dispatch('payment.completed', $payment);

            // Handle Treasury integration for all payments
            $this->createFinancialEntry($payment);
        }
    }

    /**
     * Cria entrada financeira quando o pagamento é concluído
     */
    protected function createFinancialEntry(Payment $payment): void
    {
        // Verifica se já existe uma entrada financeira para este pagamento
        $payment->load('financialEntry');
        if ($payment->financialEntry) {
            return;
        }

        try {
            // Determina a categoria e IDs relacionados
            $category = 'other';
            $campaignId = null;
            $ministryId = null;
            $goalId = null;

            $metadata = $payment->metadata ?? [];

            switch ($payment->payment_type) {
                case 'campaign':
                    $category = 'campaign';
                    $campaignId = $payment->payable_id;
                    break;
                case 'event_registration':
                    $category = 'event';
                    break;
                case 'donation':
                    $category = 'donation';
                    break;
                case 'tithe':
                    $category = 'tithe';
                    break;
                case 'offering':
                    $category = 'offering';
                    break;
            }

            // Tenta extrair IDs de metadatas se existirem
            $campaignId = $campaignId ?? ($metadata['campaign_id'] ?? null);
            $ministryId = $metadata['ministry_id'] ?? null;
            $goalId = $metadata['goal_id'] ?? null;

            // Carrega o gateway se necessário
            $payment->load('gateway');
            $gatewayName = $payment->gateway ? $payment->gateway->display_name : 'Sistema';

            // Cria a entrada financeira
            FinancialEntry::create([
                'type' => 'income',
                'category' => $category,
                'title' => $payment->description ?? "Pagamento via {$gatewayName}",
                'description' => "Transação ID: {$payment->transaction_id}",
                'amount' => $payment->amount,
                'entry_date' => $payment->paid_at ?? now(),
                'user_id' => $payment->user_id,
                'payment_id' => $payment->id,
                'campaign_id' => $campaignId,
                'ministry_id' => $ministryId,
                'goal_id' => $goalId,
                'payment_method' => $payment->payment_method ?? 'online',
                'reference_number' => $payment->transaction_id,
                'metadata' => [
                    'source' => 'PaymentGateway',
                    'gateway' => $payment->gateway->name ?? 'unknown',
                    'gateway_txn_id' => $payment->gateway_transaction_id,
                ],
            ]);

            // Se for campanha, atualiza o valor
            if ($campaignId) {
                $campaign = Campaign::find($campaignId);
                if ($campaign) {
                    $campaign->updateCurrentAmount();
                }
            }

            // Se houver meta vinculada, atualiza
            if ($goalId) {
                $goal = FinancialGoal::find($goalId);
                if ($goal) {
                    $goal->updateCurrentAmount();
                }
            }
        } catch (\Exception $e) {
            \Log::error('Erro ao sincronizar pagamento com Tesouraria: '.$e->getMessage(), [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
