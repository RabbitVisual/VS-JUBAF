<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Integração com Tesouraria: campanhas sociais podem vincular campanha financeira;
     * assistências financeiras registram despesa na tesouraria.
     */
    public function up(): void
    {
        Schema::table('social_campaigns', function (Blueprint $table) {
            $table->foreignId('treasury_campaign_id')->nullable()->after('status')
                ->constrained('campaigns')->nullOnDelete();
        });

        Schema::table('social_assistances', function (Blueprint $table) {
            $table->decimal('amount', 12, 2)->nullable()->after('description');
            $table->foreignId('financial_entry_id')->nullable()->after('amount')
                ->constrained('financial_entries')->nullOnDelete();
        });

        Schema::table('social_assistances', function (Blueprint $table) {
            $table->foreignId('social_pantry_item_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('social_assistances', function (Blueprint $table) {
            $table->dropForeign(['financial_entry_id']);
            $table->dropColumn(['amount', 'financial_entry_id']);
        });
        Schema::table('social_campaigns', function (Blueprint $table) {
            $table->dropForeign(['treasury_campaign_id']);
        });
    }
};
