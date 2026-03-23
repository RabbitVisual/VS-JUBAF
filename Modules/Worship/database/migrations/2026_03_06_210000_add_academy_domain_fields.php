<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds category, biblical_reflection to courses; teacher_tips to lessons; worship_lesson_materials table.
     */
    public function up(): void
    {
        Schema::table('worship_academy_courses', function (Blueprint $table) {
            if (! Schema::hasColumn('worship_academy_courses', 'category')) {
                $table->string('category')->nullable()->after('level'); // vocal, instrumental, teoria, espiritualidade
            }
            if (! Schema::hasColumn('worship_academy_courses', 'biblical_reflection')) {
                $table->text('biblical_reflection')->nullable()->after('description');
            }
        });

        Schema::table('worship_academy_lessons', function (Blueprint $table) {
            if (! Schema::hasColumn('worship_academy_lessons', 'teacher_tips')) {
                $table->text('teacher_tips')->nullable()->after('content');
            }
        });

        if (! Schema::hasTable('worship_lesson_materials')) {
            Schema::create('worship_lesson_materials', function (Blueprint $table) {
                $table->id();
                $table->foreignId('lesson_id')->constrained('worship_academy_lessons')->cascadeOnDelete();
                $table->string('type')->default('other'); // pdf, audio, guitar_pro, other
                $table->string('label');
                $table->string('file_path');
                $table->unsignedInteger('order')->default(0);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('worship_lesson_materials');

        Schema::table('worship_academy_lessons', function (Blueprint $table) {
            if (Schema::hasColumn('worship_academy_lessons', 'teacher_tips')) {
                $table->dropColumn('teacher_tips');
            }
        });

        Schema::table('worship_academy_courses', function (Blueprint $table) {
            if (Schema::hasColumn('worship_academy_courses', 'biblical_reflection')) {
                $table->dropColumn('biblical_reflection');
            }
            if (Schema::hasColumn('worship_academy_courses', 'category')) {
                $table->dropColumn('category');
            }
        });
    }
};
