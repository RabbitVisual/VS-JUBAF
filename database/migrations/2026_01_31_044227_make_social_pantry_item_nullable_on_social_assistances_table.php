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
            // Make pantry item nullable
            $table->unsignedBigInteger('social_pantry_item_id')->nullable()->change();

            // Add Kit ID if not exists
            if (!Schema::hasColumn('social_assistances', 'kit_id')) {
                $table->foreignId('kit_id')->nullable()->constrained('social_kits')->onDelete('set null');
            }

            // Add Type if not exists
            if (!Schema::hasColumn('social_assistances', 'type')) {
                $table->string('type')->default('item')->after('social_beneficiary_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('social_assistances', function (Blueprint $table) {
            // Revert is tricky because we might have nulls now, but we'll try to revert checks
            //$table->unsignedBigInteger('social_pantry_item_id')->nullable(false)->change();
            // We won't strictly revert nullable to avoid data loss on rollback if kits were used.

            if (Schema::hasColumn('social_assistances', 'kit_id')) {
                $table->dropForeign(['kit_id']);
                $table->dropColumn('kit_id');
            }
            if (Schema::hasColumn('social_assistances', 'type')) {
                $table->dropColumn('type');
            }
        });
    }
};
