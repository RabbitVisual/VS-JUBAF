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
            if (! Schema::hasColumn('event_price_rules', 'church_membership')) {
                $table->string('church_membership')->nullable()->after('member_status');
            }
            if (! Schema::hasColumn('event_price_rules', 'gender')) {
                $table->string('gender')->nullable()->after('participant_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_price_rules', function (Blueprint $table) {
            $table->dropColumn(['church_membership', 'gender']);
        });
    }
};
