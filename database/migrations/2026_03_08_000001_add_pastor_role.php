<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Add pastor role for Gabinete Pastoral access.
     */
    public function up(): void
    {
        if (DB::table('roles')->where('slug', 'pastor')->doesntExist()) {
            DB::table('roles')->insert([
                'name' => 'Pastor',
                'slug' => 'pastor',
                'description' => 'Acesso ao Gabinete Pastoral e painel admin (gestão ministerial)',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('roles')->where('slug', 'pastor')->delete();
    }
};
