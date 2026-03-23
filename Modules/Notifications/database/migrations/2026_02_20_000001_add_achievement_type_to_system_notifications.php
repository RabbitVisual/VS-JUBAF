<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds 'achievement' to system_notifications.type enum for badges/medals.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE system_notifications MODIFY COLUMN type ENUM('info', 'success', 'warning', 'error', 'achievement') DEFAULT 'info'");
        }
        if ($driver === 'pgsql') {
            DB::statement("ALTER TABLE system_notifications DROP CONSTRAINT IF EXISTS system_notifications_type_check");
            DB::statement("ALTER TABLE system_notifications ADD CONSTRAINT system_notifications_type_check CHECK (type IN ('info', 'success', 'warning', 'error', 'achievement'))");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement("UPDATE system_notifications SET type = 'success' WHERE type = 'achievement'");
            DB::statement("ALTER TABLE system_notifications MODIFY COLUMN type ENUM('info', 'success', 'warning', 'error') DEFAULT 'info'");
        }
        if ($driver === 'pgsql') {
            DB::statement("UPDATE system_notifications SET type = 'success' WHERE type = 'achievement'");
            DB::statement("ALTER TABLE system_notifications DROP CONSTRAINT IF EXISTS system_notifications_type_check");
            DB::statement("ALTER TABLE system_notifications ADD CONSTRAINT system_notifications_type_check CHECK (type IN ('info', 'success', 'warning', 'error'))");
        }
    }
};
