<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('prayer_requests') && ! Schema::hasColumn('prayer_requests', 'ministry_id')) {
            Schema::table('prayer_requests', function (Blueprint $table) {
                $table->foreignId('ministry_id')
                    ->nullable()
                    ->after('category_id')
                    ->constrained('ministries')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('prayer_requests') && Schema::hasColumn('prayer_requests', 'ministry_id')) {
            Schema::table('prayer_requests', function (Blueprint $table) {
                $table->dropForeign(['ministry_id']);
                $table->dropColumn('ministry_id');
            });
        }
    }
};

