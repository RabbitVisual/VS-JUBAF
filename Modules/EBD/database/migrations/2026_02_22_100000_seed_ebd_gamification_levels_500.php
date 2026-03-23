<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Seed 500 EBD gamification levels with cumulative XP.
     * Level N requires (N-1)*100 total XP: level 1 = 0, level 2 = 100, ..., level 500 = 49_900.
     * Single source of truth in DB; users.level and progress bar are derived from this table.
     */
    public function up(): void
    {
        $table = 'ebd_gamification_levels';
        $existing = DB::table($table)->pluck('level_number')->flip()->all();

        $levels = [];
        $now = now();
        for ($n = 1; $n <= 500; $n++) {
            if (isset($existing[$n])) {
                continue;
            }
            $xpRequired = ($n - 1) * 100;
            $name = $n === 1 ? 'Iniciante' : "Nível {$n}";
            $levels[] = [
                'level_number' => $n,
                'name' => $name,
                'xp_required' => $xpRequired,
                'icon_path' => 'trophy',
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach (array_chunk($levels, 100) as $chunk) {
            DB::table($table)->insert($chunk);
        }
    }

    /**
     * Reverse: do not drop levels (data); migration only seeds.
     */
    public function down(): void
    {
        // Optional: truncate to allow re-seed. Uncomment if you want down() to clear levels.
        // DB::table('ebd_gamification_levels')->truncate();
    }
};
