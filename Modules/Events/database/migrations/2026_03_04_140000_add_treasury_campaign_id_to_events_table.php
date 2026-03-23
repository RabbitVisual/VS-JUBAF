<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Optional link to Treasury Campaign for event registration income.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('events', 'treasury_campaign_id')) {
            Schema::table('events', function (Blueprint $table) {
                $table->unsignedBigInteger('treasury_campaign_id')->nullable()->after('requires_council_approval');
                if (Schema::hasTable('campaigns')) {
                    $table->foreign('treasury_campaign_id')->references('id')->on('campaigns')->onDelete('set null');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('events', 'treasury_campaign_id')) {
            return;
        }
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['treasury_campaign_id']);
            $table->dropColumn('treasury_campaign_id');
        });
    }
};
