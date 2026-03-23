<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bible_versions', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nome da versão (ex: "Almeida Revista e Atualizada")
            $table->string('abbreviation')->unique(); // Abreviação (ex: "ARA", "ARC", "BLIVRE")
            $table->string('description')->nullable();
            $table->string('language')->default('pt-BR');
            $table->string('file_name')->nullable(); // Nome do arquivo CSV original
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->integer('total_books')->default(66);
            $table->integer('total_chapters')->default(0);
            $table->integer('total_verses')->default(0);
            $table->timestamp('imported_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bible_versions');
    }
};
