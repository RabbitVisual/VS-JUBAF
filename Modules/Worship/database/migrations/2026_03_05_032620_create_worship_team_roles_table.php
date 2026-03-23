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
        Schema::create('worship_team_roles', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Vocal, Bateria, Teclado
            $table->string('color')->nullable(); // Cor no planejamento
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('worship_team_roles');
    }
};
