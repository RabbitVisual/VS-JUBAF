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
        // 1. Upgrade Courses
        Schema::table('worship_academy_courses', function (Blueprint $table) {
            if (!Schema::hasColumn('worship_academy_courses', 'instructor_id')) {
                $table->foreignId('instructor_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('worship_academy_courses', 'status')) {
                $table->string('status')->default('draft')->after('level'); // draft, published, archived
            }
        });

        // 2. Create Modules
        if (!Schema::hasTable('worship_academy_modules')) {
            Schema::create('worship_academy_modules', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_id')->constrained('worship_academy_courses')->cascadeOnDelete();
                $table->string('title');
                $table->integer('order')->default(0);
                $table->timestamps();
            });
        }

        // 3. Upgrade Lessons
        Schema::table('worship_academy_lessons', function (Blueprint $table) {
            // Add module_id. Initially nullable to allow migration of existing lessons if any.
            if (!Schema::hasColumn('worship_academy_lessons', 'module_id')) {
                $table->foreignId('module_id')->nullable()->after('id')->constrained('worship_academy_modules')->cascadeOnDelete();
            }
            if (!Schema::hasColumn('worship_academy_lessons', 'type')) {
                $table->string('type')->default('video')->after('title'); // video, chordpro, material
            }
            if (!Schema::hasColumn('worship_academy_lessons', 'content')) {
                $table->longText('content')->nullable()->after('video_url'); // For ChordPro text or generic content
            }

            // Make course_id nullable since we are moving to module-based structure
            // We keep it for now to avoid breaking existing queries immediately, or we could drop the foreign key constraint.
            // For this overhaul, we'll assume we want to enforce module structure.
            $table->foreignId('course_id')->nullable()->change();
        });

        // 4. Create Enrollments
        if (!Schema::hasTable('worship_academy_enrollments')) {
            Schema::create('worship_academy_enrollments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('course_id')->constrained('worship_academy_courses')->cascadeOnDelete();
                $table->integer('progress_percent')->default(0);
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();

                $table->unique(['user_id', 'course_id']);
            });
        }

        // 5. Upgrade Progress
        if (Schema::hasTable('worship_musician_progress')) {
            Schema::rename('worship_musician_progress', 'worship_academy_progress');
        }

        // If it didn't exist (e.g. fresh install scenario where previous migration wasn't run, though unlikely given strict order), create it.
        // But since we have a previous migration creating it, renaming is the way.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('worship_academy_progress')) {
            Schema::rename('worship_academy_progress', 'worship_musician_progress');
        }

        Schema::dropIfExists('worship_academy_enrollments');

        Schema::table('worship_academy_lessons', function (Blueprint $table) {
            if (Schema::hasColumn('worship_academy_lessons', 'content')) {
                $table->dropColumn('content');
            }
            if (Schema::hasColumn('worship_academy_lessons', 'type')) {
                $table->dropColumn('type');
            }
            if (Schema::hasColumn('worship_academy_lessons', 'module_id')) {
                $table->dropForeign(['module_id']);
                $table->dropColumn('module_id');
            }
            // We can't easily revert the nullable change to course_id without ensuring data integrity
        });

        Schema::dropIfExists('worship_academy_modules');

        Schema::table('worship_academy_courses', function (Blueprint $table) {
            if (Schema::hasColumn('worship_academy_courses', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('worship_academy_courses', 'instructor_id')) {
                $table->dropForeign(['instructor_id']);
                $table->dropColumn('instructor_id');
            }
        });
    }
};
