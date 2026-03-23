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
        Schema::create('sermon_bible_references', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sermon_id')->constrained('sermons')->cascadeOnDelete();

            // Referência bíblica
            $table->string('book'); // Livro da Bíblia (ex: "João")
            $table->integer('chapter')->nullable(); // Capítulo
            $table->string('verses')->nullable(); // Versículos (ex: "1-5", "3,5,7-10")
            $table->text('reference_text')->nullable(); // Texto completo da referência

            // Vinculação com o módulo Bible (opcional)
            $table->foreignId('bible_version_id')->nullable()->constrained('bible_versions')->nullOnDelete();
            $table->foreignId('book_id')->nullable()->constrained('books')->nullOnDelete();
            $table->foreignId('chapter_id')->nullable()->constrained('chapters')->nullOnDelete();

            // Contexto no sermão
            $table->enum('type', ['main', 'support', 'illustration', 'other'])->default('main'); // Tipo de referência
            $table->text('context')->nullable(); // Contexto de uso no sermão
            $table->text('exegesis_notes')->nullable();
            // sermon_study_notes é criado em migration posterior (2026_*), sem FK antecipada.
            $table->unsignedBigInteger('study_note_id')->nullable();
            $table->integer('order')->default(0); // Ordem de exibição

            $table->timestamps();

            $table->index(['sermon_id', 'type', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sermon_bible_references');
    }
};
