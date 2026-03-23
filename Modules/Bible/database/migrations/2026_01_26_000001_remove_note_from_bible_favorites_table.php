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
        Schema::table('bible_favorites', function (Blueprint $table) {
            // Remove 'note' column if it exists (it should not be on the pivot table)
            // Notes are now stored in a separate bible_user_notes table per day
            if (Schema::hasColumn('bible_favorites', 'note')) {
                $table->dropColumn('note');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bible_favorites', function (Blueprint $table) {
            $table->text('note')->nullable();
        });
    }
};
