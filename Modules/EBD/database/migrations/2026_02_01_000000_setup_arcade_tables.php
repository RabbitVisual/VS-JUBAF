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
        // 1. Update existing games table or create
        if (Schema::hasTable('ebd_games')) {
            Schema::table('ebd_games', function (Blueprint $table) {
                if (!Schema::hasColumn('ebd_games', 'icon')) {
                    $table->string('icon')->nullable()->after('slug');
                }
                if (!Schema::hasColumn('ebd_games', 'description')) {
                    $table->text('description')->nullable()->after('icon');
                }
            });
        } else {
            Schema::create('ebd_games', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('icon')->nullable();
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 2. Create Game Questions Table
        if (!Schema::hasTable('ebd_game_questions')) {
            Schema::create('ebd_game_questions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('game_id')->constrained('ebd_games')->onDelete('cascade');
                $table->text('question_text');
                $table->string('media_url')->nullable();
                $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
                $table->timestamps();
            });
        }

        // 3. Create Game Answers Table
        if (!Schema::hasTable('ebd_game_answers')) {
            Schema::create('ebd_game_answers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('question_id')->constrained('ebd_game_questions')->onDelete('cascade');
                $table->text('answer_text');
                $table->boolean('is_correct')->default(false);
                $table->timestamps();
            });
        }

        // 4. Create User Game Sessions
        if (!Schema::hasTable('ebd_user_game_sessions')) {
            Schema::create('ebd_user_game_sessions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('game_id')->constrained('ebd_games')->onDelete('cascade');
                $table->integer('score')->default(0);
                $table->integer('duration')->default(0); // In seconds
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ebd_user_game_sessions');
        Schema::dropIfExists('ebd_game_answers');
        Schema::dropIfExists('ebd_game_questions');

        if (Schema::hasTable('ebd_games')) {
            Schema::table('ebd_games', function (Blueprint $table) {
                $table->dropColumn(['icon', 'description']);
            });
        }
    }
};
