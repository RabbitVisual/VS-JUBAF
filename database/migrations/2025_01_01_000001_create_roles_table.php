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
        if (! Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique(); // admin, membro
                $table->string('slug')->unique(); // admin, membro
                $table->text('description')->nullable();
                $table->timestamps();
            });

            // Insert default roles
            DB::table('roles')->insert([
                ['name' => 'Administrador', 'slug' => 'admin', 'description' => 'Acesso total ao sistema', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Membro', 'slug' => 'membro', 'description' => 'Membro da igreja', 'created_at' => now(), 'updated_at' => now()],
            ]);
        } else {
            // Se a tabela já existe, apenas insere os roles se não existirem
            if (DB::table('roles')->where('slug', 'admin')->doesntExist()) {
                DB::table('roles')->insert([
                    ['name' => 'Administrador', 'slug' => 'admin', 'description' => 'Acesso total ao sistema', 'created_at' => now(), 'updated_at' => now()],
                ]);
            }
            if (DB::table('roles')->where('slug', 'membro')->doesntExist()) {
                DB::table('roles')->insert([
                    ['name' => 'Membro', 'slug' => 'membro', 'description' => 'Membro da igreja', 'created_at' => now(), 'updated_at' => now()],
                ]);
            }
        }

        // Add foreign key constraint to users table if role_id column exists
        if (Schema::hasColumn('users', 'role_id')) {
            $hasForeignKey = false;

            if (DB::getDriverName() === 'mysql' || DB::getDriverName() === 'mariadb') {
                $foreignKeys = DB::select("
                    SELECT CONSTRAINT_NAME
                    FROM information_schema.KEY_COLUMN_USAGE
                    WHERE TABLE_SCHEMA = DATABASE()
                    AND TABLE_NAME = 'users'
                    AND COLUMN_NAME = 'role_id'
                    AND REFERENCED_TABLE_NAME IS NOT NULL
                ");
                $hasForeignKey = ! empty($foreignKeys);
            } else {
                // For other drivers (like SQLite during tests), we can't easily check for the specific FK name
                // so we attempt to add it if it's the first run or handle it gracefully.
                // In SQLite tests, the table is fresh so it shouldn't have it yet.
            }

            if (! $hasForeignKey) {
                try {
                    Schema::table('users', function (Blueprint $table) {
                        $table->foreign('role_id')->references('id')->on('roles')->onDelete('restrict');
                    });
                } catch (\Exception $e) {
                    // Ignore if already exists
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
        });
        Schema::dropIfExists('roles');
    }
};
