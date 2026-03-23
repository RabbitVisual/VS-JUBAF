<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('event_price_rules', function (Blueprint $table) {
            if (! Schema::hasColumn('event_price_rules', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }
            if (! Schema::hasColumn('event_price_rules', 'priority')) {
                $table->integer('priority')->default(0);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_price_rules', function (Blueprint $table) {
            if (Schema::hasColumn('event_price_rules', 'is_active')) {
                $table->dropColumn('is_active');
            }
            if (Schema::hasColumn('event_price_rules', 'priority')) {
                $table->dropColumn('priority');
            }
        });
    }
};
