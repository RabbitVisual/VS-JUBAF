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
        // 5. ebd_lesson_media
        Schema::create('ebd_lesson_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained('ebd_lessons')->cascadeOnDelete();
            $table->enum('type', ['local_video', 'pdf', 'audio']);
            $table->string('path');
            $table->string('title');
            $table->integer('duration_seconds')->nullable();
            $table->timestamps();
        });

        // 6. ebd_student_progress
        Schema::create('ebd_student_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('lesson_id')->constrained('ebd_lessons')->cascadeOnDelete();
            $table->enum('status', ['started', 'completed'])->default('started');
            $table->dateTime('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->unique(['user_id', 'lesson_id']); // Each user has one progress record per lesson
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ebd_student_progress');
        Schema::dropIfExists('ebd_lesson_media');
    }
};
