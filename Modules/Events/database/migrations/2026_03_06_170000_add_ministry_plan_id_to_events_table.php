<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('events', 'ministry_plan_id')) {
            Schema::table('events', function (Blueprint $table) {
                $table->unsignedBigInteger('ministry_plan_id')->nullable()->after('ministry_id');
            });
        }
        if (Schema::hasTable('ministry_plans') && Schema::hasColumn('events', 'ministry_plan_id')) {
            Schema::table('events', function (Blueprint $table) {
                $table->foreign('ministry_plan_id')->references('id')->on('ministry_plans')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (Schema::hasColumn('events', 'ministry_plan_id')) {
                $table->dropForeign(['ministry_plan_id']);
                $table->dropColumn('ministry_plan_id');
            }
        });
    }
};
