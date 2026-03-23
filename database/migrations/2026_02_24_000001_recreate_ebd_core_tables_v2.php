<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('ebd_classes')) {
            Schema::create('ebd_classes', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->enum('age_group', ['adult', 'youth', 'teen', 'children'])->default('adult');
                $table->text('description')->nullable();
                $table->string('room')->nullable();
                $table->string('schedule_time')->nullable();
                $table->unsignedInteger('max_students')->default(30);
                $table->boolean('is_active')->default(true);
                $table->unsignedInteger('order')->default(0);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (! Schema::hasTable('ebd_teachers')) {
            Schema::create('ebd_teachers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('class_id')->constrained('ebd_classes')->cascadeOnDelete();
                $table->enum('role', ['teacher', 'assistant', 'substitute'])->default('teacher');
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->boolean('is_active')->default(true);
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (! Schema::hasTable('ebd_students')) {
            Schema::create('ebd_students', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('class_id')->constrained('ebd_classes')->cascadeOnDelete();
                $table->date('enrollment_date')->nullable();
                $table->date('graduation_date')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (! Schema::hasTable('ebd_lessons')) {
            Schema::create('ebd_lessons', function (Blueprint $table) {
                $table->id();
                $table->foreignId('class_id')->constrained('ebd_classes')->cascadeOnDelete();
                $table->string('title');
                $table->text('description')->nullable();
                $table->date('lesson_date');
                $table->string('lesson_time')->nullable();
                $table->string('bible_book')->nullable();
                $table->unsignedInteger('bible_chapter')->nullable();
                $table->string('bible_verses')->nullable();
                $table->string('bible_version')->nullable();
                $table->text('objective')->nullable();
                $table->text('introduction')->nullable();
                $table->text('development')->nullable();
                $table->text('conclusion')->nullable();
                $table->text('application')->nullable();
                $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled'])->default('scheduled');
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (! Schema::hasTable('ebd_attendance')) {
            Schema::create('ebd_attendance', function (Blueprint $table) {
                $table->id();
                $table->foreignId('lesson_id')->constrained('ebd_lessons')->cascadeOnDelete();
                $table->foreignId('student_id')->constrained('ebd_students')->cascadeOnDelete();
                $table->enum('status', ['present', 'absent', 'late', 'excused'])->default('absent');
                $table->time('arrival_time')->nullable();
                $table->text('notes')->nullable();
                $table->foreignId('registered_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->unique(['lesson_id', 'student_id']);
            });
        }

        if (! Schema::hasTable('ebd_evaluations')) {
            Schema::create('ebd_evaluations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('lesson_id')->constrained('ebd_lessons')->cascadeOnDelete();
                $table->foreignId('student_id')->constrained('ebd_students')->cascadeOnDelete();
                $table->decimal('score', 5, 2)->nullable();
                $table->json('answers')->nullable();
                $table->text('feedback')->nullable();
                $table->enum('status', ['pending', 'completed', 'graded'])->default('pending');
                $table->foreignId('graded_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('completed_at')->nullable();
                $table->timestamp('graded_at')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('ebd_lesson_materials')) {
            Schema::create('ebd_lesson_materials', function (Blueprint $table) {
                $table->id();
                $table->foreignId('lesson_id')->constrained('ebd_lessons')->cascadeOnDelete();
                $table->string('title');
                $table->enum('type', ['pdf', 'image', 'video', 'link', 'document', 'presentation'])->default('document');
                $table->string('file_path')->nullable();
                $table->string('url', 500)->nullable();
                $table->text('description')->nullable();
                $table->unsignedInteger('order')->default(0);
                $table->boolean('is_required')->default(false);
                $table->boolean('is_public')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('ebd_lesson_questions')) {
            Schema::create('ebd_lesson_questions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('lesson_id')->constrained('ebd_lessons')->cascadeOnDelete();
                $table->text('question');
                $table->enum('type', ['multiple_choice', 'true_false', 'short_answer', 'essay'])->default('multiple_choice');
                $table->json('options')->nullable();
                $table->text('correct_answer')->nullable();
                $table->unsignedInteger('points')->default(10);
                $table->unsignedInteger('order')->default(0);
                $table->boolean('is_required')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('ebd_lesson_media')) {
            Schema::create('ebd_lesson_media', function (Blueprint $table) {
                $table->id();
                $table->foreignId('lesson_id')->constrained('ebd_lessons')->cascadeOnDelete();
                $table->enum('type', ['local_video', 'pdf', 'audio'])->default('pdf');
                $table->string('path');
                $table->string('title');
                $table->unsignedInteger('duration_seconds')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ebd_lesson_media');
        Schema::dropIfExists('ebd_lesson_questions');
        Schema::dropIfExists('ebd_lesson_materials');
        Schema::dropIfExists('ebd_evaluations');
        Schema::dropIfExists('ebd_attendance');
        Schema::dropIfExists('ebd_lessons');
        Schema::dropIfExists('ebd_students');
        Schema::dropIfExists('ebd_teachers');
        Schema::dropIfExists('ebd_classes');
    }
};
