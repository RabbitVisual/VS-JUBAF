<?php

namespace Modules\Treasury\App\Services;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Modules\Ministries\App\Models\Ministry;
use Modules\PaymentGateway\App\Models\Payment;
use Modules\Treasury\App\Models\AuditFinancialLog;
use Modules\Treasury\App\Models\Campaign;
use Modules\Treasury\App\Models\FinancialCategory;
use Modules\Treasury\App\Models\FinancialEntry;
use Modules\Treasury\App\Models\FinancialGoal;
use Modules\Treasury\App\Models\TreasuryPermission;
use Modules\Treasury\App\Models\TreasuryMonthlyClosing;
use Modules\Treasury\App\Traits\AuditableTransaction;

/**
 * Serviço central da API v1 de Treasury.
 * Única fonte para dashboard, entradas, campanhas, metas, relatórios e permissões.
 * CBAV2026: Todas as escritas em DB::transaction com log em audit_financial_logs.
 */
class TreasuryApiService
{
    use AuditableTransaction;
    /**
     * Dashboard stats for a user (requires canViewReports).
     */
    public function getDashboardStats(User|int $userOrId): array
    {
        $user = $userOrId instanceof User ? $userOrId : User::findOrFail($userOrId);
        $permission = TreasuryPermission::forUserOrAdmin($user);
        if (! $permission->canViewReports()) {
            abort(403, 'Você não tem permissão para visualizar o dashboard da Tesouraria.');
        }

        $currentMonth = now()->month;
        $currentYear = now()->year;

        $monthlyIncome = (float) FinancialEntry::income()->month($currentYear, $currentMonth)->sum('amount');
        $monthlyExpense = (float) FinancialEntry::expense()->month($currentYear, $currentMonth)->sum('amount');
        $yearlyIncome = (float) FinancialEntry::income()->year($currentYear)->sum('amount');
        $yearlyExpense = (float) FinancialEntry::expense()->year($currentYear)->sum('amount');

        $incomeByCategory = FinancialEntry::income()
            ->month($currentYear, $currentMonth)
            ->select('category', DB::raw('sum(amount) as total'))
            ->groupBy('category')
            ->get();

        $expenseByCategory = FinancialEntry::expense()
            ->month($currentYear, $currentMonth)
            ->select('category', DB::raw('sum(amount) as total'))
            ->groupBy('category')
            ->get();

        $recentEntries = FinancialEntry::with(['user', 'campaign', 'ministry'])
            ->orderBy('entry_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $activeCampaigns = Campaign::active()->orderBy('end_date', 'asc')->get();
        $activeGoals = FinancialGoal::active()->orderBy('end_date', 'asc')->get();

        $monthlyIncomeChart = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyIncomeChart[] = [
                'month' => $date->format('M/Y'),
                'income' => (float) FinancialEntry::income()->month($date->year, $date->month)->sum('amount'),
                'expense' => (float) FinancialEntry::expense()->month($date->year, $date->month)->sum('amount'),
            ];
        }

        $monthStart = now()->startOfMonth()->toDateString();
        $monthEnd = now()->endOfMonth()->toDateString();
        $monthlyIncomeForPlano = (float) FinancialEntry::income()->month($currentYear, $currentMonth)->sum('amount');
        $planoCooperativo = $this->getPlanoCooperativoForPeriod($monthStart, $monthEnd, $monthlyIncomeForPlano);

        return [
            'permission' => $permission,
            'plano_cooperativo' => $planoCooperativo,
            'monthly_income' => $monthlyIncome,
            'monthly_expense' => $monthlyExpense,
            'monthly_balance' => $monthlyIncome - $monthlyExpense,
            'yearly_income' => $yearlyIncome,
            'yearly_expense' => $yearlyExpense,
            'yearly_balance' => $yearlyIncome - $yearlyExpense,
            'income_by_category' => $incomeByCategory,
            'expense_by_category' => $expenseByCategory,
            'recent_entries' => $recentEntries,
            'active_campaigns' => $activeCampaigns,
            'active_goals' => $activeGoals,
            'monthly_income_chart' => $monthlyIncomeChart,
        ];
    }

    /**
     * Dados agregados para o Portal de Transparência (sem entradas individuais).
     * Totais por período e por categoria, adequados para prestação de contas aos membros.
     */
    public function getTransparencySummary(?string $year = null): array
    {
        $year = (int) ($year ?? now()->year);

        $yearlyIncome = (float) FinancialEntry::income()->year($year)->sum('amount');
        $yearlyExpense = (float) FinancialEntry::expense()->year($year)->sum('amount');
        $yearlyBalance = $yearlyIncome - $yearlyExpense;

        $incomeByCategory = FinancialEntry::income()
            ->year($year)
            ->select('category', DB::raw('sum(amount) as total'))
            ->groupBy('category')
            ->get();

        $expenseByCategory = FinancialEntry::expense()
            ->year($year)
            ->select('category', DB::raw('sum(amount) as total'))
            ->groupBy('category')
            ->get();

        $monthlyChart = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyChart[] = [
                'month' => $i,
                'label' => Carbon::createFromDate($year, $i, 1)->translatedFormat('F'),
                'income' => (float) FinancialEntry::income()->month($year, $i)->sum('amount'),
                'expense' => (float) FinancialEntry::expense()->month($year, $i)->sum('amount'),
            ];
        }

