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
        Schema::create('ebd_lesson_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained('ebd_lessons')->onDelete('cascade');
            $table->string('title');
            $table->enum('type', ['pdf', 'image', 'video', 'link', 'document', 'presentation'])->default('document');
            $table->string('file_path')->nullable(); // Caminho do arquivo
            $table->text('url')->nullable(); // URL externa
            $table->text('description')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_required')->default(false);
            $table->boolean('is_public')->default(true); // Visível para alunos
            $table->timestamps();
            $table->softDeletes();

            $table->index(['lesson_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ebd_lesson_materials');
    }
};
