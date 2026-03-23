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
            $table->foreignId('registration_segment_id')
                ->nullable()
                ->after('event_id')
                ->constrained('event_registration_segments')
                ->nullOnDelete();
            $table->index('registration_segment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_price_rules', function (Blueprint $table) {
            $table->dropForeign(['registration_segment_id']);
            $table->dropIndex(['registration_segment_id']);
        });
    }
};
