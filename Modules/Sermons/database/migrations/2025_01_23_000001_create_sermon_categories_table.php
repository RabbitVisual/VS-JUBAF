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
        Schema::create('sermon_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nome da categoria (ex: "Evangelismo", "Doutrina", "Vida Cristã")
            $table->string('slug')->unique(); // Slug para URLs
            $table->text('description')->nullable();
            $table->string('color')->nullable(); // Cor para visualização
            $table->string('icon')->nullable(); // Ícone
            $table->integer('order')->default(0); // Ordem de exibição
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_active', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sermon_categories');
    }
};
