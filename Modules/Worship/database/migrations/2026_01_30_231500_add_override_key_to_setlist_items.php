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
        Schema::table('worship_setlist_items', function (Blueprint $table) {
            if (!Schema::hasColumn('worship_setlist_items', 'override_key')) {
                $table->string('override_key')->nullable()->after('song_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('worship_setlist_items', function (Blueprint $table) {
            if (Schema::hasColumn('worship_setlist_items', 'override_key')) {
                $table->dropColumn('override_key');
            }
        });
    }
};
