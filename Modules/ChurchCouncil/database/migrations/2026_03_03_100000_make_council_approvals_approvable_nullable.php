<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Allow generic approval requests without a linked entity.
     */
    public function up(): void
    {
        Schema::table('council_approvals', function (Blueprint $table) {
            $table->string('approvable_type')->nullable()->change();
            $table->unsignedBigInteger('approvable_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('council_approvals', function (Blueprint $table) {
            $table->string('approvable_type')->nullable(false)->change();
            $table->unsignedBigInteger('approvable_id')->nullable(false)->change();
        });
    }
};
