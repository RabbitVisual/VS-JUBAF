<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // ebd, jovens, crianças, etc
            $table->string('slug')->unique(); // ebd, jovens, criancas
            $table->string('module')->nullable(); // Nome do módulo/área
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Insert default permissions
        DB::table('permissions')->insert([
            ['name' => 'Escola Bíblica Dominical', 'slug' => 'ebd', 'module' => 'EBD', 'description' => 'Acesso à área de EBD', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ministério de Jovens', 'slug' => 'jovens', 'module' => 'Jovens', 'description' => 'Acesso à área de Jovens', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ministério de Crianças', 'slug' => 'criancas', 'module' => 'Crianças', 'description' => 'Acesso à área de Crianças', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ministério de Mulheres', 'slug' => 'mulheres', 'module' => 'Mulheres', 'description' => 'Acesso à área de Mulheres', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ministério de Homens', 'slug' => 'homens', 'module' => 'Homens', 'description' => 'Acesso à área de Homens', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ministério de Música', 'slug' => 'musica', 'module' => 'Música', 'description' => 'Acesso à área de Música', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
