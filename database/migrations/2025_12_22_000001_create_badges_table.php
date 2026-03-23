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
        Schema::create('badges', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('icon')->default('default'); // Nome do ícone do sistema
            $table->string('color')->default('blue'); // Cor do badge
            $table->integer('points_required')->default(0); // Pontos necessários para ganhar
            $table->enum('criteria_type', ['manual', 'auto', 'points', 'time_congregating', 'is_baptized', 'profile_complete', 'ministries_count', 'bible_favorites'])->default('manual');
            $table->json('criteria_value')->nullable(); // Valores específicos do critério (ex: {"months": 12})
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0); // Ordem de exibição
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('badges');
    }
};
