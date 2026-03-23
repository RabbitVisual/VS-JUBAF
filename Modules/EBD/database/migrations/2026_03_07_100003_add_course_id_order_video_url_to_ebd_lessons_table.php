<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('ebd_lessons')) {
            return;
        }

        Schema::table('ebd_lessons', function (Blueprint $table) {
            if (! Schema::hasColumn('ebd_lessons', 'course_id')) {
                $table->foreignId('course_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('ebd_courses')
                    ->nullOnDelete();
            }
            if (! Schema::hasColumn('ebd_lessons', 'order')) {
                $table->unsignedInteger('order')->default(0)->after('course_id');
            }
            if (! Schema::hasColumn('ebd_lessons', 'video_url')) {
                $table->string('video_url', 500)->nullable()->after('description');
            }
        });

    }

    public function down(): void
    {
        if (! Schema::hasTable('ebd_lessons')) {
            return;
        }

        Schema::table('ebd_lessons', function (Blueprint $table) {
            if (Schema::hasColumn('ebd_lessons', 'course_id')) {
                $table->dropForeign(['course_id']);
                $table->dropColumn('course_id');
            }
            if (Schema::hasColumn('ebd_lessons', 'order')) {
                $table->dropColumn('order');
            }
            if (Schema::hasColumn('ebd_lessons', 'video_url')) {
                $table->dropColumn('video_url');
            }
        });
    }
};
