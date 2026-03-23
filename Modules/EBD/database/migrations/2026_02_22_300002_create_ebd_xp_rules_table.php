<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Regras de XP por fonte (jogo, aula, bônus) – balanceamento 100% em DB.
     */
    public function up(): void
    {
        Schema::create('ebd_xp_rules', function (Blueprint $table) {
            $table->id();
            $table->string('source_type', 40)->index(); // game, lesson, streak_bonus, achievement_bonus
            $table->string('source_slug', 80)->nullable()->index(); // slug do jogo quando source_type=game
            $table->string('formula', 80)->default('score * 0.1'); // score * 0.1, fixed
            $table->unsignedInteger('value')->default(100); // para fixed ou cap
            $table->unsignedInteger('cap')->nullable(); // teto por evento (ex: 50)
            $table->unsignedInteger('min')->default(0); // mínimo (ex: 1 se score > 0)
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['source_type', 'source_slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ebd_xp_rules');
    }
};
