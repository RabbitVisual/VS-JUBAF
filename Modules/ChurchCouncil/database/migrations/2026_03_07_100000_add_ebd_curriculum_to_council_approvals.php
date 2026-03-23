<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE council_approvals MODIFY approval_type ENUM(
                'account_activation',
                'ministry_membership',
                'event_creation',
                'financial_request',
                'document_approval',
                'policy_change',
                'other',
                'membership_transfer_out',
                'ministry_plan',
                'ebd_curriculum'
            ) DEFAULT 'other'");
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE council_approvals MODIFY approval_type ENUM(
                'account_activation',
                'ministry_membership',
                'event_creation',
                'financial_request',
                'document_approval',
                'policy_change',
                'other',
                'membership_transfer_out',
                'ministry_plan'
            ) DEFAULT 'other'");
        }
    }
};
