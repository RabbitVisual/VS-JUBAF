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
        // 1. Expand Worship Songs
        Schema::table('worship_songs', function (Blueprint $table) {
            $table->string('multitrack_url')->nullable();
            $table->text('song_structure')->nullable(); // Intro, V1, C, V2, C, B, C, Outro
        });

        // 2. Expand Worship Rosters (Escalas)
        Schema::table('worship_rosters', function (Blueprint $table) {
            $table->text('member_notes')->nullable();
            $table->foreignId('worship_team_role_id')->nullable()->constrained('worship_team_roles')->nullOnDelete();
        });

        // 3. Expand Worship Setlists
        Schema::table('worship_setlists', function (Blueprint $table) {
            $table->text('producer_notes')->nullable();
            $table->string('stage_layout_pdf')->nullable(); // Caminho para PDF de layout de palco
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('worship_setlists', function (Blueprint $table) {
            $table->dropColumn(['producer_notes', 'stage_layout_pdf']);
        });

        Schema::table('worship_rosters', function (Blueprint $table) {
            $table->dropForeign(['worship_team_role_id']);
            $table->dropColumn(['member_notes', 'worship_team_role_id']);
        });

        Schema::table('worship_songs', function (Blueprint $table) {
            $table->dropColumn(['multitrack_url', 'song_structure']);
        });
    }
};
