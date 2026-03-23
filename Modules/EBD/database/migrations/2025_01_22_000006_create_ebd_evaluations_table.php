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
        Schema::create('ebd_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained('ebd_lessons')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('ebd_students')->onDelete('cascade');
            $table->decimal('score', 5, 2)->nullable(); // Nota (0-100)
            $table->text('answers')->nullable(); // JSON com respostas
            $table->text('feedback')->nullable(); // Feedback do professor
            $table->enum('status', ['pending', 'completed', 'graded'])->default('pending');
            $table->foreignId('graded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('graded_at')->nullable();
            $table->timestamps();

            $table->unique(['lesson_id', 'student_id']);
            $table->index(['student_id', 'status']);
            $table->index(['lesson_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ebd_evaluations');
    }
};
