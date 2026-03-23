<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (! Schema::hasColumn('events', 'ministry_id')) {
                $table->unsignedBigInteger('ministry_id')->nullable()->after('event_type_id');
                $table->foreign('ministry_id')->references('id')->on('ministries')->nullOnDelete();
            }
            if (! Schema::hasColumn('events', 'setlist_id')) {
                $table->unsignedBigInteger('setlist_id')->nullable()->after('ministry_id');
                $table->foreign('setlist_id')->references('id')->on('worship_setlists')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (Schema::hasColumn('events', 'ministry_id')) {
                $table->dropForeign(['ministry_id']);
                $table->dropColumn('ministry_id');
            }
            if (Schema::hasColumn('events', 'setlist_id')) {
                $table->dropForeign(['setlist_id']);
                $table->dropColumn('setlist_id');
            }
        });
    }
};
