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
            if (! Schema::hasColumn('event_price_rules', 'rule_type')) {
                $table->string('rule_type')->nullable()->after('label');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_price_rules', function (Blueprint $table) {
            if (Schema::hasColumn('event_price_rules', 'rule_type')) {
                $table->dropColumn('rule_type');
            }
        });
    }
};
