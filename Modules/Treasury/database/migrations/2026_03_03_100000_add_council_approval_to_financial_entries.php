<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Link expense entries above budget limit to ChurchCouncil approval.
     */
    public function up(): void
    {
        Schema::table('financial_entries', function (Blueprint $table) {
            $table->foreignId('council_approval_id')->nullable()->after('metadata')->constrained('council_approvals')->nullOnDelete();
            $table->timestamp('council_approved_at')->nullable()->after('council_approval_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('financial_entries', function (Blueprint $table) {
            $table->dropForeign(['council_approval_id']);
            $table->dropColumn('council_approved_at');
        });
    }
};
