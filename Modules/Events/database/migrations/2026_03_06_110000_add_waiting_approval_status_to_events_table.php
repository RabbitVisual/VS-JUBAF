<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('events')) {
            // MySQL enum adjustment; safe no-op on SQLite (tests) if column not enum.
            try {
                DB::statement("ALTER TABLE `events` MODIFY COLUMN `status` ENUM('draft','published','closed','waiting_approval') NOT NULL DEFAULT 'draft'");
            } catch (\Throwable $e) {
                // Ignore if database driver does not support this syntax (e.g. SQLite in tests)
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('events')) {
            try {
                DB::statement("ALTER TABLE `events` MODIFY COLUMN `status` ENUM('draft','published','closed') NOT NULL DEFAULT 'draft'");
            } catch (\Throwable $e) {
                // Ignore on unsupported drivers.
            }
        }
    }
};

