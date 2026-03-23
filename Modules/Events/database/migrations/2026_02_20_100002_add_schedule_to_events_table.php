<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('events', 'schedule')) {
            Schema::table('events', function (Blueprint $table) {
                $table->json('schedule')->nullable()->after('form_fields');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('events', 'schedule')) {
            Schema::table('events', function (Blueprint $table) {
                $table->dropColumn('schedule');
            });
        }
    }
};
