<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('worship_rosters', function (Blueprint $table) {
            if (Schema::hasColumn('worship_rosters', 'is_confirmed')) {
                // To avoid relying on doctrine/dbal for renaming when it might not be installed,
                // we'll just add the new columns. We can drop is_confirmed if we want, but let's
                // just add what's missing so the codebase doesn't break.
                $table->string('status')->default('pending')->after('instrument_id'); // pending, confirmed, declined
                $table->timestamp('notified_at')->nullable()->after('status');
                $table->timestamp('responded_at')->nullable()->after('notified_at');
            }
        });

        // Migrate existing basic boolean data only if the column exists
        if (Schema::hasColumn('worship_rosters', 'is_confirmed')) {
            \DB::table('worship_rosters')->update([
                'status' => \DB::raw("CASE WHEN is_confirmed = 1 THEN 'confirmed' ELSE 'pending' END"),
            ]);
        }

        Schema::table('worship_rosters', function (Blueprint $table) {
            if (Schema::hasColumn('worship_rosters', 'is_confirmed')) {
                $table->dropColumn('is_confirmed');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('worship_rosters', function (Blueprint $table) {
            if (! Schema::hasColumn('worship_rosters', 'is_confirmed')) {
                $table->boolean('is_confirmed')->default(false);
            }
        });

        if (Schema::hasColumn('worship_rosters', 'status')) {
            \DB::table('worship_rosters')->update([
                'is_confirmed' => \DB::raw("CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END"),
            ]);
        }

        Schema::table('worship_rosters', function (Blueprint $table) {
            $table->dropColumn(['status', 'notified_at', 'responded_at']);
        });
    }
};
