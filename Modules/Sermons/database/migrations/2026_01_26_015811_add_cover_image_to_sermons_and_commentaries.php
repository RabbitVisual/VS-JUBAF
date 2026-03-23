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
        Schema::table('sermons', function (Blueprint $table) {
            $table->string('cover_image')->nullable()->after('full_content');
        });

        Schema::table('bible_commentaries', function (Blueprint $table) {
            $table->string('cover_image')->nullable()->after('content');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sermons', function (Blueprint $table) {
            $table->dropColumn('cover_image');
        });

        Schema::table('bible_commentaries', function (Blueprint $table) {
            $table->dropColumn('cover_image');
        });
    }
};
