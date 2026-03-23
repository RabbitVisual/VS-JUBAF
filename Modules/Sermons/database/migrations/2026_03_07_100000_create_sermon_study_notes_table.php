<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sermon_study_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sermon_id')->nullable()->constrained('sermons')->nullOnDelete();
            $table->string('reference_text'); // e.g. "João 3:16"
            $table->foreignId('book_id')->nullable()->constrained('books')->nullOnDelete();
            $table->foreignId('chapter_id')->nullable()->constrained('chapters')->nullOnDelete();
            $table->text('content'); // termos originais, cross-refs, notas (plain or JSON)
            $table->boolean('is_global')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'reference_text']);
            $table->index(['sermon_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sermon_study_notes');
    }
};
