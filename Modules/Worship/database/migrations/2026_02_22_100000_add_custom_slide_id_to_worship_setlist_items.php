<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('worship_setlist_items', function (Blueprint $table) {
            if (! Schema::hasColumn('worship_setlist_items', 'custom_slide_id')) {
                $table->foreignId('custom_slide_id')->nullable()->after('song_id')->constrained('worship_custom_slides')->onDelete('cascade');
            }
        });
    }

    public function down(): void
    {
        Schema::table('worship_setlist_items', function (Blueprint $table) {
            if (Schema::hasColumn('worship_setlist_items', 'custom_slide_id')) {
                $table->dropForeign(['custom_slide_id']);
            }
        });
    }
};
