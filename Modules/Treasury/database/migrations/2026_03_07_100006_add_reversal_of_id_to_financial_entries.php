<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * CBAV2026: Estorno rastreável (entrada de estorno aponta para a original).
     */
    public function up(): void
    {
        Schema::table('financial_entries', function (Blueprint $table) {
            if (! Schema::hasColumn('financial_entries', 'reversal_of_id')) {
                $table->unsignedBigInteger('reversal_of_id')->nullable()->after('fund_id');
                $table->foreign('reversal_of_id')->references('id')->on('financial_entries')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('financial_entries', function (Blueprint $table) {
            if (Schema::hasColumn('financial_entries', 'reversal_of_id')) {
                $table->dropForeign(['reversal_of_id']);
            }
        });
    }
};
