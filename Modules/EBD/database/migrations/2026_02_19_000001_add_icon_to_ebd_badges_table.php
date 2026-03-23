<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('ebd_badges') && ! Schema::hasColumn('ebd_badges', 'icon')) {
            Schema::table('ebd_badges', function (Blueprint $table) {
                $table->string('icon', 80)->nullable()->after('description');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('ebd_badges') && Schema::hasColumn('ebd_badges', 'icon')) {
            Schema::table('ebd_badges', function (Blueprint $table) {
                $table->dropColumn('icon');
            });
        }
    }
};
