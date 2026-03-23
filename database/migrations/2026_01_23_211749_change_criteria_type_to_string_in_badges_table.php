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
        Schema::table('badges', function (Blueprint $table) {
            $table->string('criteria_type', 100)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('badges', function (Blueprint $table) {
            // We can't easily revert to enum with data effectively without raw SQL or Doctrine mapping issues sometimes,
            // but for now we try strict reversal or just leave as string (user didn't ask for strict down).
            // Let's attempt to restore the enum if possible, or just drop it.
            // Ideally, keeping it as string is fine.
            // $table->enum('criteria_type', ['manual', 'auto', 'points', 'time_congregating', 'is_baptized', 'profile_complete', 'ministries_count', 'bible_favorites'])->change();
        });
    }
};
