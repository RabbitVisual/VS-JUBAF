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
        Schema::table('ebd_game_questions', function (Blueprint $table) {
            $table->integer('points')->default(0)->after('question_text');
            $table->integer('time_limit')->default(30)->after('points');
            $table->json('metadata')->nullable()->after('difficulty');
        });

        Schema::table('ebd_game_answers', function (Blueprint $table) {
            $table->integer('order')->default(0)->after('is_correct');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ebd_game_answers', function (Blueprint $table) {
            $table->dropColumn('order');
        });

        Schema::table('ebd_game_questions', function (Blueprint $table) {
            $table->dropColumn('metadata');
        });
    }
};
