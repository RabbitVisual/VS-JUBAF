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
        Schema::table('worship_setlist_items', function (Blueprint $table) {
            $table->string('type')->default('song')->after('setlist_id');
            $table->string('title')->nullable()->after('type');
            $table->json('content')->nullable()->after('title');
            $table->json('metadata')->nullable()->after('content');

            // Make song_id nullable for non-song items
            $table->foreignId('song_id')->nullable()->change();
        });

        // Update existing items to have type 'song' and title from song if possible (handled by Eloquent usually, but safer here)
        // Actually, titles will be resolved at runtime if null.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('worship_setlist_items', function (Blueprint $table) {
            $table->dropColumn(['type', 'title', 'content', 'metadata']);
            $table->foreignId('song_id')->nullable(false)->change();
        });
    }
};
