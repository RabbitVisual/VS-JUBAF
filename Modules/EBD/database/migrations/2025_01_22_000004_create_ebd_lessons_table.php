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
        Schema::create('ebd_lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('ebd_classes')->onDelete('cascade');
            $table->string('title'); // Título da lição
            $table->text('description')->nullable();
            $table->date('lesson_date'); // Data da aula (domingo)
            $table->time('lesson_time')->default('09:00:00');

            // Referência bíblica
            $table->string('bible_book')->nullable(); // Ex: "1 Coríntios"
            $table->integer('bible_chapter')->nullable(); // Ex: 1, 2, 3...
            $table->string('bible_verses')->nullable(); // Ex: "1-10" ou "1-5, 10-15"
            $table->string('bible_version')->default('nvi'); // Versão da Bíblia

            // Conteúdo da lição
            $table->text('objective')->nullable(); // Objetivo da lição
            $table->text('introduction')->nullable(); // Introdução
            $table->text('development')->nullable(); // Desenvolvimento
            $table->text('conclusion')->nullable(); // Conclusão
            $table->text('application')->nullable(); // Aplicação prática

            // Status
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled'])->default('scheduled');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['class_id', 'lesson_date']);
            $table->index(['bible_book', 'bible_chapter']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ebd_lessons');
    }
};
