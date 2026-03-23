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
        Schema::table('event_price_rules', function (Blueprint $table) {
            if (!Schema::hasColumn('event_price_rules', 'rule_type')) {
                $table->string('rule_type')->nullable()->after('label');
            }
            if (!Schema::hasColumn('event_price_rules', 'member_status')) {
                $table->string('member_status')->nullable()->after('rule_type');
            }
            if (!Schema::hasColumn('event_price_rules', 'participant_type')) {
                $table->string('participant_type')->nullable()->after('member_status');
            }
            if (!Schema::hasColumn('event_price_rules', 'discount_code')) {
                $table->string('discount_code')->nullable()->after('participant_type');
            }
            if (!Schema::hasColumn('event_price_rules', 'date_from')) {
                $table->dateTime('date_from')->nullable()->after('discount_code');
            }
            if (!Schema::hasColumn('event_price_rules', 'date_to')) {
                $table->dateTime('date_to')->nullable()->after('date_from');
            }
            if (!Schema::hasColumn('event_price_rules', 'min_participants')) {
                $table->integer('min_participants')->nullable()->after('date_to');
            }
            if (!Schema::hasColumn('event_price_rules', 'max_participants')) {
                $table->integer('max_participants')->nullable()->after('min_participants');
            }
            if (!Schema::hasColumn('event_price_rules', 'location')) {
                $table->string('location')->nullable()->after('max_participants');
            }
            if (!Schema::hasColumn('event_price_rules', 'discount_percentage')) {
                $table->decimal('discount_percentage', 10, 2)->nullable()->after('location');
            }
            if (!Schema::hasColumn('event_price_rules', 'discount_fixed')) {
                $table->decimal('discount_fixed', 10, 2)->nullable()->after('discount_percentage');
            }
            if (!Schema::hasColumn('event_price_rules', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('discount_fixed');
            }
            if (!Schema::hasColumn('event_price_rules', 'priority')) {
                $table->integer('priority')->default(0)->after('is_active');
            }
            if (!Schema::hasColumn('event_price_rules', 'conditions')) {
                $table->json('conditions')->nullable()->after('priority');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_price_rules', function (Blueprint $table) {
            $table->dropColumn([
                'rule_type',
                'member_status',
                'participant_type',
                'discount_code',
                'date_from',
                'date_to',
                'min_participants',
                'max_participants',
                'location',
                'discount_percentage',
                'discount_fixed',
                'is_active',
                'priority',
                'conditions',
            ]);
        });
    }
};
