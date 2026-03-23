<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Normalize icon value from "fa-solid fa-water" to "water" (short name for Font Awesome).
     */
    private function toShortIconName(?string $value): string
    {
        if (empty($value) || ! str_contains($value, 'fa-')) {
            return $value ?? 'circle-user';
        }
        $parts = preg_split('/\s+/', trim($value));
        $short = $value;
        foreach ($parts as $p) {
            if (str_starts_with($p, 'fa-')) {
                $short = substr($p, 3);
            }
        }
        return $short;
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('badges')) {
            foreach (DB::table('badges')->get() as $row) {
                $short = $this->toShortIconName($row->icon);
                if ($short !== $row->icon) {
                    DB::table('badges')->where('id', $row->id)->update(['icon' => $short]);
                }
            }
        }

        if (Schema::hasTable('gamification_levels')) {
            foreach (DB::table('gamification_levels')->get() as $row) {
                $short = $this->toShortIconName($row->icon);
                if ($short !== $row->icon) {
                    DB::table('gamification_levels')->where('id', $row->id)->update(['icon' => $short]);
                }
            }
        }
    }

    /**
     * Reverse the migrations (no-op; we cannot reliably restore full class strings).
     */
    public function down(): void
    {
        // Intentional no-op
    }
};
