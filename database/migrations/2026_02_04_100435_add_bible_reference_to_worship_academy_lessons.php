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
        Schema::table('worship_academy_lessons', function (Blueprint $table) {
            $table->string('bible_reference')->nullable()->after('content');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('worship_academy_lessons', function (Blueprint $table) {
            $table->dropColumn('bible_reference');
        });
    }
};
