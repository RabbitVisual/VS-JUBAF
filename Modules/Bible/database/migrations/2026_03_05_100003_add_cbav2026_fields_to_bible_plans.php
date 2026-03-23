<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * CBAV2026: church plan, complexity, template, reading_mode, allow_back_tracking.
     */
    public function up(): void
    {
        Schema::table('bible_plans', function (Blueprint $table) {
            if (! Schema::hasColumn('bible_plans', 'reading_mode')) {
                $table->string('reading_mode', 32)->default('digital')->after('duration_days');
            }
            if (! Schema::hasColumn('bible_plans', 'allow_back_tracking')) {
                $table->boolean('allow_back_tracking')->default(true)->after('reading_mode');
            }
            if (! Schema::hasColumn('bible_plans', 'is_church_plan')) {
                $table->boolean('is_church_plan')->default(false)->after('is_featured');
            }
            if (! Schema::hasColumn('bible_plans', 'complexity')) {
                $table->string('complexity', 32)->nullable()->after('is_church_plan');
            }
            if (! Schema::hasColumn('bible_plans', 'template_key')) {
                $table->string('template_key', 64)->nullable()->after('complexity');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bible_plans', function (Blueprint $table) {
            $columns = ['reading_mode', 'allow_back_tracking', 'is_church_plan', 'complexity', 'template_key'];
            foreach ($columns as $col) {
                if (Schema::hasColumn('bible_plans', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
