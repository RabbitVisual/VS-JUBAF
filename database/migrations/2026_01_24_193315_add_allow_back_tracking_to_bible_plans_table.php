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
        Schema::table('bible_plans', function (Blueprint $table) {
            $table->boolean('allow_back_tracking')->default(true)->after('reading_mode')->comment('Allow users to access completed days');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bible_plans', function (Blueprint $table) {
            $table->dropColumn('allow_back_tracking');
        });
    }
};
