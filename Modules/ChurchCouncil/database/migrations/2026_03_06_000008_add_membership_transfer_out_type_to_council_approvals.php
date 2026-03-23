<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add membership_transfer_out to approval_type enum (MySQL).
     */
    public function up(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE council_approvals MODIFY approval_type ENUM('account_activation','ministry_membership','event_creation','financial_request','document_approval','policy_change','other','membership_transfer_out') DEFAULT 'other'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE council_approvals MODIFY approval_type ENUM('account_activation','ministry_membership','event_creation','financial_request','document_approval','policy_change','other') DEFAULT 'other'");
        }
    }
};

