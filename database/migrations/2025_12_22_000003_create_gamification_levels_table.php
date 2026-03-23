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
        Schema::create('gamification_levels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('icon')->default('default'); // Nome do ícone do sistema
            $table->string('color')->default('gray'); // Cor do nível
            $table->integer('points_min')->default(0); // Pontos mínimos
            $table->integer('points_max')->nullable(); // Pontos máximos (null = ilimitado)
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
        Schema::dropIfExists('gamification_levels');
    }
};
