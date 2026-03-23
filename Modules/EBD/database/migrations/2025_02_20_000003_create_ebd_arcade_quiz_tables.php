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
        // 7. ebd_games
        Schema::create('ebd_games', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->boolean('is_active')->default(true);
            $table->json('config')->nullable();
            $table->timestamps();
        });

        // 8. ebd_game_scores
        Schema::create('ebd_game_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('game_id')->constrained('ebd_games')->cascadeOnDelete();
            $table->integer('score');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        // 9. ebd_quiz_sessions
        Schema::create('ebd_quiz_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('class_id')->constrained('ebd_classes')->cascadeOnDelete();
            $table->enum('status', ['waiting', 'active', 'finished', 'ranking'])->default('waiting');
            $table->integer('current_question_index')->default(0);
            $table->boolean('show_answer')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ebd_quiz_sessions');
        Schema::dropIfExists('ebd_game_scores');
        Schema::dropIfExists('ebd_games');
    }
};
