<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('worship_songs', function (Blueprint $table) {
            // Rename existing columns to match the new professional model
            if (Schema::hasColumn('worship_songs', 'content') && !Schema::hasColumn('worship_songs', 'content_chordpro')) {
                $table->renameColumn('content', 'content_chordpro');
            }

            if (Schema::hasColumn('worship_songs', 'youtube_link') && !Schema::hasColumn('worship_songs', 'youtube_id')) {
                $table->renameColumn('youtube_link', 'youtube_id');
            }

            // Add missing columns needed for the new features
            if (!Schema::hasColumn('worship_songs', 'themes')) {
                $table->json('themes')->nullable()->after('youtube_id');
            }

            if (!Schema::hasColumn('worship_songs', 'lyrics_only')) {
                $table->text('lyrics_only')->nullable()->after('content_chordpro');
            }
        });

        // Data Cleanup: Try to extract YouTube ID from full URLs if any exist
        $songs = DB::table('worship_songs')->get();
        foreach ($songs as $song) {
            if (isset($song->youtube_id) && str_contains($song->youtube_id, 'http')) {
                $id = '';
                // Handle youtu.be/ID
                if (preg_match('/youtu\.be\/([^\/\?]+)/', $song->youtube_id, $matches)) {
                    $id = $matches[1];
                }
                // Handle youtube.com/watch?v=ID
                elseif (preg_match('/v=([^\&\?]+)/', $song->youtube_id, $matches)) {
                    $id = $matches[1];
                }

                if ($id) {
                    DB::table('worship_songs')->where('id', $song->id)->update(['youtube_id' => $id]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('worship_songs', function (Blueprint $table) {
            $table->renameColumn('content_chordpro', 'content');
            $table->renameColumn('youtube_id', 'youtube_link');
            $table->dropColumn(['themes', 'lyrics_only']);
        });
    }
};
