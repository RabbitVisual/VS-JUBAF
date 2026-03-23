<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Link council projects to Ministries (Baptist commissions).
     */
    public function up(): void
    {
        Schema::table('council_projects', function (Blueprint $table) {
            $table->foreignId('ministry_id')->nullable()->after('department')->constrained('ministries')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('council_projects', function (Blueprint $table) {
            $table->dropForeign(['ministry_id']);
        });
    }
};
