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
        Schema::table('event_registration_segments', function (Blueprint $table) {
            $table->json('price_rule_types')->nullable()->after('price');
        });

        // Migrate existing price_rule_type into price_rule_types array
        $segments = \Illuminate\Support\Facades\DB::table('event_registration_segments')
            ->whereNotNull('price_rule_type')
            ->where('price_rule_type', '!=', '')
            ->get();
        foreach ($segments as $seg) {
            \Illuminate\Support\Facades\DB::table('event_registration_segments')
                ->where('id', $seg->id)
                ->update(['price_rule_types' => json_encode([$seg->price_rule_type])]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_registration_segments', function (Blueprint $table) {
            $table->dropColumn('price_rule_types');
        });
    }
};
