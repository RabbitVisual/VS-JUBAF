<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Conquistas (500+) com critérios em DB; progresso em ebd_user_achievements.
     */
    public function up(): void
    {
        Schema::create('ebd_achievements', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 80)->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('icon_id')->nullable();
            $table->string('icon_fa_name', 80)->default('medal'); // fallback se global_icons não existir
            $table->string('tier', 20)->default('bronze'); // bronze, silver, gold, platinum
            $table->string('difficulty', 20)->nullable(); // easy, medium, hard
            $table->string('trigger_type', 60)->index(); // xp_milestone, game_plays, game_first_win, lesson_complete, streak_days
            $table->json('trigger_value')->nullable();
            $table->unsignedInteger('xp_bonus')->default(0);
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_hidden')->default(false);
            $table->timestamps();
        });

        Schema::create('ebd_user_achievements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('achievement_id')->constrained('ebd_achievements')->cascadeOnDelete();
            $table->dateTime('awarded_at');
            $table->timestamps();
            $table->unique(['user_id', 'achievement_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ebd_user_achievements');
        Schema::dropIfExists('ebd_achievements');
    }
};
