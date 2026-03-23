<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Add lideranca role for Gabinete liderancaal access.
     */
    public function up(): void
    {
        if (DB::table('roles')->where('slug', 'lideranca')->doesntExist()) {
            DB::table('roles')->insert([
                'name' => 'lideranca',
                'slug' => 'lideranca',
                'description' => 'Acesso ao Gabinete liderancaal e painel admin (gestão ministerial)',
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
        DB::table('roles')->where('slug', 'lideranca')->delete();
    }
};
