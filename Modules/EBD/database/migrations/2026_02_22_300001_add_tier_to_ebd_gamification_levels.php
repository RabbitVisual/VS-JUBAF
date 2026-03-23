<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Add tier (bronze/silver/gold/platinum) for level styling. 1-125 bronze, 126-250 silver, 251-375 gold, 376-500 platinum.
     */
    public function up(): void
    {
        if (!Schema::hasTable('ebd_gamification_levels')) {
            return;
        }

        Schema::table('ebd_gamification_levels', function (Blueprint $table) {
            if (!Schema::hasColumn('ebd_gamification_levels', 'tier')) {
                $table->string('tier', 20)->default('bronze')->after('icon_path');
            }
        });

        $this->fillTiers();
    }

    private function fillTiers(): void
    {
        DB::table('ebd_gamification_levels')->where('level_number', '<=', 125)->update(['tier' => 'bronze']);
        DB::table('ebd_gamification_levels')->whereBetween('level_number', [126, 250])->update(['tier' => 'silver']);
        DB::table('ebd_gamification_levels')->whereBetween('level_number', [251, 375])->update(['tier' => 'gold']);
        DB::table('ebd_gamification_levels')->where('level_number', '>=', 376)->update(['tier' => 'platinum']);
    }

    public function down(): void
    {
        if (Schema::hasTable('ebd_gamification_levels') && Schema::hasColumn('ebd_gamification_levels', 'tier')) {
            Schema::table('ebd_gamification_levels', function (Blueprint $table) {
                $table->dropColumn('tier');
            });
        }
    }
};
