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
        Schema::table('bible_favorites', function (Blueprint $table) {
            if (! Schema::hasColumn('bible_favorites', 'color')) {
                $table->string('color')->nullable()->after('verse_id');
            }
            if (! Schema::hasColumn('bible_favorites', 'note')) {
                $table->text('note')->nullable()->after('color');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bible_favorites', function (Blueprint $table) {
            $table->dropColumn(['color', 'note']);
        });
    }
};
