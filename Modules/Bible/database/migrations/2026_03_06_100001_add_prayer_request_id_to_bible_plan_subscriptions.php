<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * CBAV2026: Link to Intercessor prayer request when user is critically behind (≥5 days).
     */
    public function up(): void
    {
        if (Schema::hasTable('bible_plan_subscriptions') && Schema::hasTable('prayer_requests')) {
            Schema::table('bible_plan_subscriptions', function (Blueprint $table) {
                if (! Schema::hasColumn('bible_plan_subscriptions', 'prayer_request_id')) {
                    $table->foreignId('prayer_request_id')->nullable()->after('projected_end_date')->constrained('prayer_requests')->nullOnDelete();
                }
            });
        } else {
            Schema::table('bible_plan_subscriptions', function (Blueprint $table) {
                if (! Schema::hasColumn('bible_plan_subscriptions', 'prayer_request_id')) {
                    $table->unsignedBigInteger('prayer_request_id')->nullable()->after('projected_end_date');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('bible_plan_subscriptions', function (Blueprint $table) {
            if (! Schema::hasColumn('bible_plan_subscriptions', 'prayer_request_id')) {
                return;
            }
            if (Schema::hasTable('prayer_requests')) {
                $table->dropForeign(['prayer_request_id']);
            }
            $table->dropColumn('prayer_request_id');
        });
    }
};
