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
        Schema::table('event_registration_segments', function (Blueprint $table) {
            $table->string('price_rule_type', 50)->nullable()->after('price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_registration_segments', function (Blueprint $table) {
            $table->dropColumn('price_rule_type');
        });
    }
};
