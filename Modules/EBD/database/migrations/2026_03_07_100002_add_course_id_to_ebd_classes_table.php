<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('ebd_classes') || Schema::hasColumn('ebd_classes', 'course_id')) {
            return;
        }

        Schema::table('ebd_classes', function (Blueprint $table) {
            $table->foreignId('course_id')
                ->nullable()
                ->after('id')
                ->constrained('ebd_courses')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (Schema::hasTable('ebd_classes') && Schema::hasColumn('ebd_classes', 'course_id')) {
            Schema::table('ebd_classes', function (Blueprint $table) {
                $table->dropForeign(['course_id']);
                $table->dropColumn('course_id');
            });
        }
    }
};
