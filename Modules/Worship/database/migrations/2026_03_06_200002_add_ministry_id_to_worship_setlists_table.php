<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('worship_setlists') && ! Schema::hasColumn('worship_setlists', 'ministry_id')) {
            Schema::table('worship_setlists', function (Blueprint $table) {
                $table->foreignId('ministry_id')
                    ->nullable()
                    ->after('leader_id')
                    ->constrained('ministries')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('worship_setlists') && Schema::hasColumn('worship_setlists', 'ministry_id')) {
            Schema::table('worship_setlists', function (Blueprint $table) {
                $table->dropForeign(['ministry_id']);
                $table->dropColumn('ministry_id');
            });
        }
    }
};

