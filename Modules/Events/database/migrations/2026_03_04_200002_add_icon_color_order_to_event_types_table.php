<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_types', function (Blueprint $table) {
            if (!Schema::hasColumn('event_types', 'icon')) {
                $table->string('icon', 100)->default('calendar')->after('slug');
            }
            if (!Schema::hasColumn('event_types', 'color')) {
                $table->string('color', 30)->default('#6B7280')->after('icon');
            }
            if (!Schema::hasColumn('event_types', 'order')) {
                $table->unsignedSmallInteger('order')->default(99)->after('color');
            }
        });
    }

    public function down(): void
    {
        Schema::table('event_types', function (Blueprint $table) {
            $table->dropColumn(['icon', 'color', 'order']);
        });
    }
};
