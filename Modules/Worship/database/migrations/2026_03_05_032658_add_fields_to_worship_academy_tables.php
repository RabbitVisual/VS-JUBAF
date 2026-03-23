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
        // Expand Academy Courses
        Schema::table('worship_academy_courses', function (Blueprint $table) {
            $table->string('difficulty_level')->nullable(); // Iniciante, Intermediário, Avançado
            $table->foreignId('worship_team_role_id')->nullable()->constrained('worship_team_roles')->nullOnDelete();
        });

        // Expand Academy Lessons (Supports multiple media types now via Media Asset relation,
        // but we add a field for chord charts or sheet music specific to this lesson if not using the morph media)
        Schema::table('worship_academy_lessons', function (Blueprint $table) {
            $table->string('sheet_music_pdf')->nullable();
            $table->string('multicam_video_url')->nullable(); // Link adicional para vídeo multi-ângulo
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('worship_academy_lessons', function (Blueprint $table) {
            $table->dropColumn(['sheet_music_pdf', 'multicam_video_url']);
        });

        Schema::table('worship_academy_courses', function (Blueprint $table) {
            $table->dropForeign(['worship_team_role_id']);
            $table->dropColumn(['difficulty_level', 'worship_team_role_id']);
        });
    }
};
