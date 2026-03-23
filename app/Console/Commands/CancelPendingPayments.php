<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Modules\PaymentGateway\App\Models\Payment;

class CancelPendingPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:cancel-pending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancela pagamentos pendentes criados há mais de 30 minutos.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = Carbon::now()->subMinutes(30);

        $affected = Payment::where('status', 'pending')
            ->where('created_at', '<', $limit)
            ->update(['status' => 'cancelled']);

        $this->info("{$affected} pagamentos pendentes expirados foram cancelados.");
    }
}
