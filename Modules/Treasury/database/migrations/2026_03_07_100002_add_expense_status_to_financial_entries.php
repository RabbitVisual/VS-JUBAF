<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * CBAV2026: Status de despesa (Pendente, Aprovada, Paga).
     */
    public function up(): void
    {
        Schema::table('financial_entries', function (Blueprint $table) {
            if (! Schema::hasColumn('financial_entries', 'expense_status')) {
                $table->enum('expense_status', ['pending', 'approved', 'paid'])->nullable()->after('council_approved_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('financial_entries', function (Blueprint $table) {
            if (Schema::hasColumn('financial_entries', 'expense_status')) {
                $table->dropColumn('expense_status');
            }
        });
    }
};
