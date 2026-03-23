<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Optional integration with Assets: link course to a suggested resource (e.g. instrument, amplifier).
     */
    public function up(): void
    {
        Schema::table('worship_academy_courses', function (Blueprint $table) {
            if (! Schema::hasColumn('worship_academy_courses', 'asset_id')) {
                $table->unsignedBigInteger('asset_id')->nullable()->after('instrument_id');
                if (Schema::hasTable('assets')) {
                    $table->foreign('asset_id')->references('id')->on('assets')->nullOnDelete();
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('worship_academy_courses', function (Blueprint $table) {
            if (Schema::hasColumn('worship_academy_courses', 'asset_id')) {
                if (Schema::hasTable('assets')) {
                    $table->dropForeign(['asset_id']);
                }
                $table->dropColumn('asset_id');
            }
        });
    }
};
