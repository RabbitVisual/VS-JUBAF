<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('social_campaigns') && ! Schema::hasColumn('social_campaigns', 'ministry_id')) {
            Schema::table('social_campaigns', function (Blueprint $table) {
                $table->foreignId('ministry_id')
                    ->nullable()
                    ->after('status')
                    ->constrained('ministries')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('social_campaigns') && Schema::hasColumn('social_campaigns', 'ministry_id')) {
            Schema::table('social_campaigns', function (Blueprint $table) {
                $table->dropForeign(['ministry_id']);
                $table->dropColumn('ministry_id');
            });
        }
    }
};

