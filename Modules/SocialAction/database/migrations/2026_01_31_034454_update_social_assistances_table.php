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
        Schema::table('social_assistances', function (Blueprint $table) {
            $table->foreignId('kit_id')->nullable()->constrained('social_kits')->nullOnDelete()->after('beneficiary_id');
            $table->text('notes')->nullable()->change(); // Encrypted cast handling in model
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('social_assistances', function (Blueprint $table) {
            $table->dropForeign(['kit_id']);
            $table->dropColumn('kit_id');
        });
    }
};
