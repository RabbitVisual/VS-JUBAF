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
        Schema::table('bible_plans', function (Blueprint $table) {
            $table->enum('reading_mode', ['digital', 'physical_timer'])->default('digital')->after('type'); // Mode: Read in App OR Physical Book + Timer
        });

        Schema::table('bible_user_progress', function (Blueprint $table) {
            $table->integer('time_spent')->nullable()->comment('Seconds spent reading');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bible_user_progress', function (Blueprint $table) {
            $table->dropColumn('time_spent');
        });

        Schema::table('bible_plans', function (Blueprint $table) {
            $table->dropColumn('reading_mode');
        });
    }
};
