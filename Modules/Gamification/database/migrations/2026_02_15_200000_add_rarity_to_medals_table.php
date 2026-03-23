<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medals', function (Blueprint $table) {
            $table->string('rarity', 20)->nullable()->after('color');
        });
        $medals = DB::table('medals')->get();
        foreach ($medals as $m) {
            $rarity = match ($m->color ?? '') {
                'rose' => 'bronze',
                'amber' => 'silver',
                'emerald' => 'gold',
                default => 'silver',
            };
            DB::table('medals')->where('id', $m->id)->update(['rarity' => $rarity]);
        }
    }

    public function down(): void
    {
        Schema::table('medals', function (Blueprint $table) {
            $table->dropColumn('rarity');
        });
    }
};
