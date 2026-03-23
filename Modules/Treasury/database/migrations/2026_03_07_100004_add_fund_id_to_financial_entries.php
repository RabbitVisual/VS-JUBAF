<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * CBAV2026: Vincular entrada a um fundo (centro de custo).
     */
    public function up(): void
    {
        Schema::table('financial_entries', function (Blueprint $table) {
            if (! Schema::hasColumn('financial_entries', 'fund_id')) {
                $table->foreignId('fund_id')->nullable()->after('ministry_id')->constrained('financial_funds')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('financial_entries', function (Blueprint $table) {
            if (Schema::hasColumn('financial_entries', 'fund_id')) {
                $table->dropForeign(['fund_id']);
            }
        });
    }
};
