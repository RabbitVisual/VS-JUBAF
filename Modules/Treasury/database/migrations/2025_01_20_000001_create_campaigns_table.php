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
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nome da campanha (ex: "Reforma do Templo")
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('target_amount', 15, 2)->nullable(); // Meta de arrecadação
            $table->decimal('current_amount', 15, 2)->default(0); // Valor arrecadado
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('image')->nullable(); // Imagem da campanha
            $table->json('settings')->nullable(); // Configurações extras
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
