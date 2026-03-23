<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add 'deacon' to council_role enum (Baptist model).
     */
    public function up(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE council_members MODIFY council_role ENUM('president','vice_president','secretary','treasurer','member','pastor','deacon') DEFAULT 'member'");
        } elseif ($driver === 'pgsql') {
            DB::statement("ALTER TABLE council_members ALTER COLUMN council_role TYPE VARCHAR(32)");
        }
        // SQLite does not support altering column types directly, and the column is likely created as a generic string/varchar already.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE council_members MODIFY council_role ENUM('president','vice_president','secretary','treasurer','member','pastor') DEFAULT 'member'");
        }
    }
};
