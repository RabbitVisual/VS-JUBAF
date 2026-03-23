<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('ebd_courses') || ! Schema::hasTable('ebd_classes')) {
            return;
        }

        $classes = DB::table('ebd_classes')->whereNull('course_id')->get();
        foreach ($classes as $class) {
            $slug = \Illuminate\Support\Str::slug($class->name).'-'.uniqid();
            $courseId = DB::table('ebd_courses')->insertGetId([
                'name' => 'Currículo – '.$class->name,
                'slug' => $slug,
                'description' => null,
                'order' => 0,
                'is_active' => true,
                'homologation_status' => 'approved',
                'approved_at' => now(),
                'approved_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            DB::table('ebd_classes')->where('id', $class->id)->update(['course_id' => $courseId]);

            $lessons = DB::table('ebd_lessons')->where('class_id', $class->id)->orderBy('lesson_date')->orderBy('id')->get();
            $order = 0;
            foreach ($lessons as $lesson) {
                DB::table('ebd_lessons')->where('id', $lesson->id)->update([
                    'course_id' => $courseId,
                    'order' => $order++,
                ]);
            }
        }
    }

    public function down(): void
    {
        // Optional: clear course_id from classes/lessons; do not drop courses with potential manual data
    }
};
