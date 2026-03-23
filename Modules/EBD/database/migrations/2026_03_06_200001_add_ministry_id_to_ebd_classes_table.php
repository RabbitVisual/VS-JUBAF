<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('ebd_classes') && ! Schema::hasColumn('ebd_classes', 'ministry_id')) {
            Schema::table('ebd_classes', function (Blueprint $table) {
                $table->foreignId('ministry_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('ministries')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('ebd_classes') && Schema::hasColumn('ebd_classes', 'ministry_id')) {
            Schema::table('ebd_classes', function (Blueprint $table) {
                $table->dropForeign(['ministry_id']);
                $table->dropColumn('ministry_id');
            });
        }
    }
};