        $campaignsSummary = Campaign::whereYear('start_date', '<=', $year)
            ->where(function ($q) use ($year) {
                $q->whereYear('end_date', '>=', $year)->orWhereNull('end_date');
            })
            ->get(['id', 'name', 'target_amount', 'current_amount', 'start_date', 'end_date']);

        return [
            'year' => $year,
            'yearly_income' => $yearlyIncome,
            'yearly_expense' => $yearlyExpense,
            'yearly_balance' => $yearlyBalance,
            'income_by_category' => $incomeByCategory,
            'expense_by_category' => $expenseByCategory,
            'monthly_chart' => $monthlyChart,
            'campaigns_summary' => $campaignsSummary,
        ];
    }

    /**
     * List entries with filters. Permission: canViewReports.
     */
    public function listEntries(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        $query = FinancialEntry::with(['user', 'campaign', 'ministry', 'payment']);

        if (! empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }
        if (! empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }
        if (! empty($filters['start_date'])) {
            $query->where('entry_date', '>=', $filters['start_date']);
        }
        if (! empty($filters['end_date'])) {
            $query->where('entry_date', '<=', $filters['end_date']);
        }
        if (! empty($filters['campaign_id'])) {
            $query->where('campaign_id', $filters['campaign_id']);
        }
        if (! empty($filters['ministry_id'])) {
            $query->where('ministry_id', $filters['ministry_id']);
        }
        if (isset($filters['fund_id']) && $filters['fund_id'] !== '' && $filters['fund_id'] !== null) {
            $query->where('fund_id', $filters['fund_id']);
        }

        return $query->with(['fund', 'financialCategory'])->orderBy('entry_date', 'desc')->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /** Legacy enum values allowed on financial_entries.category */
    private const LEGACY_CATEGORIES = [
        'tithe', 'offering', 'donation', 'ministry_donation', 'campaign',
        'maintenance', 'utilities', 'salary', 'equipment', 'event', 'other',
    ];

    /** Map financial_categories slug to legacy enum for backward compatibility */
    private const SLUG_TO_LEGACY_CATEGORY = [
        'tithe' => 'tithe', 'offering' => 'offering', 'offering_missions_national' => 'offering',
        'offering_missions_state' => 'offering', 'offering_missions_world' => 'offering',
        'construction_fund' => 'campaign', 'donation' => 'donation', 'ministry_donation' => 'ministry_donation',
        'campaign' => 'campaign', 'other' => 'other',
        'preachers' => 'other', 'maintenance' => 'maintenance', 'social_action' => 'other',
        'christian_education' => 'other', 'salary_benefits' => 'salary', 'utilities' => 'utilities',
        'equipment' => 'equipment', 'event' => 'event', 'denominational_contribution' => 'other',
    ];

    private function normalizeEntryData(array $data): array
    {
        if (! empty($data['category']) && is_string($data['category'])) {
            $slug = $data['category'];
            $cat = FinancialCategory::where('slug', $slug)->first();
            if ($cat) {
                $data['category_id'] = $cat->id;
                $data['category'] = self::SLUG_TO_LEGACY_CATEGORY[$slug] ?? (in_array($slug, self::LEGACY_CATEGORIES, true) ? $slug : 'other');
            }
        }
        return $data;
    }

    public function getEntry(int $id): FinancialEntry
    {
        return FinancialEntry::with(['user', 'campaign', 'ministry', 'payment'])->findOrFail($id);
    }

    /**
     * Create entry. Permission: canCreateEntries. Updates campaign/goal current amounts.
     * CBAV2026: DB::transaction + audit log; expense_status = pending for expenses; category_id from slug.
     */
    public function createEntry(array $data, User|int $userOrId): FinancialEntry
    {
        $user = $userOrId instanceof User ? $userOrId : User::findOrFail($userOrId);
        $permission = TreasuryPermission::forUserOrAdmin($user);
        if (! $permission->canCreateEntries()) {
            abort(403, 'Você não tem permissão para criar entradas.');
        }

        $data = $this->normalizeEntryData($data);
        $data['user_id'] = $user->id;
        if (isset($data['type']) && $data['type'] === 'expense' && ! isset($data['expense_status'])) {
            $data['expense_status'] = FinancialEntry::EXPENSE_STATUS_PENDING;
        }

        $entry = DB::transaction(function () use ($data, $user) {
            $entry = FinancialEntry::create($data);

            $limit = class_exists(\Modules\Diretoria\App\Services\DiretoriaSettings::class)
                ? \Modules\Diretoria\App\Services\DiretoriaSettings::autoApproveBudgetLimit()
                : (float) \App\Models\Settings::get('jubaf_diretoria_auto_approve_budget_limit', 1000);
            if ($entry->type === 'expense' && (float) $entry->amount > $limit && class_exists(\Modules\Diretoria\App\Models\DiretoriaApproval::class)) {
                $approval = \Modules\Diretoria\App\Models\DiretoriaApproval::create([
                    'approvable_type' => FinancialEntry::class,
                    'approvable_id' => $entry->id,
                    'approval_type' => \Modules\Diretoria\App\Models\DiretoriaApproval::TYPE_FINANCIAL_REQUEST,
                    'status' => \Modules\Diretoria\App\Models\DiretoriaApproval::STATUS_PENDING,
                    'request_details' => "Despesa acima do limite (R$ " . number_format($limit, 2, ',', '.') . "): {$entry->title} - R$ " . number_format((float) $entry->amount, 2, ',', '.'),
                    'requested_by' => $user->id,
                    'submitted_at' => now(),
                    'metadata' => ['amount' => (float) $entry->amount, 'entry_date' => $entry->entry_date?->toDateString()],
                ]);
                $entry->update(['diretoria_approval_id' => $approval->id]);
                if (class_exists(\Modules\Notifications\App\Services\InAppNotificationService::class)) {
                    try {
                        app(\Modules\Notifications\App\Services\InAppNotificationService::class)->sendToAdmins(
                            'Despesa aguardando aprovação do conselho',
                            "Despesa acima do limite: {$entry->title} - R$ " . number_format((float) $entry->amount, 2, ',', '.') . '. Aprovação pendente.',
                            [
                                'type' => 'warning',
                                'priority' => 'high',
                                'action_url' => route('admin.Diretoria.approvals.show', $approval),
                                'action_text' => 'Ver aprovação',
                                'notification_type' => 'treasury_approval',
                            ]
                        );
                    } catch (\Throwable $e) {
                        \Log::warning('Treasury: failed to send notification for diretoria approval: ' . $e->getMessage());
                    }
                }
            }

            if ($entry->campaign_id && $entry->type === 'income' && $entry->category === 'campaign') {
                $entry->campaign->updateCurrentAmount();
            }
            if ($entry->goal_id && $entry->type === 'income') {
                $entry->goal->updateCurrentAmount();
            }

            $this->logAudit(AuditFinancialLog::ACTION_CREATED, FinancialEntry::class, $entry->id, null, $entry->fresh()->toArray(), $user->id, Request::ip());

            return $entry;
        });

        return $entry->load(['user', 'campaign', 'ministry', 'payment', 'financialCategory', 'fund']);
    }

    /**
     * Update entry. Permission: canEditEntries. CBAV2026: DB::transaction + audit log.
     */
    public function updateEntry(FinancialEntry $entry, array $data, User|int $userOrId): FinancialEntry
    {
        $user = $userOrId instanceof User ? $userOrId : User::findOrFail($userOrId);
        $permission = TreasuryPermission::forUserOrAdmin($user);
        if (! $permission->canEditEntries()) {
            abort(403, 'Você não tem permissão para editar entradas.');
        }

        $data = $this->normalizeEntryData($data);
        $oldValues = $entry->toArray();
        $oldCampaignId = $entry->campaign_id;
        $oldGoalId = $entry->goal_id;
        $oldType = $entry->type;
        $oldCategory = $entry->category;

        DB::transaction(function () use ($entry, $data, $oldValues, $oldCampaignId, $oldGoalId, $oldType, $oldCategory, $user) {
            $entry->update($data);

            if ($oldCampaignId && $oldType === 'income' && $oldCategory === 'campaign') {
                Campaign::find($oldCampaignId)?->updateCurrentAmount();
            }
            if ($entry->campaign_id && $entry->type === 'income' && $entry->category === 'campaign') {
                $entry->campaign->updateCurrentAmount();
            }
            if ($oldGoalId && $oldType === 'income') {
                FinancialGoal::find($oldGoalId)?->updateCurrentAmount();
            }
            if ($entry->goal_id && $entry->type === 'income') {
                $entry->goal->updateCurrentAmount();
            }

            $this->logAudit(AuditFinancialLog::ACTION_UPDATED, FinancialEntry::class, $entry->id, $oldValues, $entry->fresh()->toArray(), $user->id, Request::ip());
        });

        return $entry->fresh(['user', 'campaign', 'ministry', 'payment', 'financialCategory', 'fund']);
    }

    /**
     * Delete entry (soft). Permission: canDeleteEntries. CBAV2026: DB::transaction + audit log.
     */
    public function deleteEntry(FinancialEntry $entry, User|int $userOrId): void
    {
        $user = $userOrId instanceof User ? $userOrId : User::findOrFail($userOrId);
        $permission = TreasuryPermission::forUserOrAdmin($user);
        if (! $permission->canDeleteEntries()) {
            abort(403, 'Você não tem permissão para deletar entradas.');
        }

        $oldValues = $entry->toArray();
        $campaignId = $entry->campaign_id;
        $goalId = $entry->goal_id;
        $type = $entry->type;
        $category = $entry->category;

        DB::transaction(function () use ($entry, $oldValues, $campaignId, $goalId, $type, $category, $user) {
            $entry->delete();

            if ($campaignId && $type === 'income' && $category === 'campaign') {
                Campaign::find($campaignId)?->updateCurrentAmount();
            }
            if ($goalId && $type === 'income') {
                FinancialGoal::find($goalId)?->updateCurrentAmount();
            }

            $this->logAudit(AuditFinancialLog::ACTION_DELETED, FinancialEntry::class, $entry->id, $oldValues, null, $user->id, Request::ip());
        });
    }

    /**
     * Estornar entrada: cria lançamento inverso com reversal_of_id e mantém auditoria. Permission: canCreateEntries.
     */
    public function reverseEntry(FinancialEntry $entry, User|int $userOrId): FinancialEntry
    {
        $user = $userOrId instanceof User ? $userOrId : User::findOrFail($userOrId);
        $permission = TreasuryPermission::forUserOrAdmin($user);
        if (! $permission->canCreateEntries()) {
            abort(403, 'Você não tem permissão para estornar entradas.');
        }
        if ($entry->reversal_of_id) {
            abort(422, 'Esta entrada já é um estorno; não é possível estornar novamente.');
        }
        if (FinancialEntry::where('reversal_of_id', $entry->id)->exists()) {
            abort(422, 'Esta entrada já possui um estorno registrado.');
        }

        $reverseType = $entry->type === 'income' ? 'expense' : 'income';
        $data = [
            'type' => $reverseType,
            'category' => $entry->category,
            'category_id' => $entry->category_id,
            'title' => 'Estorno: ' . $entry->title,
            'description' => ($entry->description ? 'Estorno de: ' . $entry->description . "\n" : '') . 'Original: #' . $entry->id . ' em ' . $entry->entry_date->format('d/m/Y'),
            'amount' => $entry->amount,
            'entry_date' => now()->toDateString(),
            'user_id' => $user->id,
            'fund_id' => $entry->fund_id,
            'reversal_of_id' => $entry->id,
            'payment_method' => $entry->payment_method,
            'reference_number' => $entry->reference_number ? 'EST-' . $entry->reference_number : null,
        ];
        if ($reverseType === 'expense') {
            $data['expense_status'] = FinancialEntry::EXPENSE_STATUS_PENDING;
        }

        $reversal = DB::transaction(function () use ($data, $entry, $user) {
            $reversal = FinancialEntry::create($data);
            $this->logAudit(AuditFinancialLog::ACTION_REVERSED, FinancialEntry::class, $reversal->id, null, $reversal->fresh()->toArray(), $user->id, Request::ip());
            return $reversal;
        });

        return $reversal->load(['user', 'campaign', 'ministry', 'payment', 'financialCategory', 'fund', 'reversalOf']);
    }

    /**
     * Import payment into Treasury. Permission: canCreateEntries. CBAV2026: delegates to createEntry (transaction + audit).
     */
    public function importPayment(Payment $payment, User|int $userOrId): FinancialEntry
    {
        $user = $userOrId instanceof User ? $userOrId : User::findOrFail($userOrId);
        $permission = TreasuryPermission::forUserOrAdmin($user);
        if (! $permission->canCreateEntries()) {
            abort(403, 'Você não tem permissão para criar entradas.');
        }

        if ($payment->financialEntry) {
            abort(422, 'Este pagamento já foi importado.');
        }

        $categoryMap = [
            'donation' => 'donation',
            'offering' => 'offering',
            'tithe' => 'tithe',
            'ministry_donation' => 'ministry_donation',
            'campaign' => 'campaign',
        ];
        $category = $categoryMap[$payment->payment_type] ?? 'donation';
        $ministryId = $payment->payable_type === 'Modules\Ministries\App\Models\Ministry' ? $payment->payable_id : null;
        $campaignId = $payment->payable_type === 'Modules\Treasury\App\Models\Campaign' ? $payment->payable_id : null;

        $data = [
            'type' => 'income',
            'category' => $category,
            'title' => $payment->description ?? 'Pagamento via ' . (optional($payment->gateway)->display_name ?? 'Gateway'),
            'description' => "Importado do PaymentGateway. Transação: {$payment->transaction_id}",
            'amount' => $payment->amount,
            'entry_date' => ($payment->paid_at ?? $payment->created_at)->format('Y-m-d'),
            'payment_id' => $payment->id,
            'campaign_id' => $campaignId,
            'ministry_id' => $ministryId,
            'payment_method' => $payment->payment_method ?? 'gateway',
            'reference_number' => $payment->transaction_id,
            'metadata' => [
                'gateway_transaction_id' => $payment->gateway_transaction_id,
                'gateway_name' => optional($payment->gateway)->name,
            ],
        ];
        $entry = $this->createEntry($data, $user);
        $entry->load('payment');

        return $entry;
    }

    /**
     * List campaigns (paginated). Permission: canViewReports for index.
     */
    public function listCampaigns(int $perPage = 20): LengthAwarePaginator
    {
        return Campaign::orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function getCampaign(int $id): Campaign
    {
        return Campaign::with(['financialEntries.user', 'goals'])->findOrFail($id);
    }

    /**
     * Create campaign. Permission: canManageCampaigns.
     */
    public function createCampaign(array $data, User|int $userOrId): Campaign
    {
        $this->ensureCanManageCampaigns($userOrId);
        return Campaign::create($data);
    }

    /**
     * Update campaign. Permission: canManageCampaigns.
     */
    public function updateCampaign(Campaign $campaign, array $data, User|int $userOrId): Campaign
    {
        $this->ensureCanManageCampaigns($userOrId);
        $campaign->update($data);
        return $campaign->fresh();
    }

    /**
     * Delete campaign. Permission: canManageCampaigns.
     */
    public function deleteCampaign(Campaign $campaign, User|int $userOrId): void
    {
        $this->ensureCanManageCampaigns($userOrId);
        $campaign->delete();
    }

    /**
     * List goals (paginated). Permission: canManageGoals or canViewReports.
     */
    public function listGoals(int $perPage = 20): LengthAwarePaginator
    {
        return FinancialGoal::with('campaign')->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function getGoal(int $id): FinancialGoal
    {
        $goal = FinancialGoal::with(['campaign', 'financialEntries.user'])->findOrFail($id);
        $goal->updateCurrentAmount();
        return $goal->fresh();
    }

    /**
     * Create goal. Permission: canManageGoals.
     */
    public function createGoal(array $data, User|int $userOrId): FinancialGoal
    {
        $this->ensureCanManageGoals($userOrId);
        $goal = FinancialGoal::create($data);
        $goal->updateCurrentAmount();
        return $goal;
    }

    /**
     * Update goal. Permission: canManageGoals.
     */
    public function updateGoal(FinancialGoal $goal, array $data, User|int $userOrId): FinancialGoal
    {
        $this->ensureCanManageGoals($userOrId);
        $goal->update($data);
        $goal->updateCurrentAmount();
        return $goal->fresh();
    }

    /**
     * Delete goal. Permission: canManageGoals.
     */
    public function deleteGoal(FinancialGoal $goal, User|int $userOrId): void
    {
        $this->ensureCanManageGoals($userOrId);
        $goal->delete();
    }

    /**
     * Report aggregates for a date range. Permission: canViewReports.
     */
    public function getReportAggregates(string $startDate, string $endDate, User|int $userOrId): array
    {
        $user = $userOrId instanceof User ? $userOrId : User::findOrFail($userOrId);
        $permission = TreasuryPermission::forUserOrAdmin($user);
        if (! $permission->canViewReports()) {
            abort(403, 'Você não tem permissão para visualizar relatórios.');
        }

        $totalIncome = (float) FinancialEntry::income()->period($startDate, $endDate)->sum('amount');
        $totalExpense = (float) FinancialEntry::expense()->period($startDate, $endDate)->sum('amount');
        $balance = $totalIncome - $totalExpense;

        $incomeByCategory = FinancialEntry::income()
            ->period($startDate, $endDate)
            ->select('category', DB::raw('sum(amount) as total'), DB::raw('count(*) as count'))
            ->groupBy('category')
            ->orderByDesc('total')
            ->get()
            ->map(function ($item) use ($totalIncome) {
                $item->percentage = $totalIncome > 0 ? ($item->total / $totalIncome) * 100 : 0;
                return $item;
            });

        $expenseByCategory = FinancialEntry::expense()
            ->period($startDate, $endDate)
            ->select('category', DB::raw('sum(amount) as total'), DB::raw('count(*) as count'))
            ->groupBy('category')
            ->orderByDesc('total')
            ->get()
            ->map(function ($item) use ($totalExpense) {
                $item->percentage = $totalExpense > 0 ? ($item->total / $totalExpense) * 100 : 0;
                return $item;
            });

        $incomeByDay = FinancialEntry::income()
            ->period($startDate, $endDate)
            ->select(DB::raw('DATE(entry_date) as date'), DB::raw('sum(amount) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $expenseByDay = FinancialEntry::expense()
            ->period($startDate, $endDate)
            ->select(DB::raw('DATE(entry_date) as date'), DB::raw('sum(amount) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $totalEntries = FinancialEntry::period($startDate, $endDate)->count();
        $totalIncomeEntries = FinancialEntry::income()->period($startDate, $endDate)->count();
        $totalExpenseEntries = FinancialEntry::expense()->period($startDate, $endDate)->count();

        $daysDiff = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;
        $avgDailyIncome = $daysDiff > 0 ? $totalIncome / $daysDiff : 0;
        $avgDailyExpense = $daysDiff > 0 ? $totalExpense / $daysDiff : 0;

        $largestIncome = FinancialEntry::income()
            ->period($startDate, $endDate)
            ->orderByDesc('amount')
            ->first();

        $largestExpense = FinancialEntry::expense()
            ->period($startDate, $endDate)
            ->orderByDesc('amount')
            ->first();

        $incomeByPaymentMethod = FinancialEntry::income()
            ->period($startDate, $endDate)
            ->whereNotNull('payment_method')
            ->select('payment_method', DB::raw('sum(amount) as total'), DB::raw('count(*) as count'))
            ->groupBy('payment_method')
            ->orderByDesc('total')
            ->get();

        $expenseByPaymentMethod = FinancialEntry::expense()
            ->period($startDate, $endDate)
            ->whereNotNull('payment_method')
            ->select('payment_method', DB::raw('sum(amount) as total'), DB::raw('count(*) as count'))
            ->groupBy('payment_method')
            ->orderByDesc('total')
            ->get();

        $incomeByMonth = FinancialEntry::income()
            ->period($startDate, $endDate)
            ->select(DB::raw('YEAR(entry_date) as year'), DB::raw('MONTH(entry_date) as month'), DB::raw('sum(amount) as total'))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $expenseByMonth = FinancialEntry::expense()
            ->period($startDate, $endDate)
            ->select(DB::raw('YEAR(entry_date) as year'), DB::raw('MONTH(entry_date) as month'), DB::raw('sum(amount) as total'))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $planoCooperativo = $this->getPlanoCooperativoForPeriod($startDate, $endDate, $totalIncome);

        return [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense,
            'balance' => $balance,
            'plano_cooperativo' => $planoCooperativo,
            'income_by_category' => $incomeByCategory,
            'expense_by_category' => $expenseByCategory,
            'income_by_day' => $incomeByDay,
            'expense_by_day' => $expenseByDay,
            'total_entries' => $totalEntries,
            'total_income_entries' => $totalIncomeEntries,
            'total_expense_entries' => $totalExpenseEntries,
            'days_diff' => $daysDiff,
            'avg_daily_income' => $avgDailyIncome,
            'avg_daily_expense' => $avgDailyExpense,
            'largest_income' => $largestIncome,
            'largest_expense' => $largestExpense,
            'income_by_payment_method' => $incomeByPaymentMethod,
            'expense_by_payment_method' => $expenseByPaymentMethod,
            'income_by_month' => $incomeByMonth,
            'expense_by_month' => $expenseByMonth,
        ];
    }

    /**
     * Report aggregates for a ministry in a date range.
     * Allowed: user with canViewReports, or leader/co-leader of that ministry.
     */
    public function getMinistrySummary(int $ministryId, string $startDate, string $endDate, User|int $userOrId): array
    {
        $user = $userOrId instanceof User ? $userOrId : User::findOrFail($userOrId);
        $permission = TreasuryPermission::forUserOrAdmin($user);
        $ministry = Ministry::find($ministryId);
        if (! $ministry) {
            abort(404, 'Ministério não encontrado.');
        }
        if (! $permission->canViewReports() && ! $ministry->isLeader($user)) {
            abort(403, 'Você não tem permissão para visualizar o resumo financeiro deste ministério.');
        }

        $totalIncome = (float) FinancialEntry::income()->period($startDate, $endDate)->where('ministry_id', $ministryId)->sum('amount');
        $totalExpense = (float) FinancialEntry::expense()->period($startDate, $endDate)->where('ministry_id', $ministryId)->sum('amount');
        $balance = $totalIncome - $totalExpense;

        $incomeByCategory = FinancialEntry::income()
            ->period($startDate, $endDate)
            ->where('ministry_id', $ministryId)
            ->select('category', DB::raw('sum(amount) as total'), DB::raw('count(*) as count'))
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        $expenseByCategory = FinancialEntry::expense()
            ->period($startDate, $endDate)
            ->where('ministry_id', $ministryId)
            ->select('category', DB::raw('sum(amount) as total'), DB::raw('count(*) as count'))
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        return [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'ministry_id' => $ministryId,
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense,
            'balance' => $balance,
            'income_by_category' => $incomeByCategory,
            'expense_by_category' => $expenseByCategory,
        ];
    }

    /**
     * Get or create a monthly closing snapshot for a full calendar month.
     * Permission: canViewReports + (diretoria member or admin/lideranca with diretoria override).
     */
    public function getOrCreateMonthlyClosing(string $startDate, string $endDate, User|int $userOrId): TreasuryMonthlyClosing
    {
        $user = $userOrId instanceof User ? $userOrId : User::findOrFail($userOrId);
        $permission = TreasuryPermission::forUserOrAdmin($user);
        if (! $permission->canViewReports()) {
            abort(403, 'Você não tem permissão para visualizar relatórios.');
        }

        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        $monthStart = $start->copy()->startOfMonth();
        $monthEnd = $start->copy()->endOfMonth();

        if (! $start->isSameDay($monthStart) || ! $end->isSameDay($monthEnd)) {
            abort(422, 'Período não corresponde a um mês calendário completo.');
        }

        $year = (int) $start->year;
        $month = (int) $start->month;

        $closing = TreasuryMonthlyClosing::forPeriod($year, $month)->first();
        if ($closing) {
            return $closing;
        }

        $totalIncome = (float) FinancialEntry::income()->period($startDate, $endDate)->sum('amount');
        $totalExpense = (float) FinancialEntry::expense()->period($startDate, $endDate)->sum('amount');
        $balance = $totalIncome - $totalExpense;

        return TreasuryMonthlyClosing::create([
            'year' => $year,
            'month' => $month,
            'period_start' => $monthStart->toDateString(),
            'period_end' => $monthEnd->toDateString(),
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense,
            'balance' => $balance,
        ]);
    }

    /**
     * Mark a monthly closing as ready for assembly with diretoria approval.
     */
    public function markClosingReadyForAssembly(TreasuryMonthlyClosing $closing, User|int $userOrId, ?string $notes = null): TreasuryMonthlyClosing
    {
        $user = $userOrId instanceof User ? $userOrId : User::findOrFail($userOrId);
        $permission = TreasuryPermission::forUserOrAdmin($user);
        if (! $permission->canViewReports()) {
            abort(403, 'Você não tem permissão para aprovar fechamentos.');
        }

        $isDiretoriaMember = class_exists(\Modules\Diretoria\App\Models\DiretoriaMember::class)
            && \Modules\Diretoria\App\Models\DiretoriaMember::where('user_id', $user->id)->where('is_active', true)->exists();

        $allowAdminApproval = class_exists(\Modules\Diretoria\App\Services\DiretoriaSettings::class)
            ? \Modules\Diretoria\App\Services\DiretoriaSettings::allowAdminApproval()
            : (bool) \App\Models\Settings::get('jubaf_diretoria_allow_admin_approval', false);

        $isAdminOrlideranca = method_exists($user, 'hasRole')
            ? ($user->hasRole('admin') || $user->hasRole('lideranca'))
            : false;

        if (! $isDiretoriaMember && ! ($allowAdminApproval && $isAdminOrlideranca)) {
            abort(403, 'Apenas o conselho ou lideranca/admin autorizado pode aprovar fechamentos mensais.');
        }

        if (! $closing->ready_for_assembly) {
            $closing->update([
                'ready_for_assembly' => true,
                'diretoria_approved_at' => now(),
                'diretoria_approved_by' => $user->id,
                'notes' => $notes ?? $closing->notes,
            ]);

            if (class_exists(\Modules\Diretoria\App\Services\DiretoriaAuditService::class)) {
                try {
                    app(\Modules\Diretoria\App\Services\DiretoriaAuditService::class)->log('treasury_closing_ready_for_assembly', $closing, [
                        'year' => $closing->year,
                        'month' => $closing->month,
                        'balance' => $closing->balance,
                    ]);
                } catch (\Throwable $e) {
                    \Log::warning('diretoria audit for treasury closing failed: '.$e->getMessage());
                }
            }

            if (class_exists(\Modules\Notifications\App\Services\InAppNotificationService::class)) {
                try {
                    $monthLabel = sprintf('%02d/%d', $closing->month, $closing->year);
                    app(\Modules\Notifications\App\Services\InAppNotificationService::class)->sendToAdmins(
                        "Balancete {$monthLabel} pronto para assembleia",
                        "O conselho marcou o balancete de {$monthLabel} como pronto para apresentação em assembleia.",
                        [
                            'type' => 'success',
                            'priority' => 'normal',
                        ]
                    );
                } catch (\Throwable $e) {
                    \Log::warning('Failed to send treasury closing notification: '.$e->getMessage());
                }
            }
        }

        return $closing->refresh();
    }

    /**
     * Plano Cooperativo: percentual sobre base (dízimos, dízimos+ofertas ou receita total). CBAV2026.
     */
    public function getPlanoCooperativoForPeriod(string $startDate, string $endDate, ?float $totalIncome = null): array
    {
        $percent = (float) \App\Models\Settings::get('treasury_plano_cooperativo_percent', 10);
        $base = \App\Models\Settings::get('treasury_plano_cooperativo_base', 'tithes_offerings');

        if ($totalIncome === null) {
            $totalIncome = (float) FinancialEntry::income()->period($startDate, $endDate)->sum('amount');
        }

        if ($base === 'tithes_only') {
            $baseAmount = (float) FinancialEntry::income()->period($startDate, $endDate)->where('category', 'tithe')->sum('amount');
        } elseif ($base === 'total_income') {
            $baseAmount = $totalIncome;
        } else {
            $baseAmount = (float) FinancialEntry::income()->period($startDate, $endDate)->whereIn('category', ['tithe', 'offering'])->sum('amount');
        }

        $suggestedAmount = round($baseAmount * ($percent / 100), 2);

        return [
            'percent' => $percent,
            'base_key' => $base,
            'base_amount' => $baseAmount,
            'suggested_amount' => $suggestedAmount,
        ];
    }

    /**
     * List permissions (paginated). Permission: isAdmin.
     */
    public function listPermissions(int $perPage = 20): LengthAwarePaginator
    {
        return TreasuryPermission::with('user')->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function getPermission(int $id): TreasuryPermission
    {
        return TreasuryPermission::with('user')->findOrFail($id);
    }

    /**
     * Create permission. Permission: isAdmin.
     */
    public function createPermission(array $data, User|int $userOrId): TreasuryPermission
    {
        $this->ensureIsAdmin($userOrId);
        return TreasuryPermission::create($data);
    }

    /**
     * Update permission. Permission: isAdmin.
     */
    public function updatePermission(TreasuryPermission $treasuryPermission, array $data, User|int $userOrId): TreasuryPermission
    {
        $this->ensureIsAdmin($userOrId);
        $treasuryPermission->update($data);
        return $treasuryPermission->fresh('user');
    }

    /**
     * Delete permission. Permission: isAdmin.
     */
    public function deletePermission(TreasuryPermission $treasuryPermission, User|int $userOrId): void
    {
        $this->ensureIsAdmin($userOrId);
        $treasuryPermission->delete();
    }

    /**
     * Options for entry forms: campaigns, goals, ministries, funds, categories, payments (for import). CBAV2026.
     */
    public function getEntryFormOptions(): array
    {
        return [
            'campaigns' => Campaign::where('is_active', true)->get(),
            'goals' => FinancialGoal::where('is_active', true)->get(),
            'ministries' => Ministry::where('is_active', true)->get(),
            'financial_categories' => FinancialCategory::orderBy('type')->orderBy('order')->get(),
            'financial_funds' => \Modules\Treasury\App\Models\FinancialFund::orderBy('name')->get(),
            'payments' => Payment::where('status', 'completed')
                ->whereDoesntHave('financialEntry')
                ->orderBy('created_at', 'desc')
                ->limit(50)
                ->get(),
        ];
    }

    private function ensureCanManageCampaigns(User|int $userOrId): void
    {
        $user = $userOrId instanceof User ? $userOrId : User::findOrFail($userOrId);
        $permission = TreasuryPermission::forUserOrAdmin($user);
        if (! $permission->canManageCampaigns()) {
            abort(403, 'Você não tem permissão para gerenciar campanhas.');
        }
    }

    private function ensureCanManageGoals(User|int $userOrId): void
    {
        $user = $userOrId instanceof User ? $userOrId : User::findOrFail($userOrId);
        $permission = TreasuryPermission::forUserOrAdmin($user);
        if (! $permission->canManageGoals()) {
            abort(403, 'Você não tem permissão para gerenciar metas.');
        }
    }

    private function ensureIsAdmin(User|int $userOrId): void
    {
        $user = $userOrId instanceof User ? $userOrId : User::findOrFail($userOrId);
        $permission = TreasuryPermission::forUserOrAdmin($user);
        if (! $permission->isAdmin()) {
            abort(403, 'Apenas administradores podem gerenciar permissões.');
        }
    }
}
