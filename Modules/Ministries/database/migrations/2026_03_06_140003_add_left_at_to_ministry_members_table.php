<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add left_at for explicit exit date on ministry membership.
     */
    public function up(): void
    {
        Schema::table('ministry_members', function (Blueprint $table) {
            $table->timestamp('left_at')->nullable()->after('approved_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ministry_members', function (Blueprint $table) {
            $table->dropColumn('left_at');
        });
    }
};
